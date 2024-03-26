<?php
App::uses('AppModel', 'Model');
/**
 * Bank Model
 *
 * @property SalesPerson $SalesPerson
 */
class Bank extends AppModel {
	
	public $displayField = 'id';
	public $actsAs = array('Containable');
	
	public $validate = array(
		'name' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Bank Name field is required.'
			),
			'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'Bank Name already exist.'
			),
		)
	);

}
