<?php
App::uses('AppModel', 'Model');
App::uses('UserAuthComponent', 'Usermgmt.Controller/Component');

/**
 * CurrentInventory Model
 *
 * @property InventoryStore $InventoryStore
 * @property InventoryStatus $InventoryStatus
 * @property Product $Product
 * @property Batch $Batch
 */
class CurrentInventoryHistory extends AppModel {
	
	
		
	// data filter
	public function filter($params, $conditions) {
		$conditions = array();
		if(CakeSession::read('Office.parent_office_id') != 0)
		{	
			$conditions[] = array('Store.office_id' => CakeSession::read('Office.id'));
		}
		
		
		if (!empty($params['CurrentInventoryHistory.product_id'])) {
            $conditions[] = array('CurrentInventoryHistory.product_id' => $params['CurrentInventoryHistory.product_id']);
        }
        if (!empty($params['CurrentInventoryHistory.store_id'])) {
            $conditions[] = array('CurrentInventoryHistory.store_id' => $params['CurrentInventoryHistory.store_id']);
        }
		
        return $conditions;
    }
	public $useTable = 'current_inventory_history';
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Store' => array(
			'className' => 'Store',
			'foreignKey' => 'store_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'InventoryStatuses' => array(
			'className' => 'InventoryStatuses',
			'foreignKey' => 'inventory_status_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'TransactionType' => array(
			'className' => 'TransactionType',
			'foreignKey' => 'transaction_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
