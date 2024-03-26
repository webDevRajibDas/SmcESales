<?php
App::uses('AppModel', 'Model');
/**
 * Thana Model
 *
 * @property District $District
 * @property Market $Market
 */
class Thana extends AppModel {

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
							'message'=> 'Thana Name field is required.'
				),
				'isUnique' => array(
							'rule' => 'isUnique',
							'message'=> 'Thana already exists.'
				)
			),
			'district_id' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'District Name field is required.'
				)
			)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'District' => array(
			'className' => 'District',
			'foreignKey' => 'district_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
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
			'foreignKey' => 'thana_id',
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
