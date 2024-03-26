<?php
App::uses('AppModel', 'Model');
/**
 * GiftItem Model
 *
 * @property GiftItem $GiftItem
 */
class GiftItem extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';

	// data filter
	public function filter($params, $conditions) {
		// pr($params);exit;
        $conditions = array();
		if (!empty($params['GiftItem.thana_id'])) {
            $conditions[] = array('Thana.id' => $params['GiftItem.thana_id']);
        }
        elseif (!empty($params['GiftItem.territory_id'])) {
            $conditions[] = array('Territory.id' => $params['GiftItem.territory_id']);
        }

		elseif (!empty($params['GiftItem.office_id'])) {
            $conditions[] = array('SalesPerson.office_id' => $params['GiftItem.office_id']);
        }
        elseif (!empty($params['GiftItem.region_office_id'])) {
            $conditions[] = array('Office.parent_office_id' => $params['GiftItem.region_office_id']);
        }

		if (!empty($params['GiftItem.so_id'])) {
            $conditions[] = array('GiftItem.so_id' => $params['GiftItem.so_id']);
        }
		if (isset($params['GiftItem.date_from'])!='') {
            $conditions[] = array('GiftItem.date >=' => Date('Y-m-d',strtotime($params['GiftItem.date_from'])));
        }
		if (isset($params['GiftItem.date_to'])!='') {
            $conditions[] = array('GiftItem.date <=' => Date('Y-m-d',strtotime($params['GiftItem.date_to'])));
        }	
        return $conditions;
    }
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'so_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Outlet' => array(
			'className' => 'Outlet',
			'foreignKey' => 'outlet_id',
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
		'GiftItemDetail' => array(
			'className' => 'GiftItemDetail',
			'foreignKey' => 'gift_item_id',
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
