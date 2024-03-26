<?php
App::uses('AppModel', 'Model');
/**
 * DistGiftItems Model
 *
 * @property DistGiftItems $DistGiftItems
 */
class DistGiftItem extends AppModel
{

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'id';
	// data filter
	public function filter($params, $conditions)
	{
		// pr($params);exit;
		$conditions = array();
		if (!empty($params['DistGiftItem.dist_market_id'])) {
			$conditions[] = array('DistMarket.id' => $params['DistGiftItem.dist_market_id']);
		} elseif (!empty($params['DistGiftItem.dist_route_id'])) {
			$conditions[] = array('DistRoute.id' => $params['DistGiftItem.dist_route_id']);
		} elseif (!empty($params['DistGiftItem.distributor_id'])) {
			$conditions[] = array('DistDistributor.id' => $params['DistGiftItem.distributor_id']);
		} elseif (!empty($params['DistGiftItem.office_id'])) {
			$conditions[] = array('DistDistributor.office_id' => $params['DistGiftItem.office_id']);
		} elseif (!empty($params['DistGiftItem.region_office_id'])) {
			$conditions[] = array('Office.parent_office_id' => $params['DistGiftItem.region_office_id']);
		}
		if (isset($params['DistGiftItem.date_from']) != '') {
			$conditions[] = array('DistGiftItem.date >=' => Date('Y-m-d', strtotime($params['DistGiftItem.date_from'])));
		}
		if (isset($params['DistGiftItem.date_to']) != '') {
			$conditions[] = array('DistGiftItem.date <=' => Date('Y-m-d', strtotime($params['DistGiftItem.date_to'])));
		}
		return $conditions;
	}

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'DistSalesRepresentative' => array(
			'className' => 'DistSalesRepresentative',
			'foreignKey' => 'sr_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DistOutlet' => array(
			'className' => 'DistOutlet',
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
		'DistGiftItemDetail' => array(
			'className' => 'DistGiftItemDetail',
			'foreignKey' => 'gift_item_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => '',
		)
	);
}
