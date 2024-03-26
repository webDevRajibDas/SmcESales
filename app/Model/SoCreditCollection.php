<?php
App::uses('AppModel', 'Model');
/**
 * CreditMemoTransfer Model
 *
 * @property Outlet $Outlet
 */
class SoCreditCollection extends AppModel
{

	/**
	 * Use table
	 *
	 * @var mixed False or table name
	 */
	public $useTable = 'so_credit_collections';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */

	public function filter($params, $conditions)
	{

		// pr($params);exit;
		$conditions = array();

		if (CakeSession::read('Office.parent_office_id') != 0) {
			$conditions[] = array('SalesPerson.office_id' => CakeSession::read('Office.id'));
		} elseif (!empty($params['CreditMemoTransfer.office_id'])) {

			$conditions[] = array('Territory.office_id' => $params['CreditMemoTransfer.office_id']);
		}



		if (isset($params['CreditMemoTransfer.territory_id'])) {
			$conditions[] = array('SoCreditCollection.territory_id' => $params['CreditMemoTransfer.territory_id']);
		}

		if (isset($params['CreditMemoTransfer.date_from']) != '') {
			$conditions[] = array('SoCreditCollection.date >=' => Date('Y-m-d H:i:s', strtotime($params['CreditMemoTransfer.date_from'])));
		}
		if (isset($params['CreditMemoTransfer.date_to']) != '') {
			$conditions[] = array('SoCreditCollection.date <=' => Date('Y-m-d H:i:s', strtotime($params['CreditMemoTransfer.date_to'] . ' 23:59:59')));
		}
		$conditions[] = array('SoCreditCollection.due_ammount >' => 0);
		// pr($conditions);exit;

		return $conditions;
	}
	public $belongsTo = array(
		'Outlet' => array(
			'className' => 'Outlet',
			'foreignKey' => 'outlet_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
