<?php
App::uses('AppModel', 'Model');
/**
 * GroupWiseDiscountBonusPolicy Model
 *
 * @property Product $Product
 */
class GroupWiseDiscountBonusPolicyOption extends AppModel {


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
		'GroupWiseDiscountBonusPolicyOptionPriceSlab' => array(
			'className' => 'GroupWiseDiscountBonusPolicyOptionPriceSlab',
			'foreignKey' => 'group_wise_discount_bonus_policy_option_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'GroupWiseDiscountBonusPolicyOptionBonusProduct' => array(
			'className' => 'GroupWiseDiscountBonusPolicyOptionBonusProduct',
			'foreignKey' => 'group_wise_discount_bonus_policy_option_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);
	

}
