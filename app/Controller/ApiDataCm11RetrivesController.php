<?php
App::uses('AppController', 'Controller');
//App::import('Vendor', 'dompdf/dompdf_config.inc.php');

/**
 * Controller
 *
 * @property ApiDataRetrives $ApiDataRetrives
 * @property PaginatorComponent $Paginator
 */
class ApiDataCm11RetrivesController extends AppController
{

	public $components = array('RequestHandler', 'Usermgmt.UserAuth');


	public function mac_check($mac_id, $so_id)
	{
		$this->LoadModel('User');
		$this->LoadModel('CommonMac');
		$users = $this->User->find('first', array(
			'conditions' => array('User.mac_id' => $mac_id, 'User.sales_person_id' => $so_id),
			'recursive' => -1
		));
		if (empty($users)) {
			$common_mac_check = $this->CommonMac->find('first', array('conditions' => array('CommonMac.mac_id' => $mac_id)));

			if ($common_mac_check)
				return true;
			else
				return false;
		} else {
			return true;
		}
	}
	public function user_registration_check()
	{
		$json_data = $this->request->input('json_decode', true);

		$phone_number = $json_data['phone_number'];
		/* ---------- Checking phone number in outlet table --------------------*/
		$this->loadModel('Outlet');
		$outlets = $this->Outlet->find('all', array(
			'conditions' => array('Outlet.mobile like' => "%" . $phone_number . "%"),
		));
		if (!empty($outlets)) {
			/* -------- Checking user registered by this phone number ------------- */
			$this->loadModel('CmLoginAccount');
			$outlet_accounts = $this->CmLoginAccount->find('all', array(
				'conditions' => array('CmLoginAccount.phone_number like' => "%" . $phone_number . "%"),
			));
			if (empty($outlet_accounts)) {
				/* --------- OTP generate ----------------- */
				$otp = rand(10000, 99999);
				$outlet_info = array();
				$outlet_info['owners_name'] = $outlets[0]['Outlet']['ownar_name'] ? $outlets[0]['Outlet']['ownar_name'] : $outlets[0]['Outlet']['in_charge'];
				foreach ($outlets as $data) {

					$outlet_info['outlets'][] = array(
						"id" => $data['Outlet']['id'],
						"name" => $data['Outlet']['name'],
					);
				}
				$res = array(
					'status' => 1,
					'otp' => $otp,
					'outlet_info' => $outlet_info
				);
			} else {
				$res = array(
					'status' => 0,
					'msg' => 'Already registered by this phone number',
				);
			}
		} else {
			$res = array(
				'status' => 0,
				'msg' => 'No outlet registered by this phone number.',
			);
		}
		$this->set(array(
			'results' => $res,
			'_serialize' => array('results')
		));
	}
	public function register_user()
	{
		$json_data = $this->request->input('json_decode', true);
		$this->loadModel('CmLoginAccount');
		$this->loadModel('CmAccountSelectedOutlet');
		$login_insert_data['full_name'] = $json_data['name'];
		$login_insert_data['phone_number'] = $json_data['phone_number'];
		$login_insert_data['password'] = MD5($json_data['password']);
		$datasource = $this->CmLoginAccount->getDataSource();
		try {
			$datasource->begin();
			if (!$this->CmLoginAccount->save($login_insert_data)) {
				throw new Exception();
			} else {
				$login_id = $this->CmLoginAccount->getInsertID();
				$select_outlet_insert_data = array();
				foreach ($json_data['selected_outlet'] as $sel_outlet) {
					$select_outlet_insert_data[] = array(
						'cm_login_account_id' => $login_id,
						'outlet_id' => $sel_outlet,
						'enroll_date' => $this->current_date(),
						'status' => 0,
					);
				}
				if (!empty($select_outlet_insert_data)) {
					if (!$this->CmAccountSelectedOutlet->saveAll($select_outlet_insert_data)) {
						throw new Exception();
					}
				}
			}
			$res = array(
				'status' => 1,
				'msg' => 'Successfully Registered'
			);
			$datasource->commit();
		} catch (Exception $e) {
			$datasource->rollback();
			$res = array(
				'status' => 0,
				'msg' => 'Something wrong!! Try again.'
			);
		}
		$this->set(array(
			'results' => $res,
			'_serialize' => array('results')
		));
	}
	public function login_user()
	{
		$json_data = $this->request->input('json_decode', true);

		$phone_number = $json_data['phone_number'];
		$password = MD5($json_data['password']);

		/* ------------- Login check ---------------------- */
		$this->loadModel('CmLoginAccount');
		$logins = $this->CmLoginAccount->find('first', array(
			'conditions' => array(
				'CmLoginAccount.phone_number like' => "%" . $phone_number . "%",
				'CmLoginAccount.password' => $password,
			),
			'recursive' => -1
		));
		if (empty($logins)) {
			$res = array(
				'status' => 0,
				'msg' => 'Login credentials not match'
			);
		} else {
			$this->loadModel('CmAccountSelectedOutlet');
			$login_id = $logins['CmLoginAccount']['id'];
			$selected_outlet = $this->CmAccountSelectedOutlet->find('all', array(
				'conditions' => array(
					'CmAccountSelectedOutlet.cm_login_account_id' => $login_id,
					'CmAccountSelectedOutlet.status' => 1
				),
				 'joins' => array(
					array(
						'table' => 'outlets',
						'alias' => 'Outlet',
						'type' => 'inner',
						'conditions' => 'Outlet.id=CmAccountSelectedOutlet.outlet_id'
					)
				), 
				'fields' => array('Outlet.id', 'Outlet.name', 'Outlet.category_id')
			));
			if (empty($selected_outlet)) {
				$res = array(
					'status' => 0,
					'msg' => 'Login credentials not approved yet'
				);
			} else {

				$res = array(
					'status' => 1,
					'msg' => 'successfully login',
					'user_id' => $login_id,
					'registered_outlet' => $selected_outlet
				);
			}
		}
		$this->set(array(
			'results' => $res,
			'_serialize' => array('results')
		));
	}

	public function get_product_price_v2()
	{
		$this->loadModel('ProductPricesV2');
		$json_data = $this->request->input('json_decode', true);

		/*---------------------------- Mac check --------------------------------*/
		/* $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
		if (!$mac_check) {

			$mac['status'] = 0;
			$mac['message'] = 'Mac Id Not Match';
			$res = $mac;
			$this->set(array(
				'mac' => $res,
				'_serialize' => array('mac')
			));
			return 0;
		} */

		$last_update_date = $json_data['last_update_date'];

		$res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
		if ($res_status == 1) {
			$conditions = array();
		} else {
			$conditions = array('ProductPricesV2.updated_at >' => $last_update_date);
		}
		$conditions['OR'] = array('ProductPricesV2.project_id is null', 'ProductPricesV2.project_id' => 0);
		$conditions['ProductPriceSectionV2.is_so'] = 1;
		$product_price = $this->ProductPricesV2->find('all', array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'product_price_section_v2',
					'alias' => 'ProductPriceSectionV2',
					'conditions' => 'ProductPriceSectionV2.product_price_id=ProductPricesV2.id'
				),
			),
			'order' => array('ProductPricesV2.updated_at' => 'asc'),
			'recursive' => -1
		));
		foreach ($product_price as $key => $val) {
			$product_price[$key]['ProductPricesV2']['action'] = 1;
		}

		$this->set(array(
			'product_price' => $product_price,
			'_serialize' => array('product_price')
		));
	}
	public function get_product_combination_v2()
	{
		$this->loadModel('ProductCombinationsV2');
		$this->loadModel('ProductPriceOtherForSlabsV2');
		$this->loadModel('SpecialGroup');
		$json_data = $this->request->input('json_decode', true);
		/*---------------------------- Mac check --------------------------------*/
		/* $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
		if (!$mac_check) {

			$mac['status'] = 0;
			$mac['message'] = 'Mac Id Not Match';
			$res = $mac;
			$this->set(array(
				'mac' => $res,
				'_serialize' => array('mac')
			));
			return 0;
		} */

		// $territory_id = $json_data['territory_id'];
		$outlet_id = $json_data['outlet_id'];

		/*---------- get territory id from outlet table for gettinng product slab for special group-------- */
		$this->loadModel("Outlet");
		$outlet = $this->Outlet->find('first', array(
			'conditions' => array(
				'Outlet.id' => $outlet_id
			),

		));
		$territory_id = $outlet['Market']['territory_id'];

		$last_update_date = $json_data['last_update_date'];

		$this->loadModel('Territory');
		$territory_info = $this->Territory->find(
			'first',
			array(
				'conditions' => array('Territory.id' => $territory_id),
				'fields' => array('Office.id'),
				'recursive' => 0
			)
		);
		$office_id = $territory_info['Office']['id'];

		$res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);

		if ($res_status == 1) {
			$conditions = array();
		} else {
			$conditions = array('ProductCombinationsV2.updated_at >' => $last_update_date);
		}
		$conditions[] = array('ProductPriceSectionV2.is_so' => 1);

		$product_combination = $this->ProductCombinationsV2->find('all', array(
			'conditions' => $conditions,
			'order' => array('ProductCombinationsV2.updated_at' => 'asc'),
			'joins' => array(
				array(
					'table' => 'product_price_section_v2',
					'alias' => 'ProductPriceSectionV2',
					'conditions' => 'ProductPriceSectionV2.id=ProductCombinationsV2.section_id'
				),

			),
			'recursive' => -1
			// 'fields'=>array('ProductCombinationsV2.*','ProductPriceOtherForSlabsV2.*')
		));
		foreach ($product_combination as $key => $p_data) {
			$product_combination_id = $p_data['ProductCombinationsV2']['id'];
			$special_group_data = $this->ProductPriceOtherForSlabsV2->find('all', array(
				'conditions' => array(
					'ProductPriceOtherForSlabsV2.price_for' => 1,
					'ProductPriceOtherForSlabsV2.type' => 1,
					'ProductPriceOtherForSlabsV2.product_combination_id' => $product_combination_id
				),
				'fields' => array(
					'ProductPriceOtherForSlabsV2.type as type',
					'ProductPriceOtherForSlabsV2.reffrence_id as reffrence_id',
					'ProductPriceOtherForSlabsV2.price as price',
				),
				'group' => array(
					'ProductPriceOtherForSlabsV2.type',
					'ProductPriceOtherForSlabsV2.reffrence_id',
					'ProductPriceOtherForSlabsV2.price',
				),
				'recursive' => -1
			));
			$assign_group = array();
			foreach ($special_group_data as $data) {
				$group_id = $data[0]['reffrence_id'];
				$special_group_details = $this->SpecialGroup->find('all', array(
					'conditions' => array(
						'SpecialGroup.id' => $group_id,
						'SPO.reffrence_id' => $office_id
					),
					'joins' => array(
						array(
							'table' => 'special_group_other_settings',
							'alias' => 'SPO',
							'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=1'
						),
						array(
							'table' => '(
                                        select t.office_id,spg.reffrence_id as territory_id from special_groups sps
                                        inner join special_group_other_settings spg on sps.id=spg.special_group_id and create_for=2
                                        inner join territories t on t.id=spg.reffrence_id
                                        where 
                                            sps.id=' . $group_id . '
                                    )',
							'alias' => 'SPT',
							'type' => 'left',
							'conditions' => 'SPT.office_id=SPO.reffrence_id'
						),
					),
					'group' => array('SPO.reffrence_id'),
					'fields' => array('SPO.reffrence_id', 'COUNT(SPT.territory_id) as total_territory'),
					'recursive' => -1
				));
				if ($special_group_details) {
					if ($special_group_details[0][0]['total_territory'] > 0) {
						$special_group_territory_details = $this->SpecialGroup->find('all', array(
							'conditions' => array(
								'SpecialGroup.id' => $group_id,
								'SPO.reffrence_id' => $territory_id
							),
							'joins' => array(
								array(
									'table' => 'special_group_other_settings',
									'alias' => 'SPO',
									'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=2'
								)

							),
							'recursive' => -1
						));
						if ($special_group_territory_details) {
							$assign_group[] = $data[0];
						} else {
							$outlet_group_data = $this->get_special_group_outlet_group_by_territory_id($group_id, $territory_id);
							if ($outlet_group_data) {
								$assign_group[] = $data[0];
							}
						}
					} else {

						$assign_group[] = $data[0];
					}
				} else {
					$outlet_group_data = $this->get_special_group_outlet_group_by_territory_id($group_id, $territory_id);
					if ($outlet_group_data) {
						$assign_group[] = $data[0];
					}
				}
			}
			$outlet_category_data = $this->ProductPriceOtherForSlabsV2->find('all', array(
				'conditions' => array(
					'ProductPriceOtherForSlabsV2.price_for' => 1,
					'ProductPriceOtherForSlabsV2.type' => 2,
					'ProductPriceOtherForSlabsV2.product_combination_id' => $product_combination_id
				),
				'fields' => array(
					'ProductPriceOtherForSlabsV2.type as type',
					'ProductPriceOtherForSlabsV2.reffrence_id as reffrence_id',
					'ProductPriceOtherForSlabsV2.price as price',
				),
				'recursive' => -1
			));
			unset($p_data['ProductCombinationsV2']['section_id']);
			unset($p_data['ProductCombinationsV2']['sr_price']);
			$product_combination[$key] = $p_data['ProductCombinationsV2'];
			$outlet_category_data = array_map(function ($data) {
				return $data[0];
			}, $outlet_category_data);
			$product_combination[$key]['other_pricing'] = array_merge($assign_group, $outlet_category_data);
		}

		$this->set(array(
			'product_combination' => $product_combination,
			'_serialize' => array('product_combination')
		));
	}
	public function get_special_group()
	{
		$this->loadModel('SpecialGroup');
		$this->loadModel('SpecialGroupOtherSetting');
		$json_data = $this->request->input('json_decode', true);
		/*---------------------------- Mac check --------------------------------*/
		/* $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
		if (!$mac_check) {

			$mac['status'] = 0;
			$mac['message'] = 'Mac Id Not Match';
			$res = $mac;
			$this->set(array(
				'mac' => $res,
				'_serialize' => array('mac')
			));
			return 0;
		} */

		$last_update_date = $json_data['last_update_date'];

		// $territory_id = $json_data['territory_id'];

		/*---------- get territory id from outlet table for gettinng product slab for special group-------- */
		$outlet_id = $json_data['outlet_id'];
		$this->loadModel("Outlet");
		$outlet = $this->Outlet->find('first', array(
			'conditions' => array(
				'Outlet.id' => $outlet_id
			),

		));
		$territory_id = $outlet['Market']['territory_id'];

		$this->loadModel('Territory');
		$territory_info = $this->Territory->find(
			'first',
			array(
				'conditions' => array('Territory.id' => $territory_id),
				'fields' => array('Office.id'),
				'recursive' => 0
			)
		);
		$office_id = $territory_info['Office']['id'];

		$res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);

		if ($res_status == 1) {
			$conditions = array();
		} else {
			$conditions = array('SpecialGroup.updated_at >' => $last_update_date);
		}
		$conditions[] = array('SpecialGroup.is_dist' => 0);

		$special_group_data = $this->SpecialGroup->find('all', array(
			'conditions' => $conditions,
			'recursive' => -1
		));
		$group_data = array();
		foreach ($special_group_data as $data) {
			$data_array = array();
			$outlet_group_id = array();
			$group_id = $data['SpecialGroup']['id'];
			$special_group_territory_details = array();
			$special_group_details = array();
			$special_group_details = $this->SpecialGroup->find('all', array(
				'conditions' => array(
					'SpecialGroup.id' => $group_id,
					'SPO.reffrence_id' => $office_id
				),
				'joins' => array(
					array(
						'table' => 'special_group_other_settings',
						'alias' => 'SPO',
						'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=1'
					),
					array(
						'table' => '(
                                    select t.office_id,spg.reffrence_id as territory_id from special_groups sps
                                    inner join special_group_other_settings spg on sps.id=spg.special_group_id and create_for=2
                                    inner join territories t on t.id=spg.reffrence_id
                                    where 
                                        sps.id=' . $group_id . '
                                )',
						'alias' => 'SPT',
						'type' => 'left',
						'conditions' => 'SPT.office_id=SPO.reffrence_id'
					),
				),
				'group' => array('SPO.reffrence_id'),
				'fields' => array('SPO.reffrence_id', 'COUNT(SPT.territory_id) as total_territory'),
				'recursive' => -1
			));
			if ($special_group_details) {
				if ($special_group_details[0][0]['total_territory'] > 0) {
					$special_group_territory_details = $this->SpecialGroup->find('all', array(
						'conditions' => array(
							'SpecialGroup.id' => $group_id,
							'SPO.reffrence_id' => $territory_id
						),
						'joins' => array(
							array(
								'table' => 'special_group_other_settings',
								'alias' => 'SPO',
								'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=2'
							)

						),
						'recursive' => -1
					));
					if ($special_group_territory_details) {
						$data_array = $data;
					}
				} else {
					$data_array = $data;
				}
			}
			$outlet_group_id = $this->get_special_group_outlet_group_by_territory_id($group_id, $territory_id);
			if ($outlet_group_id) {
				$data_array = $data;
			}
			if ($data_array) {
				$conditions = array('SpecialGroupOtherSetting.special_group_id' => $group_id);

				$other = '(';
				/*if(isset($special_group_territory_details))
                {
                    $other.='(SpecialGroupOtherSetting.create_for = 2 AND
                        SpecialGroupOtherSetting.reffrence_id = '.$territory_id .')';                      ;
                }*/
				if ($outlet_group_id) {
					$other .= '(SpecialGroupOtherSetting.create_for = 4 AND
                        SpecialGroupOtherSetting.reffrence_id = ' . implode(',', $outlet_group_id) . ')';
				}
				if ((isset($special_group_territory_details) && !empty($special_group_territory_details)) || $special_group_details) {
					if ($outlet_group_id)
						$other .= ' OR ';
					$other .= '(SpecialGroupOtherSetting.create_for = 3)';
				}
				$other .= ')';
				$conditions[] = $other;
				$details = $this->SpecialGroupOtherSetting->find('all', array(
					'conditions' => $conditions,
					'recursive' => -1
				));
				$details = array_map(function ($data) {
					return $data['SpecialGroupOtherSetting'];
				}, $details);
				$group_data[] = array_merge($data['SpecialGroup'], array('details' => $details));
			}
		}
		$this->set(array(
			'special_group' => $group_data,
			'_serialize' => array('special_group')
		));
	}
	public function get_combination_list()
	{
		$this->loadModel('CombinationsV2');
		$json_data = $this->request->input('json_decode', true);
		/*---------------------------- Mac check --------------------------------*/
		/* $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
		if (!$mac_check) {

			$mac['status'] = 0;
			$mac['message'] = 'Mac Id Not Match';
			$res = $mac;
			$this->set(array(
				'mac' => $res,
				'_serialize' => array('mac')
			));
			return 0;
		} */

		$last_update_date = $json_data['last_update_date'];
		$res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);

		if ($res_status == 1) {
			$conditions = array();
		} else {
			$conditions = array('CombinationsV2.updated_at >' => $last_update_date);
		}
		// $conditions[] = array('(CombinationsV2.create_for=1 OR CombinationsV2.create_for=4 OR CombinationsV2.create_for=6)');
		$this->CombinationsV2->unbindModel(array(
			'belongsTo' => array('SoOutletCategory', 'SrOutletCategory', 'SoSpecialGroup', 'SrSpecialGroup')
		));
		$combination_list = $this->CombinationsV2->find('all', array(
			'conditions' => $conditions,
		));
		$combination_data = array();
		foreach ($combination_list as $data) {
			$combination_data[] = array_merge($data['CombinationsV2'], array('details' => $data['CombinationDetailsV2']));
		}
		$this->set(array(
			'combinations' => $combination_data,
			'_serialize' => array('combinations')
		));
	}
	private function get_special_group_outlet_group_by_territory_id($group_id, $territory_id)
	{
		$this->loadModel('SpecialGroupOtherSetting');
		$special_group_outlet_group_details = $this->SpecialGroupOtherSetting->find('list', array(
			'conditions' => array(
				'SpecialGroupOtherSetting.special_group_id' => $group_id,
				'SpecialGroupOtherSetting.create_for' => 4,
				'Market.territory_id' => $territory_id
			),
			'joins' => array(
				array(
					'table' => 'outlet_groups',
					'alias' => 'OutletGroup',
					'conditions' => 'OutletGroup.id=SpecialGroupOtherSetting.reffrence_id'
				),
				array(
					'table' => 'outlet_group_to_outlets',
					'alias' => 'OutletGroupOutlet',
					'conditions' => 'OutletGroup.id=OutletGroupOutlet.outlet_group_id'
				),
				array(
					'table' => 'outlets',
					'alias' => 'Outlet',
					'conditions' => 'Outlet.id=OutletGroupOutlet.outlet_id'
				),
				array(
					'table' => 'markets',
					'alias' => 'Market',
					'conditions' => 'Outlet.market_id=Market.id'
				),
			),
			'fields' => array('OutletGroup.id'),
			'recursive' => -1
		));
		return $special_group_outlet_group_details;
	}

	/* ------------------- Start Measurement Units ---------------------- */

	public function get_measurement_units()
	{
		$this->loadModel('MeasurementUnit');
		$json_data = $this->request->input('json_decode', true);

		/*---------------------------- Mac check --------------------------------*/
		/* $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
		if (!$mac_check) {

			$mac['status'] = 0;
			$mac['message'] = 'Mac Id Not Match';
			$res = $mac;
			$this->set(array(
				'mac' => $res,
				'_serialize' => array('mac')
			));
			return 0;
		} */

		$last_update_date = $json_data['last_update_date'];
		$res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
		if ($res_status == 1) {
			$conditions = array();
		} else {
			$conditions = array('MeasurementUnit.updated_at >' => $last_update_date);
		}
		$measurement_units = $this->MeasurementUnit->find('all', array(
			'fields' => array('MeasurementUnit.id', 'MeasurementUnit.name', 'MeasurementUnit.updated_at'),
			'conditions' => $conditions,
			'order' => array('MeasurementUnit.updated_at' => 'asc'),
			'recursive' => -1
		));
		foreach ($measurement_units as $key => $val) {
			$measurement_units[$key]['MeasurementUnit']['action'] = 1;
		}
		$this->set(array(
			'measurement_units' => $measurement_units,
			'_serialize' => array('measurement_units')
		));
	}
	public function get_product_units()
	{
		$this->loadModel('ProductMeasurement');
		$json_data = $this->request->input('json_decode', true);

		/*---------------------------- Mac check --------------------------------*/
		/* $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
		if (!$mac_check) {

			$mac['status'] = 0;
			$mac['message'] = 'Mac Id Not Match';
			$res = $mac;
			$this->set(array(
				'mac' => $res,
				'_serialize' => array('mac')
			));
			return 0;
		} */

		$last_update_date = $json_data['last_update_date'];
		$res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
		if ($res_status == 1) {
			$conditions = array();
		} else {
			$conditions = array('ProductMeasurement.updated_at >' => $last_update_date);
		}

		$unit_info = $this->ProductMeasurement->find('all', array(
			'fields' => array('ProductMeasurement.id', 'ProductMeasurement.measurement_unit_id', 'MeasurementUnit.name', 'ProductMeasurement.product_id', 'ProductMeasurement.qty_in_base', 'ProductMeasurement.updated_at'),
			'conditions' => $conditions,
			'joins' => array(
				array(
					'alias' => 'MeasurementUnit',
					'table' => 'measurement_units',
					'type' => 'INNER',
					'conditions' => 'ProductMeasurement.measurement_unit_id = MeasurementUnit.id'
				),
			),
			//'order' => array('ProductMeasurement.updated_at' => 'asc'),
			'recursive' => -1
		));

		//pr($unit_info);

		foreach ($unit_info as $key => $val) {
			$unit_info[$key]['ProductMeasurement']['action'] = 1;
		}
		$this->set(array(
			'unit_info' => $unit_info,
			'_serialize' => array('unit_info')
		));
	}
	/* ------------------- End Measurement Units ---------------------- */

	public function get_policy_list_v2()
	{
		$this->loadModel('DiscountBonusPolicy');
		$this->loadModel('SpecialGroup');
		$json_data = $this->request->input('json_decode', true);

		/*---------------------------- Mac check --------------------------------*/
		/* $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
		if (!$mac_check) {

			$mac['status'] = 0;
			$mac['message'] = 'Mac Id Not Match';
			$res = $mac;
			$this->set(array(
				'mac' => $res,
				'_serialize' => array('mac')
			));
			return 0;
		} */
		$last_update_date = $json_data['last_update_date'];


		// $territory_id = $json_data['territory_id'];  /// added by palash 26th May 2017
		$outlet_id = $json_data['outlet_id'];

		/*---------- get territory id from outlet table for gettinng product slab for special group-------- */
		$this->loadModel("Outlet");
		$outlet = $this->Outlet->find('first', array(
			'conditions' => array(
				'Outlet.id' => $outlet_id
			),

		));
		$territory_id = $outlet['Market']['territory_id'];
		$this->loadModel('Territory');
		$territory_info = $this->Territory->find(
			'first',
			array(
				'conditions' => array('Territory.id' => $territory_id),
				'fields' => array('Office.id'),
				'recursive' => 0
			)
		);
		$office_id = $territory_info['Office']['id'];


		$conditions = array(
			'DiscountBonusPolicy.is_so' => 1,
			'OR' => array(
				'Grp.reffrence_id' => $office_id,
				'Market.territory_id' => $territory_id,
			)
		);
		$res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
		if ($res_status != 1) {

			$conditions[] = array('DiscountBonusPolicy.updated_at >' => $last_update_date);
		}
		$this->DiscountBonusPolicy->unbindModel(array(
			'hasMany' => array(
				'DiscountBonusPolicyToSpecialGroupSr',
				'DiscountBonusPolicyToOfficeSr',
				'DiscountBonusPolicyToOfficeSo',
				'DiscountBonusPolicyToOutletGroupSr',
				'DiscountBonusPolicyToExcludingOutletGroupSr',
				'DiscountBonusPolicyToOutletCategorySr',
				'DiscountBonusPolicyOptionSr',
				'DiscountBonusPolicyOptionDB',
				'DiscountBonusPolicyOption',
			),
		));
		$policy_list = $this->DiscountBonusPolicy->find('all', array(
			//'fields' => array('OutletCategory.id', 'OutletCategory.category_name', 'OutletCategory.updated_at'),
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'discount_bonus_policy_settings',
					'alias' => 'Grp',
					'type' => 'Left',
					'conditions' => 'Grp.discount_bonus_policy_id=DiscountBonusPolicy.id and Grp.create_for=2 and Grp.for_so_sr=1'
				),
				array(
					'table' => 'discount_bonus_policy_settings',
					'alias' => 'GrpOg',
					'type' => 'Left',
					'conditions' => 'GrpOg.discount_bonus_policy_id=DiscountBonusPolicy.id and GrpOg.create_for=3 and GrpOg.for_so_sr=1'
				),
				array(
					'table' => 'outlet_group_to_outlets',
					'alias' => 'OutletGroup',
					'type' => 'Left',
					'conditions' => 'GrpOg.reffrence_id=OutletGroup.outlet_group_id'
				),
				array(
					'table' => 'outlets',
					'alias' => 'Outlet',
					'type' => 'Left',
					'conditions' => 'Outlet.id=OutletGroup.outlet_id'
				),
				array(
					'table' => 'markets',
					'alias' => 'Market',
					'type' => 'Left',
					'conditions' => 'Market.id=Outlet.market_id'
				),
			),
			'group' => array(
				'DiscountBonusPolicy.id',
				'DiscountBonusPolicy.name',
				'DiscountBonusPolicy.start_date',
				'DiscountBonusPolicy.end_date',
				'DiscountBonusPolicy.updated_at',
			),
			'fields' => array(
				'DiscountBonusPolicy.id',
				'DiscountBonusPolicy.name',
				'DiscountBonusPolicy.start_date',
				'DiscountBonusPolicy.end_date',
				'DiscountBonusPolicy.updated_at',
			),

			'recursive' => 2
		));

		$this->DiscountBonusPolicy->unbindModel(array(
			'hasMany' => array(
				'DiscountBonusPolicyToSpecialGroupSr',
				'DiscountBonusPolicyToOfficeSr',
				'DiscountBonusPolicyToOutletGroupSr',
				'DiscountBonusPolicyToExcludingOutletGroupSr',
				'DiscountBonusPolicyToOutletCategorySr',
				'DiscountBonusPolicyToOfficeSo',
				'DiscountBonusPolicyOptionSr',
				'DiscountBonusPolicyOptionDB',
				'DiscountBonusPolicyOption',
			),
		));
		$conditions = array(
			'DiscountBonusPolicy.is_so' => 1
		);
		if ($res_status != 1) {

			$conditions[] = array('DiscountBonusPolicy.updated_at >' => $last_update_date);
		}
		$special_policy_list = $this->DiscountBonusPolicy->find('all', array(
			//'fields' => array('OutletCategory.id', 'OutletCategory.category_name', 'OutletCategory.updated_at'),
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'discount_bonus_policy_settings',
					'alias' => 'Grp',
					'type' => 'inner',
					'conditions' => 'Grp.discount_bonus_policy_id=DiscountBonusPolicy.id and Grp.create_for=1 and Grp.for_so_sr=1'
				),

			),
			'group' => array(
				'DiscountBonusPolicy.id',
				'DiscountBonusPolicy.name',
				'DiscountBonusPolicy.start_date',
				'DiscountBonusPolicy.end_date',
				'DiscountBonusPolicy.updated_at',
			),
			'fields' => array(
				'DiscountBonusPolicy.id',
				'DiscountBonusPolicy.name',
				'DiscountBonusPolicy.start_date',
				'DiscountBonusPolicy.end_date',
				'DiscountBonusPolicy.updated_at',
			),

			'recursive' => 2
		));
		$assign_group = array();
		foreach ($special_policy_list as $data) {
			$group_id = $data['DiscountBonusPolicyToSpecialGroupSo'][0]['reffrence_id'];
			$special_group_details = $this->SpecialGroup->find('all', array(
				'conditions' => array(
					'SpecialGroup.id' => $group_id,
					'SPO.reffrence_id' => $office_id
				),
				'joins' => array(
					array(
						'table' => 'special_group_other_settings',
						'alias' => 'SPO',
						'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=1'
					),
					array(
						'table' => '(
                                    select t.office_id,spg.reffrence_id as territory_id from special_groups sps
                                    inner join special_group_other_settings spg on sps.id=spg.special_group_id and create_for=2
                                    inner join territories t on t.id=spg.reffrence_id
                                    where 
                                        sps.id=' . $group_id . '
                                )',
						'alias' => 'SPT',
						'type' => 'left',
						'conditions' => 'SPT.office_id=SPO.reffrence_id'
					),
				),
				'group' => array('SPO.reffrence_id'),
				'fields' => array('SPO.reffrence_id', 'COUNT(SPT.territory_id) as total_territory'),
				'recursive' => -1
			));
			if ($special_group_details) {
				if ($special_group_details[0][0]['total_territory'] > 0) {
					$special_group_territory_details = $this->SpecialGroup->find('all', array(
						'conditions' => array(
							'SpecialGroup.id' => $group_id,
							'SPO.reffrence_id' => $territory_id
						),
						'joins' => array(
							array(
								'table' => 'special_group_other_settings',
								'alias' => 'SPO',
								'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=2'
							)

						),
						'recursive' => -1
					));
					if ($special_group_territory_details) {
						$assign_group[] = $data;
					} else {
						$outlet_group_data = $this->get_special_group_outlet_group_by_territory_id($group_id, $territory_id);
						if ($outlet_group_data) {
							$assign_group[] = $data;
						}
					}
				} else {

					$assign_group[] = $data;
				}
			} else {
				$outlet_group_data = $this->get_special_group_outlet_group_by_territory_id($group_id, $territory_id);
				if ($outlet_group_data) {
					$assign_group[] = $data;
				}
			}
		}
		$policy_list = array_merge($policy_list, $assign_group);

		$this->set(array(
			'policy_list' => $policy_list,
			'_serialize' => array('policy_list')
		));
	}

	public function get_outlet_group()
	{
		$this->loadModel('OutletGroupToOutlet');
		$json_data = $this->request->input('json_decode', true);

		/*---------------------------- Mac check --------------------------------*/
		// $mac_check = $this->mac_check($json_data['mac'], $json_data['so_id']);
		// if (!$mac_check) {

		// 	$mac['status'] = 0;
		// 	$mac['message'] = 'Mac Id Not Match';
		// 	$res = $mac;
		// 	$this->set(array(
		// 		'mac' => $res,
		// 		'_serialize' => array('mac')
		// 	));
		// 	return 0;
		// }
		$outlet_id = $json_data['outlet_id'];
		$conditions = array();
		$conditions = array('OutletGroupToOutlet.is_distributor' => 0);
		$conditions = array('OutletGroupToOutlet.outlet_id' => $outlet_id);

		$outlet_group_list = $this->OutletGroupToOutlet->find('all', array(
			//'fields' => array('OutletCategory.id', 'OutletCategory.category_name', 'OutletCategory.updated_at'),
			'conditions' => $conditions,
			//'order' => array('OutletCategory.updated_at' => 'asc'),
			'recursive' => -1
		));

		//pr($outlet_group_list);
		//exit;

		$data_array = array();
		/* foreach ($outlet_group_list as $key => $val) {
            $outlet_group_list[$key]['OutletGroupToOutlet']['action'] = 1;
        }*/

		$this->set(array(
			'outlet_group_list' => $outlet_group_list,
			'_serialize' => array('outlet_group_list')
		));
	}

	public function get_outlet_ledger()
	{
		$this->loadModel('CmAccountLedger');
		$json_data = $this->request->input('json_decode', true);

		$outlet_id = $json_data['outlet_id'];
		$date_from = date('Y-m-d', strtotime($json_data['date_from']));
		$date_to = date('Y-m-d', strtotime($json_data['date_to']));

		$this->LoadModel('CmAccountSelectedOutlet');
		$outlet_account_find = $this->CmAccountSelectedOutlet->find('first', array(
			'conditions' => array(
				'CmAccountSelectedOutlet.outlet_id' => $outlet_id,
				'CmAccountSelectedOutlet.status' => 1,
				'CmAccountSelectedOutlet.account_number !=' => NULL
			),
			'fields' => array('CmAccountSelectedOutlet.account_number')
		));
		if (!empty($outlet_account_find)) {
			$account_number = $outlet_account_find['CmAccountSelectedOutlet']['account_number'];
			$opening_balance_find = $this->CmAccountLedger->find("all", array(
				'conditions' => array(
					'CmAccountLedger.account_number' => $account_number,
					'CmAccountLedger.tran_date <' => $date_from
				),
				'group' => array('CmAccountLedger.in_out'),
				'fields' => array('CmAccountLedger.in_out', 'SUM(CmAccountLedger.transaction_amount) as tran_amount')
			));
			$total_in_balance = 0;
			$total_out_balance = 0;
			foreach ($opening_balance_find as $data) {
				if ($data['CmAccountLedger']['in_out'] == 1) {
					$total_in_balance = $data[0]['tran_amount'];
				} else {
					$total_out_balance = $data[0]['tran_amount'];
				}
			}
			$opening_balance_amount = $total_in_balance - $total_out_balance;
			$other_tran_data = $this->CmAccountLedger->find("all", array(
				'conditions' => array(
					'CmAccountLedger.account_number' => $account_number,
					'CmAccountLedger.tran_date >=' => $date_from,
					'CmAccountLedger.tran_date <=' => $date_to
				),
				'order' => array('CmAccountLedger.tran_date'),
				'recursive' => -1
			));
			$account_ledger = array(
				'status' => 1,
				'opening' => $opening_balance_amount,
				'other_tran' => $other_tran_data
			);
		} else {
			$account_ledger = array(
				'status' => 0,
				'msg' => 'Account number not found'
			);
		}

		$this->set(array(
			'account_ledger' => $account_ledger,
			'_serialize' => array('account_ledger')
		));
	}

	function search_array($value, $key, $array)
	{
		foreach ($array as $k => $val) {
			if ($val[$key] == $value) {
				return $array[$k];
			}
		}
		return null;
	}

	public function create_requisition()
	{
		$this->loadModel('Product');
		$this->loadModel('CmOutletRequisition');
		$this->loadModel('CmOutletRequisitionDetail');

		$json_data = $this->request->input('json_decode', true);
		$json_data = $json_data['requisition_list'];

		$path = APP . 'logs/';
		$myfile = fopen($path . "create_memo.txt", "a") or die("Unable to open file!");
		fwrite($myfile, "\n" . $this->current_datetime() . ':' . $this->request->input());
		fclose($myfile);

		if (!empty($json_data)) {
			/*
             * This condition added for data synchronization 
             * Cteated by imrul in 09, April 2017
             * Duplicate memo check
             */
			$count = $this->CmOutletRequisition->find('count', array(
				'conditions' => array(
					'CmOutletRequisition.requisition_no' => $json_data['requisition_no']
				)
			));
			$outlet_id = $json_data['outlet_id'];
			$this->loadModel("Outlet");
			$outlet = $this->Outlet->find('first', array(
				'conditions' => array(
					'Outlet.id' => $outlet_id
				),

			));
			$territory_id = $outlet['Market']['territory_id'];
			$thana_id = $outlet['Market']['thana_id'];
			$market_id = $outlet['Market']['id'];

			/* Sales person get */
			$this->LoadModel('SalesPerson');
			$sales_person = $this->SalesPerson->find('first', array(
				'condditions' => array('SalesPerson.territory_id' => $territory_id),
				'recursive' => -1

			));
			$json_data['sales_person_id'] = $sales_person ? $sales_person['SalesPerson']['id'] : 0;
			//get office id 
			$this->loadModel('Territory');
			$territory_info = $this->Territory->find(
				'first',
				array(
					'conditions' => array('Territory.id' => $territory_id),
					'fields' => 'Territory.office_id',
					'order' => array('Territory.id' => 'asc'),
					'recursive' => -1,
					//'limit' => 100
				)
			);
			$office_id = $territory_info['Territory']['office_id'];
			/* --- Balance check for  submit ---------- */
			if ($json_data['status'] == 1) {
				$this->LoadModel('CmAccountSelectedOutlet');
				$this->LoadModel('CmAccountBalance');
				$outlet_account_find = $this->CmAccountSelectedOutlet->find('first', array(
					'conditions' => array(
						'CmAccountSelectedOutlet.outlet_id' => $outlet_id,
						'CmAccountSelectedOutlet.status' => 1,
						'CmAccountSelectedOutlet.account_number !=' => NULL
					),
					'fields' => array('CmAccountSelectedOutlet.account_number')
				));
				$account_number = '';
				if (!empty($outlet_account_find)) {
					$account_number = $outlet_account_find['CmAccountSelectedOutlet']['account_number'];
				}
				if (!$account_number) {
					$res['status'] = 0;
					$res['message'] = 'No account number found';

					$this->set(array(
						'memo' => $res,
						'_serialize' => array('memo')
					));
					return 0;
				}
				$outlet_current_balance_find = $this->CmAccountBalance->find('first', array(
					'conditions' => array(
						'CmAccountBalance.account_number' => $account_number
					),
				));
				$current_balance = 0;
				if ($outlet_current_balance_find) {
					$current_balance = $outlet_current_balance_find['CmAccountBalance']['balance'];
				}
				/* ---- current booking balance ---- */
				$this->LoadModel('CmBookingAccountBalance');
				$booking_balance_find = $this->CmBookingAccountBalance->find('first', array(
					'conditions' => array('CmBookingAccountBalance.status' => 1, 'CmBookingAccountBalance.account_number' => $account_number),
					'fields' => array('SUM(CmBookingAccountBalance.booking_amount) as booking')
				));
				$booking_balance = 0;
				if ($booking_balance_find) {
					$booking_balance = $booking_balance_find[0]['booking'];
				}
				$current_balance = $current_balance - $booking_balance;
				if ($current_balance < $json_data['gross_value']) {
					$res['status'] = 0;
					$res['message'] = 'Sortage balance';

					$this->set(array(
						'memo' => $res,
						'_serialize' => array('memo')
					));
					return 0;
				}
			}
			/* --- Balance check for  submit ---------- */

			if ($count == 0) {

				$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
				$product_list = Set::extract($products, '{n}.Product');

				$req_data['requisition_no'] = $json_data['requisition_no'];
				$req_data['requisition_date'] = date('Y-m-d', strtotime($json_data['requisition_date']));
				$req_data['requisition_time'] = $json_data['requisition_date'];
				$req_data['sales_person_id'] = $json_data['sales_person_id'];
				$req_data['outlet_id'] = $outlet_id;
				$req_data['market_id'] = $market_id;
				$req_data['territory_id'] = $territory_id;
				$req_data['gross_value'] = $json_data['gross_value'];
				$req_data['cash_recieved'] = $json_data['cash_recieved'] > $json_data['gross_value'] ? $json_data['gross_value'] : $json_data['cash_recieved'];
				$req_data['credit_amount'] = $credit_amount = $json_data['gross_value'] - $req_data['cash_recieved'];
				$req_data['latitude'] = $json_data['latitude'];
				$req_data['longitude'] = $json_data['longitude'];
				$req_data['status'] = $json_data['status'];
				$req_data['from_app'] = $json_data['from_app'];
				$req_data['action'] = 0;
				$req_data['created_at'] = $this->current_datetime();
				$req_data['updated_at'] = $this->current_datetime();
				$req_data['created_by'] = $json_data['user_id'];
				$req_data['updated_by'] = $json_data['user_id'];
				$req_data['office_id'] = $office_id ? $office_id : 0;
				$req_data['thana_id'] = $thana_id ? $thana_id : 0;
				$req_data['total_discount'] = $json_data['total_discount'];

				$this->CmOutletRequisition->save($req_data);
				/* ----------- booking amount add --------- */
				if ($json_data['status'] == 1) {
					$data_booking['account_number'] = $account_number;
					$data_booking['requisitioin_id'] = $this->CmOutletRequisition->id;
					$data_booking['status'] = 1;
					$data_booking['booking_amount'] = $json_data['gross_value'];
					$data_booking['created_at'] = $this->current_datetime();
					$data_booking['updated_at'] = $this->current_datetime();
					$this->CmBookingAccountBalance->save($data_booking);
				}
				/* ----------- booking amount add --------- */
				$req_details_array = array();
				foreach ($json_data['requisition_details'] as $val) {

					$req_details_array = array();
					$product_price_id = 0;
					$req_details['cm_outlet_requisition_id'] = $this->CmOutletRequisition->id;
					$req_details['product_id'] = $val['product_id'];
					$req_details['product_type'] = $val['product_type'];

					if ($val['measurement_unit_id'] > 0) {
						$req_details['measurement_unit_id'] = $val['measurement_unit_id'];
					} else {
						$p_units = $this->search_array($val['product_id'], 'id', $product_list);
						$req_details['measurement_unit_id'] = $p_units['sales_measurement_unit_id'];
					}
					//$req_details['measurement_unit_id'] = $val['measurement_unit_id'];

					$req_details['sales_qty'] = $val['sales_qty'];
					$req_details['actual_price'] = $val['price'];
					$req_details['price'] = $val['price'] > 0 ? ($val['price'] - (isset($val['discount_amount']) ? $val['discount_amount'] : 0)) : 0;

					$req_details['product_price_id'] = $val['price_id'];
					$req_details['product_combination_id'] = $val['combination_id'];
					$req_details['bonus_qty'] = $val['bonus_qty'];
					$req_details['bonus_product_id'] = $val['bonus_product_id'];
					$req_details['current_inventory_id'] = $val['current_inventory_id'];
					$req_details['bonus_inventory_id'] = $val['bonus_inventory_id'];
					$req_details['is_bonus'] = $val['is_bonus'];

					$req_details['discount_type'] = $val['discount_type'];
					$req_details['discount_amount'] = !isset($val['discount_amount']) && !$val['discount_amount'] ? 0 : $val['discount_amount'];

					$req_details['policy_type'] = $val['policy_type'];
					$req_details['policy_id'] = $val['policy_id'];

					$selected_set = '';
					$other_info = array();
					if (isset($val['selected_set'])) {
						$selected_set = $val['selected_set'];
					}
					if ($selected_set) {
						$other_info = array(
							'selected_set' => $selected_set
						);
					}
					$provided_qty = '';
					if (isset($val['provided_qty'])) {
						$provided_qty = $val['provided_qty'];
					}
					if ($provided_qty) {
						$other_info['provided_qty'] = $provided_qty;
					}
					if ($other_info)
						$req_details['other_info'] = json_encode($other_info);

					// insert data into MemoDetail
					$req_details_array[] = $req_details;
					$this->CmOutletRequisitionDetail->saveAll($req_details_array);
				}
				$res['status'] = 1;
				$res['requisition_no'] = $json_data['requisition_no'];
				$res['message'] = 'Requisition has been created successfuly.';
			} else {
				$memo_id_arr = $this->CmOutletRequisition->find('first', array(
					'conditions' => array(
						'CmOutletRequisition.requisition_no' => $json_data['requisition_no']
					)
				));
				$cm_outlet_requisition_id = $memo_id_arr['CmOutletRequisition']['id'];

				$this->CmOutletRequisitionDetail->deleteAll(array('CmOutletRequisitionDetail.cm_outlet_requisition_id' => $cm_outlet_requisition_id));
				$products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
				$product_list = Set::extract($products, '{n}.Product');


				/* -----------------Same as Create Memo but only id included for updating memo------------------ */


				$req_data['id'] = $cm_outlet_requisition_id;
				$req_data['requisition_no'] = $json_data['requisition_no'];
				$req_data['requisition_date'] = date('Y-m-d', strtotime($json_data['requisition_date']));
				$req_data['requisition_time'] = $json_data['requisition_date'];
				$req_data['sales_person_id'] = $json_data['sales_person_id'];
				$req_data['outlet_id'] = $outlet_id;
				$req_data['market_id'] = $market_id;
				$req_data['territory_id'] = $territory_id;
				$req_data['gross_value'] = $json_data['gross_value'];
				$req_data['cash_recieved'] = $json_data['cash_recieved'] > $json_data['gross_value'] ? $json_data['gross_value'] : $json_data['cash_recieved'];
				$req_data['credit_amount'] = $credit_amount = $json_data['gross_value'] - $req_data['cash_recieved'];
				$req_data['latitude'] = $json_data['latitude'];
				$req_data['longitude'] = $json_data['longitude'];
				$req_data['status'] = $json_data['status'];
				$req_data['from_app'] = $json_data['from_app'];
				$req_data['action'] = 0;
				$req_data['updated_at'] = $this->current_datetime();
				$req_data['updated_by'] = $json_data['user_id'];
				$req_data['office_id'] = $office_id ? $office_id : 0;
				$req_data['thana_id'] = $thana_id ? $thana_id : 0;
				$req_data['total_discount'] = $json_data['total_discount'];

				$this->CmOutletRequisition->save($req_data);
				/* ----------- booking amount add --------- */
				if ($json_data['status'] == 1) {
					$data_booking['account_number'] = $account_number;
					$data_booking['requisitioin_id'] = $this->CmOutletRequisition->id;
					$data_booking['status'] = 1;
					$data_booking['booking_amount'] = $json_data['gross_value'];
					$data_booking['created_at'] = $this->current_datetime();
					$data_booking['updated_at'] = $this->current_datetime();
					$this->CmBookingAccountBalance->save($data_booking);
				}
				/* ----------- booking amount add --------- */
				$req_details_array = array();
				foreach ($json_data['requisition_details'] as $val) {
					$req_details_array = array();
					$req_details['cm_outlet_requisition_id'] = $this->CmOutletRequisition->id;
					$req_details['product_id'] = $val['product_id'];
					$req_details['product_type'] = $val['product_type'];

					if ($val['measurement_unit_id'] > 0) {
						$req_details['measurement_unit_id'] = $val['measurement_unit_id'];
					} else {
						$p_units = $this->search_array($val['product_id'], 'id', $product_list);
						$req_details['measurement_unit_id'] = $p_units['sales_measurement_unit_id'];
					}
					$req_details['sales_qty'] = $val['sales_qty'];
					$req_details['actual_price'] = $val['price'];
					$req_details['price'] = $val['price'] > 0 ? ($val['price'] - (isset($val['discount_amount']) ? $val['discount_amount'] : 0)) : 0;
					// $req_details['vat'] = $val['vat'];
					$req_details['product_price_id'] = $val['price_id'];
					$req_details['product_combination_id'] = $val['combination_id'];
					$req_details['bonus_qty'] = $val['bonus_qty'];
					$req_details['bonus_product_id'] = $val['bonus_product_id'];
					$req_details['current_inventory_id'] = $val['current_inventory_id'];
					$req_details['bonus_inventory_id'] = $val['bonus_inventory_id'];
					$req_details['is_bonus'] = $val['is_bonus'];
					$req_details['discount_type'] = $val['discount_type'];
					$req_details['discount_amount'] = !isset($val['discount_amount']) && !$val['discount_amount'] ? 0 : $val['discount_amount'];
					$req_details['policy_type'] = $val['policy_type'];
					$req_details['policy_id'] = $val['policy_id'];
					$selected_set = '';
					$other_info = array();
					if (isset($val['selected_set'])) {
						$selected_set = $val['selected_set'];
					}
					if ($selected_set) {
						$other_info = array(
							'selected_set' => $selected_set
						);
					}

					$provided_qty = '';
					if (isset($val['provided_qty'])) {
						$provided_qty = $val['provided_qty'];
					}
					if ($provided_qty) {
						$other_info['provided_qty'] = $provided_qty;
					}
					if ($other_info)
						$req_details['other_info'] = json_encode($other_info);

					// insert data into MemoDetail
					$req_details_array[] = $req_details;
					$this->CmOutletRequisitionDetail->saveAll($req_details_array);
				}
				$res['status'] = 1;
				$res['requisition_no'] = $json_data['requisition_no'];
				$res['message'] = 'Memo has been updated successfuly.';
			}
		} else {
			$res['status'] = 0;
			$res['message'] = 'Requisition not found.';
		}

		$path = APP . 'logs/';
		$myfile = fopen($path . "create_memo_response.txt", "a") or die("Unable to open file!");
		fwrite($myfile, "\n" . $this->current_datetime() . ':' . json_encode($res));
		fclose($myfile);

		$this->set(array(
			'memo' => $res,
			'_serialize' => array('memo')
		));
	}

	/* ------------- get payment summery ------------ */
	public function get_payment_summery()
	{
		$this->loadModel('CmPaymentHistory');
		$json_data = $this->request->input('json_decode', true);

		$outlet_id = $json_data['outlet_id'];
		$date_from = date('Y-m-d', strtotime($json_data['date_from']));
		$date_to = date('Y-m-d', strtotime($json_data['date_to']));

		$this->LoadModel('CmAccountSelectedOutlet');
		$outlet_account_find = $this->CmAccountSelectedOutlet->find('first', array(
			'conditions' => array(
				'CmAccountSelectedOutlet.outlet_id' => $outlet_id,
				'CmAccountSelectedOutlet.status' => 1,
				'CmAccountSelectedOutlet.account_number !=' => NULL
			),
			'fields' => array('CmAccountSelectedOutlet.account_number')
		));
		if (!empty($outlet_account_find)) {
			$account_number = $outlet_account_find['CmAccountSelectedOutlet']['account_number'];

			$payment_history = $this->CmPaymentHistory->find("all", array(
				'conditions' => array(
					'CmPaymentHistory.account_number' => $account_number,
					'CmPaymentHistory.payment_datetime >=' => $date_from,
					'CmPaymentHistory.payment_datetime <=' => $date_to
				),
				'order' => array('CmPaymentHistory.payment_datetime'),
				'recursive' => -1
			));
			$payment_history = array(
				'status' => 1,
				'history' => $payment_history
			);
		} else {
			$account_ledger = array(
				'status' => 0,
				'msg' => 'Account number not found'
			);
		}

		$this->set(array(
			'results' => $payment_history,
			'_serialize' => array('results')
		));
	}

	/* ------------- get requisition history summery ------------ */
	public function get_requisition_summery()
	{
		$this->loadModel('CmOutletRequisition');
		$json_data = $this->request->input('json_decode', true);

		$outlet_id = $json_data['outlet_id'];
		$date_from = date('Y-m-d', strtotime($json_data['date_from']));
		$date_to = date('Y-m-d', strtotime($json_data['date_to']));
		$status = $json_data['status'];
		$conditions = array(
			'CmOutletRequisition.outlet_id' => $outlet_id,
			'CmOutletRequisition.requisition_date >=' => $date_from,
			'CmOutletRequisition.requisition_date <=' => $date_to,
			'CmOutletRequisition.status ' => $status,
		);
		$requisition_summery = $this->CmOutletRequisition->find("all", array(
			'conditions' => $conditions,
			'order' => array('CmOutletRequisition.requisition_date'),
			'recursive' => -1
		));
		$payment_history = array(
			'status' => 1,
			'summery' => $requisition_summery
		);

		$this->set(array(
			'results' => $payment_history,
			'_serialize' => array('results')
		));
	}

	/* ------------- get requisition history summery ------------ */
	public function cancel_requisition()
	{
		$this->loadModel('CmOutletRequisition');
		$this->loadModel('CmBookingAccountBalance');
		$json_data = $this->request->input('json_decode', true);
		/* $memo_id_arr = $this->CmOutletRequisition->find('first', array(
			'conditions' => array(
				'CmOutletRequisition.requisition_no' => $json_data['requisition_no']
			)
		)); */
		$cm_outlet_requisition_id = $json_data['requisition_id'];
		$req_data['id'] = $cm_outlet_requisition_id;
		$req_data['status'] = 3;
		$this->CmOutletRequisition->saveAll($req_data);
		/* booking balance free */
		$book_data['status'] = 3;
		$this->CmBookingAccountBalance->updateAll($book_data, array('CmBookingAccountBalance.requisition_id' => $cm_outlet_requisition_id));
		$this->set(array(
			'res' => array('status' => 1, 'msg' => 'Successfully cancelled'),
			'_serialize' => array('res')
		));
	}

	public function get_requisition_details()
	{
		$this->loadModel('CmOutletRequisition');
		$this->loadModel('CmOutletRequisitionDetail');
		$this->loadModel('Product');
		$json_data = $this->request->input('json_decode', true);
		$this->CmOutletRequisition->unbindModel(array(
			'belongsTo' => array('Thana', 'Office', 'Territory', 'Market', 'Outlet', 'SalesPerson')
		));
		$requisition_details = $this->CmOutletRequisition->find('first', array(
			'conditions' => array(
				'CmOutletRequisition.id' => $json_data['requisition_id']
			),
		));
		$mdata['requisition_details'] = $requisition_details['CmOutletRequisitionDetail'];
		$mm = 0;

		foreach ($requisition_details['CmOutletRequisitionDetail'] as $each_requisition_details) {

			$product_id = $each_requisition_details['product_id'];
			$product_info = $this->Product->find('first', array(
				'fields' => array('product_type_id'),
				'conditions' => array('id' => $product_id),
				'recursive' => -1
			));

			$type = "";
			if ($product_info['Product']['product_type_id'] == 1 && $each_requisition_details['price'] > 0) {
				$type = 0;
			} else if ($product_info['Product']['product_type_id'] == 3) {
				$type = 1;
			} else if ($product_info['Product']['product_type_id'] == 1 && $each_requisition_details['price'] < 1) {
				$type = 2;
			}

			$mdata['requisition_details'][$mm]['product_type_id'] = $type;
			$mdata['requisition_details'][$mm]['discount_type'] = $each_requisition_details['discount_type'] == null ? 0 : $each_requisition_details['discount_type'];
			$mdata['requisition_details'][$mm]['discount_amount'] = $each_requisition_details['discount_type'] == null ? 0 : $each_requisition_details['discount_amount'];
			$mdata['requisition_details'][$mm]['price'] = $each_requisition_details['actual_price'];

			$mdata['requisition_details'][$mm]['vat'] = $this->get_vat_by_product_id_memo_date_v2($product_id, $requisition_details['CmOutletRequisition']['requisition_date']);
			if ($each_requisition_details['other_info']) {
				$other_info = json_decode($each_requisition_details['other_info'], 1);
				$selected_set = $other_info['selected_set'];
				$provided_qty = $other_info['provided_qty'];
				$mdata['requisition_details'][$mm]['selected_set'] = $selected_set;
				$mdata['requisition_details'][$mm]['provided_qty'] = $provided_qty;
			} else {
				$selected_set = 1;
				$provided_qty = 0;
				$mdata['requisition_details'][$mm]['selected_set'] = $selected_set;
				$mdata['requisition_details'][$mm]['provided_qty'] = $provided_qty;
			}
			$mm++;
		}
		$this->set(array(
			'details' => $mdata['requisition_details'],
			'_serialize' => array('details')
		));
	}

	public function get_product_list()
	{
		$this->loadModel('Product');
		$json_data = $this->request->input('json_decode', true);

		$last_update_date = $json_data['last_update_date'];

		$res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
		if ($res_status == 1) {
			$conditions = array();
		} else {
			$conditions = array('Product.updated_at >' => $last_update_date);
		}

		$products = $this->Product->find('all', array(
			'fields' => array('Product.id', 'Product.name', 'Product.product_code', 'Product.product_category_id', 'Product.sales_measurement_unit_id', 'Product.product_type_id', 'Product.updated_at', 'Product.is_pharma', 'Product.order', 'Product.is_injectable', 'CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END as qty_in_base', 'Product.base_measurement_unit_id',),
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'product_measurements',
					'alias' => 'ProductMeasurement',
					'type' => 'Left',
					'conditions' => 'ProductMeasurement.product_id=Product.id AND Product.sales_measurement_unit_id=ProductMeasurement.measurement_unit_id'
				),
			),
			'order' => array('Product.order' => 'asc'),
			'recursive' => -1
		));

		// pr($products);exit;


		$this->loadModel('OpenCombinationProduct');
		$price_open = array();
		$bonus_open = array();

		foreach ($products as $key => $val) {
			$products[$key]['Product']['qty_in_base'] = $val[0]['qty_in_base'];
			unset($products[$key][0]);
			//for price opne
			$price_open = $this->OpenCombinationProduct->find('first', array(
				'conditions' => array(
					'OpenCombinationProduct.product_id' => $val['Product']['id'],
					'OpenCombination.is_bonus' => 0,
					'OpenCombination.start_date <=' => $this->current_date(),
					'OpenCombination.end_date >=' =>  $this->current_date()
				),
			));
			//pr($price_open);			

			if ($price_open) {
				$products[$key]['Product']['price_open_start'] = $price_open['OpenCombination']['start_date'];
				$products[$key]['Product']['price_open_end'] = $price_open['OpenCombination']['end_date'];
			} else {
				$products[$key]['Product']['price_open_start'] = '';
				$products[$key]['Product']['price_open_end'] = '';
			}


			//for bonus opne
			$bonus_open = $this->OpenCombinationProduct->find('first', array(
				'conditions' => array(
					'OpenCombinationProduct.product_id' => $val['Product']['id'],
					'OpenCombination.is_bonus' => 1,
					'OpenCombination.start_date <=' => $this->current_date(),
					'OpenCombination.end_date >=' =>  $this->current_date()
				),
			));
			//pr($bonus_open);			

			if ($bonus_open) {
				$products[$key]['Product']['price_bonus_start'] = $bonus_open['OpenCombination']['start_date'];
				$products[$key]['Product']['price_bonus_end'] = $bonus_open['OpenCombination']['end_date'];
			} else {
				$products[$key]['Product']['price_bonus_start'] = '';
				$products[$key]['Product']['price_bonus_end'] = '';
			}

			$products[$key]['Product']['action'] = 1;
		}

		$this->set(array(
			'products' => $products,
			'_serialize' => array('products')
		));
	}
	public function get_product_categories()
	{
		$this->loadModel('ProductCategory');
		$json_data = $this->request->input('json_decode', true);



		$last_update_date = $json_data['last_update_date'];
		$res_status = (isset($json_data['all']) != '' ? $json_data['all'] : 1);
		if ($res_status == 1) {
			$conditions = array();
		} else {
			$conditions = array('ProductCategory.updated_at >' => $last_update_date);
		}
		$product_categories = $this->ProductCategory->find('all', array(
			'fields' => array('ProductCategory.id', 'ProductCategory.name', 'ProductCategory.parent_id', 'ProductCategory.is_pharma_product', 'ProductCategory.updated_at'),
			'conditions' => $conditions,
			'order' => array('ProductCategory.updated_at' => 'asc'),
			'recursive' => -1
		));

		foreach ($product_categories as $key => $val) {
			$product_categories[$key]['ProductCategory']['action'] = 1;
		}

		$this->set(array(
			'product_categories' => $product_categories,
			'_serialize' => array('product_categories')
		));
	}
	public function get_dashboard_data()
	{
		$this->loadModel('CmPaymentHistory');
		$this->loadModel('CmAccountBalance');
		$this->loadModel('CmOutletRequisition');
		$json_data = $this->request->input('json_decode', true);

		$outlet_id = $json_data['outlet_id'];
		$current_balance = 0;
		$requisition_summery = array();
		$payment_history = array();
		$this->LoadModel('CmAccountSelectedOutlet');
		$outlet_account_find = $this->CmAccountSelectedOutlet->find('first', array(
			'conditions' => array(
				'CmAccountSelectedOutlet.outlet_id' => $outlet_id,
				'CmAccountSelectedOutlet.status' => 1,
				'CmAccountSelectedOutlet.account_number !=' => NULL
			),
			'fields' => array('CmAccountSelectedOutlet.account_number')
		));
		if (!empty($outlet_account_find)) {
			$account_number = $outlet_account_find['CmAccountSelectedOutlet']['account_number'];
			$outlet_current_balance_find = $this->CmAccountBalance->find('first', array(
				'conditions' => array(
					'CmAccountBalance.account_number' => $account_number
				),
			));

			if ($outlet_current_balance_find) {
				$current_balance = $outlet_current_balance_find['CmAccountBalance']['balance'];
			}

			$payment_history = $this->CmPaymentHistory->find("all", array(
				'conditions' => array(
					'CmPaymentHistory.account_number' => $account_number
				),
				'order' => array('CmPaymentHistory.id DESC'),
				'limit' => '3',
				'recursive' => -1
			));
		}
		/* ---- requisition summery ----- */
		$conditions = array(
			'CmOutletRequisition.outlet_id' => $outlet_id,
			'CmOutletRequisition.status !=' => 2,
		);
		$requisition_summery = $this->CmOutletRequisition->find("all", array(
			'conditions' => $conditions,
			'order' => array('CmOutletRequisition.id desc'),
			'limit' => '3',
			'recursive' => -1
		));
		/* delivery summery */
		$conditions = array(
			'CmOutletRequisition.outlet_id' => $outlet_id,
			'CmOutletRequisition.status' => 2,
		);
		$delivery_summery = $this->CmOutletRequisition->find("all", array(
			'conditions' => $conditions,
			'order' => array('CmOutletRequisition.id desc'),
			'limit' => '3',
			'recursive' => -1
		));

		$data = array(
			'current_balance' => $current_balance,
			'payment_history' => $payment_history,
			'requisition_summery' => $requisition_summery,
			'delivery_summery' => $delivery_summery
		);
		$this->set(array(
			'result' => $data,
			'_serialize' => array('result')
		));
	}
}
