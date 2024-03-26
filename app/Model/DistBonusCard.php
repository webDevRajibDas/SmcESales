<?php
App::uses('AppModel', 'Model');
App::uses('UserAuthComponent', 'Usermgmt.Controller/Component');
/**
 * DistBonusCard Model
 *
 * @property InventoryStore $InventoryStore
 * @property InventoryStatus $InventoryStatus
 * @property Product $Product
 * @property Batch $Batch
 */
class DistBonusCard extends AppModel {
		
	// data filter
	public function filter($params, $conditions) {
		$conditions = array();
		
		if (!empty($params['DistBonusCard.name'])) {
            $conditions[] = array('DistBonusCard.name like' => "%".$params['DistBonusCard.name']."%"	);
        }
		if (!empty($params['DistBonusCard.bonus_card_type_id'])) {
            $conditions[] = array('DistBonusCard.bonus_card_type_id' => $params['DistBonusCard.bonus_card_type_id']);
        }
        return $conditions;
    }
	
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
	);


	public $hasMany = array(
		'DistProductsBonusCard' => array(
			'className' => 'DistProductsBonusCard',
			'foreignKey' => 'dist_bonus_card_id',
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
		'DistPeriodsBonusCard' => array(
			'className' => 'DistPeriodsBonusCard',
			'foreignKey' => 'dist_bonus_card_id',
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
