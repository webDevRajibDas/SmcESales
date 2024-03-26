<?php
App::uses('AppModel', 'Model');
/**
 * NcpCollection Model
 *
 * @property So $So
 * @property Market $Market
 * @property Outlet $Outlet
 * @property Product $Product
 */
class NcpCollection extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'ncp_collection';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'So' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'so_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Market' => array(
			'className' => 'Market',
			'foreignKey' => 'market_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Outlet' => array(
			'className' => 'Outlet',
			'foreignKey' => 'outlet_id',
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
		)
	);
}
