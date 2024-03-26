<?php
App::uses('AppModel', 'Model');
/**
 * Order Model
 *
 */
class DistPeriodsBonusCard extends AppModel {

/**
 * Display field
 *
 * @var string
 */

	public $validate = array(
		/* 'name' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'District Name field is required.'
					),
			'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'District Name already exist.'
					),
		) */
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		/* 'Thana' => array(
			'className' => 'Thana',
			'foreignKey' => 'district_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		) */
	);
	
/**
 * BelongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'DistProductsBonusCard' => array(
			'className' => 'DistProductsBonusCard',
			'foreignKey' => 'dist_products_bonus_card_id',
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
	);
	


}
