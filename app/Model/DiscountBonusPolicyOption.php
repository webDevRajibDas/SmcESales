<?php
App::uses('AppModel', 'Model');
/**
 * GroupWiseDiscountBonusPolicy Model
 *
 * @property Product $Product
 */
class DiscountBonusPolicyOption extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(

		
	);
/**
 * belongsTo associations
 *
 * @var array
 */
	/*public $belongsTo = array(
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);*/

	public $hasMany = array(
		'DiscountBonusPolicyOptionPriceSlab' => array(
			'className' => 'DiscountBonusPolicyOptionPriceSlab',
			'foreignKey' => 'discount_bonus_policy_option_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyOptionBonusProduct' => array(
			'className' => 'DiscountBonusPolicyOptionBonusProduct',
			'foreignKey' => 'discount_bonus_policy_option_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyDefaultBonusProductSelection' => array(
			'className' => 'DiscountBonusPolicyDefaultBonusProductSelection',
			'foreignKey' => 'discount_bonus_policy_option_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyOptionExclusionProduct' => array(
			'className' => 'DiscountBonusPolicyOptionExclusionInclusionProduct',
			'foreignKey' => 'discount_bonus_policy_option_id',
			'conditions' => 'DiscountBonusPolicyOptionExclusionProduct.create_for=1',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyOptionInclusionProduct' => array(
			'className' => 'DiscountBonusPolicyOptionExclusionInclusionProduct',
			'foreignKey' => 'discount_bonus_policy_option_id',
			'conditions' => 'DiscountBonusPolicyOptionInclusionProduct.create_for=2',
			'fields' => '',
			'order' => ''
		),
	);
	

}
