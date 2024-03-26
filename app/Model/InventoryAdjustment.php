<?php
App::uses('AppModel', 'Model');
/**
 * InventoryAdjustment Model
 *
 * @property TransactionType $TransactionType
 * @property Store $Store
 * @property Institute $Institute
 * @property InventoryAdjustmentDetail $InventoryAdjustmentDetail
 */
class InventoryAdjustment extends AppModel {

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
		'TransactionType' => array(
			'className' => 'TransactionType',
			'foreignKey' => 'transaction_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Store' => array(
			'className' => 'Store',
			'foreignKey' => 'store_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Institute' => array(
			'className' => 'Institute',
			'foreignKey' => 'institute_id',
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
		'InventoryAdjustmentDetail' => array(
			'className' => 'InventoryAdjustmentDetail',
			'foreignKey' => 'inventory_adjustment_id',
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
		if (!empty($params['InventoryAdjustment.status'])) {
            $conditions[] = array('InventoryAdjustment.status' => $params['InventoryAdjustment.status']);
        }
		if (!empty($params['InventoryAdjustment.created_at'])) {
            $conditions[] = array('convert(varchar,InventoryAdjustment.created_at, 105)'=> $params['InventoryAdjustment.created_at']);
        }
        if(CakeSession::read('Office.parent_office_id') != 0){
			$conditions[] = array(
					array('Store.id' => CakeSession::read('Office.store_id')),
			);
		}
        return $conditions;
    }

}
