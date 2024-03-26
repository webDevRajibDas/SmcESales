<?php
App::uses('AppModel', 'Model');
/**
 * LocationType Model
 *
 * @property Market $Market
 */
class LocationType extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
			'name' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Short Name field is required.'
				)
			)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Market' => array(
			'className' => 'Market',
			'foreignKey' => 'location_type_id',
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
