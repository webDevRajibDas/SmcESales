<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class NbrReportsController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('Memo', 'Office', 'Outlet');
	public $components = array('Paginator', 'Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index($id = null)
	{
		//Configure::write('debug',2);

		$this->Session->delete('detail_results');
		$this->Session->delete('outlet_lists');

		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes


		$this->set('page_title', "Outlet Wise NBR Report");

		$request_data = array();

		$sql = "SELECT * FROM product_sources";
		$sources_datas = $this->Office->query($sql);
		$product_sources = array();
		foreach ($sources_datas as $sources_data) {
			$product_sources[$sources_data[0]['name']] = $sources_data[0]['name'];
		}
		/*$product_types = array(
            'smcel'         => 'SMCEL',
            'smc'           => 'SMC',
        );*/
		$this->set(compact('product_sources'));

		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id' => 3),
			'order' => array('office_name' => 'asc')
		));

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

		$dis_con = array();

		$resutl = array();


		if ($this->request->is('post') || $this->request->is('put')) {

			$request_data = $this->request->data;

			$date_from = date('Y-m-d', strtotime($request_data['NbrReport']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['NbrReport']['date_to']));
			$this->set(compact('date_from', 'date_to'));

			$region_office_id = isset($this->request->data['NbrReport']['region_office_id']) != '' ? $this->request->data['NbrReport']['region_office_id'] : $region_office_id;
			$product_source = isset($this->request->data['NbrReport']['product_source']) != '' ? $this->request->data['NbrReport']['product_source'] : 0;
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

			$office_id = isset($this->request->data['NbrReport']['office_id']) != '' ? $this->request->data['NbrReport']['office_id'] : $office_id;
			$this->set(compact('office_id'));

			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >=' => 0,
				'Memo.status !=' => 0,
			);


			if ($office_ids) $conditions['Memo.office_id'] = $office_ids;

			if ($office_id) $conditions['Memo.office_id'] = $office_id;
			if ($product_source) $conditions['Product.source'] = $product_source;

			$amount = $this->request->data['NbrReport']['amount'];

			$group = array('Outlet.name', 'Outlet.address', 'Memo.id', 'Memo.memo_no', 'Memo.memo_date');

			$operator_memo_count_conditions = '';
			if ($amount > 0) {

				if ($this->request->data['NbrReport']['operator'] == 1) {
					$operator_memo_count_conditions .= 'SUM(MemoDetail.sales_qty*price) <=' . $amount;
				} elseif ($this->request->data['NbrReport']['operator'] == 2) {
					$operator_memo_count_conditions .= 'SUM(MemoDetail.sales_qty*price) >=' . $amount;
				}
				if (strpos($group[4], 'HAVING')) {
					$group[14] .= ' AND ' . $operator_memo_count_conditions;
				} else {
					$group[4] .= ' HAVING ' . $operator_memo_count_conditions;
				}
			}

			$q_results = $this->Memo->find('all', array(
				'conditions' => $conditions,
				'joins' => array(
					array(
						'alias' => 'Outlet',
						'table' => 'outlets',
						'type' => 'INNER',
						'conditions' => 'Memo.outlet_id = Outlet.id'
					),
					array(
						'alias' => 'MemoDetail',
						'table' => 'memo_details',
						'type' => 'INNER',
						'conditions' => 'Memo.id = MemoDetail.memo_id'
					),
					array(
						'alias' => 'Product',
						'table' => 'products',
						'type' => 'INNER',
						'conditions' => 'Product.id = MemoDetail.product_id'
					),
				),

				'fields' => array('Outlet.name', 'Outlet.address', 'Memo.id', 'Memo.memo_no', 'Memo.memo_date', 'SUM(MemoDetail.sales_qty*price) as gross_value'),

				'group' => $group,

				'order' => array('Outlet.name asc'),

				'recursive' => -1
			));



			//echo $this->Memo->getLastQuery();exit;

			$output = '';

			if (!empty($q_results)) {
				$i = 1;
				foreach ($q_results as $v) {
					$view_url = SITE_URL . 'admin/memos/view/' . $v['Memo']['id'];
					$amount =  sprintf("%01.2f", $v['0']['gross_value']);

					$output .= '
						<tr>
							<td>' . $i . '</td>
							<td>' . $v['Outlet']['name'] . '</td>
							<td>' . $v['Outlet']['address'] . '</td>
							<td>' . $v['Memo']['memo_no'] . '</td>
							<td>' . $v['Memo']['memo_date'] . '</td>
							<td>' . $amount . '</td>
							<td><a class="btn btn-primary btn-xs" href="' . $view_url . '" target="_blank">
							<i class="glyphicon glyphicon-eye-open"></i></a></td>
						</tr>';

					$i++;
				}
			} else {
				$output .= '
						<tr>
							<td colspan="6" align="center"> Data Not Found !.</td>
						</tr>';
			}

			$this->set(compact('output'));
		}



		$this->set(compact('offices',  'region_offices', 'resutl',  'request_data'));
	}


	//get outlet list
	public function get_outlet_list()
	{
		$view = new View($this);

		$form = $view->loadHelper('Form');

		$market_id = $this->request->data['market_id'];

		$conditions = array();

		if ($market_id) {
			$ids = explode(',', $market_id);
			$conditions['DistOutlet.dist_market_id'] = $ids;
			$conditions['DistOutlet.is_active'] = 1;
			//pr($conditions);
			//exit;

			$results = $this->DistOutlet->find('list', array(
				'conditions' => $conditions,
				'order' =>  array('DistOutlet.name' => 'asc')
			));


			if ($results) {
				$output = '<div class="input select">
  							<input type="hidden" name="data[DistOutletCharacteristicReports][outlet_id]" value="" id="outlet_id"/>';
				foreach ($results as $key => $val) {
					$output .= '<div class="checkbox">
									<input type="checkbox" name="data[DistOutletCharacteristicReports][outlet_id][]" value="' . $key . '" id="outlet_id' . $key . '" />
									<label for="outlet_id' . $key . '">' . $val . '</label>
								  </div>';
				}
				$output .= '</div>';
				echo $output;
			} else {
				echo '';
			}
		} else {
			echo '';
		}


		$this->autoRender = false;
	}
}
