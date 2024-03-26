<?php
App::uses('AppModel', 'Model');
/**
 * Challan Model
 *
 * @property Sender $Sender
 * @property TransactionType $TransactionType
 * @property ChallanDetail $ChallanDetail
 */
class PrimaryMemo extends AppModel
{

	public $displayField = 'challan_no';
	// data filter
	public function filter($params, $conditions)
	{

		$conditions = array();

		if (!empty($params['PrimaryMemo.challan_no'])) {
			$conditions[] = array('PrimaryMemo.challan_no' => $params['PrimaryMemo.challan_no']);
		}
		if (!empty($params['PrimaryMemo.status'])) {
			$conditions[] = array('PrimaryMemo.status' => $params['PrimaryMemo.status']);
		}
		if (isset($params['PrimaryMemo.date_from']) != '') {
			$conditions[] = array('PrimaryMemo.challan_date >=' => Date('Y-m-d', strtotime($params['PrimaryMemo.date_from'])));
		}
		if (isset($params['PrimaryMemo.date_to']) != '') {
			$conditions[] = array('PrimaryMemo.challan_date <=' => Date('Y-m-d', strtotime($params['PrimaryMemo.date_to'])));
		}

		if (isset($params['PrimaryMemo.territory_id']) != '') {
			$conditions[] = array('ReceiverStore.territory_id' => $params['PrimaryMemo.territory_id']);
		}

		$conditions[] = array('PrimaryMemo.inventory_status_id' => $params['PrimaryMemo.inventory_status_id']);
		$conditions[] = array('OR' => array(array('PrimaryMemo.transaction_type_id' => $params['PrimaryMemo.transaction_type_id_1']), array('PrimaryMemo.transaction_type_id' => $params['PrimaryMemo.transaction_type_id_2'])));

		if (CakeSession::read('Office.parent_office_id') != 0) {
			$conditions[] = array(
				'OR' => array(
					array('PrimaryMemo.sender_store_id' => CakeSession::read('Office.store_id')),
					array('PrimaryMemo.receiver_store_id' => CakeSession::read('Office.store_id'))
				)
			);
		}

		return $conditions;
	}

	public $validate = array(
		'receiver_store_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Receiver is required.'
			),
		),
		'challan_date' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Chalan Date is required.'
			),
		),

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
			'className' => 'PrimarySenderReceiver',
			'foreignKey' => 'sender_store_id',
			'conditions' => '',
			'order' => ''
		),
		'ReceiverStore' => array(
			'className' => 'PrimarySenderReceiver',
			'foreignKey' => 'receiver_store_id',
			'conditions' => '',
			'order' => ''
		),

	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'PrimaryMemoDetail' => array(
			'className' => 'PrimaryMemoDetail',
			'foreignKey' => 'primary_memo_id',

		)
	);
}
