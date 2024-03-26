<?php
App::uses('AppModel', 'Model');
/**
 * ProductToProductConvert Model
 *
 * @property Product $Product
 * @property MeasurementUnit $MeasurementUnit
 * @property Institute $Institute
 */
class ProductToProductConvert extends AppModel {

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
		'Store' => array(
			'className' => 'Store',
			'foreignKey' => 'store_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);
 
 
}
