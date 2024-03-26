<?php
App::uses('AppModel', 'Model');
/**
 * DiscountBonusPolicyOptionExclusionInclusionProduct Model
 *
 * @property DiscountBonusPolicy $DiscountBonusPolicy
 * @property DiscountBonusPolicyOption $DiscountBonusPolicyOption
 * @property Product $Product
 */
class DiscountBonusPolicyOptionExclusionInclusionProduct extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'DiscountBonusPolicy' => array(
			'className' => 'DiscountBonusPolicy',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyOption' => array(
			'className' => 'DiscountBonusPolicyOption',
			'foreignKey' => 'discount_bonus_policy_option_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
