<?php
App::uses('AppModel', 'Model');
/**
 * Deposit Model
 *
 * @property Memo $Memo
 * @property SalesPerson $SalesPerson
 * @property BankAccount $BankAccount
 * @property Collection $Collection
 */
class CollectionLog extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	

	// data filter
	public function filter($params, $conditions) {
        
		$conditions = array();
		if(CakeSession::read('Office.parent_office_id') != 0)
		{
			$conditions[] = array('Territory.office_id' => CakeSession::read('Office.id'));
		}	
		elseif(!empty($params['CollectionLog.office_id']))
		{
			$conditions[] = array('Territory.office_id' => $params['CollectionLog.office_id']);
		}
			
		if (!empty($params['CollectionLog.territory_id'])) {
            $conditions[] = array('CollectionLog.territory_id' => $params['CollectionLog.territory_id']);
        } 

        if (!empty($params['CollectionLog.market_id'])) {
            $conditions[] = array('Memo.market_id' => $params['CollectionLog.market_id']);
        } 

        if (!empty($params['CollectionLog.outlet_id'])) {
            $conditions[] = array('Memo.outlet_id' => $params['CollectionLog.outlet_id']);
        } 
		
		if (!empty($params['CollectionLog.type'])) {
            $conditions[] = array('CollectionLog.type' => $params['CollectionLog.type']);
        }
			
		if (isset($params['CollectionLog.date_from'])!='') {
            $conditions[] = array('CollectionLog.collectionDate >=' => Date('Y-m-d',strtotime($params['CollectionLog.date_from'])));
        }
		if (isset($params['CollectionLog.date_to'])!='') {
            $conditions[] = array('CollectionLog.collectionDate <=' => Date('Y-m-d',strtotime($params['CollectionLog.date_to'])));
        }
        return $conditions;
    }

/**
 * belongsTo associations
 *
 * @var array
 */
	
	/*public $hasOne = array(
		'Memo' => array(
			'foreignKey' => false,
			'conditions' => array('CollectionLog.memo_no = Memo.memo_no')
		)
	);*/

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Collection' => array(
			'className' => 'Collection',
			'foreignKey' => 'collection_id',
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
