<?php
App::uses('AppController', 'Controller');
/**
 * DcrSettings Controller
 *
 * @property DcrReport $DcrReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistDcrReportsController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('DistOrder', 'DistOrderDetail', 'Office', 'OutletCategory', 'Thana', 'Market', 'Outlet', 'TerritoryAssignHistory', 'SalesPerson', 'Product', 'DistSalesRepresentative');
	public $components = array('Paginator', 'Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index($id = null)
	{


		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0); //300 seconds = 5 minutes

		$this->set('page_title', "Sales Representatives Daily Call Report");

		$territories = array();
		$request_data = array();
		$types = array();
		$so_list = array();


		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id' => 3),
			'order' => array('office_name' => 'asc')
		));

		//types
		$types = array(
			'territory' => 'By Terriotry',
			'so' => 'By SO',
		);
		$this->set(compact('types'));


		$this->set(compact('report_types'));



		$region_office_id = 0;

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$this->set(compact('office_parent_id'));

		$office_conditions = array('Office.office_type_id' => 2);

		if ($office_parent_id == 0) {
			$office_id = 0;
		} elseif ($office_parent_id == 14) {
			$region_office_id = $this->UserAuth->getOfficeId();
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.office_type_id' => 3, 'Office.id' => $region_office_id),
				'order' => array('office_name' => 'asc')
			));

			$office_conditions = array('Office.parent_office_id' => $region_office_id);

			$office_id = 0;

			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,

					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));

			$office_ids = array_keys($offices);

			if ($office_ids) $conditions['Territory.office_id'] = $office_ids;

			//pr($conditions);
			//exit;

		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
			$office_id = $this->UserAuth->getOfficeId();

			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'id' 	=> $office_id,
				),
				'order' => array('office_name' => 'asc')
			));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			//products
			$product_list = $this->Product->find('list', array(
				'conditions' => array(
					'NOT' => array('Product.product_category_id' => 32),
					'is_active' => 1,
					'Product.product_type_id' => 1
				),
				'order' =>  array('order' => 'asc')
			));
			$this->set(compact('product_list'));

			//for outlet category list
			$outlet_categories = $this->OutletCategory->find('list', array(
				'conditions' => array('is_active' => 1),
				'order' => array('category_name' => 'asc')
			));
			$this->set(compact('outlet_categories'));


			$request_data = $this->request->data;

			$date_from = date('Y-m-d', strtotime($request_data['DistDcrReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['DistDcrReports']['date_to']));
			$this->set(compact('date_from', 'date_to'));



			$region_office_id = isset($this->request->data['DistDcrReports']['region_office_id']) != '' ? $this->request->data['DistDcrReports']['region_office_id'] : $region_office_id;
			$this->set(compact('region_office_id'));
			$office_ids = array();
			if ($region_office_id) {
				$offices = $this->Office->find('list', array(
					'conditions' => array(
						'office_type_id' 	=> 2,
						'parent_office_id' 	=> $region_office_id,

						"NOT" => array("id" => array(30, 31, 37))
					),
					'order' => array('office_name' => 'asc')
				));

				$office_ids = array_keys($offices);
			}

			$office_id = isset($this->request->data['DistDcrReports']['office_id']) != '' ? $this->request->data['DistDcrReports']['office_id'] : $office_id;
			$this->set(compact('office_id'));

			$db_id = isset($this->request->data['DistDcrReports']['db_id']) != '' ? $this->request->data['DistDcrReports']['db_id'] : $office_id;
			$this->set(compact('db_id'));

			$sr_id = isset($this->request->data['DistDcrReports']['sr_id']) != '' ? $this->request->data['DistDcrReports']['sr_id'] : $office_id;
			$this->set(compact('sr_id'));


			//For Query Conditon
			$conditions = array(
				'DistOrder.order_date BETWEEN ? and ? ' => array($date_from, $date_to),

				'DistOrder.gross_value >' => 0,
				'DistOrder.status !=' => 0,
				/* 'DistOrderDetail.price >' => 0, */

			);


			if ($office_ids) $conditions['DistOrder.office_id'] = $office_ids;
			if ($office_id) $conditions['DistOrder.office_id'] = $office_id;


			if ($sr_id) $conditions['DistOrder.sr_id'] = $sr_id;
			if ($db_id) $conditions['DistOrder.distributor_id'] = $db_id;


			/*pr($conditions);
			exit;*/

			$q_results = $this->DistOrder->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'DistOrderDetail',
						'table' => 'dist_order_details',
						'type' => 'INNER',
						'conditions' => 'DistOrder.id = DistOrderDetail.dist_order_id'
					),
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => 'DistOrderDetail.product_id = Product.id'
					),

					array(
						'alias' => 'DistOutlet',
						'table' => 'dist_outlets',
						'type' => 'INNER',
						'conditions' => 'DistOrder.outlet_id = DistOutlet.id'
					),
					array(
						'alias' => 'DistMarket',
						'table' => 'dist_markets',
						'type' => 'INNER',
						'conditions' => 'DistOrder.market_id = DistMarket.id'
					),
					array(
						'alias' => 'DistRoute',
						'table' => 'dist_routes',
						'type' => 'INNER',
						'conditions' => 'DistRoute.id = DistMarket.dist_route_id'
					),

				),

				'fields' => array(
					'SUM(CASE WHEN DistOrderDetail.price > 0 then DistOrderDetail.sales_qty end) as sales_qty',
					'SUM(CASE WHEN DistOrderDetail.price = 0 then DistOrderDetail.sales_qty end) as b_qty',
					'SUM(DistOrderDetail.sales_qty*DistOrderDetail.price) as price',
					'DistOrder.gross_value',
					'DistOrder.id',
					'DistOrder.status',
					'DistOrder.processing_status',
					'DistOrderDetail.product_id',
					'Product.name',
					'DistOrder.outlet_id',
					'DistOutlet.id',
					'DistOutlet.name',
					'DistOutlet.category_id',
					'DistMarket.id',
					'DistMarket.name',
					'DistRoute.name'
				),

				'group' => array(
					'DistOrder.id',
					'DistOrder.status',
					'DistOrder.processing_status',
					'DistOrder.gross_value',
					'DistOrderDetail.product_id',
					'Product.name',
					'DistOrder.outlet_id',
					'DistOutlet.id',
					'DistOutlet.category_id',
					'DistOutlet.name',
					'DistMarket.id',
					'DistMarket.name',
					'DistRoute.name'
				),

				'order' => array('DistMarket.name asc', 'DistOutlet.name asc'),
				'recursive' => -1,
				//'limit' => 15
			));
			// echo $this->DistOrder->getLastQuery();
			// exit;
			//pr($q_results);
			//exit;

			$results = array();
			$dist_order_ids = array();
			$total_sales_results = array();
			$bonus_results = array();
			foreach ($q_results as $result) {


				$results[$result['DistRoute']['name'] . '-' . $result['DistMarket']['name']][$result['DistOutlet']['name']][$result['DistOrder']['id']]['dist_order_detial'][$result['DistOrderDetail']['product_id']] =
					array(
						'product_id' 			=> $result['DistOrderDetail']['product_id'],
						'sales_qty' 			=> $result[0]['sales_qty'],
						'price' 				=> $result[0]['price'],
					);

				$results[$result['DistRoute']['name'] . '-' . $result['DistMarket']['name']][$result['DistOutlet']['name']][$result['DistOrder']['id']]['dist_order'] =
					array(
						'dist_order_id' 		=> $result['DistOrder']['id'],
						'gross_value' 			=> $result['DistOrder']['gross_value'],
						'outlet_category_name' 	=> $outlet_categories[$result['DistOutlet']['category_id']],
						'outlet_id' 			=> $result['DistOrder']['outlet_id'],
						'market_id' 			=> $result['DistMarket']['id'],
						'status' 				=> $result['DistOrder']['status'],
						'processing_status' 				=> $result['DistOrder']['processing_status'],
					);
				$total_sales_results[$result['DistOrderDetail']['product_id']] = (isset($total_sales_results[$result['DistOrderDetail']['product_id']]) ? $total_sales_results[$result['DistOrderDetail']['product_id']] : 0) + $result[0]['sales_qty'];

				$bonus_results[$result['DistOrderDetail']['product_id']] = array(
					'sales_qty' => (isset($bonus_results[$result['DistOrderDetail']['product_id']]['sales_qty']) ? $bonus_results[$result['DistOrderDetail']['product_id']]['sales_qty'] : 0) + $result[0]['b_qty'],
				);
				array_push($dist_order_ids, $result['DistOrder']['id']);
			}
			$this->set(compact('total_sales_results'));
			$this->set(compact('results'));
			$this->set(compact('dist_order_ids'));
			//for bonus
			/* $bonus_conditions = array(
				'DistOrderDetail.dist_order_id' => $dist_order_ids,
				'DistOrderDetail.price <' => 1,
			);
			$bonus_q_results = $this->DistOrderDetail->find('all', array(
				'conditions' => $bonus_conditions,

				'fields' => array(
					'SUM(DistOrderDetail.sales_qty) as sales_qty',
					'SUM(DistOrderDetail.sales_qty*DistOrderDetail.price) as price',
					'DistOrderDetail.product_id'
				),

				'group' => array('DistOrderDetail.product_id'),

				//'order' => array('Market.name asc', 'Outlet.name asc'),
				'recursive' => -1,
				//'limit' => 15
			)); */

			//pr($bonus_q_results);


			/* foreach ($bonus_q_results as $bonus_q_result) {
				$bonus_results[$bonus_q_result['DistOrderDetail']['product_id']] = array(
					'sales_qty' => $bonus_q_result[0]['sales_qty'],
				);
			} */

			$this->set(compact('bonus_results'));
		}

		$this->set(compact('offices', 'region_offices', 'office_id', 'request_data'));
	}

	function get_db_list()
	{
		$this->loadModel('DistTso');
		$this->loadModel('DistTsoMapping');
		$this->loadModel('DistRouteMapping');
		$this->loadModel('DistAreaExecutive');
		$user_id = $this->UserAuth->getUserId();
		$user_group_id = $this->Session->read('UserAuth.UserGroup.id');

		$office_id = $this->request->data['office_id'];
		$memo_date_from = $this->request->data['date_from'];
		$memo_date_to = $this->request->data['date_to'];
		$output = "<option value=''>--- Select Distributor ---</option>";

		if ($memo_date_from && $office_id && $memo_date_to) {
			$memo_date_from = date("Y-m-d", strtotime($memo_date_from));
			$memo_date_to = date("Y-m-d", strtotime($memo_date_to));
		}
		$this->loadModel('DistTsoMappingHistory');
		$this->loadModel('DistDistributor');
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
				'OR' => array(
					array(
						'DistOrder.order_date' => null,
						'DistDistributor.office_id' => $office_id,
						'DistDistributor.is_active' => 1
					),
					array(
						'DistOrder.office_id' => $office_id,
						'DistOrder.order_date >=' => $memo_date_from,
						'DistOrder.order_date <=' => $memo_date_to
					)
				)
			);
		} elseif ($user_group_id == 1034) {
			$sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
			$this->loadModel('DistUserMapping');
			$distributor = $this->DistUserMapping->find('first', array(
				'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
			));
			$distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];

			$dist_conditions = array(
				'DistDistributor.id' => $distributor_id
			);
		} else {
			$dist_conditions = array(
				'OR' => array(
					array(
						'DistOrder.order_date' => null,
						'DistDistributor.office_id' => $office_id,
						'DistDistributor.is_active' => 1
					),
					array(
						'DistOrder.office_id' => $office_id,
						'DistOrder.order_date >=' => $memo_date_from,
						'DistOrder.order_date <=' => $memo_date_to
					)
				)
			);
		}
		if ($memo_date_from && $office_id && $memo_date_to) {

			$distDistributors = $this->DistDistributor->find('list', array(
				'conditions' => $dist_conditions,
				'joins' => array(
					array(
						'table' => 'dist_orders',
						'alias' => 'DistOrder',
						'conditions' => "
							DistOrder.distributor_id=DistDistributor.id 
							AND DistOrder.order_date >= '$memo_date_from'
							AND DistOrder.order_date <= '$memo_date_to'",
						'type' => 'Left'
					)
				),
				'group' => array('DistDistributor.id', 'DistDistributor.name'),
				'order' => array('DistDistributor.name' => 'asc'),
			));

			if ($distDistributors) {
				$selected = "";
				foreach ($distDistributors as $key => $data) {
					$output .= "<option $selected value='$key'>$data</option>";
				}
			}
		}
		echo $output;
		$this->autoRender = false;
	}

	function get_sr_list_by_distributot_id_date_range()
	{
		$this->LoadModel('Territory');
		$distributor_id = $this->request->data['distributor_id'];
		//$order_date = $this->request->data['order_date'];
		$output = "<option value=''>--- Select SR ---</option>";
		/***************** this is for find SR in daterange *********************/
		if ($distributor_id) {
			//$order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));
			$order_date_from = date("Y-m-d H:i:s", strtotime($this->request->data['date_from']));
			$order_date_to = date("Y-m-d H:i:s", strtotime($this->request->data['date_to']));

			$sr = $this->DistSalesRepresentative->find('list', array(
				'conditions' => array(
					'OR' => array(
						array(
							'DistOrder.order_date' => null,
							'DistSalesRepresentative.is_active' => 1,
							'DistSalesRepresentative.dist_distributor_id' => $distributor_id,
						),
						array(
							'DistOrder.distributor_id' => $distributor_id,
						)
					),

				),
				'joins' => array(
					array(
						'table' => 'dist_Orders',
						'alias' => 'DistOrder',
						'type' => 'Left',
						'conditions' => "
                			DistOrder.sr_id=DistSalesRepresentative.id
                			AND DistOrder.order_date >= '$order_date_from'
							AND DistOrder.order_date <= '$order_date_to'
                			"
					)
				),
				'order' => array('DistSalesRepresentative.name' => 'asc')
			));

			if ($sr) {
				foreach ($sr as $key => $data) {
					$output .= "<option value='$key'>$data</option>";
				}
			}
		}
		/***************** this is for find SR in daterange end *********************/

		echo $output;
		$this->autoRender = false;
	}
}
