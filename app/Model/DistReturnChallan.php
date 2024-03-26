<?php
App::uses('AppModel', 'Model');
/**
 * Challan Model
 *
 * @property Sender $Sender
 * @property TransactionType $TransactionType
 * @property ChallanDetail $ChallanDetail
 */
class DistReturnChallan extends AppModel {

	public $useDbConfig = 'default_06';
	// data filter
	public function filter($params, $conditions) {  
	
	
		$conditions = array();
		/*if(CakeSession::read('Office.parent_office_id') == 0)
		{
			$conditions[] = array('DistReturnChallan.transaction_type_id' => 4);
		}else{
			$conditions[] = array('DistReturnChallan.transaction_type_id' => 5);
		}*/
		
        if (!empty($params['DistReturnChallan.challan_no'])) {
            $conditions[] = array('DistReturnChallan.challan_no' => $params['DistReturnChallan.challan_no']);
        }
		if (!empty($params['DistReturnChallan.office_id'])) {
            $conditions[] = array('DistReturnChallan.office_id' => $params['DistReturnChallan.office_id']);
        }
        if (!empty($params['DistReturnChallan.sender_store_id'])) {
            $conditions[] = array('DistStore.id' => $params['DistReturnChallan.sender_store_id']);
        }
        else{
            if(CakeSession::read('UserAuth.User.user_group_id') == 1034){
                App::import('Model', 'DistUserMapping');
                App::import('Model', 'DistStore');
                $sp_id = CakeSession::read('UserAuth.User.sales_person_id');
                $this->DistUserMapping = new DistUserMapping();
                
                $data = $this->DistUserMapping->find('first',array('conditions'=>array('DistUserMapping.sales_person_id'=>$sp_id)));
                $distributor_id = $data['DistUserMapping']['dist_distributor_id'];

                $this->DistStore = new DistStore();
                $dist_store = $this->DistStore->find('first',array('conditions'=>array('DistStore.dist_distributor_id'=>$distributor_id)));
             
                $dist_store_id = $dist_store['DistStore']['id'];

                $conditions[] = array('DistReturnChallan.sender_store_id' => $dist_store_id);
            }
        }
		if (!empty($params['DistReturnChallan.status'])) {
            $conditions[] = array('DistReturnChallan.status' => $params['DistReturnChallan.status']);
        }
		if (isset($params['DistReturnChallan.date_from'])!='') {
            $conditions[] = array('DistReturnChallan.challan_date >=' => Date('Y-m-d',strtotime($params['DistReturnChallan.date_from'])));
        }
		if (isset($params['DistReturnChallan.date_to'])!='') {
            $conditions[] = array('DistReturnChallan.challan_date <=' => Date('Y-m-d',strtotime($params['DistReturnChallan.date_to'])));
        }
		/*if (isset($params['DistReturnChallan.transaction_type_id'])!='') {
			$conditions[] = array('DistReturnChallan.transaction_type_id' => $params['DistReturnChallan.transaction_type_id']);
        }*/
		if (isset($params['DistReturnChallan.inventory_status_id'])!='') {
			$conditions[] = array('DistReturnChallan.inventory_status_id' => $params['DistReturnChallan.inventory_status_id']);
        }
		if (isset($params['DistReturnChallan.inventory_status_id_not'])!='') {
			$conditions[] = array('DistReturnChallan.inventory_status_id !=' => $params['DistReturnChallan.inventory_status_id_not']);
        }
		
		
		/*if (isset($params['DistReturnChallan.office_id'])!='')
		{
			$conditions[] = array(
				'OR' => array(
					array('SenderStore.office_id' => $params['DistReturnChallan.office_id']),
					array('ReceiverStore.office_id' => $params['DistReturnChallan.office_id'])
				)
			);
		}
		elseif(CakeSession::read('Office.store_id') && CakeSession::read('Office.store_id')!=13)
		{
			$conditions[] = array(
				'OR' => array(
					array('DistReturnChallan.sender_store_id' => CakeSession::read('Office.store_id')),
					array('DistReturnChallan.receiver_store_id' => CakeSession::read('Office.store_id'))
				)
			);
		}*/
		
		
		/*$conditions[] = array('OR'=>array(array('DistReturnChallan.transaction_type_id' => $params['DistReturnChallan.transaction_type_id_1']),array('DistReturnChallan.transaction_type_id' => $params['DistReturnChallan.transaction_type_id_2'])));*/


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
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
			'conditions' => '',
			'fields' => 'office_name',
			'order' => ''
		),	
		'DistStore' => array(
			'className' => 'DistStore',
			'foreignKey' => 'sender_store_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
		'ReceiverStore' => array(
			'className' => 'Store',
			'foreignKey' => 'receiver_store_id',
			'conditions' => '',
			'fields' => 'id,name',
			'order' => ''
		),
		/*'TransactionType' => array(
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
		
		'Requisition' => array(
			'className' => 'Requisition',
			'foreignKey' => 'requisition_id',
			'conditions' => '',
			'fields' => 'id,do_no',
			'order' => ''
		)*/
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'DistReturnChallanDetail' => array(
			'className' => 'DistReturnChallanDetail',
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
