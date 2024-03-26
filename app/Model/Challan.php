<?php
App::uses('AppModel', 'Model');
/**
 * Challan Model
 *
 * @property Sender $Sender
 * @property TransactionType $TransactionType
 * @property ChallanDetail $ChallanDetail
 */
class Challan extends AppModel {

	public $displayField = 'challan_no';
	// data filter
	public function filter($params, $conditions) {   
		
		$conditions = array();
		/*if(CakeSession::read('Office.parent_office_id') == 0)
		{
			$conditions[] = array('OR'=>array(array('Challan.transaction_type_id' => 1),array('Challan.transaction_type_id' => 4)));
		}else{
			$conditions[] = array('Challan.transaction_type_id' => 4);
		}*/			
		
        if (!empty($params['Challan.challan_no'])) {
            $conditions[] = array('Challan.challan_no' => $params['Challan.challan_no']);
        }
		if (!empty($params['Challan.status'])) {
            $conditions[] = array('Challan.status' => $params['Challan.status']);
        }
		if (isset($params['Challan.date_from'])!='') {
            $conditions[] = array('Challan.challan_date >=' => Date('Y-m-d',strtotime($params['Challan.date_from'])));
        }
		if (isset($params['Challan.date_to'])!='') {
            $conditions[] = array('Challan.challan_date <=' => Date('Y-m-d',strtotime($params['Challan.date_to'])));
        }	 
		
		if (isset($params['Challan.territory_id'])!='') {
            $conditions[] = array('ReceiverStore.territory_id' => $params['Challan.territory_id']);
        }	
		
		$conditions[] = array('Challan.inventory_status_id' => $params['Challan.inventory_status_id']);	
		$conditions[] = array('OR'=>array(array('Challan.transaction_type_id' => $params['Challan.transaction_type_id_1']),array('Challan.transaction_type_id' => $params['Challan.transaction_type_id_2'])));
		
		if(CakeSession::read('Office.parent_office_id') != 0){
		$conditions[] = array(
			'OR' => array(
				array('Challan.sender_store_id' => CakeSession::read('Office.store_id')),
				array('Challan.receiver_store_id' => CakeSession::read('Office.store_id'))
			)
		);
		}
        		
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
		'SenderStore' => array(
			'className' => 'Store',
			'foreignKey' => 'sender_store_id',
			'conditions' => '',
			'fields' => 'id,name,territory_id',
			'order' => ''
		),
		'ReceiverStore' => array(
			'className' => 'Store',
			'foreignKey' => 'receiver_store_id',
			'conditions' => '',
			'fields' => 'id,name,territory_id',
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
		'ChallanDetail' => array(
			'className' => 'ChallanDetail',
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
