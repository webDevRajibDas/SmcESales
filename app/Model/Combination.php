<?php
App::uses('AppModel', 'Model');
/**
 * ProductPrice Model
 *
 * @property Product $Product
 * @property MeasurementUnit $MeasurementUnit
 * @property Institute $Institute
 */
class Combination extends AppModel {


/**
 * belongsTo associations
 *
 * @var array
 */
	
	public $hasMany = array(
		'ProductCombination' => array(
			'className' => 'ProductCombination',
			'foreignKey' => 'combination_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'dependent' => true
		)
	);
	
}
