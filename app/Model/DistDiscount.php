<?php
App::uses('AppModel', 'Model');
App::uses('UserAuthComponent', 'Usermgmt.Controller/Component');
/**
 * DistDiscount Model
 *
 * @property InventoryStore $InventoryStore
 * @property InventoryStatus $InventoryStatus
 * @property Product $Product
 * @property Batch $Batch
 */
class DistDiscount extends AppModel {
		
	// data filter
	public function filter($params, $conditions) {
		$conditions = array();
        return $conditions;
    }
	
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
			'conditions' => '',
			'fields' => 'office_name',
			'order' => ''
		),
		'Distributor' => array(
			'className' => 'DistDistributor',
			'foreignKey' => 'distributor_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		)
	);


	public $hasMany = array(
		'DistDiscountDetail' => array(
			'className' => 'DistDiscountDetail',
			'foreignKey' => 'dist_discount_id',
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
}
