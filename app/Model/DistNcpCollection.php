<?php
App::uses('AppModel', 'Model');
/**
 * Challan Model
 *
 * @property Sender $Sender
 * @property TransactionType $TransactionType
 * @property ChallanDetail $ChallanDetail
 */
class DistNcpCollection extends AppModel {

	public $useDbConfig = 'default_06';

	public $validate = array(
		'outlet_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Receiver is required.'
					)
		),
		'sr_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Sales person field is required.'
					)
		)
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'DistOutlet' => array(
			'className' => 'DistOutlet',
			'foreignKey' => 'outlet_id',
			'conditions' => '',
			'fields' => 'id',
			'order' => ''
		)
		/*
		'Sr' => array(
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
		'DistNcpCollectionDetail' => array(
			'className' => 'DistNcpCollectionDetail',
			'foreignKey' => 'ncp_collection_id',
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
