<?php
App::uses('AppModel', 'Model');
/**
 * DistOrderDetail Model
 *
 */
class DistOrderDetail extends AppModel {

/**
 * Display field
 *
 * @var string
 */

	public $validate = array();

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array();
	
/**
 * BelongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'DistOrder' => array(
			'className' => 'DistOrder',
			'foreignKey' => 'dist_order_id',
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
		'MeasurementUnit' => array(
			'className' => 'MeasurementUnit',
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
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
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
