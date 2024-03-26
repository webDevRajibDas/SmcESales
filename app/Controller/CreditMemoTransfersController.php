<?php
App::uses('AppController', 'Controller');
/**
 * ProxySells Controller
 *
 * @property ProxySell $ProxySell
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class CreditMemoTransfersController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Filter.Filter');
	public $uses = array('SoCreditCollection', 'CreditMemoTransfer', 'Memo', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'Combination', 'DistProductPrice', 'DistProductCombination', 'DistCombination', 'MemoDetail', 'MeasurementUnit', 'User');
	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
		// Configure::write('debug', 2);
		$requested_data = $this->request->data;
		//  pr($requested_data);exit;
		// die(); 
		$this->set('page_title', 'Credit Memo Transfer');


		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$this->set('office_parent_id', $office_parent_id);
		if ($office_parent_id == 0) {
			//pr($this->current_date());exit;
			$conditions[] = array('SoCreditCollection.date >=' => $this->current_date() . ' 00:00:00');
			$conditions[] = array('SoCreditCollection.date <=' => $this->current_date() . ' 23:59:59');
			$office_conditions = array(
				'office_type_id' => 2,
				"NOT" => array("id" => array(30, 31, 37))
			);
		} else {
			$conditions[] = array('Territory.office_id' => $this->UserAuth->getOfficeId());
			$conditions[] = array('SoCreditCollection.date >=' => $this->current_date() . ' 00:00:00');
			$conditions[] = array('SoCreditCollection.date <=' => $this->current_date() . ' 23:59:59');
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}
		$conditions[] = array('SoCreditCollection.due_ammount >' => 0);
		//  pr($conditions);exit;

		$this->SoCreditCollection->recursive = 0;
		$this->paginate = array(
			'fields' => array(
				'SoCreditCollection.id',
				'SoCreditCollection.so_id',
				'SoCreditCollection.date',
				'SoCreditCollection.memo_no',
				'SoCreditCollection.memo_value',
				'SoCreditCollection.collect_date',
				'SoCreditCollection.inst_type',
				'SoCreditCollection.due_ammount',
				'SoCreditCollection.paid_ammount',
				'SoCreditCollection.updated_at',
				'SoCreditCollection.territory_id',
				'Territory.name',
				'Office.id',
				'Office.office_name',
				'SalesPerson.name'

			),
			'joins' => array(
				array(
					'table' => 'territories',
					'alias' => 'Territory',
					'type' => 'Inner',
					'conditions' => array('SoCreditCollection.territory_id= Territory.id')
				),
				array(
					'table' => 'offices',
					'alias' => 'Office',
					'type' => 'Inner',
					'conditions' => array('Territory.office_id= Office.id')
				),
				array(
					'table' => 'memos',
					'alias' => 'Memo',
					'type' => 'Inner',
					'conditions' => array('Memo.memo_no= SoCreditCollection.memo_no')
				),
				array(
					'table' => 'sales_people',
					'alias' => 'SalesPerson',
					'type' => 'left',
					'conditions' => array('SalesPerson.id= Memo.sales_person_id')
				),

			),
			'conditions' => $conditions,


			'order' => array('SoCreditCollection.id' => 'desc'),
			'limit' => 100
		);
		//   pr($this->paginate());
		//   exit; 
		$this->set('SoCreditCollections', $this->paginate());
		$this->set('office_id', $this->UserAuth->getOfficeId());
		$this->set('territory_id', $requested_data['CreditMemoTransfer']['territory_id']);
		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$office_id = isset($this->request->data['Memo']['office_id']) != '' ? $this->request->data['Memo']['office_id'] : 0;

		$this->set(compact('offices', 'requested_data'));
	}

	/**
	 * admin_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_confirm($id = null)
	{
		// Configure::write('debug', 2);
		$this->set('page_title', 'Credit Memo Transfer');

		$this->SoCreditCollection->id = $id;
		if (!$this->SoCreditCollection->exists($id)) {
			throw new NotFoundException(__('Invalid product group'));
		}

		$SoCreditCollection = $this->SoCreditCollection->find('first', array(

			'fields' => array(
				'SoCreditCollection.id',
				'SoCreditCollection.so_id',
				'SoCreditCollection.date',
				'SoCreditCollection.memo_no',
				'SoCreditCollection.memo_value',
				'SoCreditCollection.collect_date',
				'SoCreditCollection.inst_type',
				'SoCreditCollection.due_ammount',
				'SoCreditCollection.paid_ammount',
				'SoCreditCollection.updated_at',
				'SoCreditCollection.territory_id',
				'Territory.name',
				'Office.id',
				'Office.office_name',
				'SalesPerson.name'

			),
			'joins' => array(
				array(
					'table' => 'territories',
					'alias' => 'Territory',
					'type' => 'Inner',
					'conditions' => array('SoCreditCollection.territory_id= Territory.id')
				),
				array(
					'table' => 'offices',
					'alias' => 'Office',
					'type' => 'Inner',
					'conditions' => array('Territory.office_id= Office.id')
				),
				array(
					'table' => 'memos',
					'alias' => 'Memo',
					'type' => 'Inner',
					'conditions' => array('Memo.memo_no= SoCreditCollection.memo_no')
				),
				array(
					'table' => 'sales_people',
					'alias' => 'SalesPerson',
					'type' => 'left',
					'conditions' => array('SalesPerson.id= Memo.sales_person_id')
				),

			),
			'conditions' => array('SoCreditCollection.id' => $id),
		));

		$office_id = $SoCreditCollection['Office']['id'];

		$territory_id = $SoCreditCollection['SoCreditCollection']['territory_id'];

		// pr($SoCreditCollection);exit;


		/***Show Except Parent Territory (Who Has Child) ***/
		$this->loadModel('Territory');
		$parent_id_who_has_child = $this->Territory->find('list', array(
			'conditions' => array(
				'parent_id !=' => 0,
				'id !=' => $territory_id,
			),
			'fields' => array('Territory.parent_id', 'Territory.name'),
		));

		$territories_list = $this->Territory->find('all', array(
			'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
			'conditions' => array('Territory.office_id' => $office_id, 'NOT' => array('Territory.id' => array_keys($parent_id_who_has_child))),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => 0
		));




		$territories = array();
		foreach ($territories_list as $t_result) {
			$territories[$t_result['Territory']['id']] = $t_result['Territory']['name'] . ' (' . $t_result['SalesPerson']['name'] . ')';
		}

		// pr($territories);exit;



		if ($this->request->is('post')) {
			//  pr($this->request->data);die();
			$this->loadModel('SoCreditCollection');
			$update_data = array();

			$update_data['territory_id'] = $this->request->data['CreditMemoTransfer']['territory_id'];

			//  $this->request->data['CreditMemoTransfer']['territory_id'] = $this->current_datetime();

			if ($this->SoCreditCollection->save($update_data)) {
				//  echo $this->SoCreditCollection->getLastQuery();exit;
				$insert_data = array();
				$this->CreditMemoTransfer->create();
				$insert_data['CreditMemoTransfer']['from_id'] = $SoCreditCollection['SoCreditCollection']['territory_id'];
				$insert_data['CreditMemoTransfer']['to_id'] = $this->request->data['CreditMemoTransfer']['territory_id'];
				$insert_data['CreditMemoTransfer']['memo_no'] = $SoCreditCollection['SoCreditCollection']['memo_no'];
				$insert_data['CreditMemoTransfer']['created_at'] = $this->current_datetime();
				$insert_data['CreditMemoTransfer']['created_by'] = $this->UserAuth->getUserId();

				$this->CreditMemoTransfer->save($insert_data);

				// CreditMemoTransfer
				$this->Session->setFlash(__('Credit Memo Transfered'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Credit Memo Transfer Failed. Please, try again.'), 'flash/error');
			}
		}
		$this->set(compact('SoCreditCollection', 'territories'));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */



	public function get_territory_list()
	{
		$this->loadModel('Territory');
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$office_id = $this->request->data['office_id'];
		if ($office_id) {

			/***Show Except Parent Territory (Who Has Child) ***/

			$parent_id_who_has_child = $this->Territory->find('list', array(
				'conditions' => array(
					'parent_id !=' => 0,
				),
				'fields' => array('Territory.parent_id', 'Territory.name'),
			));

			$territory = $this->Territory->find('all', array(
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'conditions' => array('Territory.office_id' => $office_id, 'NOT' => array('Territory.id' => array_keys($parent_id_who_has_child))),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));
		}

		$data_array = array();

		foreach ($territory as $key => $value) {
			$data_array[] = array(
				'id' => $value['Territory']['id'],
				'name' => $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')',
			);
		}

		if (!empty($territory)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */


	/**
	 * admin_delete method
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param string $id
	 * @return void
	 */
}
