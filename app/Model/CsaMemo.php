<?php
App::uses('AppModel', 'Model');
/**
 * CsaMemo Model
 *
 */
class CsaMemo extends AppModel {

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
			$conditions[] = array('SalesPerson.office_id' => CakeSession::read('Office.id'));
		}
		elseif(!empty($params['CsaMemo.office_id']))
		{
			$conditions[] = array('SalesPerson.office_id' => $params['CsaMemo.office_id']);
		}
		
		if (!empty($params['CsaMemo.outlet_id'])) {
            $conditions[] = array('CsaMemo.outlet_id' => $params['CsaMemo.outlet_id']);
        }
		elseif(!empty($params['CsaMemo.market_id'])) {
            $conditions[] = array('CsaMemo.market_id' => $params['CsaMemo.market_id']);
        }
        elseif (!empty($params['CsaMemo.thana_id'])) {
            $conditions[] = array('Market.thana_id' => $params['CsaMemo.thana_id']);
        }
		elseif (!empty($params['CsaMemo.territory_id'])) {
            $conditions[] = array('CsaMemo.territory_id' => $params['CsaMemo.territory_id']);
        }
        elseif (!empty($params['CsaMemo.csa_id'])) {
            $conditions[] = array('CsaMemo.csa_id' => $params['CsaMemo.csa_id']);
        }

		if (!empty($params['CsaMemo.csa_memo_no'])) {
            $conditions[] = array('CsaMemo.csa_memo_no' => $params['CsaMemo.csa_memo_no']);
        }
		if (isset($params['CsaMemo.date_from'])!='') {
            $conditions[] = array('CsaMemo.memo_date >=' => Date('Y-m-d H:i:s',strtotime($params['CsaMemo.date_from'])));
        }
		if (isset($params['CsaMemo.date_to'])!='') {
            $conditions[] = array('CsaMemo.memo_date <=' => Date('Y-m-d H:i:s',strtotime($params['CsaMemo.date_to'].' 23:59:59')));
        }        		
        return $conditions;
    }
	
	
	public $validate = array(
		'office_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Office field is required.'
					)
		),
		'sale_type_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Sale Type field is required.'
					)
		),
		'csa_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Sale Type field is required.'
					)
		),
		'territory_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Territory field is required.'
					)
		),
		 'market_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Market field is required.'
					)
		),
		 'market' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Market field is required.'
					)
		),
		 'outlet_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Outlet field is required.'
					)
		),
		 'outlet' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Outlet field is required.'
					)
		),
		 'csa_name' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'CSA Name field is required.'
					)
		),
		 'entry_date' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Entry Date field is required.'
					)
		),
		 'memo_date' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'CsaMemo Date field is required.'
					)
		),
		'memo_no' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message' => 'CsaMemo No field is required.'
					)
		) 
	);

	
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
			'fields' => 'name,office_id',
			'order' => '',
		),
		'Outlet' => array(
			'className' => 'Outlet',
			'foreignKey' => 'outlet_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
		'Territory' => array(
			'className' => 'Territory',
			'foreignKey' => 'territory_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
		'Market' => array(
			'className' => 'Market',
			'foreignKey' => 'market_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		)
	);
	
	
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'CsaMemoDetail' => array(
			'className' => 'CsaMemoDetail',
			'foreignKey' => 'csa_memo_id',
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
	
	
	/*----- quaery Methods -----*/


}
