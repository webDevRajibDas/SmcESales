<?php
App::uses('AppModel', 'Model');
/**
 * MeasurementUnit Model
 *
 * @property ChallanDetail $ChallanDetail
 * @property ProductMeasurement $ProductMeasurement
 * @property ProductPrice $ProductPrice
 * @property TargetForProductSale $TargetForProductSale
 */
class MeasurementUnit extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'name' => array(
			'notMustBeEmpty' => array(
				'rule' 		=> 'notEmpty',
				'message'   => 'Unit Name field required.'
			),
			'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'Unit Name already exists.'
			)
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'ChallanDetail' => array(
			'className' => 'ChallanDetail',
			'foreignKey' => 'measurement_unit_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ProductMeasurement' => array(
			'className' => 'ProductMeasurement',
			'foreignKey' => 'measurement_unit_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ProductPrice' => array(
			'className' => 'ProductPrice',
			'foreignKey' => 'measurement_unit_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'TargetForProductSale' => array(
			'className' => 'TargetForProductSale',
			'foreignKey' => 'measurement_unit_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'MemoDetail' => array(
			'className' => 'MemoDetail',
			'foreignKey' => 'measurement_unit_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
