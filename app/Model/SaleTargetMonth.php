<?php
App::uses('AppModel', 'Model');
/**
 * SaleTarget Model
 *
 * @property SaleTargetMonth $SaleTargetMonth
 */
class SaleTargetMonth extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed
		public $validate = array(
		   
		);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Territory' => array(
		'className' => 'Territory',
		'foreignKey' => 'territory_id',
		'conditions' => '',
		'fields' => '',
		'order' => ''
		)
	);
		
		
}

