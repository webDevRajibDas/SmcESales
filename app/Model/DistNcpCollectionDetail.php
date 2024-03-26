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
class DistNcpCollectionDetail extends AppModel {

	public $useDbConfig = 'default_06';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $validate = array(
		'ncp_collection_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Receiver is required.'
					)
		),
		'product_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Sales person field is required.'
					)
		),
		'batch_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Sales person field is required.'
					)
		),
		'measurement_unit_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Sales person field is required.'
					)
		),
		'quantity' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Sales person field is required.'
					)
		)
	);
	
	public $belongsTo = array(
		'DistNcpCollection' => array(
			'className' => 'DistNcpCollection',
			'foreignKey' => 'ncp_collection_id',
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
