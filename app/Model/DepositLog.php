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
class DepositLog extends AppModel {

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
		elseif(!empty($params['DepositLog.office_id']))
		{
			$conditions[] = array('Territory.office_id' => $params['DepositLog.office_id']);
		}
			
		if (!empty($params['DepositLog.territory_id'])) {
            $conditions[] = array('DepositLog.territory_id' => $params['DepositLog.territory_id']);
        } 
		
		/*if (!empty($params['Deposit.market_id'])) {
            $conditions[] = array('Memo.market_id' => $params['Deposit.market_id']);
        }
		if (!empty($params['Deposit.outlet_id'])) {
            $conditions[] = array('Memo.outlet_id' => $params['Deposit.outlet_id']);
        }*/
		
		if (!empty($params['DepositLog.instrument_type'])) {
            $conditions[] = array('DepositLog.type' => $params['DepositLog.instrument_type']);
        }
			
		if (isset($params['DepositLog.date_from'])!='') {
            $conditions[] = array('DepositLog.deposit_date >=' => Date('Y-m-d',strtotime($params['DepositLog.date_from'])));
        }
		if (isset($params['DepositLog.date_to'])!='') {
            $conditions[] = array('DepositLog.deposit_date <=' => Date('Y-m-d',strtotime($params['DepositLog.date_to'])));
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
			'foreignKey' => 'sales_person_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Territory' => array(
			'className' => 'Territory',
			'foreignKey' => 'territory_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'BankBranch' => array(
			'className' => 'BankBranch',
			'foreignKey' => 'bank_branch_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Week' => array(
			'className' => 'Week',
			'foreignKey' => 'week_id',
			'conditions' => '',
			'fields' => 'week_name',
			'order' => ''
		),
		'Month' => array(
			'className' => 'Month',
			'foreignKey' => 'month_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
		'FiscalYear' => array(
			'className' => 'FiscalYear',
			'foreignKey' => 'fiscal_year_id',
			'conditions' => '',
			'fields' => 'year_code',
			'order' => ''
		),
		'InstrumentType' => array(
			'className' => 'InstrumentType',
			'foreignKey' => 'instrument_type',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		)
	);
	
	public $hasOne = array(
		'Memo' => array(
			'foreignKey' => false,
			'conditions' => array('DepositLog.memo_no = Memo.memo_no')
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Collection' => array(
			'className' => 'Collection',
			'foreignKey' => 'deposit_id',
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
