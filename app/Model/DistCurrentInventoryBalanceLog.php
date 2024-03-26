<?php
App::uses('AppModel', 'Model');
/**
 * currentInventoryBalanceLog Model
 *
 * @property Territory $Territory
 * @property FiscalYear $FiscalYear
 * @property Month $Month
 */
class DistCurrentInventoryBalanceLog extends AppModel
{

	/**
	 * Use table
	 *
	 * @var mixed False or table name
	 */



	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public function filter($params, $conditions)
	{
		$conditions = array();
		if (CakeSession::read('Office.parent_office_id') != 0) {
			$conditions[] = array('DistStore.office_id' => CakeSession::read('Office.id'));
		}
		if (!empty($params['DistCurrentInventoryBalanceLog.product_code'])) {
			$conditions[] = array('Product.product_code' => $params['DistCurrentInventoryBalanceLog.product_code']);
		}
		if (!empty($params['DistCurrentInventoryBalanceLog.inventory_status_id'])) {
			$conditions[] = array('DistCurrentInventoryBalanceLog.inventory_status_id' => $params['DistCurrentInventoryBalanceLog.inventory_status_id']);
		} else {
			$conditions[] = array('DistCurrentInventoryBalanceLog.inventory_status_id !=' => 2);
		}
		if (!empty($params['DistCurrentInventoryBalanceLog.product_id'])) {
			$conditions[] = array('DistCurrentInventoryBalanceLog.product_id' => $params['DistCurrentInventoryBalanceLog.product_id']);
		}

		if (!empty($params['DistCurrentInventoryBalanceLog.store_id'])) {
			$conditions[] = array('DistCurrentInventoryBalanceLog.store_id' => $params['DistCurrentInventoryBalanceLog.store_id']);
		} else {
			if (CakeSession::read('UserAuth.User.user_group_id') == 1034) {
				App::import('Model', 'DistUserMapping');
				App::import('Model', 'DistStore');
				$sp_id = CakeSession::read('UserAuth.User.sales_person_id');
				$this->DistUserMapping = new DistUserMapping();

				$data = $this->DistUserMapping->find('first', array('conditions' => array('DistUserMapping.sales_person_id' => $sp_id)));
				$distributor_id = $data['DistUserMapping']['dist_distributor_id'];

				$this->DistStore = new DistStore();
				$dist_store = $this->DistStore->find('first', array('conditions' => array('DistStore.dist_distributor_id' => $distributor_id)));

				$dist_store_id = $dist_store['DistStore']['id'];

				$conditions[] = array('DistCurrentInventoryBalanceLog.store_id' => $dist_store_id);
			}
		}
		if (!empty($params['DistCurrentInventoryBalanceLog.product_categories_id'])) {
			$conditions[] = array('ProductCategory.id' => $params['DistCurrentInventoryBalanceLog.product_categories_id']);
		}
		$conditions[] = array('DistCurrentInventoryBalanceLog.transaction_date' => '2023-03-04');
		// $conditions['OR'] = array('DistCurrentInventoryBalanceLog.qty >' => 0, 'DistCurrentInventoryBalanceLog.bonus_qty >' => 0);
		// $conditions[] = array('DistCurrentInventoryBalanceLog.bonus_qty' => '0');
		return $conditions;
	}
	public $belongsTo = array(
		'DistStore' => array(
			'className' => 'DistStore',
			'foreignKey' => 'store_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'InventoryStatuses' => array(
			'className' => 'InventoryStatuses',
			'foreignKey' => 'inventory_status_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'TransactionType' => array(
			'className' => 'TransactionType',
			'foreignKey' => 'transaction_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
