<?php
App::uses('AppModel', 'Model');
/**
 * RequisitionDetail Model
 *
 * @property Requisition $Requisition
 * @property Product $Product
 * @property MeasurementUnit $MeasurementUnit
 */
class RequisitionDetail extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';


	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Requisition' => array(
			'className' => 'Requisition',
			'foreignKey' => 'requisition_id',
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
		'VirtualProduct' => array(
			'className' => 'Product',
			'foreignKey' => 'virtual_product_id',
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
