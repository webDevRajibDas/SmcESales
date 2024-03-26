<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class BonusCampaignReportsController extends AppController
{



	/**
	 * Components
	 *
	 * @var array
	 */

	public $uses = array('Memo', 'Office', 'Product');
	public $components = array('Paginator', 'Session', 'Filter.Filter');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index($id = null)
	{

		ini_set('memory_limit', '2048M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes


		$this->set('page_title', "Bonus Campaign Report");
		$request_data = array();
		$report_type = array();
		$so_list = array();
		$this->loadModel('DiscountBonusPolicyOptionBonusProduct');
		$this->loadModel('DiscountBonusPolicyProduct');
		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id' => 3),
			'order' => array('office_name' => 'asc')
		));
		$offices = $this->Office->find('list', array(
			'conditions' => array(
				'office_type_id' 	=> 2,
				"NOT" => array("id" => array(30, 31, 37))
			),
			'order' => array('office_name' => 'asc')
		));
		//types
		$rows_list = array(
			'national' => 'By National',
			'region' => 'By Region',
			'area' => 'By Area',
			'territory' => 'By Territory'
		);
		$this->set(compact('rows_list'));

		//report type
		$unit_types = array(
			'1' => 'Sales Unit',
			'2' => 'Base Unit',
		);
		$this->set(compact('unit_types'));


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
		$conditions = array(
			'NOT' => array('Product.product_category_id' => 32),
			'is_active' => 1,
			'Product.product_type_id' => 1
		);

		$productList = $this->Product->find('list', array(
			'fields' => array('id', 'name'),
			'conditions' => $conditions,
			'order' => array('Product.product_category_id', 'Product.order'),
			'recursive' => -1
		));
		if ($this->request->is('post') || $this->request->is('put')) {

			$request_data = $this->request->data;
			$date_from_req = date('Y-m-d', strtotime($request_data['BonusCampaignReports']['date_from']));
			$date_to_req = date('Y-m-d', strtotime($request_data['BonusCampaignReports']['date_to']));
			$region_office_id = @$request_data['BonusCampaignReports']['region_office_id'];
			$office_id = @$request_data['BonusCampaignReports']['office_id'];
			$request_office_id = @$request_data['BonusCampaignReports']['office_id'];
			$mother_product_id = $request_data['BonusCampaignReports']['mother_product_id'];
			$unit_types_req = $request_data['BonusCampaignReports']['unit_types'];
			$rows = $request_data['BonusCampaignReports']['rows'];
			$policy_id = @$request_data['policy_id'];

			if (!$policy_id) {
				$this->Session->setFlash(__('Please select any policy.'), 'flash/error');
				$this->redirect(array('action' => 'index'));
				exit;
			}
			$date_from = date('Y-m-d', strtotime($request_data['policy_start_date']));
			$date_to = date('Y-m-d', strtotime($request_data['policy_end_date']));
			if (!$office_id) {
				$region_conditions = array();
				if ($region_office_id) {
					$region_conditions['parent_office_id'] = $region_office_id;
				}
				$region_conditions['office_type_id'] = 2;
				$offices_req = $this->Office->find('list', array(
					'conditions' => $region_conditions
				));
				$office_id = array_keys($offices_req);
			}
			$policy_bonus_products = array();
			$policy_products = array();
			if ($policy_id) {
				$policy_bonus_products = $this->DiscountBonusPolicyOptionBonusProduct->find('list', array(
					'conditions' => array('DiscountBonusPolicyOptionBonusProduct.discount_bonus_policy_id' => $policy_id),
					'joins' => array(
						array(
							'table' => 'products',
							'alias' => 'Product',
							'conditions' => 'Product.id=DiscountBonusPolicyOptionBonusProduct.bonus_product_id'
						)
					),
					'fields' => array('Product.id', 'Product.name'),
				));
				$policy_products = $this->DiscountBonusPolicyProduct->find('list', array(
					'conditions' => array('DiscountBonusPolicyProduct.discount_bonus_policy_id' => $policy_id),
					'joins' => array(
						array(
							'table' => 'products',
							'alias' => 'Product',
							'conditions' => 'Product.id=DiscountBonusPolicyProduct.product_id'
						)
					),
					'fields' => array('Product.id', 'Product.name'),
				));

				$policy_all_products = array_merge(array_keys($policy_bonus_products), array_keys($policy_products));
				$policy_all_products = array_unique($policy_all_products);
				$policy_all_product_measurement_unit = $this->Product->find('list', array(
					'conditions' => array('Product.id' => $policy_all_products),
					'fields' => array('Product.id', 'Product.sales_measurement_unit_id'),
					'recursive' => -1
				));
				/* ----- policy details----- */
			}

			$bonus_data = array();
			$joins = array();
			$fields = array();
			$group = array();
			$order = array();

			if ($rows == 'territory') {
				$joins[0] = array(
					'table' => 'offices',
					'alias' => 'Office',
					'conditions' => 'Office.id=Memo.office_id'
				);
				$joins[1] = array(
					'table' => 'offices',
					'alias' => 'RegionOffice',
					'conditions' => 'RegionOffice.id=Office.parent_office_id'
				);
				$joins[2] = array(
					'table' => 'territories',
					'alias' => 'Territory',
					'conditions' => 'Territory.id=Memo.territory_id'
				);
				$fields = array(
					'Office.id', 'Office.office_name', 'RegionOffice.id', 'RegionOffice.office_name', 'Territory.id', 'Territory.name'
				);

				$group = array(
					'Office.id', 'Office.office_name', 'Office.order', 'RegionOffice.id', 'RegionOffice.office_name', 'RegionOffice.order', 'Territory.id', 'Territory.name'
				);
				$order = array('RegionOffice.order', 'Office.order', 'Territory.name');
			} else if ($rows == 'area') {
				$joins[0] = array(
					'table' => 'offices',
					'alias' => 'Office',
					'conditions' => 'Office.id=Memo.office_id'
				);
				$joins[1] = array(
					'table' => 'offices',
					'alias' => 'RegionOffice',
					'conditions' => 'RegionOffice.id=Office.parent_office_id'
				);

				$fields = array(
					'Office.id', 'Office.office_name', 'RegionOffice.id', 'RegionOffice.office_name'
				);
				$group = array(
					'Office.id', 'Office.office_name', 'Office.order', 'RegionOffice.id', 'RegionOffice.office_name', 'RegionOffice.order'
				);
				$order = array('RegionOffice.order', 'Office.order');
			} else if ($rows == 'region') {
				$joins[0] = array(
					'table' => 'offices',
					'alias' => 'Office',
					'conditions' => 'Office.id=Memo.office_id'
				);
				$joins[1] = array(
					'table' => 'offices',
					'alias' => 'RegionOffice',
					'conditions' => 'RegionOffice.id=Office.parent_office_id'
				);

				$fields = array(
					'RegionOffice.id', 'RegionOffice.office_name'
				);
				$group = array(
					'Office.order', 'RegionOffice.id', 'RegionOffice.office_name', 'RegionOffice.order'
				);
				$order = array('RegionOffice.order');
			} else {
				$joins = array();

				$fields = array();
				$group = array();
			}

			$ec_oc_fields = $fields;
			foreach ($policy_bonus_products as $p_id => $p_name) {
				$fields[] = 'SUM(ROUND((CASE WHEN MemoDetails.product_id=' . $p_id . ' AND MemoDetails.policy_id=' . $policy_id . ' THEN MemoDetails.sales_qty END) * (CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END),0)) as b_sales_qty_' . $p_id;
			}
			foreach ($policy_products as $p_id => $p_name) {
				$fields[] = 'SUM(ROUND((CASE WHEN MemoDetails.product_id=' . $p_id . ' AND MemoDetails.price >0 THEN MemoDetails.sales_qty END)* (CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END),0)) as sales_qty_' . $p_id . ' ,SUM(CASE WHEN MemoDetails.product_id=' . $p_id . ' AND MemoDetails.price >0 THEN MemoDetails.sales_qty END*Memodetails.price) as value_' . $p_id;
			}
			$conditions_memo = array();
			if ($office_id) {
				$conditions_memo['Memo.office_id'] = $office_id;
			}
			if ($date_from && $date_to) {
				$conditions_memo['Memo.memo_date BETWEEN ? AND ?'] = array($date_from, $date_to);
			}
			/* if ($policy_id) {
				$conditions_memo['MemoDetails.policy_id'] = $policy_id;
			} */
			$ec_oc_join = $joins;
			$joins[] = array(
				'table' => '(SELECT m.id FROM memos m INNER JOIN memo_details md ON m.id=md.memo_id where m.memo_date between \'' . $date_from . '\' AND \'' . $date_to . '\' AND md.policy_id=' . $policy_id . ')',
				'alias' => 'PolicyMemo',
				'conditions' => 'PolicyMemo.id=Memo.id'
			);
			$joins[] = array(
				'table' => 'memo_details',
				'alias' => 'MemoDetails',
				'conditions' => 'MemoDetails.memo_id=Memo.id'
			);
			$joins[] = array(
				'alias' => 'Product',
				'table' => 'products',
				'type' => 'INNER',
				'conditions' => 'MemoDetails.product_id = Product.id'
			);

			$joins[] = array(
				'alias' => 'ProductMeasurement',
				'table' => 'product_measurements',
				'type' => 'LEFT',
				'conditions' => 'Product.id = ProductMeasurement.product_id AND 
						CASE WHEN (MemoDetails.measurement_unit_id is null or MemoDetails.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
						ELSE 
							MemoDetails.measurement_unit_id
						END =ProductMeasurement.measurement_unit_id'
			);

			$bonus_data = $this->Memo->find('all', array(
				'conditions' => $conditions_memo,
				'joins' => $joins,
				'fields' => $fields,
				'group' => $group,
				'order' => $order,
				'recursive' => -1
			));

			$ec_oc_join[] = array(
				'table' => 'memo_details',
				'alias' => 'MemoDetails',
				'conditions' => 'MemoDetails.memo_id=Memo.id'
			);
			$ec_oc_fields[] = 'COUNT(DISTINCT Memo.id) as ec,COUNT(DISTINCT Memo.outlet_id) as oc';
			$ec_oc = $this->Memo->find('all', array(
				'conditions' => array(
					'MemoDetails.policy_id' => $policy_id,
					'Memo.memo_date BETWEEN ? AND ?' => array($date_from, $date_to)
				),
				'joins' => $ec_oc_join,
				'fields' => $ec_oc_fields,
				'group' => $group,
				'recursive' => -1
			));
			$ec_od_data = array();
			foreach ($ec_oc as $data) {
				if ($rows == 'territory') {
					$ec_od_data[$data['Territory']['id']] = $data[0];
				} else if ($rows == 'area') {
					$ec_od_data[$data['Office']['id']] = $data[0];
				} else if ($rows == 'region') {
					$ec_od_data[$data['RegionOffice']['id']] = $data[0];
				} else if ($rows == 'national') {
					$ec_od_data[0] = $data[0];
				}
			}
		}
		$this->set(compact('offices', 'productList', 'totalDay', 'bonus_data', 'region_offices', 'request_office_id', 'request_data', 'region_office_id', 'rows', 'policy_bonus_products', 'policy_products', 'date_from', 'date_to', 'policy_id', 'ec_od_data', 'policy_all_product_measurement_unit', 'unit_types_req'));
	}


	public function get_policy_list()
	{
		$this->loadModel('DiscountBonusPolicy');
		$view = new View();
		$view->loadHelper('Form');
		$date_from = date('Y-m-d', strtotime($this->request->data['date_from']));
		$date_to = date('Y-m-d', strtotime($this->request->data['date_to']));
		$product_id = $this->request->data['product_id'];
		$conditioins = array();
		$conditioins['OR'] = array('DiscountBonusPolicy.is_so' => 1, 'DiscountBonusPolicy.is_db' => 1);
		if ($date_from && $date_to) {
			$conditioins['DiscountBonusPolicy.start_date <='] = $date_to;
			$conditioins['DiscountBonusPolicy.end_date >='] = $date_from;
		}
		if ($product_id) {
			$conditioins['DiscountBonusPolicyProduct.product_id'] = $product_id;
		}
		$policy_list = $this->DiscountBonusPolicy->find('all', array(
			'conditions' => $conditioins,
			'joins' => array(
				array(
					'table' => 'discount_bonus_policy_products',
					'alias' => 'DiscountBonusPolicyProduct',
					'conditions' => 'DiscountBonusPolicyProduct.discount_bonus_policy_id=DiscountBonusPolicy.id'
				)
			),
			/* 'fields' => array('DiscountBonusPolicy.id', 'DiscountBonusPolicy.name'), */
			'recursive' => -1
		));
		$output =
			'<label style="float:left; width:15%;">Policy List : </label>
			<div id="market_list" class="input select" style="float:left; width:80%; padding-left:0px;">
				<div style="margin:auto; width:90%; float:left;">
					
				</div>
				<div class="product selection" style="float:left; width:100%; padding:5px 5px 5px 30px; border:#ccc solid 1px; height:142px; overflow:auto">
				<table class="table table-bordered">
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
						<th>Remarks</th>
						<th>Start date</th>
						<th>End date</th>
					</tr>
				<tbody>
						';
		foreach ($policy_list as $data) {
			$output .= '<tr>';
			$output .= '<td>' . $view->Form->input('policy_id', array('id' => '', 'label' => false, 'class' => 'policy_id', 'div' => false, 'hiddenField' => false, 'type' => 'checkbox', 'value' => $data['DiscountBonusPolicy']['id'])) . '</td>';
			$output .= '<td>' . $data['DiscountBonusPolicy']['name'] . '</td>';
			$output .= '<td>' . $data['DiscountBonusPolicy']['remarks'] . '</td>';
			$output .= '<td>' . $data['DiscountBonusPolicy']['start_date'] . '</td>';
			$output .= '<td>' . $data['DiscountBonusPolicy']['end_date'] . '</td>';
			$output .= '<input type="hidden" name="policy_start_date" value="' . $data['DiscountBonusPolicy']['start_date'] . '"><input type="hidden" name="policy_end_date" value="' . $data['DiscountBonusPolicy']['end_date'] . '"></tr>';
		}
		$output .=
			'
			</tbody>
			</table>		
			</div>
			</div>';
		echo $output;
		exit;
		$this->$this->autoRender = false;
	}
}
