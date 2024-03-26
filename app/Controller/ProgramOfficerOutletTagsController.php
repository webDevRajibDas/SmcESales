<?php
App::uses('AppController', 'Controller');
/**
 * ProductSettings Controller
 *
 * @property OutletGroup $OutletGroup
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProgramOfficerOutletTagsController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $uses = array('Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'ProgramType', 'Program');
	public $components = array('Paginator', 'Session', 'Filter.Filter');
	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index($id = null)
	{
		$this->set('page_title', 'Program officer outlet tag');
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$this->set('office_parent_id', $office_parent_id);
		$program_officer_tags = array();
		if ($office_parent_id == 0) {
			$office_conditions = array(
				'office_type_id' => 2,
				"NOT" => array("id" => array(30, 31, 37))
			);
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
			$program_officer_tags['SalesPerson.office_id'] = 1016;
		}
		$program_officer_tags['User.user_group_id'] = 1016; // 1016=program officer user group id
		$this->set('office_id', $this->UserAuth->getOfficeId());
		$this->loadModel('Office');
		$this->loadModel('SalesPerson');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$ProgramOfficers = $this->SalesPerson->find('list', array(
			'conditions' => $program_officer_tags,
			'joins' => array(
				array(
					'table' => 'users',
					'alias' => 'User',
					'conditions' => 'User.sales_person_id=SalesPerson.id'
				)
			)
		));

		//$this->dd($ProgramOfficers);
		$this->set(compact('offices'));
		$programTypes = array(
			'1'             => 'GSP',
			'2'             => 'BSP',
			'3'             => 'Pink Star',
			// '6'             => 'Notundin',
			'4'             => 'Stockist For Injectable',
			'5'             => 'NGO For Injectable',
		);
		$assign_deassign_array = array(
			'1' => 'Assign',
			'2' => 'Deassign'
		);
		$this->set(compact('programTypes', 'assign_deassign_array', 'ProgramOfficers'));
		if ($this->request->is('post')) {
			$assign_deassign = $this->request->data['ProgramOfficerTag']['assign_deassign'];
			$program_officer_id = $this->request->data['ProgramOfficerTag']['program_officer_id'];
			$update_data = array();
			foreach ($this->request->data['ProgramOfficerTag']['program_id'] as $key => $data) {
				$data_u['Program']['id'] = $data;
				if ($assign_deassign == 1) {
					$data_u['Program']['program_officer_id'] = $program_officer_id;
				} else {
					$data_u['Program']['program_officer_id'] = 0;
				}
				$update_data[] = $data_u;
			}
			if ($update_data) {

				$this->Program->saveAll($update_data);
			}
		}
	}

	public function get_outlet_list()
	{
		$this->loadModel('Territory');
		$office_id = $this->request->data['office_id'];
		$territory_id = $this->request->data['territory_id'];
		$market_id = $this->request->data['market_id'];
		$thana_id = $this->request->data['thana_id'];
		$program_type_id = $this->request->data['program_type_id'];
		$program_officer_id = $this->request->data['program_officer_id'];
		$assign_deassign = $this->request->data['assign_deassign'];

		$conditions = array();


		if ($office_id) {
			$conditions['Territory.office_id'] = $office_id;
		}
		if ($territory_id) {
			$conditions['Territory.id'] = $territory_id;
		}
		if ($market_id) {
			$conditions['Outlet.market_id'] = $market_id;
		}
		if ($thana_id) {
			$conditions['Market.thana_id'] = $thana_id;
		}
		if ($program_type_id) {
			$conditions['Program.program_type_id'] = $program_type_id;
		}
		if ($assign_deassign == 1) {
			$conditions['Program.program_officer_id'] = 0;
		} elseif ($assign_deassign == 2) {
			$conditions['Program.program_officer_id'] = $program_officer_id;
		}
		$conditions['Outlet.is_active'] = 1;
		$conditions['Market.is_active'] = 1;
		$conditions['Program.deassigned_date'] = NULL;

		//$conditions['Outlet.is_ngo'] = 1;

		$this->Territory->unbindModel(
			array('hasMany' => array('Market', 'SaleTarget', 'SaleTargetMonth', 'TerritoryPerson'), 'belongsTo' => array('Office'))
		);

		$program_joins = array(
			'alias' => 'Program',
			'table' => 'programs',
			'type' => 'INNER',
			'conditions' => 'Program.outlet_id = Outlet.id'
		);
		$outlet_list = $this->Territory->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'Market',
					'table' => 'markets',
					'type' => 'INNER',
					'conditions' => 'Market.territory_id = Territory.id'
				),
				array(
					'alias' => 'Outlet',
					'table' => 'outlets',
					'type' => 'INNER',
					'conditions' => 'Market.id = Outlet.market_id'
				),
				array(
					'alias' => 'Office',
					'table' => 'offices',
					'type' => 'INNER',
					'conditions' => 'Office.id = Territory.office_id'
				),
				$program_joins
			),
			'order' => array('Outlet.name' => 'asc'),
			'fields' => array('Office.office_name', 'Territory.name', 'Market.name', 'Outlet.id', 'Outlet.name', 'Program.id'),
			'group' => array('Office.office_name', 'Territory.name', 'Market.name', 'Outlet.id', 'Outlet.name', 'Program.id')
		));
		/* 
		echo $this->Territory->getLastQuery();
		pr($outlet_list);
		exit; */

		$data_array = Set::extract($outlet_list, '{n}.Outlet');
		$html = '';
		$outlet_other_info = array();
		if (!empty($outlet_list)) {
			foreach ($outlet_list as $data) {
				$outlet_other_info[$data['Outlet']['id']] = array(
					'office' => $data['Office']['office_name'],
					'territory' => $data['Territory']['name'],
					'market' => $data['Market']['name'],
					'program_id' => $data['Program']['id']
				);
			}
			$html .= '<option value="all">---- All ----</option>';
			foreach ($data_array as $key => $val) {
				$html .= '<option value="' . $val['id'] . '">' . $val['name'] . '</option>';
			}
		} else {
			$html .= '<option value="">---- All ----</option>';
		}
		$json_array['outlet_html'] = $html;
		$json_array['other_info'] = $outlet_other_info;
		// echo $html;
		echo json_encode($json_array);
		$this->autoRender = false;
	}

	function get_outlet_info($outlet_id)
	{
		$this->loadModel('Outlet');
		$outlet_info = $this->Outlet->find('first', array(
			'conditions' => array('Outlet.id' => $outlet_id),
			'joins' => array(
				array(
					'table' => 'markets',
					'alias' => 'Market',
					'conditions' => 'Market.id=Outlet.market_id',
				),
				array(
					'table' => 'territories',
					'alias' => 'Territory',
					'conditions' => 'Territory.id=Market.territory_id',
				),
				array(
					'table' => 'offices',
					'alias' => 'Office',
					'conditions' => 'Office.id=Territory.office_id',
				),
			),
			'fields' => array(
				'Outlet.*',
				'Market.name',
				'Territory.name',
				'Office.office_name'
			),
			'recursive' => -1
		));
		return $outlet_info;
	}
}
