<?php
App::uses('AppModel', 'Model');
/**
 * CommonMac Model
 *
 */
class CreditNote extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		
	);

	public function filter($params, $conditions) {

        $conditions = array();
        if (CakeSession::read('Office.parent_office_id') != 0) {
            $conditions[] = array('CreditNote.office_id' => CakeSession::read('Office.id'));
        } elseif (!empty($params['CreditNote.office_id'])) {
            $conditions[] = array('CreditNote.office_id' => $params['CreditNote.office_id']);
        }

        if (!empty($params['CreditNote.territory_id'])) {
            $conditions[] = array('CreditNote.territory_id' => $params['CreditNote.territory_id']);
        }
        if (!empty($params['CreditNote.market_id'])) {
            $conditions[] = array('CreditNote.market_id' => $params['CreditNote.market_id']);
        }
        if (!empty($params['CreditNote.outlet_id'])) {
            $conditions[] = array('CreditNote.outlet_id' => $params['CreditNote.outlet_id']);
        }
        if (isset($params['CreditNote.date_from']) != '') {
            $conditions[] = array('date(CreditNote.created_at) >=' => Date('Y-m-d', strtotime($params['CreditNote.date_from'])));
        }
        if (isset($params['CreditNote.date_to']) != '') {
            $conditions[] = array('date(CreditNote.created_at) <=' => Date('Y-m-d', strtotime($params['CreditNote.date_to'])));
        }
        
        return $conditions;
    }
}
