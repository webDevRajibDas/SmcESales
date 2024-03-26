<?php
App::uses('AppController', 'Controller');
/**
 * GiftItems Controller
 *
 * @property Doctor $Doctor
 * @property PaginatorComponent $Paginator
 */
class DistGiftItemsController extends AppController
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
		$this->set('page_title', 'Dist Gift Item List');
		$conditions = array();
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$distributor_conditions = array();
		$route_conditions = array();
		$market_conditions = array();
		if ($office_parent_id != 0) {
			$this->LoadModel('Office');
			$office_type = $this->Office->find('first', array(
				'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
				'recursive' => -1
			));
			$office_type_id = $office_type['Office']['office_type_id'];
		}


		if ($office_parent_id == 0) {
			$region_office_condition = array('office_type_id' => 3);
			$office_conditions = array('office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
		} else {
			if ($office_type_id == 3) {
				$distributor_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
				$route_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
				$conditions[] = array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
				$region_office_condition = array('office_type_id' => 3, 'Office.id' => $this->UserAuth->getOfficeId());
				$office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId(), 'office_type_id' => 2);
			} elseif ($office_type_id == 2) {

				$distributor_conditions = array('distdistributor.office_id' => $this->UserAuth->getOfficeId());
				$route_conditions = array('distdistributor.office_id' => $this->UserAuth->getOfficeId());
				$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'office_type_id' => 2);
				$conditions[] = array('DistSalesRepresentative.office_id' => $this->UserAuth->getOfficeId());
			}
		}
		$this->LoadModel('DistDistributor');
		$this->LoadModel('DistRoute');
		$routes = $this->DistRoute->find(
			'all',
			array(
				'conditions' => $route_conditions,
				'joins' => array(
					array(
						'table' => 'dist_distributors',
						'alias' => 'DistDistributor',
						'type' => 'inner',
						'conditions' => 'DistDistributor.id=DistRoute.id'
					),
					array(
						'table' => 'offices',
						'alias' => 'Office',
						'type' => 'inner',
						'conditions' => 'Office.id=DistRoute.office_id'
					),
				),
				'recursive' => -1,
				'fields' => array('DistRoute.id', 'DistRoute.name')
			)
		);

		$route_list = array();
		foreach ($routes as $key => $value) {
			$route_list[$value['DistRoute']['id']] = $value['DistRoute']['name'];
		}


		$distributor_list_r = $this->DistDistributor->find('all', array(
			'fields' => array('DistDistributor.id', 'DistDistributor.name'),
			'conditions' => $distributor_conditions,
			'joins' => array(
				array(
					'table' => 'offices',
					'alias' => 'Office',
					'type' => 'inner',
					'conditions' => 'Office.id=DistDistributor.office_id'
				),
			),
			'recursive' => -1
		));
		foreach ($distributor_list_r as $key => $value) {
			$distributor_list[$value['DistDistributor']['id']] = $value['DistDistributor']['name'];
		}
		$this->set(compact('distributor_list', 'route_list'));
		$this->LoadModel('Office');

		$offices = $this->Office->find('list', array(
			'conditions' => $office_conditions,
			'fields' => array('office_name')
		));

		if (isset($region_office_condition)) {
			$region_offices = $this->Office->find('list', array(
				'conditions' => $region_office_condition,
				'order' => array('office_name' => 'asc')
			));

			$this->set(compact('region_offices'));
		}
		$this->set(compact('offices', 'office_id'));

		$this->DistGiftItem->recursive = 0;
		$this->DistGiftItem->virtualFields = array(
			'route' => 'DistRoute.name',
			'distributor' => 'DistDistributor.name',
			'market' => 'DistMarket.name',
			'product' => 'Product.name',
			'quantity' => 'DistGiftItemDetail.quantity',
		);
		$this->paginate = array(
			'fields' => array('DistGiftItem.*', 'DistOutlet.*', 'DistMarket.name', 'DistRoute.name', 'DistDistributor.name', 'DistGiftItemDetail.quantity', 'Product.name', 'DistSalesRepresentative.name'),
			'order' => array('DistGiftItem.id' => 'DESC'),
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'dist_markets',
					'alias' => 'DistMarket',
					'type' => 'Inner',
					'conditions' => 'DistMarket.id=DistOutlet.dist_market_id'
				),
				array(
					'table' => 'dist_distributors',
					'alias' => 'DistDistributor',
					'type' => 'Inner',
					'conditions' => 'DistDistributor.id=DistGiftItem.distributor_id'
				),
				array(
					'table' => 'dist_routes',
					'alias' => 'DistRoute',
					'type' => 'Inner',
					'conditions' => 'DistRoute.id=DistMarket.dist_route_id'
				),
				array(
					'table' => 'offices',
					'alias' => 'Office',
					'type' => 'Inner',
					'conditions' => 'Office.id=DistDistributor.office_id'
				),
				array(
					'table' => 'dist_gift_item_details',
					'alias' => 'DistGiftItemDetail',
					'type' => 'Inner',
					'conditions' => 'DistGiftItem.id=DistGiftItemDetail.gift_item_id'
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'Inner',
					'conditions' => 'Product.id=DistGiftItemDetail.product_id'
				),
			),
		);
		// pr($this->paginate());exit;
		$this->set('dist_gift_items', $this->paginate());

		//===============================
		$this->LoadModel('DistRoute');
		$this->LoadModel('DistMarket');
		$markets = $this->DistMarket->find(
			'all',
			array(
				'conditions' => $market_conditions,
				'joins' => array(
					array(
						'table' => 'dist_routes',
						'alias' => 'DistRoute',
						'type' => 'Inner',
						'conditions' => 'DistRoute.id=DistMarket.dist_route_id'
					),
				),
				'recursive' => -1,
				'fields' => array('DistMarket.id', 'DistMarket.name')
			)
		);
		$market_list = array();
		foreach ($markets as $key => $value) {
			$market_list[$value['DistMarket']['id']] = $value['DistMarket']['name'];
		}
		$this->set(compact('market_list'));
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
		$this->set('page_title', 'List Gift Item Details');
		if (!$this->DistGiftItem->exists($id)) {
			throw new NotFoundException(__('Invalid doctor'));
		}
		$options = array('conditions' => array('DistGiftItem.' . $this->DistGiftItem->primaryKey => $id), 'recursive' => 0);
		$this->set('DistGiftItem', $this->DistGiftItem->find('first', $options));

		$GiftItemDetails = $this->DistGiftItem->GiftItemDetail->find('all', array(
			'conditions' => array('GiftItemDetail.gift_item_id' => $id),
			'recursive' => 0
		));
		$this->set('GiftItemDetails', $GiftItemDetails);
	}

	public function get_distributor_list()
	{
		$this->LoadModel('Office');
		$this->LoadModel('DistDistributor');
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$office_id = $this->request->data['office_id'];

		$this->DistDistributor->unbindModel(
			array(
				'belongsTo' => array('Office')
			)
		);
		$distributor_list_r = $this->DistDistributor->find('all', array(
			'fields' => array('DistDistributor.id', 'DistDistributor.name'),
			'conditions' => array(
				'DistDistributor.office_id' => $office_id,
			),
			'recursive' => 0
		));
		foreach ($distributor_list_r as $key => $value) {
			$distributor_list[$value['DistDistributor']['id']] = $value['DistDistributor']['name'];
		}


		if (isset($distributor_list)) {
			$form->create('DistGiftItem', array('role' => 'form', 'action' => 'filter'));

			echo $form->input('distributor_id', array('class' => 'form-control distributor_id', 'empty' => '--- Select---', 'options' => $distributor_list));
			$form->end();
		} else {
			echo '';
		}


		$this->autoRender = false;
	}

	public function get_route_list()
	{

		$view = new View($this);

		$form = $view->loadHelper('Form');

		// $distributor_id = $this->request->data['distributor_id'];
		$dist_distributor_id = $this->request->data['distributor_id'];
		//get territory list
		$this->LoadModel('DistRouteMapping');
		$this->LoadModel('DistRoute');
		$routes = $this->DistRoute->find(
			'all',
			array(
				// 'conditions'=>array('DistRoute.distributor_id'=>$distributor_id),
				'conditions' => array('DistRouteMapping.dist_distributor_id' => $dist_distributor_id),
				'joins' => array(
					array(
						'table' => 'dist_route_mappings',
						'alias' => 'DistRouteMapping',
						'type' => 'inner',
						'conditions' => 'DistRoute.id=DistRouteMapping.dist_route_id'
					),

				),
				'fields' => array('DistRoute.id', 'DistRoute.name'),
			)
		);

		$dist_route_ids = array();
		foreach ($routes as $key => $value) {
			$dist_route_ids[$value['DistRoute']['id']] = $value['DistRoute']['name'];
		}

		if (isset($dist_route_ids)) {
			$form->create('DistGiftItem', array('role' => 'form', 'action' => 'filter'));

			echo $form->input('dist_route_id', array('class' => 'form-control dist_route_id', 'empty' => '--- Select---', 'options' => $dist_route_ids));
			$form->end();
		} else {
			echo '';
		}


		$this->autoRender = false;
	}

	public function get_market_list()
	{

		$view = new View($this);

		$form = $view->loadHelper('Form');

		$dist_route_id = $this->request->data['dist_route_id'];
		//get market list
		$this->LoadModel('DistRoute');
		$this->LoadModel('DistMarket');

		// $this->DistMarket->unbindModel(
		// 	array(
		// 		'belongsTo'=>array('DistRoute')
		// 		)
		// 	);
		$markets = $this->DistMarket->find(
			'all',
			array(
				'fields' => array('DistMarket.id', 'DistMarket.name'),
				'conditions' => array(

					'DistMarket.dist_route_id' => $dist_route_id,
				),
				'recursive' => 0

			)
		);

		$market_list = array();
		foreach ($markets as $key => $value) {
			$market_list[$value['DistMarket']['id']] = $value['DistMarket']['name'];
		}

		if (isset($market_list)) {
			$form->create('DistGiftItem', array('role' => 'form', 'action' => 'filter'));

			echo $form->input('dist_market_id', array('class' => 'form-control dist_market_id', 'empty' => '--- Select---', 'options' => $market_list));
			$form->end();
		} else {
			echo '';
		}


		$this->autoRender = false;
	}

	function download_xl()
	{
		// pr($this->request->data);exit;
		$params = $this->request->data;
		$conditions = array();
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id != 0) {
			$this->LoadModel('Office');
			$office_type = $this->Office->find('first', array(
				'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
				'recursive' => -1
			));
			$office_type_id = $office_type['Office']['office_type_id'];
		}
		if ($office_parent_id != 0) {
			if ($office_type_id == 3) {
				if (!empty($params['office_id'])) {
					$conditions[] = array('DistDistributor.office_id' => $params['office_id']);
				} else {
					$conditions[] = array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
				}
			} elseif ($office_type_id == 2) {
				if (!empty($params['office_id'])) {
					$conditions[] = array('DistDistributor.office_id' => $params['office_id']);
				} else {
					$conditions[] = array('DistDistributor.office_id' => $this->UserAuth->getOfficeId());
				}
			}
		}
		if (!empty($params['DistGiftItem']['dist_market_id'])) {
			$conditions[] = array('DistMarket.id' => $params['DistGiftItem']['dist_market_id']);
		} elseif (!empty($params['DistGiftItem']['dist_route_id'])) {
			$conditions[] = array('DistRoute.id' => $params['DistGiftItem']['dist_route_id']);
		} elseif (!empty($params['DistGiftItem']['distributor_id'])) {
			$conditions[] = array('DistDistributor.id' => $params['DistGiftItem']['distributor_id']);
		} elseif (!empty($params['DistGiftItem']['office_id'])) {
			$conditions[] = array('DistDistributor.office_id' => $params['DistGiftItem']['office_id']);
		} elseif (!empty($params['DistGiftItem']['region_office_id'])) {
			$conditions[] = array('Office.parent_office_id' => $params['DistGiftItem']['region_office_id']);
		}
		if ($params['DistGiftItem']['date_from'] != '') {
			$conditions[] = array('DistGiftItem.date >=' => Date('Y-m-d', strtotime($params['DistGiftItem']['date_from'])));
		}
		if ($params['DistGiftItem']['date_to'] != '') {
			$conditions[] = array('DistGiftItem.date <=' => Date('Y-m-d', strtotime($params['DistGiftItem']['date_to'])));
		}
		$this->DistGiftItem->recursive = 0;
		$this->DistGiftItem->virtualFields = array(
			'route' => 'DistRoute.name',
			'distributor' => 'DistDistributor.name',
			'market' => 'DistMarket.name',
			'product' => 'Product.name',
			'quantity' => 'DistGiftItemDetail.quantity',
		);
		$dist_gift_items = $this->DistGiftItem->find('all', array(
			'fields' => array('DistGiftItem.*', 'DistOutlet.*', 'DistMarket.name', 'DistRoute.name', 'DistDistributor.name', 'DistGiftItemDetail.quantity', 'Product.name', 'DistSalesRepresentative.name'),
			'order' => array('DistGiftItem.id' => 'DESC'),
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'dist_markets',
					'alias' => 'DistMarket',
					'type' => 'Inner',
					'conditions' => 'DistMarket.id=DistOutlet.dist_market_id'
				),
				array(
					'table' => 'dist_distributors',
					'alias' => 'DistDistributor',
					'type' => 'Inner',
					'conditions' => 'DistDistributor.id=DistGiftItem.distributor_id'
				),
				array(
					'table' => 'dist_routes',
					'alias' => 'DistRoute',
					'type' => 'Inner',
					'conditions' => 'DistRoute.id=DistMarket.dist_route_id'
				),
				array(
					'table' => 'offices',
					'alias' => 'Office',
					'type' => 'Inner',
					'conditions' => 'Office.id=DistDistributor.office_id'
				),
				array(
					'table' => 'dist_gift_item_details',
					'alias' => 'DistGiftItemDetail',
					'type' => 'Inner',
					'conditions' => 'DistGiftItem.id=DistGiftItemDetail.gift_item_id'
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'Inner',
					'conditions' => 'Product.id=DistGiftItemDetail.product_id'
				),
			),
		));
		$this->autoRender = false;
		// pr($dist_gift_items);exit;
		/* echo $this->DistGiftItem->getLastQuery();
		pr($dist_gift_items);
		exit; */
		$View = new View($this, false);
		$View->set(compact('dist_gift_items'));
		$html = $View->render('download_xl');
		echo $html;
	}
	public function get_office_list()
	{
		$this->loadModel('Office');
		$rs = array(array('id' => '', 'name' => '---- All -----'));

		$parent_office_id = $this->request->data['region_office_id'];

		$office_conditions = array('Office.parent_office_id' => $parent_office_id, 'Office.office_type_id' => 2);

		$offices = $this->Office->find(
			'all',
			array(
				'fields' => array('id', 'office_name'),
				'conditions' => $office_conditions,
				'order' => array('office_name' => 'asc'),
				'recursive' => -1
			)
		);

		$data_array = array();
		foreach ($offices as $office) {
			$data_array[] = array(
				'id' => $office['Office']['id'],
				'name' => $office['Office']['office_name'],
			);
		}

		//$data_array = Set::extract($offices, '{n}.Office');

		if (!empty($offices)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}

		$this->autoRender = false;
	}
}
