<?php
App::uses('AppModel', 'Model');
/**
 * TerritoryAssignHistory Model
 *
 */
class TerritoryAssignHistory extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';
	
	
	public $belongsTo = array(
		'Territory' => array(
			'className' => 'Territory',
			'foreignKey' => 'territory_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'so_id',
			'conditions' => '',
			'fields' => 'id, name',
			'order' => ''
		)
	);

}
