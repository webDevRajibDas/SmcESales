<?php
App::uses('AppModel', 'Model');
/**
 * GroupWiseDiscountBonusPolicy Model
 *
 * @property Product $Product
 */
class DiscountBonusPolicy extends AppModel
{

	public $displayField = 'name';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	public $validate = array(
		'name' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Field is required.'
			),
			/*'isUnique' => array(
					'rule' => 'isUnique',
					'message'=> 'Name already exists.'
			)*/
		),
		'start_date' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Field is required.'
			)
		),
		'end_date' => array(
			'notMustBeEmpty' => array(
				'rule'    => 'notEmpty',
				'message' => 'Field is required.'
			)
		),

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
		'DiscountBonusPolicyToSpecialGroupSo' => array(
			'className' => 'DiscountBonusPolicySetting',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => 'DiscountBonusPolicyToSpecialGroupSo.create_for=1 AND DiscountBonusPolicyToSpecialGroupSo.for_so_sr=1',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyToSpecialGroupSr' => array(
			'className' => 'DiscountBonusPolicySetting',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => 'DiscountBonusPolicyToSpecialGroupSr.create_for=1 AND DiscountBonusPolicyToSpecialGroupSr.for_so_sr=2',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyToOfficeSo' => array(
			'className' => 'DiscountBonusPolicySetting',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => 'DiscountBonusPolicyToOfficeSo.create_for=2 AND DiscountBonusPolicyToOfficeSo.for_so_sr=1',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyToOfficeSr' => array(
			'className' => 'DiscountBonusPolicySetting',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => 'DiscountBonusPolicyToOfficeSr.create_for=2 AND DiscountBonusPolicyToOfficeSr.for_so_sr=2',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyToOutletGroupSo' => array(
			'className' => 'DiscountBonusPolicySetting',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => 'DiscountBonusPolicyToOutletGroupSo.create_for=3 AND DiscountBonusPolicyToOutletGroupSo.for_so_sr=1',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyToOutletGroupSr' => array(
			'className' => 'DiscountBonusPolicySetting',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => 'DiscountBonusPolicyToOutletGroupSr.create_for=3 AND DiscountBonusPolicyToOutletGroupSr.for_so_sr=2',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyToExcludingOutletGroupSo' => array(
			'className' => 'DiscountBonusPolicySetting',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => 'DiscountBonusPolicyToExcludingOutletGroupSo.create_for=5 AND DiscountBonusPolicyToExcludingOutletGroupSo.for_so_sr=1',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyToExcludingOutletGroupSr' => array(
			'className' => 'DiscountBonusPolicySetting',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => 'DiscountBonusPolicyToExcludingOutletGroupSr.create_for=5 AND DiscountBonusPolicyToExcludingOutletGroupSr.for_so_sr=2',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyToOutletCategorySo' => array(
			'className' => 'DiscountBonusPolicySetting',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => 'DiscountBonusPolicyToOutletCategorySo.create_for=4 AND DiscountBonusPolicyToOutletCategorySo.for_so_sr=1',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyToOutletCategorySr' => array(
			'className' => 'DiscountBonusPolicySetting',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => 'DiscountBonusPolicyToOutletCategorySr.create_for=4 AND DiscountBonusPolicyToOutletCategorySr.for_so_sr=2',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyProduct' => array(
			'className' => 'DiscountBonusPolicyProduct',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyOption' => array(
			'className' => 'DiscountBonusPolicyOption',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyOptionSo' => array(
			'className' => 'DiscountBonusPolicyOption',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => 'DiscountBonusPolicyOptionSo.is_so=1',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyOptionSr' => array(
			'className' => 'DiscountBonusPolicyOption',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => 'DiscountBonusPolicyOptionSr.is_sr=1',
			'fields' => '',
			'order' => ''
		),
		'DiscountBonusPolicyOptionDB' => array(
			'className' => 'DiscountBonusPolicyOption',
			'foreignKey' => 'discount_bonus_policy_id',
			'conditions' => 'DiscountBonusPolicyOptionDB.is_db=1',
			'fields' => '',
			'order' => ''
		),
	);



	// data filter
	public function filter($params, $conditions)
	{
		if (!empty($params['GroupWiseDiscountBonusPolicy.product_id'])) {
			$conditions = array('GroupWiseDiscountBonusPolicy.product_id' => $params['GroupWiseDiscountBonusPolicy.product_id']);
		}
		return $conditions;
	}
}
