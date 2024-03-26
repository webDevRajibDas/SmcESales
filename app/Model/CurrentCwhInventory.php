<?php
App::uses('AppModel', 'Model');
App::uses('UserAuthComponent', 'Usermgmt.Controller/Component');
/**
 * CurrentCwhInventory Model
 *
 * @property InventoryStore $InventoryStore
 * @property InventoryStatus $InventoryStatus
 * @property Product $Product
 * @property Batch $Batch
 */
class CurrentCwhInventory extends AppModel {
		
	// data filter
	public function filter($params, $conditions) {
		$conditions = array();
		if(CakeSession::read('Office.parent_office_id') != 0)
		{	
			$conditions[] = array('Store.office_id' => CakeSession::read('Office.id'));
		}
		if (!empty($params['CurrentCwhInventory.product_code'])) {
            $conditions[] = array('Product.product_code' => $params['CurrentCwhInventory.product_code']);
        }
		if (!empty($params['CurrentCwhInventory.inventory_status_id'])) {
            $conditions[] = array('CurrentCwhInventory.inventory_status_id' => $params['CurrentCwhInventory.inventory_status_id']);
        }else
		{
			$conditions[] = array('CurrentCwhInventory.inventory_status_id !=' => 2);
		}	
		if (!empty($params['CurrentCwhInventory.product_id'])) {
            $conditions[] = array('CurrentCwhInventory.product_id' => $params['CurrentCwhInventory.product_id']);
        }
        if (!empty($params['CurrentCwhInventory.store_id'])) {
            $conditions[] = array('CurrentCwhInventory.store_id' => $params['CurrentCwhInventory.store_id']);
        }
		if (!empty($params['CurrentCwhInventory.product_categories_id'])) {
            $conditions[] = array('ProductCategory.id' => $params['CurrentCwhInventory.product_categories_id']);
        }
        return $conditions;
    }
	
	
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
