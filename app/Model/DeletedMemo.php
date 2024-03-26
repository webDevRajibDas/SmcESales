<?php
App::uses('AppModel', 'Model');
/**
 * Memo Model
 *
 */
class DeletedMemo extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
	
	// data filter
	public function filter($params, $conditions) {   
		

		$conditions = array();
		
		/*if(CakeSession::read('Office.parent_office_id') != 0)
		{
			$conditions[] = array('SalesPerson.office_id' => CakeSession::read('Office.id'));
		}
		elseif(!empty($params['DeletedMemo.office_id']))
		{
			$conditions[] = array('SalesPerson.office_id' => $params['DeletedMemo.office_id']);
		}*/
		
		if(CakeSession::read('Office.parent_office_id') != 0)
		{
			$conditions[] = array('DeletedMemo.office_id' => CakeSession::read('Office.id'));
		}
		elseif(!empty($params['DeletedMemo.office_id']))
		{
			$conditions[] = array('DeletedMemo.office_id' => $params['DeletedMemo.office_id']);
		}
			
		if (!empty($params['DeletedMemo.memo_no'])) {
            $conditions[] = array('DeletedMemo.memo_no' => $params['DeletedMemo.memo_no']);
        }
		if (!empty($params['DeletedMemo.memo_reference_no'])) {
            $conditions[] = array('DeletedMemo.memo_reference_no' => $params['DeletedMemo.memo_reference_no']);
        }
		if (!empty($params['DeletedMemo.territory_id'])) {
            $conditions[] = array('DeletedMemo.territory_id' => $params['DeletedMemo.territory_id']);
        }
		if (!empty($params['DeletedMemo.thana_id'])) {
            $conditions[] = array('DeletedMemo.thana_id' => $params['DeletedMemo.thana_id']);
        }
		if (!empty($params['DeletedMemo.market_id'])) {
            $conditions[] = array('DeletedMemo.market_id' => $params['DeletedMemo.market_id']);
        }
		if (!empty($params['DeletedMemo.outlet_id'])) {
            $conditions[] = array('DeletedMemo.outlet_id' => $params['DeletedMemo.outlet_id']);
        }
		if (isset($params['DeletedMemo.date_from'])!='') {
            $conditions[] = array('DeletedMemo.memo_date >=' => Date('Y-m-d H:i:s',strtotime($params['DeletedMemo.date_from'])));
        }
		if (isset($params['DeletedMemo.date_to'])!='') {
            $conditions[] = array('DeletedMemo.memo_date <=' => Date('Y-m-d H:i:s',strtotime($params['DeletedMemo.date_to'].' 23:59:59')));
        } 
        if(isset($params['DeletedMemo.operator']))
        {
        	if($params['DeletedMemo.operator']==3)
        	{
        		$conditions[] = array('DeletedMemo.gross_value BETWEEN ? AND ?'=>array($params['DeletedMemo.memo_value_from'],$params['DeletedMemo.memo_value_to']));
        	}
        	elseif($params['DeletedMemo.operator']==1)
        	{
        		$conditions[] = array('DeletedMemo.gross_value <'=>$params['DeletedMemo.mamo_value']);
        	}
        	elseif($params['DeletedMemo.operator']==2)
        	{
        		$conditions[] = array('DeletedMemo.gross_value >'=>$params['DeletedMemo.mamo_value']);
        	}
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
						'message'=> 'Memo Date field is required.'
					)
		),
		'memo_no' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message' => 'Memo No field is required.'
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
		'DeletedMemoDetail' => array( 
			'className' => 'DeletedMemoDetail',
			'foreignKey' => 'memo_id',
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
