<?php
App::uses('AppModel', 'Model');
/**
 * DistMemoDetail Model
 *
 */
class DistMemoDetail extends AppModel {

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
		'DistMemo' => array(
			'className' => 'DistMemo',
			'foreignKey' => 'dist_memo_id',
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
