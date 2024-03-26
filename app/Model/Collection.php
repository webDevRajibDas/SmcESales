<?php

App::uses('AppModel', 'Model');

/**
 * MarketUpdate Model
 *
 * @property Product $Product
 */
class Collection extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'id';
    public $belongsTo = array(
        'Outlet' => array(
            'className' => 'Outlet',
            'foreignKey' => 'outlet_id',
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

    /* public $hasOne = array(
      'Memo' => array(
      'foreignKey' => false,
      'conditions' => array('Collection.memo_id = Memo.memo_no')
      )
      ); */

    // data filter
    public function filter($params, $conditions) {

        $conditions = array();
        if (CakeSession::read('Office.parent_office_id') != 0) {
            $conditions[] = array('Memo.office_id' => CakeSession::read('Office.id'));
        } elseif (!empty($params['Collection.office_id'])) {
            $conditions[] = array('Memo.office_id' => $params['Collection.office_id']);
        }

        if (!empty($params['Collection.territory_id'])) {
            $conditions[] = array('Memo.territory_id' => $params['Collection.territory_id']);
        }
        if (!empty($params['Collection.market_id'])) {
            $conditions[] = array('Memo.market_id' => $params['Collection.market_id']);
        }
        if (!empty($params['Collection.outlet_id'])) {
            $conditions[] = array('Memo.outlet_id' => $params['Collection.outlet_id']);
        }
        if (isset($params['Collection.date_from']) != '') {
            $conditions[] = array('Collection.collectionDate >=' => Date('Y-m-d', strtotime($params['Collection.date_from'])));
        }
        if (isset($params['Collection.date_to']) != '') {
            $conditions[] = array('Collection.collectionDate <=' => Date('Y-m-d', strtotime($params['Collection.date_to'])));
        }
        if(isset($params['Collection.type']))
        {
            $conditions[] = array('Collection.type ' =>$params['Collection.type']);
        }
        return $conditions;
    }

}
