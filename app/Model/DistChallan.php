<?php
App::uses('AppModel', 'Model');
/**
 * Challan Model
 *
 * @property Sender $Sender
 * @property TransactionType $TransactionType
 */
class DistChallan extends AppModel
{

	public $displayField = 'challan_no';
	//public $useDbConfig = 'default_06';
	// data filter
	public function filter($params, $conditions)
	{
		// pr($params);die();
		$conditions = array();
		if (CakeSession::read('Office.parent_office_id') != 0) {
			$conditions[] = array('DistChallan.office_id' => CakeSession::read('Office.id'));
		} else {
			if (!empty($params['DistChallan.office_id'])) {
				$conditions[] = array('DistChallan.office_id' => $params['DistChallan.office_id']);
			}
		}

		if (!empty($params['DistChallan.dist_distributor_id'])) {
			$conditions[] = array('DistChallan.dist_distributor_id' => $params['DistChallan.dist_distributor_id']);
		} else {
			if (CakeSession::read('UserAuth.User.user_group_id') == 1034) {
				App::import('Model', 'DistUserMapping');
				$sp_id = CakeSession::read('UserAuth.User.sales_person_id');
				$this->DistUserMapping = new DistUserMapping();
				$data = $this->DistUserMapping->find('first', array('conditions' => array('DistUserMapping.sales_person_id' => $sp_id)));
				$distributor_id = $data['DistUserMapping']['dist_distributor_id'];
				$conditions[] = array('DistChallan.dist_distributor_id' => $distributor_id);
			}
		}
		if (!empty($params['DistChallan.memo_no'])) {
			$conditions[] = array('DistChallan.memo_no' => $params['DistChallan.memo_no']);
		}
		if (!empty($params['DistChallan.status'])) {
			$conditions[] = array('DistChallan.status' => $params['DistChallan.status']);
		}
		if (isset($params['DistChallan.date_from']) != '') {
			$conditions[] = array('DistChallan.challan_date >=' => Date('Y-m-d', strtotime($params['DistChallan.date_from'])));
		}
		if (isset($params['DistChallan.date_to']) != '') {
			$conditions[] = array('DistChallan.challan_date <=' => Date('Y-m-d', strtotime($params['DistChallan.date_to'])));
		}
		if (isset($params['DistChallan.is_bonus_challan']) != '') {
			$conditions[] = array('DistChallan.is_bonus_challan' => $params['DistChallan.is_bonus_challan']);
		}

		return $conditions;
	}

	public $validate = array(
		'receiver_dist_store_id' => array(
			'mustNotEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Receiver Store is required.'
			)
		)
	);

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'DistTransactionType' => array(
			'className' => 'DistTransactionType',
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
			'className' => 'DistStore',
			'foreignKey' => 'receiver_dist_store_id',
			'conditions' => '',
			'fields' => 'id,name,dist_distributor_id',
			'order' => ''
		),
		'SalesPerson' => array(
			'className' => 'SalesPerson',
			'foreignKey' => 'so_id',
			'conditions' => '',
			'fields' => 'id,name,territory_id',
			'order' => ''
		)
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'DistChallanDetail' => array(
			'className' => 'DistChallanDetail',
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
