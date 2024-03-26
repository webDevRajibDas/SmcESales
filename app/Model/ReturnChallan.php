<?php
App::uses('AppModel', 'Model');
/**
 * Challan Model
 *
 * @property Sender $Sender
 * @property TransactionType $TransactionType
 * @property ChallanDetail $ChallanDetail
 */
class ReturnChallan extends AppModel {

	
	// data filter
	public function filter($params, $conditions) {   
				
		$conditions = array();
		/*if(CakeSession::read('Office.parent_office_id') == 0)
		{
			$conditions[] = array('ReturnChallan.transaction_type_id' => 4);
		}else{
			$conditions[] = array('ReturnChallan.transaction_type_id' => 5);
		}*/
		
        if (!empty($params['ReturnChallan.challan_no'])) {
            $conditions[] = array('ReturnChallan.challan_no' => $params['ReturnChallan.challan_no']);
        }
        if (!empty($params['ReturnChallan.sender_store_id'])) {
            $conditions[] = array('ReturnChallan.sender_store_id' => $params['ReturnChallan.sender_store_id']);
        }
		if (!empty($params['ReturnChallan.status'])) {
            $conditions[] = array('ReturnChallan.status' => $params['ReturnChallan.status']);
        }
		if (isset($params['ReturnChallan.date_from'])!='') {
            $conditions[] = array('ReturnChallan.challan_date >=' => Date('Y-m-d',strtotime($params['ReturnChallan.date_from'])));
        }
		if (isset($params['ReturnChallan.date_to'])!='') {
            $conditions[] = array('ReturnChallan.challan_date <=' => Date('Y-m-d',strtotime($params['ReturnChallan.date_to'])));
        }
		/*if (isset($params['ReturnChallan.transaction_type_id'])!='') {
			$conditions[] = array('ReturnChallan.transaction_type_id' => $params['ReturnChallan.transaction_type_id']);
        }*/
		if (isset($params['ReturnChallan.inventory_status_id'])!='') {
			$conditions[] = array('ReturnChallan.inventory_status_id' => $params['ReturnChallan.inventory_status_id']);
        }
		if (isset($params['ReturnChallan.inventory_status_id_not'])!='') {
			$conditions[] = array('ReturnChallan.inventory_status_id !=' => $params['ReturnChallan.inventory_status_id_not']);
        }
		
		
		if (isset($params['ReturnChallan.office_id'])!='')
		{
			$conditions[] = array(
				'OR' => array(
					array('SenderStore.office_id' => $params['ReturnChallan.office_id']),
					array('ReceiverStore.office_id' => $params['ReturnChallan.office_id'])
				)
			);
		}
		elseif(CakeSession::read('Office.store_id') && CakeSession::read('Office.store_id')!=13)
		{
			$conditions[] = array(
				'OR' => array(
					array('ReturnChallan.sender_store_id' => CakeSession::read('Office.store_id')),
					array('ReturnChallan.receiver_store_id' => CakeSession::read('Office.store_id'))
				)
			);
		}
		
		
		$conditions[] = array('OR'=>array(array('ReturnChallan.transaction_type_id' => $params['ReturnChallan.transaction_type_id_1']),array('ReturnChallan.transaction_type_id' => $params['ReturnChallan.transaction_type_id_2'])));


        return $conditions;
    }
	
	public $validate = array(
		'receiver_store_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Receiver is required.'
					)
		),
		'challan_referance_no' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Sales person field is required.'
					),
			'isUnique' => array(
						'rule' => 'isUnique',
						'message'=> 'Challan No. already exist.'
					),		
		)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(		
		'TransactionType' => array(
			'className' => 'TransactionType',
			'foreignKey' => 'transaction_type_id',
			'conditions' => '',
			'fields' => 'id,name',
			'order' => ''
		),
		'InventoryStatus' => array(
			'className' => 'InventoryStatus',
			'foreignKey' => 'inventory_status_id',
			'conditions' => '',
			'fields' => 'id,name',
			'order' => ''
		),
		'SenderStore' => array(
			'className' => 'Store',
			'foreignKey' => 'sender_store_id',
			'conditions' => '',
			'fields' => 'id,name',
			'order' => ''
		),
		'ReceiverStore' => array(
			'className' => 'Store',
			'foreignKey' => 'receiver_store_id',
			'conditions' => '',
			'fields' => 'id,name',
			'order' => ''
		),
		'Requisition' => array(
			'className' => 'Requisition',
			'foreignKey' => 'requisition_id',
			'conditions' => '',
			'fields' => 'id,do_no',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'ReturnChallanDetail' => array(
			'className' => 'ReturnChallanDetail',
			'foreignKey' => 'challan_id',
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
