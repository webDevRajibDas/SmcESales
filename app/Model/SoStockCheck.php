<?php
App::uses('AppModel', 'Model');
/**
 * SoStockCheck Model
 *
 * @property So $So
 * @property Store $Store
 */
class SoStockCheck extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'so_stock_check';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';
	// data filter
	public function filter($params, $conditions) {
		$conditions = array();

		if (!empty($params['SoStockCheck.store_id'])) {
            $conditions[] = array('SoStockCheck.store_id' => $params['SoStockCheck.store_id']);
        }	
		if (!empty($params['SoStockCheck.date_from'])) {
            $conditions[] = array('convert(date,SoStockCheck.reported_time) >=' =>  Date('Y-m-d',strtotime($params['SoStockCheck.date_from'])));
        }
        if (!empty($params['SoStockCheck.date_to'])) {
            $conditions[] = array('convert(date,SoStockCheck.reported_time) <=' =>  Date('Y-m-d',strtotime($params['SoStockCheck.date_to'])));
        }
        
        return $conditions;
    }

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'so_id',
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
		)
	);
}
