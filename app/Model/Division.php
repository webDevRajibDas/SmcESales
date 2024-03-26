<?php
App::uses('AppModel', 'Model');
/**
 * Division Model
 *
 * @property District $District
 */
class Division extends AppModel {

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
							'message'=> 'District Name field is required.'
						),
				'isUnique' => array(
							'rule' => 'isUnique',
							'message'=> 'District Name already exist.'
						),
			)
		);
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'District' => array(
			'className' => 'District',
			'foreignKey' => 'division_id',
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
