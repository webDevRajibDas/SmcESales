<?php
App::uses('AppModel', 'Model');
/**
 * ProductPrice Model
 *
 * @property Product $Product
 * @property MeasurementUnit $MeasurementUnit
 * @property Institute $Institute
 */
class DistSrCombination extends AppModel {


/**
 * belongsTo associations
 *
 * @var array
 */
	
	public $hasMany = array(
		'DistSrProductCombination' => array(
			'className' => 'DistSrProductCombination',
			'foreignKey' => 'combination_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'dependent' => true
		)
	);
	
}
