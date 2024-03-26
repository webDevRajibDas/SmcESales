<?php
App::uses('AppModel', 'Model');
/**
 * BonusCardType Model
 *
 * @property BonusCard $BonusCard
 */
class BonusCardType extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';


	public $validate = array(
			'name' => array(
				'mustNotEmpty' => array(
							'rule' => 'notEmpty',
							'message'=> 'Name field is required.'
				),
				'isUnique' => array(
							'rule' => 'isUnique',
							'message'=> 'Name already exists.'
				)
			)
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'BonusCard' => array(
			'className' => 'BonusCard',
			'foreignKey' => 'bonus_card_type_id',
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
