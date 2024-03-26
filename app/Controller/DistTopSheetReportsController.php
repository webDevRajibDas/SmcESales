<?php
App::uses('AppController', 'Controller');
/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class DistTopSheetReportsController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $uses = array('DistMemo', 'Thana', 'Market', 'DistDistributor', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'Combination', 'MemoDetail', 'MeasurementUnit', 'ProductCategory', 'TerritoryAssignHistory', 'Office', 'Territory', 'ProductType');
	public $components = array('Paginator', 'Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{

		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes


		$this->set('page_title', 'DB wise Top Sheet Report');

		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id' => 3),
			'order' => array('office_name' => 'asc')
		));

		$dbs = array();
		$request_data = array();
		$report_type = array();

		$product_type_list = $this->ProductType->find('list');
		$this->set(compact('product_type_list'));
		$product_category_list = $this->ProductCategory->find('list');

		$types = array(
			'db' => 'By DB',
			'sr' => 'By SR',
			'tso' => 'By TSO',
			'ae' => 'By AE',
		);
		$this->set(compact('types'));

		$product_name = $this->Product->find('all', array(
			'fields' => array('Product.name', 'Product.id', 'MU.name as mes_name', 'Product.product_category_id'),
			'joins' => array(
				array(
					'table' => 'measurement_units',
					'alias' => 'MU',
					'type' => 'LEFT',
					'conditions' => array('MU.id= Product.sales_measurement_unit_id')
				)
			),
			'conditions' => array('NOT' => array('Product.product_category_id' => 32)),
			'order' => array('Product.order' => 'asc'),
			'recursive' => -1
		));

		//pr($product_name);
		//pr($this->request->data);

		//report type
		$unit_types = array(
			'1' => 'Sales Unit',
			'2' => 'Base Unit',
		);
		$this->set(compact('unit_types'));


		//product_measurement
		$product_measurement = $this->Product->find('list', array(
			//'conditions'=> $pro_conditions,
			'fields' => array('Product.id', 'Product.sales_measurement_unit_id'),
			'order' =>  array('order' => 'asc'),
			'recursive' => -1
		));
		$this->set(compact('product_measurement'));

		//pr($product_measurement);


		$region_office_id = 0;

		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$this->set(compact('office_parent_id'));

		if ($office_parent_id == 0) {
			$office_conditions = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));

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

			$office_id = array_keys($offices);
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
			$office_id = $this->UserAuth->getOfficeId();

			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'id' 	=> $office_id,
				),
				'order' => array('office_name' => 'asc')
			));

			$dbs = $this->DistDistributor->find('list', array(
				'conditions' => array(
					'DistDistributor.office_id' => $office_id,
					'DistDistributor.is_active' => 1
				),
			));
		}


		$this->set('page_title', 'DB Wise Top Sheet Report');


		if ($this->request->is('post')) {
			$request_data = $this->request->data;
			$this->Session->write('request_data', $request_data);
			$office_id = $request_data['Memo']['office_id'];
			//pr($office_id);die();
		}
		$this->set('page_title', 'Top Sheet Report');

		/*
         * Report generation query start ;
         */
		if ($this->request->is('post')) {

			$request_data = $this->request->data;
			$region_office_id = isset($this->request->data['Memo']['region_office_id']) != '' ? $this->request->data['Memo']['region_office_id'] : $region_office_id;
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
			} else {
				$offices = $this->Office->find('list', array(
					'conditions' => array(
						'office_type_id' 	=> 2,
						"NOT" => array("id" => array(30, 31, 37))
					),
					'order' => array('office_name' => 'asc')
				));

				$office_ids = array_keys($offices);
			}

			$office_id = $request_data['Memo']['office_id'];
			$this->set(compact('office_id'));

			if (!$office_id) {
				$office_id = $office_ids;
				$office_id_raw_query = $office_ids;
			} else {
				$office_id_raw_query = array($office_id);
			}

			$officeids = implode(',', $office_id_raw_query);
			$office_con = "AND m.office_id IN( " . $officeids . ")";

			//if (!empty($request_data['Memo']['office_id'])) {
			// pr($request_data);
			// exit;

			//Start so list
			$sos = array();

			$type = $this->request->data['Memo']['type'];
			$this->set(compact('type'));

			@$date_from = date('Y-m-d', strtotime($this->request->data['Memo']['date_from']));
			@$date_to = date('Y-m-d', strtotime($this->request->data['Memo']['date_to']));

			$unit_type = $this->request->data['Memo']['unit_type'];
			$this->set(compact('unit_type'));
			$unit_type_text = ($unit_type == 2) ? 'Base' : 'Sales';
			$this->set(compact('unit_type_text'));

			$office_id = $request_data['Memo']['office_id'];
			$db_id_post = $request_data['Memo']['db_id'];
			$sr_id_post = $request_data['Memo']['sr_id'];
			$this->set(compact('office_id'));
			$this->set(compact('db_id'));
			$this->set(compact('sr_id'));
			$db_q = '';
			/* if (@$request_data['Memo']['db_id'] && $type == 'db') {

					// $db_id = implode(',', $request_data['Memo']['db_id']);
					$db_id = $request_data['Memo']['db_id'];
					$db_q = "AND db.id IN(" . $db_id . ")";
				} elseif (@$request_data['Memo']['sr_id'] && $type == 'sr') {
					// $db_id = implode(',', $request_data['Memo']['sr_id']);
					$db_id =  $request_data['Memo']['sr_id'];
					$db_q = "AND sr.id IN(" . $db_id . ")";
				} */

			if (!empty($request_data['Memo']['db_id'])) {

				$db_id = implode(',', $request_data['Memo']['db_id']);
				//$db_id = $request_data['Memo']['db_id'];
				$db_q = "AND db.id IN(" . $db_id . ")";
			}


			$group_by = '';
			$fields = '';
			$orders = '';

			if ($type == 'sr') {
				$fields = 'sr.id as ref_id,sr.name as name,db.name as dist_name,DistTso.name as tso_name,DistAE.name as ae_name,';
				$group_by = 'sr.id,sr.name,db.name,db.name,DistTso.name,DistAE.name,';
				$orders = 'DistAE.name,DistTso.name,db.name,sr.name';
			}

			if ($type == 'db') {
				$fields = 'db.id as ref_id,db.name as dist_name,DistTso.name as tso_name,DistAE.name as ae_name,';
				$group_by = 'db.id,db.name,DistTso.name,DistAE.name,';
				$orders = 'DistAE.name,DistTso.name,db.name';
			}

			if ($type == 'tso') {
				$fields = 'DistTso.id as ref_id,DistTso.name as tso_name,DistAE.name as ae_name,';
				$group_by = 'DistTso.id,DistTso.name,DistAE.name,';
				$orders = 'DistAE.name,DistTso.name';
			}

			if ($type == 'ae') {
				$fields = 'DistAE.id as ref_id,DistAE.name as ae_name,';
				$group_by = 'DistAE.id,DistAE.name,';
				$orders = 'DistAE.name';
			}

			$this->set(compact('r_db_list'));

			$pro_q = '';
			if ($request_data['Memo']['product_id']) {
				$product_type_ids = @$request_data['Memo']['product_type'];
				$product_ids = implode(',', $request_data['Memo']['product_id']);
				if ($product_type_ids == 1)
					$pro_q = "AND md.price >0 AND md.product_id IN(" . $product_ids . ")";
				else
					$pro_q = "AND md.product_id IN(" . $product_ids . ")";
			} else {
				$product_type_ids = @$request_data['Memo']['product_type'];
				if ($product_type_ids == 1)
					$pro_q = "AND md.price >0 AND p.product_type_id IN(" . $product_type_ids . ")";
				else
					$pro_q = "AND p.product_type_id IN(" . $product_type_ids . ")";
			}
			$sql = "SELECT $fields md.product_id, SUM(md.sales_qty) as pro_quantity, SUM(md.sales_qty*md.price) as price, p.cyp as cyp_v, p.cyp_cal as cyp,Office.office_name as office_name, Office.[order] as office_order
				   FROM dist_memos m 
				   INNER JOIN dist_memo_details md On md.dist_memo_id=m.id
				   INNER JOIN dist_distributors db on db.id=m.distributor_id
				   LEFT JOIN dist_tso_mappings DistTsoMapping on DistTsoMapping.dist_distributor_id=m.distributor_id
				   LEFT JOIN dist_tsos DistTso on DistTso.id = DistTsoMapping.dist_tso_id
				   LEFT JOIN dist_area_executives DistAE on DistAE.id = DistTso.dist_area_executive_id
				   LEFT JOIN offices Office on Office.id = DistTso.office_id
				   INNER JOIN dist_sales_representatives sr on sr.id=m.sr_id
				   INNER JOIN products AS p ON md.product_id = p.id 
				   
				   WHERE (m.memo_date BETWEEN  '" . $date_from . "' AND '" . $date_to . "') $db_q  AND m.status > 0   " . $office_con . " $pro_q  GROUP BY  $group_by md.product_id, p.cyp, p.cyp_cal,Office.office_name, Office.[order] ORDER BY Office.[order] asc, $orders";
			//echo $sql ;exit;
			$product_quantity = $this->DistMemo->query($sql);
			// pr($product_quantity);exit;
			$db_sales_results = array();
			$product_id = array();
			foreach ($product_quantity as $result) {
				$product_id[] = $result[0]['product_id'];
				$db_sales_results[$result[0]['ref_id']] = array(
					'ref_id' => $result[0]['ref_id'],
					'dist_name' => $result[0]['dist_name'],
					'tso_name' => $result[0]['tso_name'],
					'ae_name' => $result[0]['ae_name'],
					'office_name' => $result[0]['office_name'],
					'ref_name' => $result[0]['name'],
					'product_id' => $result[0]['product_id'],
					'pro_quantity' => $result[0]['pro_quantity'],
					'price' => $result[0]['price'],
					'cyp_v' => $result[0]['cyp_v'],
					'cyp' => $result[0]['cyp'],
				);
			}

			$this->set(compact('product_quantity', 'sales_people', 'db_sales_results', 'product_return_quantity', 'db_return_results'));
			//}
		}
		$this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'request_data', 'product_name', 'office_id', 'office_parent_id', 'dbs', 'date_from', 'date_to', 'region_offices', 'offices', 'product_category_list'));

		$conditions = 	array('NOT' => array('ProductCategory.id' => 32));
		// $conditions['Product']['id']=$product_id;
		if (!@$this->data['Memo']['product_id']) {
			$categories_products = $this->ProductCategory->find(
				'all',
				array(
					'conditions' => $conditions,
					//'fields' => 'name',
					'order' => array('ProductCategory.id' => 'asc'),
					'contain' => array(
						'Product' => array(
							'conditions' => array('Product.id' => $this->data['Memo']['product_id'], 'Product.id' => $product_id)
						),
					),
					'recursive' => 1,
				)
			);
			// pr($categories_products);exit;
		} else {
			$categories_products = $this->ProductCategory->find(
				'all',
				array(
					'conditions' => $conditions,
					'contain' => array(
						'Product' => array(
							'conditions' => array(
								'Product.id' => $this->data['Memo']['product_id'],
								'Product.is_distributor_product' => 1,
							)
						),
					),
					'order' => array('ProductCategory.id' => 'asc'),
					'recursive' => 1,
				)
			);
		}



		$this->set(compact('categories_products'));
	}

	public function getECTotal($request_data = array(), $id = 0, $office_id = 0)
	{
		$date_from = date('Y-m-d', strtotime($request_data['Memo']['date_from']));
		$date_to = date('Y-m-d', strtotime($request_data['Memo']['date_to']));

		//----------office conding-----------\\
		$region_office_id = $this->request->data['Memo']['region_office_id'];
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
		} else {
			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' 	=> 2,
					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));

			$office_ids = array_keys($offices);
		}

		$office_id = $request_data['Memo']['office_id'];

		if (!$office_id) {
			$office_id = $office_ids;
			$office_id_raw_query = $office_ids;
		} else {
			$office_id_raw_query = array($office_id);
		}

		//$officeids = implode(',', $office_id_raw_query);
		//$office_con = "AND m.office_id IN( " . $officeids . ")";

		//---------end-----------\\

		$conditions = array(
			'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
			/*'Memo.gross_value >' => 0,*/
			'DistMemo.status >' => 0,
			'DistMemo.office_id' => $office_id_raw_query
		);
		if ($request_data['Memo']['type'] == 'db') {
			$conditions['db.id'] = $id;
		} elseif ($request_data['Memo']['type'] == 'tso') {
			$conditions['DistTso.id'] = $id;
		} elseif ($request_data['Memo']['type'] == 'ae') {
			$conditions['DistAE.id'] = $id;
		} else {
			$conditions['sr.id'] = $id;
		}

		if (!empty($request_data['Memo']['db_id']) and  $request_data['Memo']['type'] != 'db') {
			$conditions['db.id'] = $request_data['Memo']['db_id'];
		}

		if ($request_data['Memo']['product_id']) {
			$product_type_ids = @$request_data['Memo']['product_type'];
			$product_ids =  @$request_data['Memo']['product_id'];
			if ($product_type_ids == 1) {
				// $pro_q = "AND md.price >0 AND md.product_id IN(".$product_ids.")";
				$conditions['MemoDetail.price >'] = 0;
				$conditions['MemoDetail.product_id'] = $product_ids;
			} else {
				// $pro_q = "AND md.product_id IN(".$product_ids.")";
				$conditions['MemoDetail.product_id'] = $product_ids;
			}
		} else {
			$product_type_ids = @$request_data['Memo']['product_type'];
			if ($product_type_ids == 1)
				$pro_q = "AND md.price >0 AND p.product_type_id IN(" . $product_type_ids . ")";
			else
				$pro_q = "AND p.product_type_id IN(" . $product_type_ids . ")";
			if ($product_type_ids == 1) {
				// $pro_q = "AND md.price >0 AND md.product_id IN(".$product_ids.")";
				$conditions['MemoDetail.price >'] = 0;
				$conditions['Product.product_type_id'] = $product_type_ids;
			} else {
				// $pro_q = "AND md.product_id IN(".$product_ids.")";
				$conditions['Product.product_type_id'] = $product_type_ids;
			}
		}

		//echo '<pre>';print_r($conditions);exit;

		$result = $this->DistMemo->find('count', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'MemoDetail',
					'table' => 'dist_memo_details',
					'type' => 'INNER',
					'conditions' => 'DistMemo.id = MemoDetail.dist_memo_id'
				),
				array(
					'alias' => 'db',
					'table' => 'dist_distributors',
					'type' => 'LEFT',
					'conditions' => 'DistMemo.distributor_id = db.id'
				),
				array(
					'alias' => 'DistTsoMapping',
					'table' => 'dist_tso_mappings',
					'type' => 'LEFT',
					'conditions' => 'DistTsoMapping.dist_distributor_id=DistMemo.distributor_id'
				),
				array(
					'alias' => 'DistTso',
					'table' => 'dist_tsos',
					'type' => 'LEFT',
					'conditions' => 'DistTso.id = DistTsoMapping.dist_tso_id'
				),
				array(
					'alias' => 'DistAE',
					'table' => 'dist_area_executives',
					'type' => 'LEFT',
					'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'
				),
				array(
					'alias' => 'sr',
					'table' => 'dist_sales_representatives',
					'type' => 'LEFT',
					'conditions' => 'DistMemo.sr_id = sr.id'
				),
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => 'Product.id = MemoDetail.product_id'
				),
			),
			'fields' => array('COUNT(DistMemo.dist_memo_no) AS count'),
			'group' => array('DistMemo.dist_memo_no'),
			'recursive' => -1
		));

		// pr($result);
		// exit;

		return $result;
	}

	public function getOCTotal($request_data = array(), $id = 0, $office_id = 0)
	{
		$date_from = date('Y-m-d', strtotime($request_data['Memo']['date_from']));
		$date_to = date('Y-m-d', strtotime($request_data['Memo']['date_to']));

		//----------office conding-----------\\
		$region_office_id = $this->request->data['Memo']['region_office_id'];
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
		} else {
			$offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' 	=> 2,
					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));

			$office_ids = array_keys($offices);
		}

		$office_id = $request_data['Memo']['office_id'];

		if (!$office_id) {
			$office_id = $office_ids;
			$office_id_raw_query = $office_ids;
		} else {
			$office_id_raw_query = array($office_id);
		}

		//$officeids = implode(',', $office_id_raw_query);
		//$office_con = "AND m.office_id IN( " . $officeids . ")";

		//---------end-----------\\

		$conditions = array(
			'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
			'DistMemo.status >' => 0,
			'DistMemo.office_id' => $office_id_raw_query
		);
		if ($request_data['Memo']['type'] == 'db') {
			$conditions['db.id'] = $id;
		} elseif ($request_data['Memo']['type'] == 'tso') {
			$conditions['DistTso.id'] = $id;
		} elseif ($request_data['Memo']['type'] == 'ae') {
			$conditions['DistAE.id'] = $id;
		} else {
			$conditions['sr.id'] = $id;
		}

		if (!empty($request_data['Memo']['db_id']) and  $request_data['Memo']['type'] != 'db') {
			$conditions['db.id'] = $request_data['Memo']['db_id'];
		}

		if ($request_data['Memo']['product_id']) {
			$product_type_ids = @$request_data['Memo']['product_type'];
			$product_ids = @$request_data['Memo']['product_id'];
			if ($product_type_ids == 1) {
				// $pro_q = "AND md.price >0 AND md.product_id IN(".$product_ids.")";
				$conditions['MemoDetail.price >'] = 0;
				$conditions['MemoDetail.product_id'] = $product_ids;
			} else {
				// $pro_q = "AND md.product_id IN(".$product_ids.")";
				$conditions['MemoDetail.product_id'] = $product_ids;
			}
		} else {
			$product_type_ids = @$request_data['Memo']['product_type'];
			if ($product_type_ids == 1)
				$pro_q = "AND md.price >0 AND p.product_type_id IN(" . $product_type_ids . ")";
			else
				$pro_q = "AND p.product_type_id IN(" . $product_type_ids . ")";
			if ($product_type_ids == 1) {
				// $pro_q = "AND md.price >0 AND md.product_id IN(".$product_ids.")";
				$conditions['MemoDetail.price >'] = 0;
				$conditions['Product.product_type_id'] = $product_type_ids;
			} else {
				// $pro_q = "AND md.product_id IN(".$product_ids.")";
				$conditions['Product.product_type_id'] = $product_type_ids;
			}
		}
		$result = $this->DistMemo->find('count', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'MemoDetail',
					'table' => 'dist_memo_details',
					'type' => 'INNER',
					'conditions' => 'DistMemo.id = MemoDetail.dist_memo_id'
				),
				array(
					'alias' => 'db',
					'table' => 'dist_distributors',
					'type' => 'LEFT',
					'conditions' => 'DistMemo.distributor_id = db.id'
				),
				array(
					'alias' => 'DistTsoMapping',
					'table' => 'dist_tso_mappings',
					'type' => 'LEFT',
					'conditions' => 'DistTsoMapping.dist_distributor_id=DistMemo.distributor_id'
				),
				array(
					'alias' => 'DistTso',
					'table' => 'dist_tsos',
					'type' => 'LEFT',
					'conditions' => 'DistTso.id = DistTsoMapping.dist_tso_id'
				),
				array(
					'alias' => 'DistAE',
					'table' => 'dist_area_executives',
					'type' => 'LEFT',
					'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'
				),
				array(
					'alias' => 'sr',
					'table' => 'dist_sales_representatives',
					'type' => 'LEFT',
					'conditions' => 'DistMemo.sr_id = sr.id'
				),
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => 'Product.id = MemoDetail.product_id'
				),
			),
			'fields' => array('COUNT(DISTINCT DistMemo.outlet_id) as count'),
			'group' => array('DistMemo.outlet_id'),
			'recursive' => -1
		));
		// echo $this->DistMemo->getLastQuery();
		// exit;
		// pr($result);
		// exit;

		return $result;
	}


	//xls download
	public function admin_dwonload_xls()
	{
		$request_data = $this->Session->read('request_data');
		$categories_products = $this->Session->read('categories_products');

		$sales_people = $this->Session->read('sales_people');
		$product_quantity = $this->Session->read('product_quantity');
		$office_id = $request_data['Memo']['office_id'];


		$header = "";
		$data1 = "";



		$product_qnty = array();
		$product_price = array();
		$product_cyp_v = array();
		$product_cyp = array();
		//pr($product_quantity);
		foreach ($product_quantity as $data) {
			$product_qnty[$data['0']['sales_person_id']][$data['0']['product_id']] = $data['0']['pro_quantity'];
			$product_price[$data['0']['sales_person_id']][$data['0']['product_id']] = $data['0']['price'];
			$product_cyp_v[$data['0']['sales_person_id']][$data['0']['product_id']] = $data['0']['cyp_v'];
			$product_cyp[$data['0']['sales_person_id']][$data['0']['product_id']] = $data['0']['cyp'];
		}

		$data1 .= ucfirst("Sales Officer\t");
		foreach ($categories_products as $c_value) {
			if ($c_value['Product']) {
				foreach ($c_value['Product'] as $p_value) {
					$data1 .= ucfirst($p_value['name'] . "\t");
				}
				$data1 .= ucfirst('Total ' . $c_value['ProductCategory']['name'] . "\t");
			}
		}
		$data1 .= ucfirst("Total Sales (Tk.)\t");
		$data1 .= ucfirst("CYP\t");
		$data1 .= ucfirst("EC Call\t");
		$data1 .= ucfirst("Outlet Cover\t");


		$data1 .= "\n";

		foreach ($sales_people as $data_s) {
			$so_id = $data_s['0']['sales_person_id'];

			$data1 .= ucfirst($data_s['SalesPerson']['name'] . ' (' . $data_s['Territory']['name'] . ')' . "\t");


			$total_sales = 0;
			$total_cyp = 0;
			foreach ($categories_products as $c_value) {
				$total_pro_qty = 0;
				$total_pro_price = 0;
				$total_pro_cyp = 0;
				if ($c_value['Product']) {
					foreach ($c_value['Product'] as $p_value) {
						$pro_id = $p_value['id'];

						$pro_qty = isset($product_qnty[$so_id][$pro_id]) ? $product_qnty[$so_id][$pro_id] : '0.00';
						$total_pro_qty += $pro_qty;

						$pro_price = isset($product_price[$so_id][$pro_id]) ? $product_price[$so_id][$pro_id] : '0.00';
						$total_pro_price += $pro_price;

						//FOR CYP
						$pro_cyp_v = isset($product_cyp_v[$so_id][$pro_id]) ? $product_cyp_v[$so_id][$pro_id] : '0';

						$pro_cyp = isset($product_cyp[$so_id][$pro_id]) ? $product_cyp[$so_id][$pro_id] : '';
						$pro_cyp_t = 0;
						if ($pro_cyp_v && $pro_cyp) {
							if ($pro_cyp == '*') {
								$pro_cyp_t = ($pro_qty) * ($pro_cyp_v);
							} elseif ($pro_cyp == '/') {
								$pro_cyp_t = ($pro_qty) / ($pro_cyp_v);
							} elseif ($pro_cyp == '+') {
								$pro_cyp_t = ($pro_qty) + ($pro_cyp_v);
							} elseif ($pro_cyp == '-') {
								$pro_cyp_t = ($pro_qty) - ($pro_cyp_v);
							}
						}
						$total_pro_cyp += $pro_cyp_t;

						$data1 .= ucfirst($pro_qty . "\t");
					}
					$total_sales += $total_pro_price;
					$total_cyp += $total_pro_cyp;

					$data1 .= ucfirst(sprintf("%01.2f", $total_pro_qty) . "\t");
				}
			}

			$data1 .= ucfirst(sprintf("%01.2f", $total_sales) . "\t");
			$data1 .= ucfirst(sprintf("%01.2f", $total_cyp) . "\t");
			$data1 .= ucfirst($this->getECTotal($request_data, $so_id, $office_id) . "\t");
			$data1 .= ucfirst($this->getOCTotal($request_data, $so_id, $office_id) . "\t");

			$data1 .= "\n";
		}


		$data1 = str_replace("\r", "", $data1);
		if ($data1 == "") {
			$data1 = "\n(0) Records Found!\n";
		}

		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: attachment; filename=\"Top-Sheet-Reports-" . date("jS-F-Y-H:i:s") . ".xls\"");
		header("Pragma: no-cache");
		header("Expires: 0");

		echo $data1;
		exit;

		$this->autoRender = false;
	}


	public function get_db_list()
	{
		$this->loadModel('DistDistributor');
		$office_id = $this->request->data['office_id'];
		$date_from = date('Y-m-d', strtotime($this->request->data['date_from']));
		$date_to = date('Y-m-d', strtotime($this->request->data['date_to']));
		$dist_conditions = array('DistMemo.office_id' => $office_id, 'DistMemo.memo_date >=' => $date_from, 'DistMemo.memo_date <=' => $date_to);
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$data_array = array();
		if ($date_from && $office_id && $date_to) {

			$distDistributors = $this->DistDistributor->find('all', array(
				'conditions' => $dist_conditions,
				'joins' => array(array('table' => 'dist_memos', 'alias' => 'DistMemo', 'conditions' => 'DistMemo.distributor_id=DistDistributor.id')),
				'group' => array('DistDistributor.id', 'DistDistributor.name'),
				'fields' => array('DistDistributor.id', 'DistDistributor.name'),
				'order' => array('DistDistributor.name' => 'asc'),
			));
			$data_array = Set::extract($distDistributors, '{n}.DistDistributor');
		}
		if (!empty($distDistributors)) {
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		$this->autoRender = false;
	}
	public function get_sr_list()
	{
		$this->loadModel('DistSalesRepresentative');
		$office_id = $this->request->data['office_id'];
		$date_from = date('Y-m-d', strtotime($this->request->data['date_from']));
		$date_to = date('Y-m-d', strtotime($this->request->data['date_to']));
		$dist_conditions = array('DistMemo.office_id' => $office_id, 'DistMemo.memo_date >=' => $date_from, 'DistMemo.memo_date <=' => $date_to);
		$data_array = array();
		if ($date_from && $office_id && $date_to) {

			$distSalesrepresentative = $this->DistSalesRepresentative->find('list', array(
				'conditions' => $dist_conditions,
				'joins' => array(array('table' => 'dist_memos', 'alias' => 'DistMemo', 'conditions' => 'DistMemo.sr_id=DistSalesRepresentative.id')),
				'group' => array('DistSalesRepresentative.id', 'DistSalesRepresentative.name'),
				'fields' => array('DistSalesRepresentative.id', 'DistSalesRepresentative.name'),
				'order' => array('DistSalesRepresentative.name' => 'asc'),
			));
		}
		$output = '<option value="">---- All -----</option>';
		if (!empty($distSalesrepresentative)) {
			foreach ($distSalesrepresentative as $key => $name) {
				$output .= '<option value="' . $key . '">' . $name . '</option>';
			}

			echo $output;
		} else {
			echo $output;
		}
		$this->autoRender = false;
	}


	// unit convert to base unit
	public function unit_convert($product_id = '', $measurement_unit_id = '', $qty = '')
	{
		$this->loadModel('ProductMeasurement');
		$unit_info = $this->ProductMeasurement->find('first', array(
			'conditions' => array(
				'ProductMeasurement.product_id' => $product_id,
				'ProductMeasurement.measurement_unit_id' => $measurement_unit_id
			)
		));
		$number = $qty;
		if (!empty($unit_info)) {
			$number = $unit_info['ProductMeasurement']['qty_in_base'] * $qty;
			$number = round($number);
			/*$decimals = 2;
		   //$number = 221.12345;
		   $number = $number * pow(10,$decimals);
		   $number = intval($number);
		   $number = $number / pow(10,$decimals);
		   return sprintf('%.2f', ($number));*/
			return $number;
		} else {
			// return sprintf('%.2f', $number);
			return  $number;
		}
	}
	// function get_product_list()
	// {
	// 	$view = new View($this);

	// 	$form = $view->loadHelper('Form');
	// 	// $product_types=@array_values($this->request->data['Memo']['product_type']);
	// 	$product_types = @$this->request->data['Memo']['product_type'];
	// 	// pr($this->request->data['Memo']['product_type']);exit;
	// 	$conditions = array();
	// 	if ($product_types) {
	// 		$conditions['product_type_id'] = $product_types;
	// 	}
	// 	$conditions['is_distributor_product'] = 1;
	// 	$product_list = $this->Product->find('list', array(
	// 		'conditions' => $conditions,
	// 		'order' =>  array('order' => 'asc')
	// 	));
	// 	if ($product_list) {
	// 		$form->create('Memo', array('role' => 'form', 'action' => 'index'));

	// 		echo $form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'product_id checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $product_list));
	// 		$form->end();
	// 	} else {
	// 		echo '';
	// 	}
	// 	$this->autoRender = false;
	// }

	function get_product_list()
	{
		$view = new View($this);
		$form = $view->loadHelper('Form');
		$product_types_arr = $this->request->data['product_type'];
		$product_types = $product_types_arr[0]['value'];


		if (isset($this->request->data['product_type_categories_id'])) {

			$product_categories = $this->request->data['product_type_categories_id'];
		}

		$conditions = array();
		if ($product_types) {
			$conditions['product_type_id'] = $product_types;
		}
		if (isset($this->request->data['product_type_categories_id']) && $product_categories) {


			$conditions['product_category_id'] = $product_categories;
		}
		$product_list = $this->Product->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('order' => 'asc')
		));
		// echo $this->Product->getLastquery();
		if ($product_list) {
			echo $form->create('Memo', array('role' => 'form'));

			echo $form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'product_id checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $product_list));
			$form->end();
		} else {
			echo '';
		}
		$this->autoRender = false;
	}

	public function get_product_list_by_category()
	{
		$view = new View($this);
		$form = $view->loadHelper('Form');
		$product_type_categories_id = @$this->request->data['product_type_categories_id'];
		$product_types = @$this->request->data['product_type'][0]['value'];

		$conditions = array();
		if ($product_types) {
			$conditions['product_type_id'] = $product_types;
		}
		if ($product_type_categories_id) {
			$conditions[] = array('product_category_id' => $product_type_categories_id);
		}

		$product_list = $this->Product->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('order' => 'asc')
		));
		// echo $this->Product->getLastquery();
		if ($product_list) {
			echo $form->create('Memo', array('role' => 'form'));

			echo $form->input('product_id', array('id' => 'product_id', 'label' => false, 'class' => 'product_id checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required' => true, 'options' => $product_list));
			$form->end();
		} else {
			echo '';
		}
		$this->autoRender = false;
	}
}
