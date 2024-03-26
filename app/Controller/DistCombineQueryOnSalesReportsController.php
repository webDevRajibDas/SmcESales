<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistCombineQueryOnSalesReportsController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('Product', 'ProductCategory', 'DistMemo', 'Office', 'Territory', 'Division', 'District', 'Thana', 'Market', 'DistOutletCategory', 'DistOutlet', 'Institute', 'DistTso', 'DistTsoMapping', 'DistTsoMappingHistory');
	public $components = array('Paginator', 'Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index($id = null)
	{
		$this->Session->delete('detail_results');
		$this->Session->delete('outlet_lists');

		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes


		$this->set('page_title', "Query On Sales Information");

		$distributor = array();
		$tso = array();
		$request_data = array();
		$report_type = array();
		$so_list = array();


		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id' => 3),
			'order' => array('office_name' => 'asc')
		));



		//report type
		$unit_types = array(
			'1' => 'Sales Unit',
			'2' => 'Base Unit',
		);
		$this->set(compact('unit_types'));


		//report type
		$rows_array = array(
			'db' => 'By DB',
			'sr' => 'By SR',
		);
		$this->set(compact('rows_array'));

		$compotators = array(
			'<' => '<',
			'>' => '>',
			'<=' => '<=',
			'>=' => '>=',
			'between' => 'Between',
		);
		$this->set(compact('compotators'));

		$memo_totals = array(
			'permemo' => 'Per Memo',
			'memototal' => 'Memo Total',
		);
		$this->set(compact('memo_totals'));

		$volumes_values = array(
			'1' => 'Volume',
			'2' => 'Value',
		);
		$this->set(compact('volumes_values'));


		//for outlet category list
		$outlet_categories = $this->DistOutletCategory->find('list', array(
			'conditions' => array('is_active' => 1),
			'order' => array('category_name' => 'asc')
		));
		$this->set(compact('outlet_categories'));


		$conditions = array(
			'NOT' => array('Product.product_category_id' => 32),
			'is_active' => 1,
			'Product.product_type_id' => 1,
			'Product.is_distributor_product' => 1
		);
		$product_list = $this->Product->find('list', array(
			'conditions' => $conditions,
			'order' =>  array('order' => 'asc')
		));
		$this->set(compact('product_list'));

		//product_measurement
		$product_measurement = $this->Product->find('list', array(
			//'conditions'=> $pro_conditions,
			'fields' => array('Product.id', 'Product.sales_measurement_unit_id'),
			'order' =>  array('order' => 'asc'),
			'recursive' => -1
		));
		$this->set(compact('product_measurement'));



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


			$tsos = $this->DistTso->find('list', array(
				'conditions' => array(
					'DistTso.office_id' => $office_id,
					'DistTso.is_active' => 1,
				),
				'recursive' => 0,
			));
		}

		//pr($offices);


		if ($this->request->is('post') || $this->request->is('put')) {
			$all_offices = $this->Office->find('list', array(
				'conditions' => array(
					'office_type_id' 	=> 2,
					//'parent_office_id' 	=> $region_office_id,

					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));

			$request_data = $this->request->data;

			$request_data1 = $this->request->data['DistCombineQueryOnSalesReports'];
			// pr($request_data1);exit;
			$product_rows = array();
			$product_ids = array();
			$product_compotator = array();
			$in = 1;
			foreach ($request_data1['product_id'] as $i => $p) {
				if (!empty($p)) {
					foreach ($p as $p_key => $pid) {

						$product_ids[$in][] = $pid;
						$product_rows[$in]['product_id'][] = $pid;
					}
					$product_rows[$in]['compotator'] = $request_data1['compotator'][$i];
					$product_rows[$in]['qty'] = $request_data1['qty'][$i];
					$product_rows[$in]['qty2'] = @$request_data1['qty2'][$i] ? $request_data1['qty2'][$i] : 0;
					$product_rows[$in]['memo_total'] = $request_data1['memo_total'][$i];
					$product_rows[$in]['volume_or_value'] = $request_data1['volume_or_value'][$i];
					$in++;
				}
			}
			$this->set(compact('product_rows'));

			//pr($product_rows);
			//pr($product_ids);
			//exit;


			$date_from = date('Y-m-d', strtotime($request_data['DistCombineQueryOnSalesReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['DistCombineQueryOnSalesReports']['date_to']));
			$this->set(compact('date_from', 'date_to'));

			$this->set(compact('type'));

			$region_office_id = isset($this->request->data['DistCombineQueryOnSalesReports']['region_office_id']) != '' ? $this->request->data['DistCombineQueryOnSalesReports']['region_office_id'] : $region_office_id;
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

			$office_id = isset($this->request->data['DistCombineQueryOnSalesReports']['office_id']) != '' ? $this->request->data['DistCombineQueryOnSalesReports']['office_id'] : $office_id;
			$this->set(compact('office_id'));

			$tso_id = isset($this->request->data['DistCombineQueryOnSalesReports']['tso_id']) != '' ? $this->request->data['DistCombineQueryOnSalesReports']['tso_id'] : 0;
			$this->set(compact('tso_id'));
			$dist_id = isset($this->request->data['DistCombineQueryOnSalesReports']['dist_id']) != '' ? $this->request->data['DistCombineQueryOnSalesReports']['dist_id'] : 0;
			$this->set(compact('dist_id'));

			$by_rows =  $this->request->data['DistCombineQueryOnSalesReports']['rows'];
			$this->set(compact('by_rows'));

			/*$unit_type = $this->request->data['DistCombineQueryOnSalesReports']['unit_type'];
			$this->set(compact('unit_type'));
			$unit_type_text = ($unit_type==2)?'Base':'Sales';
			$this->set(compact('unit_type_text'));*/

			// Tso list making for post
			$tsos = $this->DistTso->find('list', array(
				'conditions' => array(
					'DistTso.office_id' => $office_id,
					//'DistTso.is_active'=> 1,
				),
				'recursive' => 0,
			));


			/*$distributors = $this->DistTsoMapping->find('all', array(
				'conditions'=> array(
					'DistTsoMapping.office_id'=>$office_id,
					'DistTsoMapping.dist_tso_id'=>$tso_id,
					),
				'joins'=>
				array(
					array(
						'table'=>'dist_distributors',
						'alias'=>'DB',
						'type' => 'LEFT',
						'conditions'=>array('DB.id= DistTsoMapping.dist_distributor_id')
						)
					),
				'fields'=>array('DB.id','DB.name'),
				));*/
			$distributors = $this->DistTsoMappingHistory->find('all', array(
				'conditions' => array(
					'DistTsoMappingHistory.office_id' => $office_id,
					'DistTsoMappingHistory.dist_tso_id' => $tso_id,
				),
				'joins' =>
				array(
					array(
						'table' => 'dist_distributors',
						'alias' => 'DB',
						'type' => 'LEFT',
						'conditions' => array('DB.id= DistTsoMappingHistory.dist_distributor_id')
					)
				),
				'fields' => array('Distinct(DB.id) as db_id', 'DB.name'),
				'group' => array('DB.id', 'DB.name'),
			));
			$dists = array();

			foreach ($distributors as $key => $value) {
				//$dists[$value['DB']['id']] = $value['DB']['name'];
				$dists[$value[0]['db_id']] = $value['DB']['name'];
			}
			$this->set(compact('dists'));
			$outlet_category_id = isset($this->request->data['DistCombineQueryOnSalesReports']['outlet_category_id']) != '' ? $this->request->data['DistCombineQueryOnSalesReports']['outlet_category_id'] : 0;
			$results = array();
			foreach ($product_ids as $key_p => $pid) {
				$pro_con = array(
					'NOT' => array('Product.product_category_id' => 32),
					'is_active' => 1,
					'Product.product_type_id' => 1,
					'Product.id' => $pid
				);
				$all_products = $this->Product->find('list', array(
					'conditions' => $pro_con,
					'order' =>  array('order' => 'asc')
				));
				$product_name_list = implode(',', $all_products);

				//For Query Conditon
				$conditions = array(
					'DistMemo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					'DistMemo.gross_value >' => 0,
					'DistMemo.status !=' => 0,
					'DistMemoDetail.price >' => 0,
					/*'Memo.outlet_id' => 297037*/
				);


				if ($office_ids) $conditions['DistMemo.office_id'] = $office_ids;
				if ($office_id) $conditions['DistMemo.office_id'] = $office_id;

				if ($tso_id) {
					$conditions['DistMemo.tso_id'] = $tso_id;
				}
				if ($dist_id) {
					$conditions['DistMemo.distributor_id'] = $dist_id;
				}

				if ($outlet_category_id) $conditions['DistOutlet.category_id'] = $outlet_category_id;
				if ($product_ids) $conditions['DistMemoDetail.product_id'] = $pid;

				//pr($conditions);
				//exit;


				$group = array(
					'DistMemo.outlet_id',
					'DistOutlet.name',
					'DistOutlet.category_id',
					'DistMemo.office_id',
					'DistMemo.territory_id',
					'DistMarket.name',
					'Office.order',
					'Office.office_name',
					'DistTso.name',
					'DistDistributor.name',
					'DistRoute.name',
				);

				$fields = array(
					'COUNT(DistMemo.outlet_id) as ec',
					'SUM(DistMemoDetail.sales_qty) as sales_qty',
					'SUM(DistMemoDetail.sales_qty*DistMemoDetail.price) as price',
					'DistMemo.office_id',
					'DistMemo.territory_id',
					'DistMemo.outlet_id',
					'DistOutlet.name',
					'DistOutlet.category_id',
					'DistMarket.name',
					'Office.office_name',
					'DistTso.name',
					'DistDistributor.name',

					'DistRoute.name',
				);
				if ($by_rows == 'sr') {

					$group[] = 'DistSalesRrepresentative.name';
					$fields[] = 'DistSalesRrepresentative.name';
				}
				if ($product_rows[$key_p]['memo_total'] == 'permemo') {
					$group[] = 'DistMemoDetail.dist_memo_id';
					$fields[] = 'DistMemoDetail.dist_memo_id';
				}
				if ($product_rows[$key_p]['volume_or_value'] == '1') {
					if ($product_rows[$key_p]['compotator'] == '>') {
						$group[count($group) - 1] .= ' HAVING SUM(DistMemoDetail.sales_qty) > ' . $product_rows[$key_p]['qty'];
					} elseif ($product_rows[$key_p]['compotator'] == '<') {
						$group[count($group) - 1] .= ' HAVING SUM(DistMemoDetail.sales_qty) < ' . $product_rows[$key_p]['qty'];
					} elseif ($product_rows[$key_p]['compotator'] == '>=') {
						$group[count($group) - 1] .= ' HAVING SUM(DistMemoDetail.sales_qty) >= ' . $product_rows[$key_p]['qty'];
					} elseif ($product_rows[$key_p]['compotator'] == '<=') {
						$group[count($group) - 1] .= ' HAVING SUM(DistMemoDetail.sales_qty) <= ' . $product_rows[$key_p]['qty'];
					} elseif ($product_rows[$key_p]['compotator'] == 'between') {
						$group[count($group) - 1] .= ' HAVING SUM(DistMemoDetail.sales_qty) BETWEEN ' . $product_rows[$key_p]['qty'] . ' AND ' . $product_rows[$key_p]['qty2'];
					}
				} else if ($product_rows[$key_p]['volume_or_value'] == '2') {
					if ($product_rows[$key_p]['compotator'] == '>') {
						$group[count($group) - 1] .= ' HAVING SUM(DistMemoDetail.sales_qty*DistMemoDetail.price) > ' . $product_rows[$key_p]['qty'];
					} elseif ($product_rows[$key_p]['compotator'] == '<') {
						$group[count($group) - 1] .= ' HAVING SUM(DistMemoDetail.sales_qty*DistMemoDetail.price) < ' . $product_rows[$key_p]['qty'];
					} elseif ($product_rows[$key_p]['compotator'] == '>=') {
						$group[count($group) - 1] .= ' HAVING SUM(DistMemoDetail.sales_qty*DistMemoDetail.price) >= ' . $product_rows[$key_p]['qty'];
					} elseif ($product_rows[$key_p]['compotator'] == '<=') {
						$group[count($group) - 1] .= ' HAVING SUM(DistMemoDetail.sales_qty*DistMemoDetail.price) <= ' . $product_rows[$key_p]['qty'];
					} elseif ($product_rows[$key_p]['compotator'] == 'between') {
						$group[count($group) - 1] .= ' HAVING SUM(DistMemoDetail.sales_qty*DistMemoDetail.price) BETWEEN ' . $product_rows[$key_p]['qty'] . ' AND ' . $product_rows[$key_p]['qty2'];
					}
				}
				$q_results = $this->DistMemo->find('all', array(
					'conditions' => $conditions,
					'joins' => array(
						array(
							'alias' => 'DistMemoDetail',
							'table' => 'dist_memo_details',
							'type' => 'INNER',
							'conditions' => 'DistMemo.id = DistMemoDetail.dist_memo_id'
						),
						array(
							'alias' => 'Product',
							'table' => 'products',
							'type' => 'INNER',
							'conditions' => 'DistMemoDetail.product_id = Product.id'
						),
						array(
							'alias' => 'DistOutlet',
							'table' => 'dist_outlets',
							'type' => 'INNER',
							'conditions' => 'DistMemo.outlet_id = DistOutlet.id'
						),
						array(
							'alias' => 'DistMarket',
							'table' => 'dist_markets',
							'type' => 'INNER',
							'conditions' => 'DistMemo.market_id = DistMarket.id'
						),
						/*array(
							'alias' => 'Thana',
							'table' => 'thanas',
							'type' => 'Left',
							'conditions' => 'DistMarket.thana_id = Thana.id'
						),
						array(
							'alias' => 'District',
							'table' => 'districts',
							'type' => 'Left',
							'conditions' => 'Thana.district_id = District.id'
						),*/
						array(
							'alias' => 'Office',
							'table' => 'offices',
							'type' => 'INNER',
							'conditions' => 'Office.id = DistMemo.Office_id'
						),
						array(
							'alias' => 'DistTso',
							'table' => 'dist_tsos',
							'type' => 'left',
							'conditions' => 'DistTso.id = DistMemo.tso_id'
						),
						array(
							'alias' => 'DistDistributor',
							'table' => 'dist_distributors',
							'type' => 'left',
							'conditions' => 'DistDistributor.id = DistMemo.distributor_id'
						),
						array(
							'alias' => 'DistSalesRrepresentative',
							'table' => 'dist_sales_representatives',
							'type' => 'left',
							'conditions' => 'DistSalesRrepresentative.id = DistMemo.sr_id'
						),
						array(
							'alias' => 'DistRoute',
							'table' => 'dist_routes',
							'type' => 'left',
							'conditions' => 'DistRoute.id = DistMemo.dist_route_id'
						),
					),

					'fields' => $fields,

					'group' => $group,

					'order' => array(
						'Office.order asc',
						'Office.office_name',
						'DistTso.name',
						'DistDistributor.name',
						'DistRoute.name',
						'sales_qty desc',
						'DistOutlet.name asc'
					),

					//'order' => $order,

					'recursive' => -1,
					//'limit' => 200
				));
				/*echo $this->DistMemo->getLastQuery();
				pr($q_results);*/
				$report_details_data = array();
				foreach ($q_results as $data) {
					$report_details_data[] = array(
						'aso' => $data['Office']['office_name'],
						'tso' => $data['DistTso']['name'],
						'db' => $data['DistDistributor']['name'],
						'sr' => $data['DistSalesRrepresentative']['name'],
						'route' => $data['DistRoute']['name'],
						'market' => $data['DistMarket']['name'],
						'outlet' => $data['DistOutlet']['name'],
						'outlet_category' => $outlet_categories[$data['DistOutlet']['category_id']],
						'sales_qty' => $data[0]['sales_qty'],
						'ec' => $data[0]['ec'],
						'value' => $data[0]['price'],
					);
				}
				if ($report_details_data) {
					$compare_to = $product_rows[$key_p]['compotator'] . ' ' . $product_rows[$key_p]['qty'];
					if ($product_rows[$key_p]['compotator'] == 'between')
						$compare_to = 'TO ' . $product_rows[$key_p]['qty2'];
					$per_memo = '';
					if ($product_rows[$key_p]['memo_total'] == 'permemo')
						$per_memo .= 'Per Memo';
					else
						$per_memo .= 'Memo Total';

					$results[] = array(
						'heading_td' => '<b>Report for: (' . $product_name_list . ') ' . $compare_to . ' ' . $per_memo . '</b>',
						'details' => $report_details_data
					);
				}
			}
			$this->set(compact('results'));
		}

		$this->set(compact('offices', 'tsos', 'outlet_type', 'region_offices', 'office_id', 'request_data'));
	}
	function get_tso_list()
	{
		$office_id = $this->request->data['office_id'];
		$date_from = date('Y-m-d', strtotime($this->request->data['date_from']));
		$date_to = date('Y-m-d', strtotime($this->request->data['date_to']));
		$this->loadModel('DistTso');
		$this->loadModel('DistTsoMappingHistory');
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$options = "<option value =''>---- All -----</option>";
		//$current_date = date('Y-m-d');
		$qry = "select distinct dist_tso_id from dist_tso_mapping_histories
			  where office_id=$office_id and is_change=1 and effective_date <= '" . $date_to . "'
			  AND (end_date is null or end_date >='" . $date_from . "') ";

		$dist_data = $this->DistTsoMappingHistory->query($qry);
		$dist_ids = array();

		foreach ($dist_data as $k => $v) {
			$dist_ids[] = $v[0]['dist_tso_id'];
		}
		$dist_tsos = $this->DistTso->find('all', array(
			'conditions' => array(
				'DistTso.id' => $dist_ids,
				//'DistTso.is_active'=> 1,
			),
			'recursive' => 0,
		));

		$data_array = array();
		foreach ($dist_tsos as $key => $value) {
			$data_array[] = array(
				'id' => $value['DistTso']['id'],
				'name' => $value['DistTso']['name'],
			);
			$id = $value['DistTso']['id'];
			$name = $value['DistTso']['name'];
			$options = $options . "<option value =" . $id . ">" . $name . "</option>";
		}
		//pr($data_array);die();
		if (!empty($dist_tsos)) {
			//echo json_encode(array_merge($rs,$data_array));
			echo $options;
		} else {
			//echo json_encode($rs);
			echo $options;
		}
		$this->autoRender = false;
	}
	function get_distributor_list()
	{
		$office_id = @$this->request->data['office_id'];
		$tso_id = @$this->request->data['tso_id'];
		$this->loadModel('DistTsoMapping');
		$this->loadModel('DistTsoMappingHistory');
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$distributors = $this->DistTsoMappingHistory->find('all', array(
			'conditions' => array(
				'DistTsoMappingHistory.office_id' => $office_id,
				'DistTsoMappingHistory.dist_tso_id' => $tso_id,
			),
			'joins' =>
			array(
				array(
					'table' => 'dist_distributors',
					'alias' => 'DB',
					'type' => 'LEFT',
					'conditions' => array('DB.id= DistTsoMappingHistory.dist_distributor_id')
				)
			),
			'fields' => array('Distinct(DB.id) as db_id', 'DB.name'),
			'group' => array('DB.id', 'DB.name'),
		));
		if (!empty($distributors)) {
			//$data_array = Set::extract($distributors, '{n}.DB');
			foreach ($distributors as $key => $value) {
				$data_array[] = array(
					'id' => $value[0]['db_id'],
					'name' => $value['DB']['name'],
				);
			}
			echo json_encode(array_merge($rs, $data_array));
		} else {
			echo json_encode($rs);
		}
		exit;
	}
}
