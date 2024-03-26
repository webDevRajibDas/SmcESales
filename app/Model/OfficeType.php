<?php
App::uses('AppModel', 'Model');
/**
 * OfficeType Model
 *
 * @property Office $Office
 */
class OfficeType extends AppModel {

	public $displayField = 'type_name';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'type_name' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Type Name field is required.'
			)
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_type_id',
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
