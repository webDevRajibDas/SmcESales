<?php
App::uses('AppModel', 'Model');
/**
 * ProductPrice Model
 *
 * @property Product $Product
 * @property MeasurementUnit $MeasurementUnit
 * @property Institute $Institute
 */
class SpecialCombination extends AppModel {


/**
 * belongsTo associations
 *
 * @var array
 */
	
	public $hasMany = array(
		'SpecialProductCombination' => array(
			'className' => 'SpecialProductCombination',
			'foreignKey' => 'combination_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'dependent' => true
		)
	);
	
}
