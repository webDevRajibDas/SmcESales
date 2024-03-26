<?php
App::uses('AppController', 'Controller');
/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class TerritoriesController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$this->set('page_title', 'Territory List');
		$this->loadModel('Office');
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$conditions = array();
			//$OfficeConditions = array();
		} else {
			$conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			//$OfficeConditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}

		$this->paginate = array(
			'conditions' => $conditions,
			'recursive' => 0,
			'order' => array('Territory.id' => 'DESC')
		);



		$this->set('territories', $this->paginate());

		$offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));

		$this->set(compact('offices'));
	}

	/**
	 * admin_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null)
	{
		$this->set('page_title', 'Territory Details');
		if (!$this->Territory->exists($id)) {
			throw new NotFoundException(__('Invalid territory'));
		}
		$options = array('conditions' => array('Territory.' . $this->Territory->primaryKey => $id));
		$this->set('territory', $this->Territory->find('first', $options));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add()
	{
		$this->set('page_title', 'Add Territory');

		$this->loadModel('TerritoryProductGroup');
		if ($this->request->is('post')) {

			$this->request->data['Territory']['created_at'] = $this->current_datetime();
			$this->request->data['Territory']['created_by'] = $this->UserAuth->getUserId();
			$this->request->data['Territory']['updated_at'] = $this->current_datetime();
			$this->Territory->create();
			if (!isset($this->request->data['Territory']['product_group_id']) || empty($this->request->data['Territory']['product_group_id'])) {
				$this->Territory->invalidate('product_group_id', 'Product group field is required');
			}
			if ($this->request->data['Territory']['is_child'] == 1 && !$this->request->data['Territory']['parent_id']) {

				$this->Territory->invalidate('parent_id', 'Parent territory field is required');
			}


			if ($this->request->data['Territory']['is_child'] == 0) {
				$this->request->data['Territory']['parent_id'] = 0;
			}

			if ($this->Territory->validates() && $this->Territory->save($this->request->data)) {
				if (!empty($this->request->data['thana_id']) && $this->request->data['Territory']['is_child'] == 0) {
					$thana_array = array();
					foreach ($this->request->data['thana_id'] as $key => $val) {
						$data['thana_id'] = $val;
						$data['territory_id'] = $this->Territory->id;
						$data['updated_at'] = $this->current_datetime();
						$thana_array[] = $data;
					}
					$this->loadModel('ThanaTerritory');
					$this->ThanaTerritory->saveAll($thana_array);
				}

				if (!empty($this->request->data['Territory']['product_group_id'])) {
					$product_group = array();
					foreach ($this->request->data['Territory']['product_group_id'] as $key => $val) {
						$p_data['product_group_id'] = $val;
						$p_data['territory_id'] = $this->Territory->id;
						$product_group[] = $p_data;
					}
					$this->TerritoryProductGroup->saveAll($product_group);
				}

				//create a store automatically in store table
				$this->request->data['Store']['name'] = $this->request->data['Territory']['name'] . '_Store';
				$this->request->data['Store']['store_type_id'] = 3;
				$this->request->data['Store']['office_id'] = $this->request->data['Territory']['office_id'];
				$this->request->data['Store']['territory_id'] = $this->Territory->getLastInsertID();;
				$this->request->data['Store']['created_at'] = $this->current_datetime();
				$this->request->data['Store']['updated_at'] = $this->current_datetime();
				$this->request->data['Store']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['Store']['updated_by'] = $this->UserAuth->getUserId();


				$this->loadModel('Store');
				$this->Store->create();
				$this->Store->save($this->request->data);

				$this->Session->setFlash(__('The territory has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {

				//$this->Session->setFlash(__('The territory could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$conditions = array();
			//$OfficeConditions = array();
		} else {
			$conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId());
			//$OfficeConditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}
		$conditions['Territory.parent_id'] = 0;
		$parents = $this->Territory->find('list', array(
			'conditions' => $conditions
		));
		$this->set(compact('parents'));
		/* ------------- product group ----------------- */
		$this->LoadModel('ProductGroup');
		$productGroups = $this->ProductGroup->find('list', array(
			'recursive' => -1
		));
		$this->set(compact('productGroups'));

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$conditions = array();
			//$OfficeConditions = array();
		} else {
			$conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			//$OfficeConditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}
		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));


		$this->loadModel('District');
		$districts = $this->District->find('list', array('order' => array('District.name' => 'asc')));
		$this->set(compact('offices', 'districts'));
	}

	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null)
	{
		$this->set('page_title', 'Edit Territory');

		$this->loadModel('TerritoryProductGroup');
		$this->Territory->id = $id;
		if (!$this->Territory->exists($id)) {
			throw new NotFoundException(__('Invalid territory'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {

			$this->request->data['Territory']['updated_by'] = $this->UserAuth->getUserId();
			/* pr($this->request->data);
			exit; */
			if (!isset($this->request->data['Territory']['product_group_id']) || empty($this->request->data['Territory']['product_group_id'])) {
				$this->Territory->invalidate('product_group_id', 'Product group field is required');
			}
			if ($this->request->data['Territory']['is_child'] == 1 && !$this->request->data['Territory']['parent_id']) {

				$this->Territory->invalidate('parent_id', 'Parent territory field is required');
			}


			if ($this->request->data['Territory']['is_child'] == 0) {
				$this->request->data['Territory']['parent_id'] = 0;
			}

			if ($this->Territory->validates() && $this->Territory->save($this->request->data)) {

				$this->loadModel('ThanaTerritory');

				if ($this->request->data['Territory']['is_child'] == 1) {

					$this->ThanaTerritory->deleteAll(array('ThanaTerritory.territory_id' => $id));
				}

				if (!empty($this->request->data['thana_id']) && $this->request->data['Territory']['is_child'] == 0) {

					$thana_array = array();
					foreach ($this->request->data['thana_id'] as $key => $val) {
						$data['thana_id'] = $val;
						$data['territory_id'] = $id;
						$data['updated_at'] = $this->current_datetime();
						$thana_array[] = $data;
					}

					$this->ThanaTerritory->saveAll($thana_array);
				}
				if (!empty($this->request->data['Territory']['product_group_id'])) {
					$this->TerritoryProductGroup->deleteAll(array('territory_id' => $id));
					$product_group = array();
					foreach ($this->request->data['Territory']['product_group_id'] as $key => $val) {
						$p_data['product_group_id'] = $val;
						$p_data['territory_id'] = $id;
						$product_group[] = $p_data;
					}
					$this->TerritoryProductGroup->saveAll($product_group);
				}
				$this->loadModel('Store');
				$store_update['Store.name'] = "'" . $this->request->data['Territory']['name'] . '_Store' . "'";
				$store_update['Store.updated_at'] = "'" . $this->current_datetime() . "'";
				$store_update['Store.updated_by'] = "'" . $this->UserAuth->getUserId() . "'";
				$this->Store->updateAll($store_update, array('Store.territory_id' => $id));

				$this->Session->setFlash(__('The territory has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$options = array('conditions' => array('Territory.' . $this->Territory->primaryKey => $id));
			$this->request->data = $this->Territory->find('first', $options);

			$selected_product_group = $this->TerritoryProductGroup->find('list', array(
				'conditions' => array('TerritoryProductGroup.territory_id' => $id),
				'fields' => array('id', 'product_group_id')
			));
			if ($this->request->data['Territory']['parent_id']) {
				$this->request->data['Territory']['is_child'] = 1;
			} else {
				$this->request->data['Territory']['is_child'] = 0;
			}
			$this->request->data['Territory']['product_group_id'] = $selected_product_group;
		}

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			if (isset($this->request->data['Territory']['office_id']))
				$conditions = array('Territory.office_id' => $this->request->data['Territory']['office_id']);
			else
				$conditions = array();
			//$OfficeConditions = array();
		} else {
			if (isset($this->request->data['Territory']['office_id']))
				$conditions = array('Territory.office_id' => $this->request->data['Territory']['office_id']);
			else
				$conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId());
			//$OfficeConditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}
		/* Ignore Territory who is already assigned with SO  Start*/
		$this->LoadModel('SalesPerson');
		$except_territory_ids = $this->SalesPerson->find('list', array(
			'conditions' => array(

				'Territory.parent_id' => 0
			),
			'joins' => array(
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.territory_id = Territory.id'
				)
			),
			'fields' => array('Territory.id', 'Territory.name'),

		));
		/* Ignore Territory who is already assigned with SO  Start*/
		$conditions['NOT'] = array('Territory.id' => array_keys($except_territory_ids));
		$conditions['Territory.parent_id'] = 0;
		$parents = $this->Territory->find('list', array(
			'conditions' => $conditions
		));
		$this->set(compact('parents'));
		/* ------------- product group ----------------- */
		$this->LoadModel('ProductGroup');
		$productGroups = $this->ProductGroup->find('list', array(
			'recursive' => -1
		));
		$this->set(compact('productGroups'));

		//$offices = $this->Territory->Office->find('list');

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$conditions = array();
			//$OfficeConditions = array();
		} else {
			$conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			//$OfficeConditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}
		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));

		$this->loadModel('District');
		$this->loadModel('ThanaTerritory');
		$districts = $this->District->find('list', array('order' => array('District.name' => 'asc')));
		$thanas = $this->ThanaTerritory->find('all', array('conditions' => array('ThanaTerritory.territory_id' => $id)));
		$this->set(compact('offices', 'districts', 'thanas'));
	}

	/**
	 * admin_delete method
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param string $id
	 * @return void
	 */
	public function admin_delete($id = null)
	{
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Territory->id = $id;
		if (!$this->Territory->exists()) {
			throw new NotFoundException(__('Invalid territory'));
		}
		if ($this->Territory->delete()) {
			$this->Session->setFlash(__('Territory deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Territory was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_delete_thana($id = null, $territory_id = null)
	{
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->loadModel('ThanaTerritory');
		$this->ThanaTerritory->id = $id;
		if (!$this->ThanaTerritory->exists()) {
			throw new NotFoundException(__('Invalid territory'));
		}
		if ($this->ThanaTerritory->delete()) {
			$this->Session->setFlash(__('Thana deleted'), 'flash/success');
			$this->redirect(array('action' => 'edit/' . $territory_id));
		}
	}


	public function admin_get_thana()
	{
		$this->loadModel('Thana');
		$district_id = $this->request->data['district_id'];
		$rs = array(array('id' => '', 'name' => '---- Select Thana -----'));
		$thana_list = $this->Thana->find('all', array(
			'fields' => array('Thana.id', 'Thana.name'),
			'conditions' => array(
				'Thana.district_id' => $district_id
			),
			'order' => array('Thana.name' => 'asc'),
			'recursive' => -1
		));
		$data_array = Set::extract($thana_list, '{n}.Thana');
		if (!empty($thana_list)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
	/***************** Start Changes 30-10-2019***********************************/
	public function admin_getOfficeSrData()
	{
		$office_id = $this->request->data['office_id'];
		$user_group = $this->request->data['user_group'];
		$user_list = array();
		$this->loadModel('SalesPerson');
		$users = $this->SalesPerson->find('all', array(
			'conditions' => array(
				'SalesPerson.dist_sales_representative_id !=' => null,
				'SalesPerson.office_id' => $office_id,
			)
		));
		foreach ($users as $key => $value) {
			$user_list[$key] = $value['SalesPerson']['dist_sales_representative_id'];
		}
		//pr($user_list);die();
		$rs = array();
		//if($user_group == 1064){

		$sr_conditions = array(
			'DistSalesRepresentative.office_id' => $office_id,
			'DistSalesRepresentative.is_active' => 1,
			'Not' => array('DistSalesRepresentative.id' => $user_list),

		);
		/*}
          else{
          	$sr_conditions = array(
          		'DistSalesRepresentative.office_id'=>$office_id,
          		'DistSalesRepresentative.is_active'=>1,
          		
          	);
          }*/
		$this->loadModel('DistSalesRepresentative');
		$sr_list = $this->DistSalesRepresentative->find('all', array(
			'conditions' => $sr_conditions,
			'joins' => array(
				array(
					'table' => 'dist_distributors',
					'alias' => 'Dist',
					'conditions' => 'Dist.id=DistSalesRepresentative.dist_distributor_id',
					'type' => 'Left'
				),
			),
			'fields' => array('DistSalesRepresentative.id', 'DistSalesRepresentative.name', 'Dist.Name'),
			'order' => array('DistSalesRepresentative.name' => 'asc'),
			'recursive' => -1,
		));

		$data_array = array();
		//pr($sr_list);die();
		foreach ($sr_list as $key => $value) {
			$data_array[] = array(
				'id' => $value['DistSalesRepresentative']['id'],
				'name' => $value['DistSalesRepresentative']['name'] . "(" . $value['Dist']['Name'] . ")",
			);
		}
		//pr($data_array);die();
		if (!empty($sr_list)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
		exit();
		// pr($sr_list);die();
	}

	/***************** end Changes 30-10-2019***********************************/
	public function admin_get_distributor_list_for_db_user()
	{
		$office_id = $this->request->data['office_id'];
		$this->loadModel('DistTso');
		$this->loadModel('DistTsoMapping');
		$this->loadModel('DistRouteMapping');
		$this->loadModel('DistAreaExecutive');
		$this->loadModel('DistUserMapping');

		$user_id = $this->UserAuth->getUserId();
		$user_group_id = $this->Session->read('UserAuth.UserGroup.id');

		$exist_db_id = $this->DistUserMapping->find('list', array('conditions' => array('office_id' => $office_id), 'fields' => array('DistUserMapping.dist_distributor_id', 'DistUserMapping.office_id')));

		if ($user_group_id == 1029 || $user_group_id == 1028) {
			if ($user_group_id == 1028) {
				$dist_ae_info = $this->DistAreaExecutive->find('first', array(
					'conditions' => array('DistAreaExecutive.user_id' => $user_id),
					'recursive' => -1,
				));
				$dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
				$dist_tso_info = $this->DistTso->find('list', array(
					'conditions' => array('dist_area_executive_id' => $dist_ae_id),
					'fields' => array('DistTso.id', 'DistTso.dist_area_executive_id'),
				));

				$dist_tso_id = array_keys($dist_tso_info);
			} else {
				$dist_tso_info = $this->DistTso->find('first', array(
					'conditions' => array('DistTso.user_id' => $user_id),
					'recursive' => -1,
				));
				$dist_tso_id = $dist_tso_info['DistTso']['id'];
			}

			$tso_dist_list = $this->DistTsoMapping->find('list', array(
				'conditions' => array(
					'dist_tso_id' => $dist_tso_id,
				),
				'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
			));
			$dist_conditions = array(
				'DistDistributor.id' => array_keys($tso_dist_list),
				'DistDistributor.is_active' => 1,
				'NOT' => array('DistDistributor.id' => array_keys($exist_db_id)),
			);
		} else {

			$dist_conditions = array(
				'DistDistributor.office_id' => $office_id,
				'DistDistributor.is_active' => 1,
				'NOT' => array('DistDistributor.id' => array_keys($exist_db_id)),
			);
		}
		//pr($dist_conditions);die();
		$rs = array(array('id' => '', 'name' => '---- Select ----'));
		$this->loadModel('DistDistributor');
		$dist_distributors = $this->DistDistributor->find('all', array(
			'conditions' => $dist_conditions,
			'order' => array('DistDistributor.name' => 'asc'),
			'recursive' => 0
		));

		$data_array = array();
		if (!empty($dist_distributors)) {
			foreach ($dist_distributors as $key => $value) {
				$data_array[] = array(
					'id' => $value['DistDistributor']['id'],
					'name' => $value['DistDistributor']['name']
				);
			}
		}

		if (!empty($dist_distributors)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}

		$this->autoRender = false;
	}

	function get_territory_list()
	{

		$office_id = $this->request->data['office_id'];
		$rs = array(array('id' => '', 'name' => '---- Select Territory -----'));

		$territory_list = $this->Territory->find('all', array(
			'fields' => array('Territory.id', 'Territory.name'),
			'conditions' => array(
				'Territory.office_id' => $office_id,
				'Territory.parent_id' => 0
			),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => -1
		));
		$data_array = Set::extract($territory_list, '{n}.Territory');
		if (!empty($territory_list)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	function get_territory_list_new()
	{

		$office_id = $this->request->data['office_id'];
		$rs = array(array('id' => '', 'name' => '---- Select Territory -----'));

		$this->loadModel('SalesPerson');
		/* Ignore Territory who is already assigned with SO  Start*/
		$except_territory_ids = $this->SalesPerson->find('list', array(
			'conditions' => array(
				'Territory.office_id' => $office_id,
				'Territory.parent_id' => 0
			),
			'joins' => array(
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.territory_id = Territory.id'
				)
			),
			'fields' => array('Territory.id', 'Territory.name'),
		));

		/* Ignore Territory who is already assigned with SO  Start*/


		$territory_list = $this->Territory->find('all', array(
			'fields' => array('Territory.id', 'Territory.name'),
			'conditions' => array(
				'Territory.office_id' => $office_id,
				'Territory.parent_id' => 0,
				'NOT' => array('Territory.id' => array_keys($except_territory_ids))
			),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => -1
		));
		$data_array = Set::extract($territory_list, '{n}.Territory');
		if (!empty($territory_list)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}

	public function download_xl()
	{
		// echo 'hello';exit;
		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes

		$this->loadModel('Office');
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$params = $this->request->query['data'];
		// print_r($params);exit;
		if ($office_parent_id == 0) {
			$conditions = array();
			//$OfficeConditions = array();
		} else {
			$conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			//$OfficeConditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}

		/*here start */

		$conditions = array();
		// $conditions[] = array('Territory.office_id' => $params['Territory']['office_id']);
		if (!empty($params['Territory']['office_id'])) {
			$conditions[] = array('Territory.office_id' => $params['Territory']['office_id']);
		}
		if (!empty($params['Territory']['name'])) {
			$conditions[] = array('Territory.name' => $params['Territory']['name']);
		}



		$results = $this->Territory->find('all', array(

			'conditions' => $conditions,
			'recursive' => 0,
			'order' => array('Territory.id' => 'DESC')
		));

		$territories = $results;





		// $this->set(compact('offices'));

		$table = '<table border="1">
					<thead>
						<tr>
							<td><b>Id</b></td>
							<td><b>Name</b></td>
							<td><b>Address</b></td>
							<td><b>Office</b></td>
							<td><b>Is Active<b></td>
							
						</tr>
					</thead>
					<tbody>';
		foreach ($territories as $territory) :
			$table .= '<tr>
						<td>' . h($territory['Territory']['id']) . '</td>
						<td>' . h($territory['Territory']['name']) . '</td>
						<td>' . h($territory['Territory']['address']) . '</td>
						<td>' . h($territory['Office']['office_name']) . '</td>
						<td >';

			if ($territory['Territory']['is_active'] == 1) {
				$table .= '' . h('Yes') . '';
			} else {
				$table .= '' . h('No') . '';
			}

			$table .= '</td>
						
					</tr>';
		endforeach;
		$table .= '</tbody>
				</table>';

		header("Content-Type: application/vnd.ms-excel");
		header('Content-Disposition: attachment; filename="Territories.xls"');
		header("Cache-Control: ");
		header("Pragma: ");
		header("Expires: 0");
		echo $table;
		$this->autoRender = false;
	}
}
