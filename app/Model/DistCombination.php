<?php
App::uses('AppModel', 'Model');
/**
 * ProductPrice Model
 *
 * @property Product $Product
 * @property MeasurementUnit $MeasurementUnit
 * @property Institute $Institute
 */
class DistCombination extends AppModel {


/**
 * belongsTo associations
 *
 * @var array
 */
	
	public $hasMany = array(
		'DistProductCombination' => array(
			'className' => 'DistProductCombination',
			'foreignKey' => 'combination_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'dependent' => true
		)
	);
	
}
