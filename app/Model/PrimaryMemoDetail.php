<?php
App::uses('AppModel', 'Model');
/**
 * ChallanDetail Model
 *
 * @property Challan $Challan
 * @property Product $Product
 * @property MeasurementUnit $MeasurementUnit
 * @property Batch $Batch
 */
class PrimaryMemoDetail extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'PrimaryMemo' => array(
			'className' => 'PrimaryMemo',
			'foreignKey' => 'primary_memo_id',
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
		'MeasurementUnit' => array(
			'className' => 'MeasurementUnit',
			'foreignKey' => 'measurement_unit_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
