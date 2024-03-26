<?php
App::uses('AppModel', 'Model');
/**
 * NcpSoStock Model
 *
 * @property Store $Store
 * @property InventoryStatus $InventoryStatus
 * @property Product $Product
 * @property TransactionType $TransactionType
 */
class NcpSoStock extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'ncp_so_stocks';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

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
		'InventoryStatus' => array(
			'className' => 'InventoryStatus',
			'foreignKey' => 'inventory_status_id',
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
		'TransactionType' => array(
			'className' => 'TransactionType',
			'foreignKey' => 'transaction_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
