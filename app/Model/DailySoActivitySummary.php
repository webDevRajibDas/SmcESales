<?php
App::uses('AppModel', 'Model');
/**
 * DailySoActivitySummary Model
 *
 * @property SalesPerson $SalesPerson
 */
class DailySoActivitySummary extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'sales_person_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
