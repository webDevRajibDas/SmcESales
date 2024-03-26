<?php
App::uses('AppModel', 'Model');
/**
 * Challan Model
 *
 * @property Sender $Sender
 * @property TransactionType $TransactionType*/
class Claim extends AppModel {

	
	// data filter
	public function filter($params, $conditions) {   
		
		$conditions = array();
		/*if(CakeSession::read('Office.parent_office_id') == 0)
		{
			$conditions[] = array('ReturnChallan.transaction_type_id' => 4);
		}else{
			$conditions[] = array('ReturnChallan.transaction_type_id' => 5);
		}*/
		
        if (!empty($params['Claim.claim_no'])) {
            $conditions[] = array('Claim.claim_no' => $params['Claim.claim_no']);
        }
		if (!empty($params['Claim.status'])) {
            $conditions[] = array('Claim.status' => $params['Claim.status']);
        }
		if (isset($params['Claim.date_from'])!='') {
            $conditions[] = array('Claim.created_at >=' => Date('Y-m-d',strtotime($params['Claim.date_from'])));
        }
		if (isset($params['Claim.date_to'])!='') {
            $conditions[] = array('Claim.created_at <=' => Date('Y-m-d',strtotime($params['Claim.date_to'])).' 23:59:59');
        }
		if (isset($params['Claim.transaction_type_id_1'])!='' && isset($params['Claim.transaction_type_id_2'])!='') {
			$conditions[] = array('OR'=>array(array('Claim.transaction_type_id' => $params['Claim.transaction_type_id_1']),array('Claim.transaction_type_id' => $params['Claim.transaction_type_id_2'])));
        }
		

        if(CakeSession::read('Office.parent_office_id') != 0)
        {
			$conditions[] = array(
				'OR' => array(
					array('Claim.sender_store_id' => CakeSession::read('Office.store_id')),
					array('Claim.receiver_store_id' => CakeSession::read('Office.store_id'))
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
			'fields' => 'id,name',
			'order' => ''
		),
		'ReceiverStore' => array(
			'className' => 'Store',
			'foreignKey' => 'receiver_store_id',
			'conditions' => '',
			'fields' => 'id,name',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'ClaimDetail' => array(
			'className' => 'ClaimDetail',
			'foreignKey' => 'claim_id',
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
