<?php
App::uses('AppController', 'Controller');
/**
 * ProductSettings Controller
 *
 * @property DiscountBonusPolicie $DiscountBonusPolicie
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */

class DiscountBonusPoliciesController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $uses = array(
		'DiscountBonusPolicy',
		'OutletGroup',
		'Thana',
		'Market',
		'SalesPerson',
		'Outlet',
		'Product',
		'DiscountBonusPolicySetting',
		'DiscountBonusPolicyOptionPriceSlab',
		'DiscountBonusPolicyProduct',
		'DiscountBonusPolicyOption',
		'OutletCategory',
		'DiscountBonusPolicyOptionBonusProduct',
		'DiscountBonusPolicyDefaultBonusProductSelection',
		'DistOutletCategory',
		'DiscountBonusPolicyOptionExclusionInclusionProduct',
		'SpecialGroup'
	);

	public $components = array('Paginator', 'Session', 'Filter.Filter');
	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index($id = null)
	{

		$this->set('page_title', 'View List');
		$conditions = array();
		$this->paginate = array(
			'limit' => 100,
			'order' => array('DiscountBonusPolicy.id' => 'DESC'),
			'recursive' => -1
		);
		$this->set('results', $this->paginate());
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
		$this->set('page_title', 'View Details');
		if (!$this->DiscountBonusPolicy->exists($id)) {
			throw new NotFoundException(__('Invalid request!'));
		}
		$options = array('conditions' => array('DiscountBonusPolicy.' . $this->DiscountBonusPolicy->primaryKey => $id));
		$this->set('productCombination', $this->DiscountBonusPolicy->find('first', $options));
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add()
	{
		$this->set('page_title', 'Add New');

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$this->set('office_parent_id', $office_parent_id);
		if ($office_parent_id == 0) {
			$office_conditions = array(
				'office_type_id' => 2,
				"NOT" => array("id" => array(30, 31, 37))
			);
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}
		$this->set('office_id', $this->UserAuth->getOfficeId());
		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$this->set(compact('offices'));

		$outlet_categories = $this->OutletCategory->find('list', array('conditions' => array('is_active' => 1)));
		$this->set(compact('outlet_categories'));

		$sr_outlet_categories = $this->DistOutletCategory->find('list', array('conditions' => array('is_active' => 1)));
		$this->set(compact('sr_outlet_categories'));

		$outlet_groups = array();
		$o_con = array('OutletGroup.is_distributor' => 0);
		$outlet_groups = $this->OutletGroup->find('list', array('conditions' => $o_con, 'order' => array('name' => 'asc')));
		$o_con = array('OutletGroup.is_distributor' => 1);
		$sr_outlet_groups = $this->OutletGroup->find('list', array('conditions' => $o_con, 'order' => array('name' => 'asc')));
		//pr($outlet_groups);exit;

		$this->set(compact('outlet_groups', 'sr_outlet_groups'));

		$this->loadModel('Product');
		$products = $this->Product->find('list', array('order' => array('Product.order' => 'ASC')));
		$this->html = '';
		foreach ($products as $key => $val) {
			$this->html .= '<option value="' . $key . '">' . addslashes($val) . '</option>';
		}
		$product_list = $this->html;
		$this->set(compact('products', 'product_list'));

		$policy_types = array(
			'0' => 'Only Discount',
			'1' => 'Only Bonus',
			'2' => 'Discount and Bonus',
			'3' => 'Discount or Bonus',
		);
		$this->set(compact('policy_types'));

		$disccount_types = array(
			'0' => '%',
			'1' => 'Tk'
		);
		$this->set(compact('disccount_types'));


		if ($this->request->is('post')) {
			foreach (@$this->request->data['DiscountBonusPolicyOption'] as $key => $val) {
				$min_qty_measurement_unit_id = $val['min_qty_measurement_unit_id'];
				if ($min_qty_measurement_unit_id == '') {
					$this->Session->setFlash(__('Measurement Unit is null. Please, select Measurement Unit.'), 'flash/error');
					$url = 'index';
					$this->redirect(array('action' => $url));
				}
			}

			$data_policy = array();
			$data_policy['DiscountBonusPolicy']['start_date'] = date('Y-m-d', strtotime($this->request->data['DiscountBonusPolicy']['start_date']));
			$data_policy['DiscountBonusPolicy']['end_date'] = date('Y-m-d', strtotime($this->request->data['DiscountBonusPolicy']['end_date']));
			$data_policy['DiscountBonusPolicy']['name'] = $this->request->data['DiscountBonusPolicy']['name'];
			$data_policy['DiscountBonusPolicy']['remarks'] = $this->request->data['DiscountBonusPolicy']['remarks'];

			$data_policy['DiscountBonusPolicy']['created_at'] = $this->current_datetime();
			$data_policy['DiscountBonusPolicy']['updated_at'] = $this->current_datetime();
			$data_policy['DiscountBonusPolicy']['created_by'] = $this->UserAuth->getUserId();
			$data_policy['DiscountBonusPolicy']['updated_by'] = $this->UserAuth->getUserId();
			$data_policy['DiscountBonusPolicy']['numbering'] = $this->request->data['DiscountBonusPolicy']['numbering'];
			$is_so = 0;
			$is_sr = 0;
			if ($this->request->data['DiscountBonusPolicy']['create_policy_for']) {
				foreach ($this->request->data['DiscountBonusPolicy']['create_policy_for'] as $ch_val) {
					if ($ch_val == 1) {
						$data_policy['DiscountBonusPolicy']['is_so'] = 1;
						$is_so = 1;
					} elseif ($ch_val == 2) {
						$data_policy['DiscountBonusPolicy']['is_sr'] = 1;
						$is_sr = 1;
					} elseif ($ch_val == 3) {
						$data_policy['DiscountBonusPolicy']['is_db'] = 1;
					}
				}
			}
			$this->DiscountBonusPolicy->create();
			if ($this->DiscountBonusPolicy->save($data_policy)) {
				$policy_id = $this->DiscountBonusPolicy->getInsertID();
				//-------------------file-------------\\
				$bonusfile = $this->request->data['DiscountBonusPolicy']['bonus_file'];
				$this->loadModel('DiscountBonusPolicyFile');
				if (!empty($bonusfile)) {
					//echo '<pre>';print_r($bonusfile);exit;
					foreach ($bonusfile as $file_val) {
						$file = $file_val;
						$ext = substr(strtolower(strrchr($file['name'], '.')), 1);
						$image_name = rand(10000, 99999) . '.' . $ext;
						$arr_ext = array('jpg', 'jpeg', 'png', 'docx', 'pdf', 'xls', 'csv', 'xlsx');

						if (in_array($ext, $arr_ext)) {
							$maxDimW = 512;
							$maxDimH = 512;
							list($width, $height, $type, $attr) = getimagesize($file['tmp_name']);
							move_uploaded_file($file['tmp_name'], WWW_ROOT . 'bonus_policy' . DS . $image_name);
							//prepare the filename for database entry

							$bonusdata['DiscountBonusPolicyFile']['discount_bonus_policy_id'] = $policy_id;
							$bonusdata['DiscountBonusPolicyFile']['file_name'] = $image_name;
							$bonusdata['DiscountBonusPolicyFile']['created_at'] = $this->current_datetime();
							$bonusdata['DiscountBonusPolicyFile']['created_by'] = $this->UserAuth->getUserId();
							$databonus[] = $bonusdata;
						}
					}
					//echo '<pre>';print_r($databonus);exit;
					if (!empty($databonus)) {
						$this->DiscountBonusPolicyFile->saveAll($databonus);
					}
				}
				//-------------------end----------------\\




				//Outlet Category Insert
				if (!empty(@$this->request->data['DiscountBonusPolicySpecialGroup'])) {
					if (@$this->request->data['DiscountBonusPolicySpecialGroup']['so_special_group_id'] && $is_so == 1) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 1; //1=so
						$data_array['DiscountBonusPolicySetting']['create_for'] = 1; //1=special group id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicySpecialGroup']['so_special_group_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
					if (@$this->request->data['DiscountBonusPolicySpecialGroup']['sr_special_group_id'] && $is_sr == 1) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 2; //2=sr
						$data_array['DiscountBonusPolicySetting']['create_for'] = 1; //1=special group id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicySpecialGroup']['sr_special_group_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
				}

				if (!empty(@$this->request->data['DiscountBonusPolicyToOffice'])) {
					if (@$this->request->data['DiscountBonusPolicyToOffice']['so_office_id'] && $is_so == 1) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 1; //1=so
						$data_array['DiscountBonusPolicySetting']['create_for'] = 2; //2=office
						$office_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToOffice']['so_office_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$office_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($office_data);
					}


					/*---- for SR --------*/
					if (@$this->request->data['DiscountBonusPolicyToOffice']['sr_office_id'] && $is_sr == 1) {

						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 2; //2=sr
						$data_array['DiscountBonusPolicySetting']['create_for'] = 2; //2=office
						$office_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToOffice']['sr_office_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$office_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($office_data);
					}
				}


				//Outlet Group Insert
				if (!empty(@$this->request->data['DiscountBonusPolicyToOutletGroup'])) {
					if (@$this->request->data['DiscountBonusPolicyToOutletGroup']['so_outlet_group_id'] && $is_so == 1) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 1; //1=so
						$data_array['DiscountBonusPolicySetting']['create_for'] = 3; //3=outlet group id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToOutletGroup']['so_outlet_group_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
					if (@$this->request->data['DiscountBonusPolicyToOutletGroup']['sr_outlet_group_id'] && $is_sr == 1) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 2; //2=sr
						$data_array['DiscountBonusPolicySetting']['create_for'] = 3; //3=outlet group id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToOutletGroup']['sr_outlet_group_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
				}

				//Excluding Outlet Group Insert
				if (!empty(@$this->request->data['DiscountBonusPolicyToExcludingOutletGroup'])) {
					if (@$this->request->data['DiscountBonusPolicyToExcludingOutletGroup']['so_excluding_outlet_group_id'] && $is_so == 1) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 1; //1=so
						$data_array['DiscountBonusPolicySetting']['create_for'] = 5; //5=Excluding outlet group id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToExcludingOutletGroup']['so_excluding_outlet_group_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
					if (@$this->request->data['DiscountBonusPolicyToExcludingOutletGroup']['sr_excluding_outlet_group_id'] && $is_sr == 1) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 2; //2=sr
						$data_array['DiscountBonusPolicySetting']['create_for'] = 5; //5=Excluding outlet group id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToExcludingOutletGroup']['sr_excluding_outlet_group_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
				}

				//Outlet Category Insert
				if (!empty(@$this->request->data['DiscountBonusPolicyToOutletCategory'])) {
					if (@$this->request->data['DiscountBonusPolicyToOutletCategory']['so_outlet_category_id'] && $is_so == 1 && @$this->request->data['DiscountBonusPolicyToOffice']['so_office_id']) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 1; //1=so
						$data_array['DiscountBonusPolicySetting']['create_for'] = 4; //4=outlet category id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToOutletCategory']['so_outlet_category_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
					if (@$this->request->data['DiscountBonusPolicyToOutletCategory']['sr_outlet_category_id'] && $is_sr == 1 && @$this->request->data['DiscountBonusPolicyToOffice']['sr_office_id']) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 2; //2=sr
						$data_array['DiscountBonusPolicySetting']['create_for'] = 4; //4=outlet category id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToOutletCategory']['sr_outlet_category_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
				}


				//Policy Product Insert
				if (!empty(@$this->request->data['DiscountBonusPolicyProduct'])) {
					$data_array = array();
					$data_array['DiscountBonusPolicyProduct']['discount_bonus_policy_id'] = $policy_id;
					$root_products = array();
					foreach ($this->request->data['DiscountBonusPolicyProduct']['policy_product_id'] as $key => $val) {
						if ($val) $data_array['DiscountBonusPolicyProduct']['product_id'] = $val;
						$root_products[] = $data_array;
					}
					$this->DiscountBonusPolicyProduct->saveAll($root_products);
				}

				//Policy Option Insert
				if (!empty(@$this->request->data['DiscountBonusPolicyOption'])) {
					$data_array = array();

					$option_data = array();
					foreach (@$this->request->data['DiscountBonusPolicyOption'] as $key => $val) {
						$data_array['DiscountBonusPolicyOption']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicyOption']['policy_type'] = $val['policy_type'];
						$data_array['DiscountBonusPolicyOption']['min_qty'] = $val['min_qty'];
						$data_array['DiscountBonusPolicyOption']['min_value'] = $val['min_value'];

						if (!empty($val['deduct_from_value'])) {
							$deductvalue = 1;
						} else {
							$deductvalue = 0;
						}

						$data_array['DiscountBonusPolicyOption']['deduct_from_value'] = $deductvalue;

						$data_array['DiscountBonusPolicyOption']['qty_value_flag'] = 0; //-- 0= and , 1=or
						$product_id = $this->request->data['DiscountBonusPolicyProduct']['policy_product_id'][0];
						$product_info = $this->Product->find('first', array(
							'conditions' => array('Product.id' => $product_id),
							'recursive' => -1
						));
						if ($val['min_qty_measurement_unit_id'] && $product_info['Product']['sales_measurement_unit_id'] != $val['min_qty_measurement_unit_id']) {

							$converted_qty = $this->convert_unit_to_unit($product_id, $val['min_qty_measurement_unit_id'], $product_info['Product']['sales_measurement_unit_id'], $val['min_qty']);
							$data_array['DiscountBonusPolicyOption']['min_qty_sale_unit'] = $converted_qty;
						} else {
							$data_array['DiscountBonusPolicyOption']['min_qty_sale_unit'] = $val['min_qty'];
						}
						$data_array['DiscountBonusPolicyOption']['measurement_unit_id'] = $val['min_qty_measurement_unit_id'];
						$data_array['DiscountBonusPolicyOption']['min_memo_value'] = $val['min_memo_value'];
						$data_array['DiscountBonusPolicyOption']['discount_amount'] = $val['discount_amount'];
						$data_array['DiscountBonusPolicyOption']['disccount_type'] = $val['disccount_type'];
						$data_array['DiscountBonusPolicyOption']['bonus_formula_text'] = trim($val['bonus_formula']);
						$data_array['DiscountBonusPolicyOption']['bonus_formula_text_with_product_id'] = trim($val['bonus_formula_with_product_id']);
						$for_so = 0;
						$for_sr = 0;
						$for_db = 0;
						if (@$val['create_slab_for']) {
							foreach ($val['create_slab_for'] as $ch_val) {
								if ($ch_val == 1) {
									$data_array['DiscountBonusPolicyOption']['is_so'] = 1;
									$for_so = 1;
								} elseif ($ch_val == 2) {
									$data_array['DiscountBonusPolicyOption']['is_sr'] = 1;
									$for_sr = 1;
								} elseif ($ch_val == 3) {
									$data_array['DiscountBonusPolicyOption']['is_db'] = 1;
									$data_array['DiscountBonusPolicyOption']['in_hand_discount_amount'] = $val['discount_in_hand'];
									$for_db = 1;
								}
							}
						}
						$this->DiscountBonusPolicyOption->create();
						if ($this->DiscountBonusPolicyOption->save($data_array)) {

							$policy_option_id = $this->DiscountBonusPolicyOption->getInsertID();

							//insert bonus products
							$b_data_array = array();
							$b_data_array2 = array();
							if (@$val['bonus_product_id']) {
								$m_unit_ids = $val['measurement_unit_id'];
								$bonus_qtys = $val['bonus_qty'];
								foreach ($val['bonus_product_id'] as $b_key => $b_val) {
									if ($b_val) {
										$b_data_array['DiscountBonusPolicyOptionBonusProduct']['discount_bonus_policy_id'] = $policy_id;
										$b_data_array['DiscountBonusPolicyOptionBonusProduct']['discount_bonus_policy_option_id'] = $policy_option_id;
										$b_data_array['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] = $b_val;
										$b_data_array['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'] = $bonus_qtys[$b_key];
										$b_data_array['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] = $m_unit_ids[$b_key];
										$b_data_array['DiscountBonusPolicyOptionBonusProduct']['relation'] = 0;
										if ($for_db == 1) {
											$b_data_array['DiscountBonusPolicyOptionBonusProduct']['in_hand_bonus_qty'] = $val['bonus_in_hand'][$b_key];
										}
										$b_data_array2[] = $b_data_array;
									}
								}
								$this->DiscountBonusPolicyOptionBonusProduct->saveAll($b_data_array2);
							}

							//-----------default bonus product selection insert---------\\

							$default_data_array = array();
							$default_data_array2 = array();
							if (@$val['default_bonus_product_id']) {
								foreach ($val['default_bonus_product_id'] as $b_key => $default_val) {
									if ($default_val) {
										$default_data_array['DiscountBonusPolicyDefaultBonusProductSelection']['discount_bonus_policy_id'] = $policy_id;
										$default_data_array['DiscountBonusPolicyDefaultBonusProductSelection']['discount_bonus_policy_option_id'] = $policy_option_id;
										$default_data_array['DiscountBonusPolicyDefaultBonusProductSelection']['product_id'] = $default_val;
										$default_data_array2[] = $default_data_array;
									}
								}
								$this->DiscountBonusPolicyDefaultBonusProductSelection->saveAll($default_data_array2);
							}

							//-------------end------------------\\

							if (@$val['exclusion_product_id']) {
								$ex_data_array = array();
								$ex_data_array2 = array();
								$bonus_qtys = $val['exclusion_min_qty'];
								foreach (@$val['exclusion_product_id'] as $b_key => $b_val) {
									if ($b_val) {
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['discount_bonus_policy_id'] = $policy_id;
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['discount_bonus_policy_option_id'] = $policy_option_id;
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['product_id'] = $b_val;
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['min_qty'] = $bonus_qtys[$b_key];
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['create_for'] = 1;

										$ex_data_array2[] = $ex_data_array;
									}
								}
								$this->DiscountBonusPolicyOptionExclusionInclusionProduct->saveAll($ex_data_array2);
							}
							if (@$val['inclusion_product_id']) {
								$ex_data_array = array();
								$ex_data_array2 = array();
								$bonus_qtys = $val['inclusion_min_qty'];
								foreach ($val['inclusion_product_id'] as $b_key => $b_val) {
									if ($b_val) {
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['discount_bonus_policy_id'] = $policy_id;
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['discount_bonus_policy_option_id'] = $policy_option_id;
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['product_id'] = $b_val;
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['min_qty'] = $bonus_qtys[$b_key];
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['create_for'] = 2;

										$ex_data_array2[] = $ex_data_array;
									}
								}
								$this->DiscountBonusPolicyOptionExclusionInclusionProduct->saveAll($ex_data_array2);
							}
							//insert price slab
							$data_array = array();
							$price_slab = array();
							//pr($key);

							if (@$this->request->data['DiscountBonusPolicyOptionPriceSlab'][$key]['discount_product_id']) {
								foreach (@$this->request->data['DiscountBonusPolicyOptionPriceSlab'][$key]['discount_product_id'] as $key2 => $val) {
									//if($val){
									$data_array['DiscountBonusPolicyOptionPriceSlab']['discount_bonus_policy_id'] = $policy_id;
									$data_array['DiscountBonusPolicyOptionPriceSlab']['discount_bonus_policy_option_id'] = $policy_option_id;

									$data_array['DiscountBonusPolicyOptionPriceSlab']['discount_product_id'] = $val;
									if ($for_so == 1) {
										$data_array['DiscountBonusPolicyOptionPriceSlab']['so_slab_id'] = $this->request->data['DiscountBonusPolicyOptionPriceSlab'][$key]['so_slab_id'][$key2];
									}
									if ($for_sr == 1) {
										$data_array['DiscountBonusPolicyOptionPriceSlab']['sr_slab_id'] = $this->request->data['DiscountBonusPolicyOptionPriceSlab'][$key]['sr_slab_id'][$key2];
									}
									if ($for_db == 1) {
										$data_array['DiscountBonusPolicyOptionPriceSlab']['db_slab_id'] = $this->request->data['DiscountBonusPolicyOptionPriceSlab'][$key]['db_slab_id'][$key2];
									}
									$price_slab[] = $data_array;
									//}
								}
								$this->DiscountBonusPolicyOptionPriceSlab->saveAll($price_slab);
							}
						}
					}
				}

				$this->Session->setFlash(__('The data has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
				exit;
			}
		}
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

		$this->set('page_title', 'Edit Product Setting');
		$this->DiscountBonusPolicy->id = $id;
		if (!$this->DiscountBonusPolicy->exists($id)) {
			throw new NotFoundException(__('Invalid Setting'));
		}
		$this->loadModel('DiscountBonusPolicyFile');
		$this->loadModel('Product');
		$products = $this->Product->find('list', array('order' => array('Product.order' => 'ASC')));
		$this->set(compact('products'));

		$office_parent_id = $this->UserAuth->getOfficeParentId();
		$this->set('office_parent_id', $office_parent_id);
		if ($office_parent_id == 0) {
			$office_conditions = array(
				'office_type_id' => 2,
				"NOT" => array("id" => array(30, 31, 37))
			);
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}
		$this->set('office_id', $this->UserAuth->getOfficeId());
		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$this->set(compact('offices'));

		$outlet_categories = $this->OutletCategory->find('list', array('conditions' => array('is_active' => 1)));
		$this->set(compact('outlet_categories'));
		$sr_outlet_categories = $this->DistOutletCategory->find('list', array('conditions' => array('is_active' => 1)));
		$this->set(compact('sr_outlet_categories'));
		$outlet_groups = array();
		$o_con = array('OutletGroup.is_distributor' => 0);
		$outlet_groups = $this->OutletGroup->find('list', array('conditions' => $o_con, 'order' => array('name' => 'asc')));
		$o_con = array('OutletGroup.is_distributor' => 1);
		$sr_outlet_groups = $this->OutletGroup->find('list', array('conditions' => $o_con, 'order' => array('name' => 'asc')));
		//pr($outlet_groups);exit;

		$this->set(compact('outlet_groups', 'sr_outlet_groups'));

		$this->loadModel('Product');
		$products = $this->Product->find('list', array('order' => array('Product.order' => 'ASC')));
		$this->html = '';
		foreach ($products as $key => $val) {
			$this->html .= '<option value="' . $key . '">' . addslashes($val) . '</option>';
		}
		$product_list = $this->html;
		$this->set(compact('products', 'product_list'));

		$policy_types = array(
			'0' => 'Only Discount',
			'1' => 'Only Bonus',
			'2' => 'Discount and Bonus',
			'3' => 'Discount or Bonus',
		);
		$this->set(compact('policy_types'));

		$disccount_types = array(
			'0' => '%',
			'1' => 'Tk'
		);
		$this->set(compact('disccount_types'));


		if ($this->request->is('post') || $this->request->is('put')) {

			foreach (@$this->request->data['DiscountBonusPolicyOption'] as $key => $val) {
				$min_qty_measurement_unit_id = $val['min_qty_measurement_unit_id'];
				if ($min_qty_measurement_unit_id == '') {
					$this->Session->setFlash(__('Measurement Unit is null. Please, select Measurement Unit.'), 'flash/error');
					$url = 'edit/' . $id;
					$this->redirect(array('action' => $url));
				}
			}


			$data_policy = array();
			$data_policy['DiscountBonusPolicy']['id'] = $id;
			$data_policy['DiscountBonusPolicy']['start_date'] = date('Y-m-d', strtotime($this->request->data['DiscountBonusPolicy']['start_date']));
			$data_policy['DiscountBonusPolicy']['end_date'] = date('Y-m-d', strtotime($this->request->data['DiscountBonusPolicy']['end_date']));
			$data_policy['DiscountBonusPolicy']['name'] = $this->request->data['DiscountBonusPolicy']['name'];
			$data_policy['DiscountBonusPolicy']['remarks'] = $this->request->data['DiscountBonusPolicy']['remarks'];

			$data_policy['DiscountBonusPolicy']['updated_at'] = $this->current_datetime();
			$data_policy['DiscountBonusPolicy']['updated_by'] = $this->UserAuth->getUserId();
			$data_policy['DiscountBonusPolicy']['numbering'] = $this->request->data['DiscountBonusPolicy']['numbering'];

			$is_so = 0;
			$is_sr = 0;
			if (@$this->request->data['DiscountBonusPolicy']['create_policy_for']) {
				foreach (@$this->request->data['DiscountBonusPolicy']['create_policy_for'] as $ch_val) {
					if ($ch_val == 1) {
						$data_policy['DiscountBonusPolicy']['is_so'] = 1;
						$is_so = 1;
					} elseif ($ch_val == 2) {
						$data_policy['DiscountBonusPolicy']['is_sr'] = 1;
						$is_sr = 1;
					} elseif ($ch_val == 3) {
						$data_policy['DiscountBonusPolicy']['is_db'] = 1;
					}
				}
			}

			//$this->DiscountBonusPolicy->create();
			if ($this->DiscountBonusPolicy->save($data_policy)) {

				//-------------------file-------------\\
				$bonusfile = $this->request->data['DiscountBonusPolicy']['bonus_file'];
				$discount_bonus_policy_file_id = $this->request->data['DiscountBonusPolicy']['discount_bonus_policy_file_id'];

				if (!empty($discount_bonus_policy_file_id)) {
					$this->DiscountBonusPolicyFile->query("DELETE  FROM discount_bonus_policy_files WHERE id IN ($discount_bonus_policy_file_id) ");
				}
				if (!empty($bonusfile)) {
					foreach ($bonusfile as $file_val) {
						$file = $file_val;
						$ext = substr(strtolower(strrchr($file['name'], '.')), 1);
						$image_name = rand(10000, 99999) . '.' . $ext;
						$arr_ext = array('jpg', 'jpeg', 'png', 'docx', 'pdf', 'xls', 'csv', 'xlsx');

						if (in_array($ext, $arr_ext)) {
							$maxDimW = 512;
							$maxDimH = 512;
							list($width, $height, $type, $attr) = getimagesize($file['tmp_name']);
							move_uploaded_file($file['tmp_name'], WWW_ROOT . 'bonus_policy' . DS . $image_name);
							//prepare the filename for database entry
							$bonusdata['DiscountBonusPolicyFile']['discount_bonus_policy_id'] = $id;
							$bonusdata['DiscountBonusPolicyFile']['file_name'] = $image_name;
							$bonusdata['DiscountBonusPolicyFile']['updated_at'] = $this->current_datetime();
							$bonusdata['DiscountBonusPolicyFile']['updated_by'] = $this->UserAuth->getUserId();
							$databonus[] = $bonusdata;
						}
					}

					if (!empty($databonus)) {
						$this->DiscountBonusPolicyFile->saveAll($databonus);
					}
				}
				//-------------------end----------------\\



				$policy_id = $id;

				$this->DiscountBonusPolicySetting->deleteAll(array('DiscountBonusPolicySetting.discount_bonus_policy_id' => $id));
				//Outlet Category Insert
				if (!empty(@$this->request->data['DiscountBonusPolicySpecialGroup'])) {
					if (@$this->request->data['DiscountBonusPolicySpecialGroup']['so_special_group_id'] && $is_so == 1) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 1; //1=so
						$data_array['DiscountBonusPolicySetting']['create_for'] = 1; //1=special group id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicySpecialGroup']['so_special_group_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
					if (@$this->request->data['DiscountBonusPolicySpecialGroup']['sr_special_group_id'] && $is_sr == 1) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 2; //2=sr
						$data_array['DiscountBonusPolicySetting']['create_for'] = 1; //1=special group id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicySpecialGroup']['sr_special_group_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
				}

				if (!empty(@$this->request->data['DiscountBonusPolicyToOffice'])) {
					if (@$this->request->data['DiscountBonusPolicyToOffice']['so_office_id'] && $is_so == 1) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 1; //1=so
						$data_array['DiscountBonusPolicySetting']['create_for'] = 2; //2=office
						$office_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToOffice']['so_office_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$office_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($office_data);
					}


					/*---- for SR --------*/
					if (@$this->request->data['DiscountBonusPolicyToOffice']['sr_office_id'] && $is_sr == 1) {

						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 2; //2=sr
						$data_array['DiscountBonusPolicySetting']['create_for'] = 2; //2=office
						$office_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToOffice']['sr_office_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$office_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($office_data);
					}
				}


				//Outlet Group Insert
				if (!empty(@$this->request->data['DiscountBonusPolicyToOutletGroup'])) {
					if (@$this->request->data['DiscountBonusPolicyToOutletGroup']['so_outlet_group_id'] && $is_so == 1) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 1; //1=so
						$data_array['DiscountBonusPolicySetting']['create_for'] = 3; //3=outlet group id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToOutletGroup']['so_outlet_group_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
					if (@$this->request->data['DiscountBonusPolicyToOutletGroup']['sr_outlet_group_id'] && $is_sr == 1) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 2; //2=sr
						$data_array['DiscountBonusPolicySetting']['create_for'] = 3; //3=outlet group id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToOutletGroup']['sr_outlet_group_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
				}

				//Excluding Outlet Group Insert
				if (!empty(@$this->request->data['DiscountBonusPolicyToExcludingOutletGroup'])) {
					if (@$this->request->data['DiscountBonusPolicyToExcludingOutletGroup']['so_excluding_outlet_group_id'] && $is_so == 1) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 1; //1=so
						$data_array['DiscountBonusPolicySetting']['create_for'] = 5; //5=Excluding outlet group id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToExcludingOutletGroup']['so_excluding_outlet_group_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
					if (@$this->request->data['DiscountBonusPolicyToExcludingOutletGroup']['sr_excluding_outlet_group_id'] && $is_sr == 1) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 2; //2=sr
						$data_array['DiscountBonusPolicySetting']['create_for'] = 5; //5=Excluding outlet group id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToExcludingOutletGroup']['sr_excluding_outlet_group_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
				}
				//Outlet Category Insert
				if (!empty(@$this->request->data['DiscountBonusPolicyToOutletCategory'])) {
					if (@$this->request->data['DiscountBonusPolicyToOutletCategory']['so_outlet_category_id'] && $is_so == 1 && @$this->request->data['DiscountBonusPolicyToOffice']['so_office_id']) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 1; //1=so
						$data_array['DiscountBonusPolicySetting']['create_for'] = 4; //4=outlet category id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToOutletCategory']['so_outlet_category_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
					if (@$this->request->data['DiscountBonusPolicyToOutletCategory']['sr_outlet_category_id'] && $is_sr == 1 && @$this->request->data['DiscountBonusPolicyToOffice']['sr_office_id']) {
						$data_array = array();
						$data_array['DiscountBonusPolicySetting']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicySetting']['for_so_sr'] = 2; //2=sr
						$data_array['DiscountBonusPolicySetting']['create_for'] = 4; //4=outlet category id
						$outlet_group_data = array();
						foreach ($this->request->data['DiscountBonusPolicyToOutletCategory']['sr_outlet_category_id'] as $key => $val) {
							if ($val) $data_array['DiscountBonusPolicySetting']['reffrence_id'] = $val;
							$outlet_group_data[] = $data_array;
						}
						$this->DiscountBonusPolicySetting->saveAll($outlet_group_data);
					}
				}


				//Policy Product Insert
				if (!empty(@$this->request->data['DiscountBonusPolicyProduct'])) {
					$this->DiscountBonusPolicyProduct->deleteAll(array('DiscountBonusPolicyProduct.discount_bonus_policy_id' => $id));
					$data_array = array();
					$data_array['DiscountBonusPolicyProduct']['discount_bonus_policy_id'] = $policy_id;
					$root_products = array();
					foreach ($this->request->data['DiscountBonusPolicyProduct']['policy_product_id'] as $key => $val) {
						if ($val) $data_array['DiscountBonusPolicyProduct']['product_id'] = $val;
						$root_products[] = $data_array;
					}
					$this->DiscountBonusPolicyProduct->saveAll($root_products);
				}

				//Policy Option Insert
				if (!empty(@$this->request->data['DiscountBonusPolicyOption'])) {

					$this->DiscountBonusPolicyOption->deleteAll(array('DiscountBonusPolicyOption.discount_bonus_policy_id' => $id));

					$this->DiscountBonusPolicyOptionPriceSlab->deleteAll(array('DiscountBonusPolicyOptionPriceSlab.discount_bonus_policy_id' => $id));

					$this->DiscountBonusPolicyOptionBonusProduct->deleteAll(array('DiscountBonusPolicyOptionBonusProduct.discount_bonus_policy_id' => $id));
					$this->DiscountBonusPolicyDefaultBonusProductSelection->deleteAll(array('DiscountBonusPolicyDefaultBonusProductSelection.discount_bonus_policy_id' => $id));
					$this->DiscountBonusPolicyOptionExclusionInclusionProduct->deleteAll(array('DiscountBonusPolicyOptionExclusionInclusionProduct.discount_bonus_policy_id' => $id));
					$data_array = array();
					$product_id = $this->request->data['DiscountBonusPolicyProduct']['policy_product_id'][0];
					$product_info = $this->Product->find('first', array(
						'conditions' => array('Product.id' => $product_id),
						'recursive' => -1
					));

					$option_data = array();
					foreach (@$this->request->data['DiscountBonusPolicyOption'] as $key => $val) {
						$data_array['DiscountBonusPolicyOption']['discount_bonus_policy_id'] = $policy_id;
						$data_array['DiscountBonusPolicyOption']['policy_type'] = $val['policy_type'];
						$data_array['DiscountBonusPolicyOption']['min_qty'] = $val['min_qty'];
						$data_array['DiscountBonusPolicyOption']['min_value'] = $val['min_value'];

						if (!empty($val['deduct_from_value'])) {
							$deductvalue = 1;
						} else {
							$deductvalue = 0;
						}

						$data_array['DiscountBonusPolicyOption']['deduct_from_value'] = $deductvalue;


						$data_array['DiscountBonusPolicyOption']['qty_value_flag'] = 0; //-- 0= and , 1=or
						if ($val['min_qty_measurement_unit_id'] && $product_info['Product']['sales_measurement_unit_id'] != $val['min_qty_measurement_unit_id']) {


							$converted_qty = $this->convert_unit_to_unit($product_id, $val['min_qty_measurement_unit_id'], $product_info['Product']['sales_measurement_unit_id'], $val['min_qty']);
							$data_array['DiscountBonusPolicyOption']['min_qty_sale_unit'] = $converted_qty;
						} else {
							$data_array['DiscountBonusPolicyOption']['min_qty_sale_unit'] = $val['min_qty'];
						}
						$data_array['DiscountBonusPolicyOption']['measurement_unit_id'] = $val['min_qty_measurement_unit_id'];
						$data_array['DiscountBonusPolicyOption']['min_memo_value'] = $val['min_memo_value'];
						$data_array['DiscountBonusPolicyOption']['discount_amount'] = $val['discount_amount'];
						$data_array['DiscountBonusPolicyOption']['disccount_type'] = $val['disccount_type'];
						$data_array['DiscountBonusPolicyOption']['bonus_formula_text'] = trim($val['bonus_formula']);
						$data_array['DiscountBonusPolicyOption']['bonus_formula_text_with_product_id'] = trim($val['bonus_formula_with_product_id']);
						$for_so = 0;
						$for_sr = 0;
						$for_db = 0;
						if (@$val['create_slab_for']) {
							foreach ($val['create_slab_for'] as $ch_val) {
								if ($ch_val == 1) {
									$data_array['DiscountBonusPolicyOption']['is_so'] = 1;
									$for_so = 1;
								} elseif ($ch_val == 2) {
									$data_array['DiscountBonusPolicyOption']['is_sr'] = 1;
									$for_sr = 1;
								} elseif ($ch_val == 3) {
									$data_array['DiscountBonusPolicyOption']['is_db'] = 1;
									$data_array['DiscountBonusPolicyOption']['in_hand_discount_amount'] = $val['discount_in_hand'];
									$for_db = 1;
								}
							}
						}
						$this->DiscountBonusPolicyOption->create();
						if (@$this->DiscountBonusPolicyOption->save($data_array)) {

							$policy_option_id = $this->DiscountBonusPolicyOption->getInsertID();

							//insert bonus products
							$b_data_array = array();
							$b_data_array2 = array();
							if (@$val['bonus_product_id']) {
								$m_unit_ids = $val['measurement_unit_id'];
								$bonus_qtys = $val['bonus_qty'];
								foreach ($val['bonus_product_id'] as $b_key => $b_val) {
									if ($b_val) {
										$b_data_array['DiscountBonusPolicyOptionBonusProduct']['discount_bonus_policy_id'] = $policy_id;
										$b_data_array['DiscountBonusPolicyOptionBonusProduct']['discount_bonus_policy_option_id'] = $policy_option_id;
										$b_data_array['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] = $b_val;
										$b_data_array['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'] = $bonus_qtys[$b_key];
										$b_data_array['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] = $m_unit_ids[$b_key];
										$b_data_array['DiscountBonusPolicyOptionBonusProduct']['relation'] = 0;
										if ($for_db == 1) {
											$b_data_array['DiscountBonusPolicyOptionBonusProduct']['in_hand_bonus_qty'] = $val['bonus_in_hand'][$b_key];
										}
										$b_data_array2[] = $b_data_array;
									}
								}
								$this->DiscountBonusPolicyOptionBonusProduct->saveAll($b_data_array2);
							}

							//-----------default bonus product selection insert---------\\

							$default_data_array = array();
							$default_data_array2 = array();
							if (@$val['default_bonus_product_id']) {
								foreach ($val['default_bonus_product_id'] as $b_key => $default_val) {
									if ($default_val) {
										$default_data_array['DiscountBonusPolicyDefaultBonusProductSelection']['discount_bonus_policy_id'] = $policy_id;
										$default_data_array['DiscountBonusPolicyDefaultBonusProductSelection']['discount_bonus_policy_option_id'] = $policy_option_id;
										$default_data_array['DiscountBonusPolicyDefaultBonusProductSelection']['product_id'] = $default_val;
										$default_data_array2[] = $default_data_array;
									}
								}
								$this->DiscountBonusPolicyDefaultBonusProductSelection->saveAll($default_data_array2);
							}

							if (@$val['exclusion_product_id']) {
								$ex_data_array = array();
								$ex_data_array2 = array();
								$bonus_qtys = $val['exclusion_min_qty'];
								foreach ($val['exclusion_product_id'] as $b_key => $b_val) {
									if ($b_val) {
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['discount_bonus_policy_id'] = $policy_id;
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['discount_bonus_policy_option_id'] = $policy_option_id;
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['product_id'] = $b_val;
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['min_qty'] = $bonus_qtys[$b_key];
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['create_for'] = 1;

										$ex_data_array2[] = $ex_data_array;
									}
								}
								$this->DiscountBonusPolicyOptionExclusionInclusionProduct->saveAll($ex_data_array2);
							}
							if (@$val['inclusion_product_id']) {
								$ex_data_array = array();
								$ex_data_array2 = array();
								$bonus_qtys = $val['inclusion_min_qty'];
								foreach ($val['inclusion_product_id'] as $b_key => $b_val) {
									if ($b_val) {
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['discount_bonus_policy_id'] = $policy_id;
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['discount_bonus_policy_option_id'] = $policy_option_id;
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['product_id'] = $b_val;
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['min_qty'] = $bonus_qtys[$b_key];
										$ex_data_array['DiscountBonusPolicyOptionExclusionInclusionProduct']['create_for'] = 2;

										$ex_data_array2[] = $ex_data_array;
									}
								}
								$this->DiscountBonusPolicyOptionExclusionInclusionProduct->saveAll($ex_data_array2);
							}
							//insert price slab
							$data_array = array();
							$price_slab = array();
							//pr($key);

							if (@$this->request->data['DiscountBonusPolicyOptionPriceSlab'][$key]['discount_product_id']) {
								foreach ($this->request->data['DiscountBonusPolicyOptionPriceSlab'][$key]['discount_product_id'] as $key2 => $val) {
									//if($val){
									$data_array['DiscountBonusPolicyOptionPriceSlab']['discount_bonus_policy_id'] = $policy_id;
									$data_array['DiscountBonusPolicyOptionPriceSlab']['discount_bonus_policy_option_id'] = $policy_option_id;

									$data_array['DiscountBonusPolicyOptionPriceSlab']['discount_product_id'] = $val;
									if ($for_so == 1) {
										$data_array['DiscountBonusPolicyOptionPriceSlab']['so_slab_id'] = $this->request->data['DiscountBonusPolicyOptionPriceSlab'][$key]['so_slab_id'][$key2];
									}
									if ($for_sr == 1) {
										$data_array['DiscountBonusPolicyOptionPriceSlab']['sr_slab_id'] = $this->request->data['DiscountBonusPolicyOptionPriceSlab'][$key]['sr_slab_id'][$key2];
									}
									if ($for_db == 1) {
										$data_array['DiscountBonusPolicyOptionPriceSlab']['db_slab_id'] = $this->request->data['DiscountBonusPolicyOptionPriceSlab'][$key]['db_slab_id'][$key2];
									}
									$price_slab[] = $data_array;
									//}
								}
								$this->DiscountBonusPolicyOptionPriceSlab->saveAll($price_slab);
							}
						}
					}
				}

				$this->Session->setFlash(__('The request has been update'), 'flash/success');
				$this->redirect(array('action' => 'index'));
				exit;
			}
		} else {
			$this->DiscountBonusPolicy->unbindModel(array(
				'hasMany' => array(

					'DiscountBonusPolicyOptionSr',
					'DiscountBonusPolicyOptionDB',
					'DiscountBonusPolicyOptionSo',
				),
			));
			$options = array(
				'conditions' => array(
					'DiscountBonusPolicy.' . $this->DiscountBonusPolicy->primaryKey => $id
				),
				'recursive' => 2
			);

			$this->request->data = $this->DiscountBonusPolicy->find('first', $options);
			$this->request->data['DiscountBonusPolicy']['start_date'] = date('d-m-Y', strtotime($this->request->data['DiscountBonusPolicy']['start_date']));
			$this->request->data['DiscountBonusPolicy']['end_date'] = date('d-m-Y', strtotime($this->request->data['DiscountBonusPolicy']['end_date']));
			// pr($this->request->data);
			// exit;	
			$selected_create_for = array();
			if ($this->request->data['DiscountBonusPolicy']['is_so']) {
				$selected_create_for[] = 1;
			}
			if ($this->request->data['DiscountBonusPolicy']['is_sr']) {
				$selected_create_for[] = 2;
			}
			if ($this->request->data['DiscountBonusPolicy']['is_db']) {
				$selected_create_for[] = 3;
			}
			$this->set(compact('selected_create_for'));
			$so_special_group_ids = array();
			foreach ($this->request->data['DiscountBonusPolicyToSpecialGroupSo'] as $key => $val) {
				array_push($so_special_group_ids, $val['reffrence_id']);
			}
			$sr_special_group_ids = array();
			foreach ($this->request->data['DiscountBonusPolicyToSpecialGroupSr'] as $key => $val) {
				array_push($sr_special_group_ids, $val['reffrence_id']);
			}
			$this->set(compact('so_special_group_ids', 'sr_special_group_ids'));

			$so_office_ids = array();
			foreach ($this->request->data['DiscountBonusPolicyToOfficeSo'] as $key => $val) {
				array_push($so_office_ids, $val['reffrence_id']);
			}
			$sr_office_ids = array();
			foreach ($this->request->data['DiscountBonusPolicyToOfficeSr'] as $key => $val) {
				array_push($sr_office_ids, $val['reffrence_id']);
			}
			$this->set(compact('so_office_ids', 'sr_office_ids'));

			$so_outlet_group_ids = array();
			foreach ($this->request->data['DiscountBonusPolicyToOutletGroupSo'] as $key => $val) {
				array_push($so_outlet_group_ids, $val['reffrence_id']);
			}
			$sr_outlet_group_ids = array();
			foreach ($this->request->data['DiscountBonusPolicyToOutletGroupSr'] as $key => $val) {
				array_push($sr_outlet_group_ids, $val['reffrence_id']);
			}
			$this->set(compact('so_outlet_group_ids', 'sr_outlet_group_ids'));

			$so_excluding_outlet_group_ids = array();
			foreach ($this->request->data['DiscountBonusPolicyToExcludingOutletGroupSo'] as $key => $val) {
				array_push($so_excluding_outlet_group_ids, $val['reffrence_id']);
			}
			$sr_excluding_outlet_group_ids = array();
			foreach ($this->request->data['DiscountBonusPolicyToExcludingOutletGroupSr'] as $key => $val) {
				array_push($sr_excluding_outlet_group_ids, $val['reffrence_id']);
			}
			$this->set(compact('so_excluding_outlet_group_ids', 'sr_excluding_outlet_group_ids'));

			$so_outlet_category_ids = array();
			foreach ($this->request->data['DiscountBonusPolicyToOutletCategorySo'] as $key => $val) {
				array_push($so_outlet_category_ids, $val['reffrence_id']);
			}
			$sr_outlet_category_ids = array();
			foreach ($this->request->data['DiscountBonusPolicyToOutletCategorySr'] as $key => $val) {
				array_push($sr_outlet_category_ids, $val['reffrence_id']);
			}
			$this->set(compact('so_outlet_category_ids', 'sr_outlet_category_ids'));
			$so_special_group = $this->SpecialGroup->find('list', array(
				'conditions' => array('SpecialGroup.is_dist' => 0, 'SpecialGroup.end_date >=' => date('Y-m-d', strtotime($this->request->data['DiscountBonusPolicy']['start_date']))),
				'fields' => array('SpecialGroup.id', 'SpecialGroup.name'),
				'order' => array('SpecialGroup.id'),
				'recursive' => -1
			));

			$sr_special_group = $this->SpecialGroup->find('list', array(
				'conditions' => array('SpecialGroup.is_dist' => 1, 'SpecialGroup.end_date >=' => date('Y-m-d', strtotime($this->request->data['DiscountBonusPolicy']['start_date']))),
				'fields' => array('SpecialGroup.id', 'SpecialGroup.name'),
				'order' => array('SpecialGroup.id'),
				'recursive' => -1
			));
			$this->set(compact('so_special_group', 'sr_special_group'));
			//pr($this->request->data);
			//pr($this->request->data['DiscountBonusPolicyOption']);
			//exit;

			$dbpfile_list = $this->DiscountBonusPolicyFile->find('all', array(
				'conditions' => array(
					'DiscountBonusPolicyFile.discount_bonus_policy_id' => $id,
				),
				'fields' => array('DiscountBonusPolicyFile.id', 'DiscountBonusPolicyFile.file_name'),
				'recursive' => -1
			));
			//pr($dbpfile_list);exit;
			$this->set(compact('dbpfile_list'));
		}
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
		$this->DiscountBonusPolicy->id = $id;
		if (!$this->DiscountBonusPolicy->exists()) {
			throw new NotFoundException(__('Invalid Data!'));
		}

		$this->loadModel('DiscountBonusPolicyFile');

		if ($this->DiscountBonusPolicy->delete()) {

			$this->DiscountBonusPolicySetting->deleteAll(array('DiscountBonusPolicySetting.discount_bonus_policy_id' => $id));
			$this->DiscountBonusPolicyProduct->deleteAll(array('DiscountBonusPolicyProduct.discount_bonus_policy_id' => $id));
			$this->DiscountBonusPolicyOption->deleteAll(array('DiscountBonusPolicyOption.discount_bonus_policy_id' => $id));
			$this->DiscountBonusPolicyFile->deleteAll(array('DiscountBonusPolicyFile.discount_bonus_policy_id' => $id));

			$this->DiscountBonusPolicyOptionPriceSlab->deleteAll(array('DiscountBonusPolicyOptionPriceSlab.discount_bonus_policy_id' => $id));

			$this->DiscountBonusPolicyOptionBonusProduct->deleteAll(array('DiscountBonusPolicyOptionBonusProduct.discount_bonus_policy_id' => $id));
			$this->DiscountBonusPolicyOptionExclusionInclusionProduct->deleteAll(array('DiscountBonusPolicyOptionExclusionInclusionProduct.discount_bonus_policy_id' => $id));

			$this->Session->setFlash(__('Deleted successfully!'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('List was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}


	public function admin_get_slab_list()
	{
		$this->loadModel('ProductPricesV2');
		$this->loadModel('ProductCombinationsV2');
		$effective_date = date('Y-m-d', strtotime($this->request->data['effective_date']));
		$product_id = $this->request->data['product_id'];

		/*------- start get prev effective date and next effective date ---------*/
		$product_price_id = $this->ProductPricesV2->find(
			'first',
			array(
				'conditions' => array(
					'ProductPricesV2.product_id' => $product_id,
					'ProductPricesV2.effective_date <=' => $effective_date,
					'ProductPricesV2.institute_id' => 0,
					'ProductPricesV2.has_combination' => 0,
					'ProductPriceSectionV2.is_so' => 1
				),
				'joins' => array(
					array(
						'table' => 'product_price_section_v2',
						'alias' => 'ProductPriceSectionV2',
						'conditions' => 'ProductPriceSectionV2.product_price_id=ProductPricesV2.id'
					),
				),
				'order' => array('ProductPricesV2.effective_date desc'),
				'recursive' => -1
			)
		);
		if ($product_price_id)
			$price_id = $product_price_id['ProductPricesV2']['id'];
		else
			$price_id = 0;

		$result = array();
		$condition_value['ProductPriceSectionV2.is_so'] = 1;
		$condition_value['ProductCombinationsV2.price >'] = 0;
		$condition_value['ProductCombinationsV2.product_price_id'] = $price_id;
		$condition_value['ProductCombinationsV2.product_id'] = $product_id;
		$condition_value['ProductCombinationsV2.combination_id'] = 0;
		/*------- end get prev effective date and next effective date ---------*/


		if (!empty($product_id)) {
			$slab_list = $this->ProductCombinationsV2->find('all', array(
				'conditions' => array($condition_value),
				'joins' => array(
					array(
						'table' => 'product_price_section_v2',
						'alias' => 'ProductPriceSectionV2',
						'conditions' => 'ProductPriceSectionV2.id=ProductCombinationsV2.section_id'
					),
					array(
						'table' => 'product_price_other_for_slabs_v2',
						'alias' => 'ProductPriceOtherSlabs',
						'conditions' => 'ProductCombinationsV2.id=ProductPriceOtherSlabs.product_combination_id',
						'type' => 'left'
					)
				),
				'group' => array('ProductCombinationsV2.id', 'ProductCombinationsV2.effective_date', 'ProductCombinationsV2.min_qty'),
				'fields' => array('ProductCombinationsV2.id', 'ProductCombinationsV2.effective_date', 'ProductCombinationsV2.min_qty'),
				'recursive' => -1
			));
		} else {
			$slab_list = '';
		}


		$html = '<option value="">---- SO Slab ----</option>';
		foreach ($slab_list as $key => $val) {

			$html = $html . '<option value="' . $val['ProductCombinationsV2']['id'] . '">' . $val['ProductCombinationsV2']['min_qty'] . ' (' . $val['ProductCombinationsV2']['effective_date'] . ') </option>';
		}
		$result['so'] = $html;

		$product_price_id = $this->ProductPricesV2->find(
			'first',
			array(
				'conditions' => array(
					'ProductPricesV2.product_id' => $product_id,
					'ProductPricesV2.effective_date <=' => $effective_date,
					'ProductPricesV2.institute_id' => 0,
					'ProductPricesV2.has_combination' => 0,
					'ProductPriceSectionV2.is_sr' => 1
				),
				'joins' => array(
					array(
						'table' => 'product_price_section_v2',
						'alias' => 'ProductPriceSectionV2',
						'conditions' => 'ProductPriceSectionV2.product_price_id=ProductPricesV2.id'
					),
				),
				'order' => array('ProductPricesV2.effective_date desc'),
				'recursive' => -1
			)
		);
		if ($product_price_id)
			$price_id = $product_price_id['ProductPricesV2']['id'];
		else
			$price_id = 0;

		unset($condition_value['ProductCombinationsV2.product_price_id']);
		unset($condition_value['ProductPriceSectionV2.is_so']);
		unset($condition_value['ProductCombinationsV2.price >']);

		$condition_value['ProductCombinationsV2.product_price_id'] = $price_id;
		$condition_value['ProductPriceSectionV2.is_sr'] = 1;
		$condition_value['ProductCombinationsV2.sr_price >'] = 0;
		if (!empty($product_id)) {
			$slab_list = $this->ProductCombinationsV2->find('all', array(
				'conditions' => array($condition_value),
				'joins' => array(
					array(
						'table' => 'product_price_section_v2',
						'alias' => 'ProductPriceSectionV2',
						'conditions' => 'ProductPriceSectionV2.id=ProductCombinationsV2.section_id'
					),
					array(
						'table' => 'product_price_other_for_slabs_v2',
						'alias' => 'ProductPriceOtherSlabs',
						'conditions' => 'ProductCombinationsV2.id=ProductPriceOtherSlabs.product_combination_id',
						'type' => 'left'
					)
				),
				'group' => array('ProductCombinationsV2.id', 'ProductCombinationsV2.effective_date', 'ProductCombinationsV2.min_qty'),
				'fields' => array('ProductCombinationsV2.id', 'ProductCombinationsV2.effective_date', 'ProductCombinationsV2.min_qty'),
				'recursive' => -1
			));
		} else {
			$slab_list = '';
		}


		$html = '<option value="">---- SR Slab ----</option>';
		foreach ($slab_list as $key => $val) {

			$html = $html . '<option value="' . $val['ProductCombinationsV2']['id'] . '">' . $val['ProductCombinationsV2']['min_qty'] . ' (' . $val['ProductCombinationsV2']['effective_date'] . ') </option>';
		}
		$result['sr'] = $html;

		$product_price_id = $this->ProductPricesV2->find(
			'first',
			array(
				'conditions' => array(
					'ProductPricesV2.product_id' => $product_id,
					'ProductPricesV2.effective_date <=' => $effective_date,
					'ProductPricesV2.institute_id' => 0,
					'ProductPricesV2.has_combination' => 0,
					'ProductPriceSectionV2.is_db' => 1
				),
				'joins' => array(
					array(
						'table' => 'product_price_section_v2',
						'alias' => 'ProductPriceSectionV2',
						'conditions' => 'ProductPriceSectionV2.product_price_id=ProductPricesV2.id'
					),
				),
				'order' => array('ProductPricesV2.effective_date desc'),
				'recursive' => -1
			)
		);
		if ($product_price_id)
			$price_id = $product_price_id['ProductPricesV2']['id'];
		else
			$price_id = 0;

		unset($condition_value['ProductCombinationsV2.product_price_id']);
		unset($condition_value['ProductPriceSectionV2.is_so']);
		unset($condition_value['ProductCombinationsV2.price >']);
		unset($condition_value['ProductPriceSectionV2.is_sr']);
		$condition_value['ProductCombinationsV2.product_price_id'] = $price_id;
		$condition_value['ProductPriceSectionV2.is_db'] = 1;
		if (!empty($product_id)) {
			$slab_list = $this->ProductCombinationsV2->find('all', array(
				'conditions' => array($condition_value),
				'joins' => array(
					array(
						'table' => 'product_price_section_v2',
						'alias' => 'ProductPriceSectionV2',
						'conditions' => 'ProductPriceSectionV2.id=ProductCombinationsV2.section_id'
					),
					array(
						'table' => 'product_price_other_for_slabs_v2',
						'alias' => 'ProductPriceOtherSlabs',
						'conditions' => 'ProductCombinationsV2.id=ProductPriceOtherSlabs.product_combination_id',
						'type' => 'left'
					)
				),
				'group' => array('ProductCombinationsV2.id', 'ProductCombinationsV2.effective_date', 'ProductCombinationsV2.min_qty'),
				'fields' => array('ProductCombinationsV2.id', 'ProductCombinationsV2.effective_date', 'ProductCombinationsV2.min_qty'),
				'recursive' => -1
			));
		} else {
			$slab_list = '';
		}


		$html = '<option value="">---- DB Slab ----</option>';
		foreach ($slab_list as $key => $val) {

			$html = $html . '<option value="' . $val['ProductCombinationsV2']['id'] . '">' . $val['ProductCombinationsV2']['min_qty'] . ' (' . $val['ProductCombinationsV2']['effective_date'] . ') </option>';
		}
		$result['db'] = $html;
		echo json_encode($result);
		$this->autoRender = false;
	}


	public function admin_get_product_units()
	{
		$this->loadModel('ProductMeasurement');

		$product_id = $this->request->data['product_id'];

		$this->loadModel('Product');
		$product_info = $this->Product->find('first', array(
			'conditions' => array(
				'Product.id' => $product_id,
			),
			'joins' => array(
				array(
					'alias' => 'MeasurementUnit',
					'table' => 'measurement_units',
					'type' => 'INNER',
					'conditions' => 'Product.base_measurement_unit_id = MeasurementUnit.id'
				),
			),
			'fields' => array('MeasurementUnit.id', 'MeasurementUnit.name'),
			'recursive' => -1
		));


		$conditions = array('ProductMeasurement.product_id' => $product_id);
		$unit_info = $this->ProductMeasurement->find('all', array(
			'fields' => array('ProductMeasurement.measurement_unit_id', 'MeasurementUnit.name'),
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
		//exit;

		$parent_unit_id = isset($this->request->data['parent_unit_id']) ? $this->request->data['parent_unit_id'] : 0;

		//$html = '<option value="">---- Select Unit ----</option>';
		$html = '';

		//exit;
		foreach ($product_info as $key => $val) {
			//pr($val);
			if ($parent_unit_id == $val['id']) {
				$html = $html . '<option selected value="' . $val['id'] . '">' . $val['name'] . '</option>';
			} else {
				$html = $html . '<option value="' . $val['id'] . '">' . $val['name'] . '</option>';
			}
		}

		foreach ($unit_info as $key => $val) {
			if ($parent_unit_id == $val['ProductMeasurement']['measurement_unit_id']) {
				$html = $html . '<option selected value="' . $val['ProductMeasurement']['measurement_unit_id'] . '">' . $val['MeasurementUnit']['name'] . '</option>';
			} else {
				$html = $html . '<option value="' . $val['ProductMeasurement']['measurement_unit_id'] . '">' . $val['MeasurementUnit']['name'] . '</option>';
			}
		}
		echo $html;
		$this->autoRender = false;
	}
	public function get_product_units_for_min_qty()
	{
		$this->loadModel('ProductMeasurement');

		$product_id = $this->request->data['product_id'];
		$this->loadModel('Product');
		$product_info = $this->Product->find('all', array(
			'conditions' => array(
				'Product.id' => $product_id,
			),
			'joins' => array(
				array(
					'alias' => 'MeasurementUnit',
					'table' => 'measurement_units',
					'type' => 'INNER',
					'conditions' => 'Product.base_measurement_unit_id = MeasurementUnit.id'
				),
				array(
					'alias' => 'ProductMeasurement',
					'table' => 'product_measurements',
					'type' => 'left',
					'conditions' => 'Product.sales_measurement_unit_id = ProductMeasurement.measurement_unit_id and Product.id=ProductMeasurement.product_id'
				),
			),
			'group' => array('MeasurementUnit.id', 'MeasurementUnit.name', 'ProductMeasurement.measurement_unit_id', 'ProductMeasurement.qty_in_base'),
			'fields' => array('MeasurementUnit.id', 'ProductMeasurement.measurement_unit_id', 'MeasurementUnit.name', 'ProductMeasurement.qty_in_base', 'count(ProductMeasurement.id) as count'),
			'recursive' => -1
		));



		$parent_unit_id = isset($this->request->data['parent_unit_id']) ? $this->request->data['parent_unit_id'] : 0;

		//$html = '<option value="">---- Select Unit ----</option>';
		$html = '';

		//exit;
		if (count($product_info) == 1) {
			foreach ($product_info as $key => $val) {
				//pr($val);
				if ($parent_unit_id == $val['MeasurementUnit']['id']) {
					$html = $html . '<option selected value="' . $val['MeasurementUnit']['id'] . '">' . $val['MeasurementUnit']['name'] . '</option>';
				} else {
					$html = $html . '<option value="' . $val['MeasurementUnit']['id'] . '">' . $val['MeasurementUnit']['name'] . '</option>';
				}
			}


			$conditions = array('ProductMeasurement.product_id' => $product_id);
			$unit_info = $this->ProductMeasurement->find('all', array(
				'fields' => array('ProductMeasurement.measurement_unit_id', 'MeasurementUnit.name', 'ProductMeasurement.qty_in_base', 'count(ProductMeasurement.id) as count'),
				'group' => array('ProductMeasurement.measurement_unit_id', 'MeasurementUnit.name', 'ProductMeasurement.qty_in_base'),
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
			foreach ($unit_info as $key => $val) {
				if ($val['0']['count'] != count($product_id)) {
					continue;
				}
				if ($parent_unit_id == $val['ProductMeasurement']['measurement_unit_id']) {
					$html = $html . '<option selected value="' . $val['ProductMeasurement']['measurement_unit_id'] . '">' . $val['MeasurementUnit']['name'] . '</option>';
				} else {
					$html = $html . '<option value="' . $val['ProductMeasurement']['measurement_unit_id'] . '">' . $val['MeasurementUnit']['name'] . '</option>';
				}
			}
		}
		if (!$html) {
			$html = $html . '<option value="">--- Select ---</option>';
		}
		echo $html;
		$this->autoRender = false;
	}
	public function get_so_sr_special_group()
	{
		$this->LoadModel('SpecialGroup');
		$price_effective_date = date('Y-m-d', strtotime($this->request->data['effective_date']));
		$results_output = array();
		$so_special_group = $this->SpecialGroup->find('list', array(
			'conditions' => array('SpecialGroup.is_dist' => 0, 'SpecialGroup.end_date >=' => $price_effective_date),
			'fields' => array('SpecialGroup.id', 'SpecialGroup.name'),
			'order' => array('SpecialGroup.id'),
			'recursive' => -1
		));
		$output = '';
		if ($so_special_group) {
			foreach ($so_special_group as $key => $val) {
				$output .= '<option value="' . $key . '">' . $val . '</option>';
			}
		}
		$results_output['so_special'] = $output;
		$sr_special_group = $this->SpecialGroup->find('list', array(
			'conditions' => array('SpecialGroup.is_dist' => 1, 'SpecialGroup.end_date >=' => $price_effective_date),
			'fields' => array('SpecialGroup.id', 'SpecialGroup.name'),
			'order' => array('SpecialGroup.id'),
			'recursive' => -1
		));
		$output = '';
		if ($sr_special_group) {
			foreach ($sr_special_group as $key => $val) {
				$output .= '<option value="' . $key . '">' . $val . '</option>';
			}
		}
		$results_output['sr_special'] = $output;
		echo json_encode($results_output);
		exit;
	}
}
