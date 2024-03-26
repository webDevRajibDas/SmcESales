<?php
App::uses('AppModel', 'Model');
/**
 * GroupWiseDiscountBonusPolicy Model
 *
 * @property Product $Product
 */
class GroupWiseDiscountBonusPolicy extends AppModel {

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
		'GroupWiseDiscountBonusPolicyToOffice' => array(
			'className' => 'GroupWiseDiscountBonusPolicyToOffice',
			'foreignKey' => 'group_wise_discount_bonus_policy_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'GroupWiseDiscountBonusPolicyToOutletGroup' => array(
			'className' => 'GroupWiseDiscountBonusPolicyToOutletGroup',
			'foreignKey' => 'group_wise_discount_bonus_policy_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'GroupWiseDiscountBonusPolicyToOutletCategory' => array(
			'className' => 'GroupWiseDiscountBonusPolicyToOutletCategory',
			'foreignKey' => 'group_wise_discount_bonus_policy_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'GroupWiseDiscountBonusPolicyProduct' => array(
			'className' => 'GroupWiseDiscountBonusPolicyProduct',
			'foreignKey' => 'group_wise_discount_bonus_policy_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'GroupWiseDiscountBonusPolicyOption' => array(
			'className' => 'GroupWiseDiscountBonusPolicyOption',
			'foreignKey' => 'group_wise_discount_bonus_policy_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		/*'GroupWiseDiscountBonusPolicyOptionBonusProduct' => array(
			'className' => 'GroupWiseDiscountBonusPolicyOptionBonusProduct',
			'foreignKey' => 'group_wise_discount_bonus_policy_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),*/
	);
	
	
	
	// data filter
	public function filter($params, $conditions) {
		if (!empty($params['GroupWiseDiscountBonusPolicy.product_id'])) {
            $conditions = array('GroupWiseDiscountBonusPolicy.product_id' => $params['GroupWiseDiscountBonusPolicy.product_id']);
        }
        return $conditions;
    }

}
