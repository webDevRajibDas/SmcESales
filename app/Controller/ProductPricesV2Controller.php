<?php
App::uses('AppController', 'Controller');
/**
 * ProductPrices Controller
 *
 * @property ProductPrice $ProductPrice
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProductPricesV2Controller extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Session', 'Filter.Filter');
	public $uses =  array('Product', 'ProductPricesV2');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$this->set('page_title', 'Product Price List');
		$this->Product->recursive = 0;

		$this->paginate = array(
			'conditions' => array(
				'Product.product_type_id' => 1
			),
			'order' => array('Product.order' => 'ASC')
		);
		$this->set('products', $this->paginate());

		// $productCategories = $this->Product->ProductCategory->generateTreeList(null, null, null, '__');
		$productCategories = $this->Product->ProductCategory->find('list');
		$productTypes = $this->Product->ProductType->find('list', array('order' => array('name' => 'asc')));
		$this->set(compact('productCategories', 'productTypes'));
	}

	public function admin_price_list($id = null)
	{
		$this->set('page_title', 'Price List');
		$this->loadModel('ProductPricesV2');
		$this->loadModel('Product');
		$this->ProductPricesV2->recursive = -1;
		$this->paginate = array('order' => array('ProductPricesV2.id' => 'DESC'), 'conditions' => array('ProductPricesV2.product_id' => $id, 'ProductPricesV2.has_combination' => 0, 'ProductPricesV2.institute_id' => 0));
		$this->set('product_prices', $this->paginate('ProductPricesV2'));
		$product = $this->Product->find('first', array('conditions' => array('Product.id' => $id), 'recursive' => -1));
		$this->set('product', $product);
		$this->set('id', $id);
	}

	public function get_price_create_for_by_price_id($price_id)
	{
		$this->LoadModel('ProductPriceSectionV2');
		$price_section = $this->ProductPriceSectionV2->find('first', array(
			'conditions' => array(
				'ProductPriceSectionV2.product_price_id' => $price_id
			),
			'fields' => array(
				'max(is_so) as is_so',
				'max(is_sr) as is_sr',
				'max(is_db) as is_db',
			),
			'group' => array('ProductPriceSectionV2.product_price_id'),
			'recursive' => -1
		));
		$string = '';
		if ($price_section[0]['is_so']) {
			$string .= 'SO';
		}
		if ($price_section[0]['is_sr']) {
			$string .= ',SR';
		}
		if ($price_section[0]['is_db']) {
			$string .= ',DB';
		}
		return trim($string, ',');
	}
	/**
	 * admin_set_price_slot method
	 *
	 * @return void
	 */
	public function admin_set_unique_price($id = null, $product_price_id = null)
	{
		$this->loadModel('ProductCombinationsV2');
		$this->loadModel('ProductPriceSectionV2');
		$this->loadModel('ProductPriceDbSlabs');
		$this->loadModel('ProductPriceOtherForSlabsV2');
		$this->loadModel('OutletCategory');
		$this->loadModel('DistOutletCategory');
		$this->set('product_id', $id);

		if ($this->request->is('post')) {

			$this->request->data['ProductPrice']['effective_date'] = date('Y-m-d', strtotime($this->request->data['ProductPrice']['effective_date']));
			$this->request->data['ProductPrice']['product_id'] = $id;
			$this->request->data['ProductPrice']['created_at'] = $this->current_datetime();
			$this->request->data['ProductPrice']['updated_at'] = $this->current_datetime();
			$this->request->data['ProductPrice']['created_by'] = $this->UserAuth->getUserId();
			$this->ProductPricesV2->create();

			$datasource = $this->ProductPricesV2->getDataSource();
			try {
				$datasource->begin();
				if (!$this->ProductPricesV2->save($this->request->data['ProductPrice'])) {
					throw new Exception();
				} else {
					$product_price_id = $this->ProductPricesV2->getInsertID();
					foreach ($this->request->data['section_id'] as $section_key => $section_id) {
						$section['product_price_id'] = $product_price_id;
						$section['is_so'] = $this->request->data['is_so'][$section_id];
						$section['is_sr'] = $this->request->data['is_sr'][$section_id];
						$section['is_db'] = $this->request->data['is_db'][$section_id];
						$this->ProductPriceSectionV2->create();
						if (!$this->ProductPriceSectionV2->save($section)) {
							throw new Exception();
						} else {
							$db_section_id = $this->ProductPriceSectionV2->getInsertID();
							$product_combinations['product_id'] = $id;
							$product_combinations['product_price_id'] = $product_price_id;
							$product_combinations['section_id'] = $db_section_id;
							$product_combinations['created_at'] = $this->current_datetime();
							$product_combinations['updated_at'] = $this->current_datetime();
							$product_combinations['created_by'] = $this->UserAuth->getUserId();
							$product_combinations['effective_date'] = date('Y-m-d', strtotime($this->request->data['ProductPrice']['effective_date']));
							foreach ($this->request->data['min_qty'][$section_id] as $min_key => $min_value) {
								$product_combinations['min_qty'] = $min_value;
								$product_combinations['price'] = $this->request->data['trade_price'][$section_id][$min_key];
								$product_combinations['sr_price'] = isset($this->request->data['sr_price'][$section_id][$min_key]) ? $this->request->data['sr_price'][$section_id][$min_key] : 0.00;
								$this->ProductCombinationsV2->create();
								if (!$this->ProductCombinationsV2->save($product_combinations)) {
									throw new Exception();
								} else {
									$product_combination_id = $this->ProductCombinationsV2->getInsertID();
									if (isset($this->request->data['db_price'][$section_id])) {
										$db_slabs['product_combination_id'] = $product_combination_id;
										$db_slabs['discount_amount'] = $this->request->data['db_discount'][$section_id][$min_key];
										$db_slabs['price'] = $this->request->data['db_price'][$section_id][$min_key];
										$this->ProductPriceDbSlabs->create();
										if (!$this->ProductPriceDbSlabs->save($db_slabs)) {
											throw new Exception();
										}
									}
									if ($this->request->data['is_so_outlet_category'][$section_id] == 1) {
										$other_slabs_outlet_category['product_combination_id'] = $product_combination_id;
										$other_slabs_outlet_category['price_for'] = 1;
										$other_slabs_outlet_category['type'] = 2;
										foreach ($this->request->data['so_outlet_category_id'][$section_id] as $outlet_category_id) {
											$other_slabs_outlet_category['reffrence_id'] = $outlet_category_id;
											$other_slabs_outlet_category['price'] = $this->request->data['so_category_price'][$section_id][$outlet_category_id][$min_key];
											$this->ProductPriceOtherForSlabsV2->create();
											if (!$this->ProductPriceOtherForSlabsV2->save($other_slabs_outlet_category)) {
												throw new Exception();
											}
										}
									}
									if ($this->request->data['is_sr_outlet_category'][$section_id] == 1) {
										$other_slabs_outlet_category['product_combination_id'] = $product_combination_id;
										$other_slabs_outlet_category['price_for'] = 2;
										$other_slabs_outlet_category['type'] = 2;
										foreach ($this->request->data['sr_outlet_category_id'][$section_id] as $outlet_category_id) {
											$other_slabs_outlet_category['reffrence_id'] = $outlet_category_id;
											$other_slabs_outlet_category['price'] = $this->request->data['sr_category_price'][$section_id][$outlet_category_id][$min_key];
											$this->ProductPriceOtherForSlabsV2->create();
											if (!$this->ProductPriceOtherForSlabsV2->save($other_slabs_outlet_category)) {
												throw new Exception();
											}
										}
									}

									/*--------------------- Special group price insert --------------------------------*/
									if ($this->request->data['is_so_special'][$section_id] == 1) {
										$other_slabs_outlet_category['product_combination_id'] = $product_combination_id;
										$other_slabs_outlet_category['price_for'] = 1; //1=For SO
										$other_slabs_outlet_category['type'] = 1; //1 =special group
										foreach ($this->request->data['so_special_group_id'][$section_id] as $special_group_id) {
											$other_slabs_outlet_category['reffrence_id'] = $special_group_id;
											$other_slabs_outlet_category['price'] = $this->request->data['so_special_group_price'][$section_id][$special_group_id][$min_key];
											$this->ProductPriceOtherForSlabsV2->create();
											if (!$this->ProductPriceOtherForSlabsV2->save($other_slabs_outlet_category)) {
												throw new Exception();
											}
										}
									}
									if ($this->request->data['is_sr_special'][$section_id] == 1) {
										$other_slabs_outlet_category['product_combination_id'] = $product_combination_id;
										$other_slabs_outlet_category['price_for'] = 2; //2=For SR
										$other_slabs_outlet_category['type'] = 1; //1 =special group
										foreach ($this->request->data['sr_special_group_id'][$section_id] as $special_group_id) {
											$other_slabs_outlet_category['reffrence_id'] = $special_group_id;
											$other_slabs_outlet_category['price'] = $this->request->data['sr_special_group_price'][$section_id][$special_group_id][$min_key];
											$this->ProductPriceOtherForSlabsV2->create();
											if (!$this->ProductPriceOtherForSlabsV2->save($other_slabs_outlet_category)) {
												throw new Exception();
											}
										}
									}
								}
							}
						}
					}
				}
				$datasource->commit();
			} catch (Exception $e) {
				$datasource->rollback();
				$this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
				$this->redirect(array('action' => 'price_list/' . $id));
			}
			$this->Session->setFlash(__('The product price has been saved'), 'flash/success');
			$this->redirect(array('action' => 'price_list/' . $id));
		}
		$outlet_categories = $this->OutletCategory->find('list', array(
			'conditions' => array('is_active' => 1, 'id !=' => 17),
			'order' => array('category_name' => 'asc')
		));
		$this->set(compact('outlet_categories'));

		$dist_outlet_categories = $this->DistOutletCategory->find('list', array(
			'conditions' => array('is_active' => 1),
			'order' => array('category_name' => 'asc')
		));
		$this->set(compact('dist_outlet_categories'));
	}
	/**
	 * admin_set_price_slot method
	 *
	 * @return void
	 */
	public function admin_set_price($id = null, $product_price_id = null)
	{

		$this->set('page_title', 'Set Product Price');
		$this->loadModel('ProductCombination');
		$this->loadModel('MemoDetail');



		/* check product_price_id is existed in memo_details table */
		if ($product_price_id) {

			$sql = "select count(*) as id_num from memo_details md
				inner join product_combinations pc on pc.id=md.product_price_id
				 where pc.product_price_id=$product_price_id";
			$data_sql = $this->MemoDetail->Query($sql);

			$update_allow = 1;

			if ($data_sql[0][0]['id_num'] > 0) {
				$update_allow = 0;
			}

			$this->set('update_allow', $update_allow);
		}
		$info = $this->ProductPrice->find('first', array('conditions' => array('ProductPrice.id' => $product_price_id)));
		$update_effective_date = $info['ProductPrice']['effective_date'];
		/*------------ start update prev date ------------*/
		$update_prev_date_list = $this->ProductPrice->find(
			'all',
			array(
				'conditions' => array('ProductPrice.product_id' => $id, 'ProductPrice.effective_date <' => $update_effective_date, 'ProductPrice.institute_id' => 0, 'ProductPrice.has_combination' => 0),
				'fields' => array('ProductPrice.effective_date')
			)
		);
		if (!empty($update_prev_date_list)) {
			$update_prev_short_list = array();
			foreach ($update_prev_date_list as $update_prev_date_key => $update_prev_date_val) {
				array_push($update_prev_short_list, $update_prev_date_val['ProductPrice']['effective_date']);
			}
			asort($update_prev_short_list);
			$update_prev_date = end($update_prev_short_list);
			/* ----------- end get prev less date ----------*/
			/*----------- start get prev data -----------*/
			$update_prev_data = $this->ProductPrice->find(
				'first',
				array(
					'conditions' => array('ProductPrice.product_id' => $id, 'ProductPrice.effective_date' => $update_prev_date, 'ProductPrice.institute_id' => 0, 'ProductPrice.has_combination' => 0),
					'fields' => array('ProductPrice.effective_date')
				)
			);
			/* echo "<pre>";
			print_r($update_prev_data);
			exit; */
			/*--------- end get prev data -------------*/
		}
		/*----------- end update prev date --------------*/
		/*---------- start update next date ------------*/
		$update_next_date_list = $this->ProductPrice->find(
			'all',
			array(
				'conditions' => array('ProductPrice.product_id' => $id, 'ProductPrice.effective_date >' => $update_effective_date, 'ProductPrice.institute_id' => 0, 'ProductPrice.has_combination' => 0),
				'fields' => array('ProductPrice.effective_date')
			)
		);
		if (!empty($update_next_date_list)) {
			$update_next_short_list = array();
			foreach ($update_next_date_list as $update_next_date_key => $update_next_date_val) {
				array_push($update_next_short_list, $update_next_date_val['ProductPrice']['effective_date']);
			}
			rsort($update_next_short_list);
			$update_next_date = end($update_next_short_list);
			/* ----------- end get prev less date ----------*/
			/*----------- start get prev data -----------*/
			$update_next_data = $this->ProductPrice->find(
				'first',
				array(
					'conditions' => array('ProductPrice.product_id' => $id, 'ProductPrice.effective_date' => $update_next_date, 'ProductPrice.institute_id' => 0, 'ProductPrice.has_combination' => 0),
					'fields' => array('ProductPrice.effective_date')
				)
			);
			/*--------- end get prev data -------------*/
		}
		/*---------- end update next date ------------*/
		if (isset($info['ProductPrice']['id']) != '') {
			$this->ProductPrice->id = $info['ProductPrice']['id'];
			if ($this->request->is('post') || $this->request->is('put')) {
				$this->request->data['ProductPrice']['updated_by'] = $this->UserAuth->getUserId();
				$this->request->data['ProductPrice']['updated_at'] = $this->current_datetime();
				$this->request->data['ProductPrice']['effective_date'] = date('Y-m-d', strtotime($this->request->data['ProductPrice']['effective_date']));
				if ($this->ProductPrice->save($this->request->data)) {
					if (!empty($this->request->data['ProductCombination'])) {
						$update_data['ProductCombination']['effective_date'] = date('Y-m-d', strtotime($this->request->data['ProductPrice']['effective_date']));
						if (isset($this->request->data['ProductCombination']['update_min_qty'])) {
							$update_data_array = array();
							$update_data['ProductCombination']['updated_by'] = $this->UserAuth->getUserId();
							$update_data['ProductCombination']['updated_at'] = $this->current_datetime();
							foreach ($this->request->data['ProductCombination']['update_min_qty'] as $ukey => $uval) {
								$update_data['ProductCombination']['id'] = $ukey;
								$update_data['ProductCombination']['min_qty'] = $this->request->data['ProductCombination']['update_min_qty'][$ukey];
								$update_data['ProductCombination']['product_id'] = $info['ProductPrice']['product_id'];
								$update_data['ProductCombination']['price'] = $this->request->data['ProductCombination']['update_price'][$ukey];
								$update_data_array[] = $update_data;
							}
							$this->ProductCombination->saveAll($update_data_array);
						}
						if (isset($this->request->data['ProductCombination']['min_qty'])) {
							$data['ProductCombination']['effective_date'] = date('Y-m-d', strtotime($this->request->data['ProductPrice']['effective_date']));
							$data['ProductCombination']['product_id'] = $id;
							$data_array = array();
							$data['ProductCombination']['created_by'] = $this->UserAuth->getUserId();
							$data['ProductCombination']['updated_at'] = $this->current_datetime();
							$data['ProductCombination']['created_at'] = $this->current_datetime();
							foreach ($this->request->data['ProductCombination']['min_qty'] as $key => $val) {
								$data['ProductCombination']['product_price_id'] = $this->ProductPrice->id;
								$data['ProductCombination']['min_qty'] = $val;
								$data['ProductCombination']['price'] = $this->request->data['ProductCombination']['price'][$key];
								$data_array[] = $data;
							}
							$this->ProductCombination->saveAll($data_array);
						}
						/*-------- start update prev row by update next row --------*/
						if (!empty($update_next_date) && !empty($update_prev_date)) {
							$update_prev_end_date['ProductPrice']['id'] = $update_prev_data['ProductPrice']['id'];
							$update_prev_end_date['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d', strtotime($update_next_data['ProductPrice']['effective_date'])))));
							if ($this->ProductPrice->save($update_prev_end_date)) {
								foreach ($update_prev_data['ProductCombination'] as $prev_update_val) {
									$update_product_combination['ProductCombination']['id'] = $prev_update_val['id'];
									$update_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d', strtotime($update_next_data['ProductPrice']['effective_date'])))));
									$this->ProductCombination->save($update_product_combination);
								}
							}
						}
						if (empty($update_next_date) && !empty($update_prev_date)) {
							$update_prev_end_date['ProductPrice']['id'] = $update_prev_data['ProductPrice']['id'];
							$update_prev_end_date['ProductPrice']['end_date'] = NULL;
							if ($this->ProductPrice->save($update_prev_end_date)) {
								foreach ($update_prev_data['ProductCombination'] as $prev_update_val) {
									$update_product_combination['ProductCombination']['id'] = $prev_update_val['id'];
									$update_product_combination['ProductCombination']['end_date'] = NULL;
									$this->ProductCombination->save($update_product_combination);
								}
							}
						}
						/*-------- end update prev row by update next row --------*/
						$info_after_update = $this->ProductPrice->find('first', array('conditions' => array('ProductPrice.id' => $id, 'ProductPrice.id' => $product_price_id)));
						$updated_effective_date = $info_after_update['ProductPrice']['effective_date'];
						/*------------ start updated prev date ------------*/
						$updated_prev_date_list = $this->ProductPrice->find(
							'all',
							array(
								'conditions' => array('ProductPrice.product_id' => $id, 'ProductPrice.effective_date <' => $updated_effective_date, 'ProductPrice.institute_id' => 0, 'ProductPrice.has_combination' => 0),
								'fields' => array('ProductPrice.effective_date')
							)
						);
						if (!empty($updated_prev_date_list)) {
							$updated_prev_short_list = array();
							foreach ($updated_prev_date_list as $updated_prev_date_key => $updated_prev_date_val) {
								array_push($updated_prev_short_list, $updated_prev_date_val['ProductPrice']['effective_date']);
							}
							asort($updated_prev_short_list);
							$updated_prev_date = end($updated_prev_short_list);
							/* ----------- end get prev less date ----------*/
							/*----------- start get prev data -----------*/
							$updated_prev_data = $this->ProductPrice->find(
								'first',
								array(
									'conditions' => array('ProductPrice.product_id' => $id, 'ProductPrice.effective_date' => $updated_prev_date, 'ProductPrice.institute_id' => 0, 'ProductPrice.has_combination' => 0),
									'fields' => array('ProductPrice.effective_date')
								)
							);
							/*--------- end get prev data -------------*/
						}
						/*----------- end update prev date --------------*/
						/*---------- start update next date ------------*/
						$updated_next_date_list = $this->ProductPrice->find(
							'all',
							array(
								'conditions' => array('ProductPrice.product_id' => $id, 'ProductPrice.effective_date >' => $updated_effective_date, 'ProductPrice.institute_id' => 0, 'ProductPrice.has_combination' => 0),
								'fields' => array('ProductPrice.effective_date')
							)
						);
						if (!empty($updated_next_date_list)) {
							$updated_next_short_list = array();
							foreach ($updated_next_date_list as $updated_next_date_key => $updated_next_date_val) {
								array_push($updated_next_short_list, $updated_next_date_val['ProductPrice']['effective_date']);
							}
							rsort($updated_next_short_list);
							$updated_next_date = end($updated_next_short_list);
							/* ----------- end get prev less date ----------*/
							/*----------- start get prev data -----------*/
							$updated_next_data = $this->ProductPrice->find(
								'first',
								array(
									'conditions' => array('ProductPrice.product_id' => $id, 'ProductPrice.effective_date' => $updated_next_date, 'ProductPrice.institute_id' => 0, 'ProductPrice.has_combination' => 0),
									'fields' => array('ProductPrice.effective_date')
								)
							);
							//echo $updated_next_date;
							//exit;
							/*--------- end get prev data -------------*/
						}
						/*---------- end update next date ------------*/
						/*-------- start updated prev row by updated next row --------*/
						if (!empty($updated_prev_date)) {
							$updated_prev_end_date['ProductPrice']['id'] = $updated_prev_data['ProductPrice']['id'];
							$updated_prev_end_date['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d', strtotime($updated_effective_date)))));
							if ($this->ProductPrice->save($updated_prev_end_date)) {
								foreach ($updated_prev_data['ProductCombination'] as $prev_updated_val) {
									$updated_product_combination['ProductCombination']['id'] = $prev_updated_val['id'];
									$updated_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d', strtotime($updated_effective_date)))));
									$this->ProductCombination->save($updated_product_combination);
								}
							}
						}
						if (!empty($updated_next_date)) {
							$update_current_row['ProductPrice']['id'] = $product_price_id;
							$update_current_row['ProductPrice']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d', strtotime($updated_next_data['ProductPrice']['effective_date'])))));
							if ($this->ProductPrice->save($update_current_row)) {
								foreach ($info['ProductCombination'] as $val) {
									$current_product_combination['ProductCombination']['id'] = $val['id'];
									$current_product_combination['ProductCombination']['end_date'] = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d', strtotime($updated_next_data['ProductPrice']['effective_date'])))));
									$this->ProductCombination->save($current_product_combination);
								}
							}
							if (!empty($updated_next_data)) {
								$update_next_row['ProductPrice']['id'] = $updated_next_data['ProductPrice']['id'];
								$update_next_row['ProductPrice']['end_date'] = NULL;
								//print_r($update_next_row);
								//exit;
								if ($this->ProductPrice->save($update_next_row)) {
									foreach ($updated_next_data['ProductCombination'] as $next_val) {
										$next_product_combination['ProductCombination']['id'] = $next_val['id'];
										$next_product_combination['ProductCombination']['end_date'] = NULL;
										$this->ProductCombination->save($next_product_combination);
									}
								}
							}
						} else {
							$update_current_row['ProductPrice']['id'] = $product_price_id;
							$update_current_row['ProductPrice']['end_date'] = NULL;
							if ($this->ProductPrice->save($update_current_row)) {
								foreach ($info['ProductCombination'] as $val) {
									$current_product_combination['ProductCombination']['id'] = $val['id'];
									$current_product_combination['ProductCombination']['end_date'] = NULL;
									$this->ProductCombination->save($current_product_combination);
								}
							}
						}
						/*-------- end updated prev row by updated next row --------*/
					}

					$this->Session->setFlash(__('The product price has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index/' . $id));
				}
			}
			$options = array('conditions' => array('ProductPrice.' . $this->ProductPrice->primaryKey => $info['ProductPrice']['id']));
			$this->request->data = $this->ProductPrice->find('first', $options);
		} else {
			if ($this->request->is('post')) {
				$this->request->data['ProductPrice']['product_id'] = $id;
				$this->request->data['ProductPrice']['created_at'] = $this->current_datetime();
				$this->request->data['ProductPrice']['updated_at'] = $this->current_datetime();
				$this->request->data['ProductPrice']['created_by'] = $this->UserAuth->getUserId();
				$this->request->data['ProductPrice']['effective_date'] = date('Y-m-d', strtotime($this->request->data['ProductPrice']['effective_date']));
				$this->ProductPrice->create();
				if ($this->ProductPrice->save($this->request->data)) {

					if (!empty($this->request->data['ProductCombination'])) {
						$data_array = array();
						$data['ProductCombination']['product_id'] = $id;
						$data['ProductCombination']['created_at'] = $this->current_datetime();
						$data['ProductCombination']['updated_at'] = $this->current_datetime();
						$data['ProductCombination']['created_by'] = $this->UserAuth->getUserId();
						$data['ProductCombination']['effective_date'] = date('Y-m-d', strtotime($this->request->data['ProductPrice']['effective_date']));
						foreach ($this->request->data['ProductCombination']['min_qty'] as $key => $val) {
							$data['ProductCombination']['product_price_id'] = $this->ProductPrice->id;
							$data['ProductCombination']['min_qty'] = $val;
							$data['ProductCombination']['price'] = $this->request->data['ProductCombination']['price'][$key];
							$data_array[] = $data;
						}
						$this->ProductCombination->saveAll($data_array);
					}

					$this->Session->setFlash(__('The product price has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index/' . $id));
				}
			}
		}
	}

	public function admin_view_price($id = null, $product_price_id = null)
	{

		$this->set('page_title', 'View Product Price');
		$this->loadModel('ProductCombinationsV2');
		$this->loadModel('ProductPricesV2');
		$this->loadModel('OutletCategory');
		$this->loadModel('DistOutletCategory');
		$this->ProductPricesV2->UnbindModel(
			array(
				'hasMany' => array('ProductCombinationsV2'),
				'belongsTo' => array('Product'),
			)
		);
		$this->ProductCombinationsV2->UnbindModel(
			array(
				'belongsTo' => array('ProductPricesV2'),
			)
		);
		$options = array('conditions' => array('ProductPricesV2.' . $this->ProductPricesV2->primaryKey => $product_price_id));
		$price_data = $this->ProductPricesV2->find('first', $options);
		foreach ($price_data['ProductPriceSectionV2'] as $key_sec => $section_data) {
			$product_combination = $this->ProductCombinationsV2->find(
				'all',
				array(
					'conditions' => array(
						'ProductCombinationsV2.section_id' => $section_data['id']
					)
				)
			);

			$so_selected_special_group = array();
			$sr_selected_special_group = array();
			$so_selected_outlet_category_id = array();
			$sr_selected_outlet_category_id = array();
			foreach ($product_combination as $key => $pro_comb) {
				$so_special_group = array();
				$sr_special_group = array();
				$so_outlet_category_id = array();
				$sr_outlet_category_id = array();


				foreach ($pro_comb['ProductPriceOtherForSlabsV2'] as $other_price) {
					if ($other_price['price_for'] == 1 && $other_price['type'] == 1) {
						$so_special_group[] = $other_price;
						$so_selected_special_group[$other_price['reffrence_id']] = $other_price['reffrence_id'];
					} else if ($other_price['price_for'] == 2 && $other_price['type'] == 1) {
						$sr_special_group[] = $other_price;
						$sr_selected_special_group[$other_price['reffrence_id']] = $other_price['reffrence_id'];
					} elseif ($other_price['price_for'] == 1 && $other_price['type'] == 2) {
						$so_outlet_category_id[] = $other_price;
						$so_selected_outlet_category_id[$other_price['reffrence_id']] = $other_price['reffrence_id'];
					} elseif ($other_price['price_for'] == 2 && $other_price['type'] == 2) {
						$sr_outlet_category_id[] = $other_price;
						$sr_selected_outlet_category_id[$other_price['reffrence_id']] = $other_price['reffrence_id'];
					}
				}
				unset($product_combination[$key]['ProductPriceOtherForSlabsV2']);
				$product_combination[$key]['SoSpecialGroup'] = $so_special_group;
				$product_combination[$key]['SrSpecialGroup'] = $sr_special_group;
				$product_combination[$key]['SoOutletCategory'] = $so_outlet_category_id;
				$product_combination[$key]['SrOutletCategory'] = $sr_outlet_category_id;
			}
			$price_data['ProductPriceSectionV2'][$key_sec]['so_selected_special_group'] = $so_selected_special_group;
			$price_data['ProductPriceSectionV2'][$key_sec]['sr_selected_special_group'] = $sr_selected_special_group;
			$price_data['ProductPriceSectionV2'][$key_sec]['so_selected_outlet_category_id'] = $so_selected_outlet_category_id;
			$price_data['ProductPriceSectionV2'][$key_sec]['sr_selected_outlet_category_id'] = $sr_selected_outlet_category_id;
			$price_data['ProductPriceSectionV2'][$key_sec]['slab_data'] = $product_combination;
		}
		$this->set(compact('price_data'));
		$outlet_categories = $this->OutletCategory->find('list', array(
			'conditions' => array('is_active' => 1, 'id !=' => 17),
			'order' => array('category_name' => 'asc')
		));
		$this->set(compact('outlet_categories'));

		$dist_outlet_categories = $this->DistOutletCategory->find('list', array(
			'conditions' => array('is_active' => 1),
			'order' => array('category_name' => 'asc')
		));
		$this->set(compact('dist_outlet_categories'));
		$this->LoadModel('SpecialGroup');
		$results_output = array();
		$price_effective_date = date('Y-m-d', strtotime($price_data['ProductPricesV2']['effective_date']));
		$so_special_group = $this->SpecialGroup->find('list', array(
			'conditions' => array('SpecialGroup.is_dist' => 0, 'SpecialGroup.end_date >=' => $price_effective_date),
			'fields' => array('SpecialGroup.id', 'SpecialGroup.name'),
			'order' => array('SpecialGroup.id'),
			'recursive' => -1
		));
		/*if($so_special_group)
		{	
			$output = '<div class="input select">
			<input type="hidden" name="data[so_special_group_id][1]" value="" id="so_special_group_id"/>';
			foreach($so_special_group as $key => $val)
			{
				$output.= '<div class="checkbox so_special_group_id">
					<input type="checkbox" name="data[so_special_group_id][1][]" value="'.$key.'" id="so_special_group_id'.$key.'" />
					<label for="so_special_group_id'.$key.'">'.$val.'</label>
				  </div>';
			}
			$output.='</div>';
			$results_output['so_special']=$output;
		}*/
		$sr_special_group = $this->SpecialGroup->find('list', array(
			'conditions' => array('SpecialGroup.is_dist' => 1, 'SpecialGroup.end_date >=' => $price_effective_date),
			'fields' => array('SpecialGroup.id', 'SpecialGroup.name'),
			'order' => array('SpecialGroup.id'),
			'recursive' => -1
		));
		/*if($sr_special_group)
		{	
			$output = '<div class="input select">
			<input type="hidden" name="data[sr_special_group_id][$i]" value="" id="sr_special_group_id"/>';
			foreach($sr_special_group as $key => $val)
			{
				$output.= '<div class="checkbox sr_special_group_id">
					<input type="checkbox" name="data[sr_special_group_id][$i][]" value="'.$key.'" id="sr_special_group_id'.$key.'" />
					<label for="sr_special_group_id'.$key.'">'.$val.'</label>
				  </div>';
			}
			$output.='</div>';
			$results_output['sr_special']=$output;
		}*/
		// pr($price_data);exit;
		$this->set(compact('sr_special_group', 'so_special_group'));
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
		if ($so_special_group) {
			$output = '<div class="input select">
			<input type="hidden" name="data[so_special_group_id][1]" value="" id="so_special_group_id"/>';
			foreach ($so_special_group as $key => $val) {
				$output .= '<div class="checkbox so_special_group_id">
					<input type="checkbox" name="data[so_special_group_id][1][]" value="' . $key . '" id="so_special_group_id' . $key . '" />
					<label for="so_special_group_id' . $key . '">' . $val . '</label>
				  </div>';
			}
			$output .= '</div>';
			$results_output['so_special'] = $output;
		}
		$sr_special_group = $this->SpecialGroup->find('list', array(
			'conditions' => array('SpecialGroup.is_dist' => 1, 'SpecialGroup.end_date >=' => $price_effective_date),
			'fields' => array('SpecialGroup.id', 'SpecialGroup.name'),
			'order' => array('SpecialGroup.id'),
			'recursive' => -1
		));
		if ($sr_special_group) {
			$output = '<div class="input select">
			<input type="hidden" name="data[sr_special_group_id][1]" value="" id="sr_special_group_id"/>';
			foreach ($sr_special_group as $key => $val) {
				$output .= '<div class="checkbox sr_special_group_id">
					<input type="checkbox" name="data[sr_special_group_id][1][]" value="' . $key . '" id="sr_special_group_id' . $key . '" />
					<label for="sr_special_group_id' . $key . '">' . $val . '</label>
				  </div>';
			}
			$output .= '</div>';
			$results_output['sr_special'] = $output;
		}
		echo json_encode($results_output);
		exit;
	}

	public function admin_assign_special_group($id = null, $product_price_id = null)
	{
		$this->loadModel('ProductCombinationsV2');
		$this->loadModel('ProductPriceSectionV2');
		$this->loadModel('ProductPriceDbSlabs');
		$this->loadModel('ProductPriceOtherForSlabsV2');
		$this->loadModel('SpecialGroup');
		$this->set('product_id', $id);
		$this->set('product_price_id', $product_price_id);
		if ($this->request->is('post')) {
			$sr_trade_price = $this->request->data['trade_price'];
			$datasource = $this->ProductPricesV2->getDataSource();
			try {
				$datasource->begin();
				foreach ($this->request->data['so_product_combination_id'][1] as $key_so_com_id => $product_combination_id) {
					if ($product_combination_id) {
						$other_slabs_outlet_category['product_combination_id'] = $product_combination_id;
						$other_slabs_outlet_category['price_for'] = 1; //1=For SO
						$other_slabs_outlet_category['type'] = 1; //1 =special group
						foreach ($this->request->data['so_special_group_id'][1] as $special_group_id) {
							$other_slabs_outlet_category['reffrence_id'] = $special_group_id;
							$other_slabs_outlet_category['price'] = $this->request->data['so_special_group_price'][1][$special_group_id][$key_so_com_id];
							$this->ProductPriceOtherForSlabsV2->create();
							if (!$this->ProductPriceOtherForSlabsV2->save($other_slabs_outlet_category)) {
								throw new Exception();
							} else {
								unset($this->request->data['so_special_group_price'][1][$special_group_id][$key_so_com_id]);
							}
						}
						unset($this->request->data['trade_price'][1][$key_so_com_id]);
						$pro_com_update['id'] = $product_combination_id;
						$pro_com_update['updated_at'] = $this->current_datetime();
						if (!$this->ProductCombinationsV2->saveAll($pro_com_update)) {
							throw new Exception();
						}
					}
				}

				foreach ($this->request->data['sr_product_combination_id'][1] as $key_sr_com_id => $product_combination_id) {
					if ($product_combination_id) {
						$other_slabs_outlet_category['product_combination_id'] = $product_combination_id;
						$other_slabs_outlet_category['price_for'] = 2; //2=For SR
						$other_slabs_outlet_category['type'] = 1; //1 =special group
						foreach ($this->request->data['sr_special_group_id'][1] as $special_group_id) {
							$other_slabs_outlet_category['reffrence_id'] = $special_group_id;
							$other_slabs_outlet_category['price'] = $this->request->data['sr_special_group_price'][1][$special_group_id][$key_sr_com_id];
							$this->ProductPriceOtherForSlabsV2->create();
							if (!$this->ProductPriceOtherForSlabsV2->save($other_slabs_outlet_category)) {
								throw new Exception();
							} else {
								unset($this->request->data['sr_special_group_price'][1][$special_group_id][$key_sr_com_id]);
							}
						}
						unset($this->request->data['sr_price'][1][$key_sr_com_id]);
						$pro_com_update['id'] = $product_combination_id;
						$pro_com_update['updated_at'] = $this->current_datetime();
						if (!$this->ProductCombinationsV2->saveAll($pro_com_update)) {
							throw new Exception();
						}
					}
				}

				if (!empty($this->request->data['trade_price'][1]) && $this->request->data['is_so'][1] == 1) {
					$section['product_price_id'] = $product_price_id;
					$section['is_so'] = $this->request->data['is_so'][1];
					$section['is_sr'] = 0;
					$section['is_db'] = 0;
					$this->ProductPriceSectionV2->create();
					if (!$this->ProductPriceSectionV2->save($section)) {
						throw new Exception();
					} else {
						$db_section_id = $this->ProductPriceSectionV2->getInsertID();
						$product_combinations['product_id'] = $id;
						$product_combinations['product_price_id'] = $product_price_id;
						$product_combinations['section_id'] = $db_section_id;
						$product_combinations['created_at'] = $this->current_datetime();
						$product_combinations['updated_at'] = $this->current_datetime();
						$product_combinations['created_by'] = $this->UserAuth->getUserId();
						$product_combinations['effective_date'] = date('Y-m-d', strtotime($this->request->data['ProductPrice']['effective_date']));
						foreach ($this->request->data['min_qty'][1] as $min_key => $min_value) {
							if (!isset($this->request->data['trade_price'][1][$min_key])) {
								continue;
							}
							$product_combinations['min_qty'] = $min_value;
							$product_combinations['price'] = $this->request->data['trade_price'][1][$min_key];
							$product_combinations['sr_price'] = 0;
							$this->ProductCombinationsV2->create();
							if (!$this->ProductCombinationsV2->save($product_combinations)) {
								throw new Exception();
							} else {
								$product_combination_id = $this->ProductCombinationsV2->getInsertID();


								/*--------------------- Special group price insert --------------------------------*/
								if ($this->request->data['is_so'][1] == 1) {
									$other_slabs_outlet_category['product_combination_id'] = $product_combination_id;
									$other_slabs_outlet_category['price_for'] = 1; //1=For SO
									$other_slabs_outlet_category['type'] = 1; //1 =special group
									foreach ($this->request->data['so_special_group_id'][1] as $special_group_id) {
										$other_slabs_outlet_category['reffrence_id'] = $special_group_id;
										$other_slabs_outlet_category['price'] = $this->request->data['so_special_group_price'][1][$special_group_id][$min_key];
										$this->ProductPriceOtherForSlabsV2->create();
										if (!$this->ProductPriceOtherForSlabsV2->save($other_slabs_outlet_category)) {
											throw new Exception();
										}
									}
								}
							}
						}
					}
				}
				if (!empty($this->request->data['sr_price'][1]) && $this->request->data['is_sr'][1] == 1) {
					$section['product_price_id'] = $product_price_id;
					$section['is_so'] = 0;
					$section['is_sr'] = $this->request->data['is_sr'][1];
					$section['is_db'] = 0;
					$this->ProductPriceSectionV2->create();
					if (!$this->ProductPriceSectionV2->save($section)) {
						throw new Exception();
					} else {
						$db_section_id = $this->ProductPriceSectionV2->getInsertID();
						$product_combinations['product_id'] = $id;
						$product_combinations['product_price_id'] = $product_price_id;
						$product_combinations['section_id'] = $db_section_id;
						$product_combinations['created_at'] = $this->current_datetime();
						$product_combinations['updated_at'] = $this->current_datetime();
						$product_combinations['created_by'] = $this->UserAuth->getUserId();
						$product_combinations['effective_date'] = date('Y-m-d', strtotime($this->request->data['ProductPrice']['effective_date']));
						foreach ($this->request->data['min_qty'][1] as $min_key => $min_value) {
							if (!isset($this->request->data['sr_price'][1][$min_key])) {
								continue;
							}
							$product_combinations['min_qty'] = $min_value;
							$product_combinations['price'] = $sr_trade_price[1][$min_key];
							$product_combinations['sr_price'] = isset($this->request->data['sr_price'][1][$min_key]) ? $this->request->data['sr_price'][1][$min_key] : 0.00;;
							$this->ProductCombinationsV2->create();
							if (!$this->ProductCombinationsV2->save($product_combinations)) {
								throw new Exception();
							} else {
								$product_combination_id = $this->ProductCombinationsV2->getInsertID();


								/*--------------------- Special group price insert --------------------------------*/
								if ($this->request->data['is_sr'][1] == 1) {
									$other_slabs_outlet_category['product_combination_id'] = $product_combination_id;
									$other_slabs_outlet_category['price_for'] = 2; //2=For SR
									$other_slabs_outlet_category['type'] = 1; //1 =special group
									foreach ($this->request->data['sr_special_group_id'][1] as $special_group_id) {
										$other_slabs_outlet_category['reffrence_id'] = $special_group_id;
										$other_slabs_outlet_category['price'] = $this->request->data['sr_special_group_price'][1][$special_group_id][$min_key];
										$this->ProductPriceOtherForSlabsV2->create();
										if (!$this->ProductPriceOtherForSlabsV2->save($other_slabs_outlet_category)) {
											throw new Exception();
										}
									}
								}
							}
						}
					}
				}
				$datasource->commit();
			} catch (Exception $e) {
				$datasource->rollback();
				$this->Session->setFlash(__('SomeThing Went Wrong! Please Try Again.'), 'flash/error');
				$this->redirect(array('action' => 'price_list/' . $id));
			}
			$this->Session->setFlash(__('Successfully Assigned'), 'flash/success');
			$this->redirect(array('action' => 'price_list/' . $id));
		}
		$price_info = $this->ProductPricesV2->find('first', array('conditions' => array('ProductPricesV2.id' => $product_price_id)));
		$price_effective_date = $price_info['ProductPricesV2']['effective_date'];
		$this->set('effective_date', $price_effective_date);
		$so_special_group = $this->SpecialGroup->find('list', array(
			'conditions' => array('SpecialGroup.is_dist' => 0, 'SpecialGroup.end_date >=' => $price_effective_date, 'ExistGroup.id' => NULL),
			'joins' => array(
				array(
					'table' => '(select ppof.* from product_combinations_v2 pcv
								left join product_price_other_for_slabs_v2 ppof on ppof.product_combination_id=pcv.id
								where 
									pcv.product_price_id=' . $product_price_id . ')',
					'type' => 'Left',
					'alias' => 'ExistGroup',
					'conditions' => 'ExistGroup.price_for=1 and ExistGroup.type=1 and ExistGroup.reffrence_id=SpecialGroup.id'
				),
			),
			'fields' => array('SpecialGroup.id', 'SpecialGroup.name'),
			'order' => array('SpecialGroup.id'),
			'recursive' => -1
		));

		$sr_special_group = $this->SpecialGroup->find('list', array(
			'conditions' => array('SpecialGroup.is_dist' => 1, 'SpecialGroup.end_date >=' => $price_effective_date, 'ExistGroup.id' => NULL),
			'joins' => array(
				array(
					'table' => '(select ppof.* from product_combinations_v2 pcv
								left join product_price_other_for_slabs_v2 ppof on ppof.product_combination_id=pcv.id
								where 
									pcv.product_price_id=' . $product_price_id . ')',
					'type' => 'Left',
					'alias' => 'ExistGroup',
					'conditions' => 'ExistGroup.price_for=2 and ExistGroup.type=1 and ExistGroup.reffrence_id=SpecialGroup.id'
				),
			),
			'fields' => array('SpecialGroup.id', 'SpecialGroup.name'),
			'order' => array('SpecialGroup.id'),
			'recursive' => -1
		));
		$this->set(compact('so_special_group', 'sr_special_group'));
	}
	public function get_min_qty_details()
	{
		$this->loadModel('ProductCombinationsV2');
		$min_qty = $this->request->data['min_qty'];
		$price_id = $this->request->data['product_price_id'];
		$details_so = $this->ProductCombinationsV2->find('first', array(
			'conditions' => array(
				'ProductCombinationsV2.min_qty' => $min_qty,
				'ProductCombinationsV2.product_price_id' => $price_id,
				'ProductPriceSectionV2.is_so' => 1
			),
			'joins' => array(
				array(
					'table' => 'product_price_section_v2',
					'alias' => 'ProductPriceSectionV2',
					'conditions' => 'ProductCombinationsV2.section_id=ProductPriceSectionV2.id'
				)
			),
			'recursive' => -1
		));
		$price['tp_price'] = $details_so ? $details_so['ProductCombinationsV2']['price'] : '';
		$price['so_product_combination_id'] = $details_so ? $details_so['ProductCombinationsV2']['id'] : '';


		$details_sr = $this->ProductCombinationsV2->find('first', array(
			'conditions' => array(
				'ProductCombinationsV2.min_qty' => $min_qty,
				'ProductCombinationsV2.product_price_id' => $price_id,
				'ProductPriceSectionV2.is_sr' => 1
			),
			'joins' => array(
				array(
					'table' => 'product_price_section_v2',
					'alias' => 'ProductPriceSectionV2',
					'conditions' => 'ProductCombinationsV2.section_id=ProductPriceSectionV2.id'
				)
			),
			'recursive' => -1
		));
		$price['sr_tp_price'] = $details_sr ? $details_sr['ProductCombinationsV2']['price'] : '';
		$price['sr_price'] = $details_sr ? $details_sr['ProductCombinationsV2']['sr_price'] : '';
		$price['sr_product_combination_id'] = $details_sr ? $details_sr['ProductCombinationsV2']['id'] : '';
		echo json_encode($price);
		exit;
	}
}
