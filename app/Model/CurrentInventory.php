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
class CurrentInventory extends AppModel {
		
	// data filter
	public function filter($params, $conditions) {
		$conditions = array();
		if(CakeSession::read('Office.parent_office_id') != 0)
		{	
			$conditions[] = array('Store.office_id' => CakeSession::read('Office.id'));
		}
		if (!empty($params['CurrentInventory.product_code'])) {
            $conditions[] = array('Product.product_code' => $params['CurrentInventory.product_code']);
        }
		if (!empty($params['CurrentInventory.inventory_status_id'])) {
            $conditions[] = array('CurrentInventory.inventory_status_id' => $params['CurrentInventory.inventory_status_id']);
        }else
		{
			$conditions[] = array('CurrentInventory.inventory_status_id !=' => 2);
		}	
		if (!empty($params['CurrentInventory.product_id'])) {
            $conditions[] = array('CurrentInventory.product_id' => $params['CurrentInventory.product_id']);
        }
        if (!empty($params['CurrentInventory.store_id'])) {
            $conditions[] = array('CurrentInventory.store_id' => $params['CurrentInventory.store_id']);
        }
		if (!empty($params['CurrentInventory.product_categories_id'])) {
            $conditions[] = array('ProductCategory.id' => $params['CurrentInventory.product_categories_id']);
        }
        if (!empty($params['CurrentInventory.product_type_id'])) {
            $conditions[] = array('Product.product_type_id' => $params['CurrentInventory.product_type_id']);
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
