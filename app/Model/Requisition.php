<?php
App::uses('AppModel', 'Model');
/**
 * Requisition Model
 *
 * @property SenderStore $SenderStore
 * @property ReceiverStore $ReceiverStore
 */
class Requisition extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'title';
	
	// data filter
	public function filter($params, $conditions) {   
		$conditions = array();
		if(CakeSession::read('Office.parent_office_id') != 0)
		{
			$conditions[] = array('Requisition.sender_store_id' => CakeSession::read('Office.store_id'));
		}			
		
        if (!empty($params['Requisition.do_no'])) {
            $conditions[] = array('Requisition.do_no' => $params['Requisition.do_no']);
        }
		if (!empty($params['Requisition.sender_store_id'])) {
            $conditions[] = array('Requisition.sender_store_id' => $params['Requisition.sender_store_id']);
        }
		if (!empty($params['Requisition.receiver_store_id'])) {
            $conditions[] = array('Requisition.receiver_store_id' => $params['Requisition.receiver_store_id']);
        }
		if (!empty($params['Requisition.status'])) {
            $conditions[] = array('Requisition.status' => $params['Requisition.status']);
        }
		if (isset($params['Requisition.date_from'])!='') {
            $conditions[] = array('Date(Requisition.created_at) >=' => $params['Requisition.date_from']);
        }
		if (isset($params['Requisition.date_to'])!='') {
            $conditions[] = array('Date(Requisition.created_at) <=' => $params['Requisition.date_to']);
        }
        		
        return $conditions;
    }	
	
	// validation
	public $validate = array(
		'title' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Title is required.'
					)
		),
		'receiver_store_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Receiver field is required.'
					)		
		)
	);
	
	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'SenderStore' => array(
			'className' => 'Store',
			'foreignKey' => 'sender_store_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ReceiverStore' => array(
			'className' => 'Store',
			'foreignKey' => 'receiver_store_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'RequisitionDetail' => array(
			'className' => 'RequisitionDetail',
			'foreignKey' => 'requisition_id',
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
