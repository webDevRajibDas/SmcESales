<?php
App::uses('AppController', 'Controller');
/**
 * Brands Controller
 *
 * @property Brand $Brand
 * @property PaginatorComponent $Paginator
 */

class StockProcessesController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0); //300 seconds = 5 minutes
		$this->set('page_title', 'Stock Process');
		$this->LoadModel('Office');
		$this->LoadModel('ProductType');
		$this->LoadModel('Store');
		$office_id = $this->UserAuth->getOfficeId();
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' 	=> 2,
				),
				'order' => array('office_name' => 'asc')
			));
		} else {
			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'id' 	=> $office_id,
				),
				'order' => array('office_name' => 'asc')
			));
		}
		$product_type_list = $this->ProductType->find('list');
		$this->set(compact('product_type_list'));
		$this->set(compact('offices'));
		if ($this->request->is('post')) {
			$request_data = $this->request->data;
			$process_run_for = $request_data['StockProcesses']['type'];
			$office_id = $request_data['StockProcesses']['office_id'];
			$from_date = $request_data['StockProcesses']['from_date'];
			if ($process_run_for == 'office') {
				$store_info = $this->Store->find('first', array(
					'conditions' => array(
						'Store.store_type_id' => 2,
						'Store.office_id' => $office_id
					),
					'recursive' => -1
				));
				$store_id = $store_info['Store']['id'];
				foreach ($request_data['StockProcesses']['product_id'] as $product_id) {
					if ($request_data['StockProcesses']['process'] == 'both' || $request_data['StockProcesses']['process'] == 'stock') {

						if ($from_date <= '2023-03-19') {

							$query = "
							EXEC  [dbo].[sp_inventory_history_regenerate_by_store_id_product_id_for_aso]
									@process_store_id = $store_id,
									@process_product_id = $product_id,
									@process_tran_date = '$from_date'
							";
							$result = $this->Store->Query($query);
							$from_date = '2023-03-20';
							//$from_date = date('Y-m-d', strtotime($from_date . ' +1 day'));

							$query2 = "
							EXEC  [dbo].[sp_inventory_history_regenerate_by_store_id_product_id_for_aso_after_batch_expire_date_all_inv_status]
									@process_store_id = $store_id,
									@process_product_id = $product_id,
									@process_tran_date = '$from_date'
							";
							$result2 = $this->Store->Query($query2);
						} else {

							$query = "
							EXEC  [dbo].[sp_inventory_history_regenerate_by_store_id_product_id_for_aso_after_batch_expire_date_all_inv_status]
									@process_store_id = $store_id,
									@process_product_id = $product_id,
									@process_tran_date = '$from_date'
							";
							$result = $this->Store->Query($query);
						}
					}

					if ($request_data['StockProcesses']['process'] == 'both' || $request_data['StockProcesses']['process'] == 'report') {
						$query = "
						 EXEC  [dbo].[rpt_daily_tran_balance_generate_date_wise]
								@process_store_id = $store_id,
								@process_product_id = $product_id,
								@process_tran_date = '$from_date'
						";
						$result = $this->Store->Query($query);
					}
				}
			} else {
				$territory_id = $request_data['StockProcesses']['territory_id'];
				$store_info = $this->Store->find('first', array(
					'conditions' => array(
						'Store.store_type_id' => 3,
						'Store.office_id' => $office_id,
						'Store.territory_id' => $territory_id
					),
					'recursive' => -1
				));
				$store_id = $store_info['Store']['id'];
				foreach ($request_data['StockProcesses']['product_id'] as $product_id) {
					if ($request_data['StockProcesses']['process'] == 'both' || $request_data['StockProcesses']['process'] == 'stock') {
						$query = "
						 EXEC  [dbo].[sp_inventory_history_regenerate_by_store_id_product_id_for_so]
								@process_store_id = $store_id,
								@process_product_id = $product_id,
								@process_tran_date = '$from_date'
						";
						// echo $query;exit;
						$result = $this->Store->Query($query);
					}

					if ($request_data['StockProcesses']['process'] == 'both' || $request_data['StockProcesses']['process'] == 'report') {
						$query = "
						 EXEC  [dbo].[rpt_daily_tran_balance_generate_date_wise]
								@process_store_id = $store_id,
								@process_product_id = $product_id,
								@process_tran_date = '$from_date'
						";
						$result = $this->Store->Query($query);
					}
				}
			}
			$this->Session->setFlash(__('Sucessfully Processed'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
	}

	function get_product_list()
	{
		$this->loadModel('Product');
		$view = new View($this);

		$form = $view->loadHelper('Form');
		// $product_types=@array_values($this->request->data['Memo']['product_type']);
		$product_types = @$this->request->data['StockProcesses']['product_type'];
		// pr($this->request->data['Memo']['product_type']);exit;
		$conditions = array();
		if ($product_types) {
			$conditions['product_type_id'] = $product_types;
		}
		$product_list = $this->Product->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('order' => 'asc')
		));
		if ($product_list) {
			$form->create('StockProcesses', array('role' => 'form', 'action' => 'index'));

			echo $form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'product_id checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $product_list));
			$form->end();
		} else {
			echo '';
		}
		$this->autoRender = false;
	}
}
