<?php

App::uses('AppController', 'Controller');

/**
 * Memos Controller
 *
 * @property CsaMemo $CsaMemo
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class CsaMemosController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $uses = array('CsaMemo', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'Combination', 'CsaMemoDetail', 'MeasurementUnit');
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index() {
			
		// pr($this->request->data);exit;
		$this->loadModel('CsaMemoDetail');
		$product_name = $this->Product->find('all',array(
			'fields'=>array('Product.name','Product.id','MU.name as mes_name','Product.product_category_id'),
			'joins'=>array(
				array(
					'table'=>'measurement_units',
					'alias'=>'MU',
					'type' => 'LEFT',
					'conditions'=>array('MU.id= Product.sales_measurement_unit_id')
					)
				),
			'conditions'=>array('NOT'=>array('Product.product_category_id'=>32)),
			'order'=>'Product.product_category_id',
			'recursive'=>-1
			));
		//pr($product_name);
		$requested_data = $this->request->data;
		//pr($requested_data);die();
		$this->set('page_title', 'CSA Memo List');
		$this->Session->delete('cart_session_data');
		$this->Session->delete('matched_session_data');
		$this->Session->delete('combintaion_qty_data');
	
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
			$conditions = array('CsaMemo.memo_date >=' => $this->current_date() . ' 00:00:00', 'CsaMemo.memo_date <=' => $this->current_date() . ' 23:59:59');
			$office_conditions = array();
		} else {
			$conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId(), 'CsaMemo.memo_date >=' => $this->current_date() . ' 00:00:00', 'CsaMemo.memo_date <=' => $this->current_date() . ' 23:59:59');
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
		}
	
		$this->CsaMemo->recursive = 0;
		$this->paginate = array(
			'conditions' => $conditions,
			
			'order' => array('CsaMemo.id' => 'desc'),
			'limit' => 50
			);
	
		$this->set('memos', $this->paginate());
		
		
		$this->set('office_id', $this->UserAuth->getOfficeId());
		$this->loadModel('Office');
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		$office_id = isset($this->request->data['CsaMemo']['office_id']) != '' ? $this->request->data['CsaMemo']['office_id'] : 0;
		$territory_id = isset($this->request->data['CsaMemo']['territory_id']) != '' ? $this->request->data['CsaMemo']['territory_id'] : 0;
		$market_id = isset($this->request->data['CsaMemo']['market_id']) != '' ? $this->request->data['CsaMemo']['market_id'] : 0;
		
		
		/*$this->loadModel('Territory');
		$territory = $this->Territory->find('all', array(
			'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => 0
		));
					
		$data_array = array();
		
		foreach($territory as $key => $value)
		{
			$t_id=$value['Territory']['id'];
			$t_val=$value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
			$data_array[$t_id] =$t_val;
		}
				
		 $territories =$data_array;
		
		/*
		$territories = $this->CsaMemo->Territory->find('list', array(
			'conditions' => array('Territory.office_id' => $office_id),
			'order' => array('Territory.name' => 'asc')
			));
		
		
		if($territory_id){
			$markets = $this->CsaMemo->Market->find('list', array(
				'conditions' => array('Market.territory_id' => $territory_id),
				'order' => array('Market.name' => 'asc')
				));
		}else{
			$markets = array();
		}*/
		
		$outlets = $this->CsaMemo->Outlet->find('list', array(
			'conditions' => array('Outlet.market_id' => $market_id),
			'order' => array('Outlet.name' => 'asc')
			));
		$current_date = date('d-m-Y', strtotime($this->current_date()));
	
		/*
		 * Report generation query start ;
		 */
		if(!empty($requested_data)){
			if (!empty($requested_data['Memo']['office_id'])) {
				$office_id = $requested_data['Memo']['office_id'];
				$this->CsaMemo->recursive=-1;
				$sales_people=$this->CsaMemo->find('all',array(
					'fields'=>array('DISTINCT(sales_person_id) as sales_person_id','SalesPerson.name'),
					'joins'=>array(
						array('table' => 'sales_people',
							'alias' => 'SalesPerson',
							'type' => 'INNER',
							'conditions' => array(
								' SalesPerson.id=Memo.sales_person_id',
								'SalesPerson.office_id'=>$office_id
								)
							)
						),
					'conditions'=>array(
						'Memo.memo_date BETWEEN ? and ?'=>array(date('Y-m-d', strtotime($requested_data['Memo']['date_from'])),date('Y-m-d', strtotime($requested_data['Memo']['date_to'])))
						),
					));
				
				$sales_person=array();
				foreach ($sales_people as  $data) {
					$sales_person[]=$data['0']['sales_person_id'];
				}
				$sales_person=implode(',', $sales_person);
	   //pr($sales_person);
				if (!empty($sales_person)) {
					$product_quantity=$this->Memo->query(" SELECT m.sales_person_id,md.product_id,SUM(md.sales_qty) as pro_quantity
					   FROM csa_memos m RIGHT JOIN csa_memo_details md on md.csa_memo_id=m.id
					   WHERE (m.memo_date BETWEEN  '".date('Y-m-d', strtotime($requested_data['Memo']['date_from']))."' AND '". date('Y-m-d', strtotime($requested_data['Memo']['date_to']))."') AND sales_person_id IN (".$sales_person.")  GROUP BY m.sales_person_id,md.product_id");
					$this->set(compact('product_quantity','sales_people'));
				}
			}
		}
		
		
	
		$this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'requested_data', 'product_name'));
		
	}

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null) {
        $this->set('page_title', 'Csa Memo Details');
		$this->loadModel('CsaMemo');
        $this->CsaMemo->unbindModel(array('hasMany' => array('CsaMemoDetail')));
        $memo = $this->CsaMemo->find('first', array(
            'conditions' => array('CsaMemo.id' => $id)
            ));

        $this->loadModel('CsaMemoDetail');
        if (!$this->CsaMemo->exists($id)) {
            throw new NotFoundException(__('Invalid district'));
        }
        $this->CsaMemoDetail->recursive = 0;
        $memo_details = $this->CsaMemoDetail->find('all', array(
            'conditions' => array('CsaMemoDetail.csa_memo_id' => $id),
			'order' => array('Product.order'=>'asc')
            )
        );
        $this->set(compact('memo', 'memo_details'));
    }

    /**
     * admin_delete method
     *
     * @return void
     */
    public function admin_delete($id = null, $redirect=1) {
		
		$this->loadModel('Product');
		$this->loadModel('Memo');
		$this->loadModel('MemoDetail');
		$this->loadModel('Deposit');
		$this->loadModel('Collection');
	
        if ($this->request->is('post')) {

            $this->loadModel('CsaMemo');
            $memo_id_arr = $this->CsaMemo->find('first',array(
                'conditions' => array(
                    'CsaMemo.id'=> $id                    
                    )
                ));
			
			/*
            $this->loadModel('Store');
            $store_id_arr = $this->Store->find('first',array(
                'conditions' => array(
                    'Store.territory_id'=> $memo_info_arr['CsaMemo']['territory_id']
                    )
                ));

            $store_id = $store_id_arr['Store']['id'];

            for($memo_detail_count=0; $memo_detail_count < count($memo_info_arr['MemoDetail']); $memo_detail_count++)
			{
                $product_id = $memo_info_arr['MemoDetail'][$memo_detail_count]['product_id'];
                $sales_qty = $memo_info_arr['MemoDetail'][$memo_detail_count]['sales_qty'];
                $measurement_unit_id = $memo_info_arr['MemoDetail'][$memo_detail_count]['measurement_unit_id'];
                $base_quantity = $this->unit_convert($product_id,$measurement_unit_id,$sales_qty);
                $update_type = 'add';
                $this->update_current_inventory($base_quantity,$product_id,$store_id,$update_type);
            }*/

       

        $this->loadModel('CsaMemoDetail');
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->CsaMemo->id = $id;
        if (!$this->CsaMemo->exists()) {
            throw new NotFoundException(__('Invalid CSA Memo'));
        }
		
		// EC Calculation 
		$this->ec_calculation($memo_id_arr['CsaMemo']['gross_value'], $memo_id_arr['CsaMemo']['outlet_id'], $memo_id_arr['CsaMemo']['territory_id'], $memo_id_arr['CsaMemo']['memo_date'], 2);
		// OC Calculation 
		$this->oc_calculation($memo_id_arr['CsaMemo']['territory_id'], $memo_id_arr['CsaMemo']['gross_value'], $memo_id_arr['CsaMemo']['outlet_id'], $memo_id_arr['CsaMemo']['memo_date'], $memo_id_arr['CsaMemo']['memo_time'], 2);
		
		//pr($memo_id_arr);
		//exit;
		
		$memo_id = $memo_id_arr['CsaMemo']['id'];
		$memo_no = $memo_id_arr['CsaMemo']['csa_memo_no'];
		$this->CsaMemo->id = $memo_id;
		$this->CsaMemo->delete();
		$this->CsaMemoDetail->deleteAll(array('CsaMemoDetail.csa_memo_id' => $memo_id));
		$this->Deposit->deleteAll(array('Deposit.memo_id' => $memo_no));
		$this->Collection->deleteAll(array('Collection.memo_id' => $memo_no));
		
		if($redirect==1){
			//$this->flash(__('Memo was not deleted'), array('action' => 'index'));
			$this->flash(__('Csa Memo with details deleted!'), array('action' => 'index'));
			$this->redirect(array('action' => 'index'));
			
			//$this->flash(__('Csa Memo was not deleted'), array('action' => 'index'));
        	//$this->redirect(array('action' => 'index'));
			
		}else{
			
		}
		
		
			/*if ($this->CsaMemo->delete()) 
			{
				$this->CsaMemoDetail->csa_memo_id = $id;
				$delet = $this->CsaMemoDetail->deleteAll(array('CsaMemoDetail.csa_memo_id' => $id));
				if ($delet) {
					$this->flash(__('Csa Memo with details deleted'), array('action' => 'index'));
				}
			}*/
		
        	//$this->flash(__('Csa Memo was not deleted'), array('action' => 'index'));
        	//$this->redirect(array('action' => 'index'));
    	
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
        $this->loadModel('ProductCombination');
        $this->loadModel('Combination');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');

        $current_date = date('d-m-Y', strtotime($this->current_date()));


        /* ------- start get edit data -------- */
        $this->CsaMemo->recursive = 1;
        $options = array(
            'conditions' => array('CsaMemo.id' => $id)
        );

        $existing_record = $this->CsaMemo->find('first', $options);
		
        //pr($existing_record);die();
		
        $details_data = array();
        foreach ($existing_record['CsaMemoDetail'] as $detail_val) {
            $product = $detail_val['product_id'];
            $this->Combination->unbindModel(
                array('hasMany' => array('ProductCombination'))
                );
            $combination_list = $this->Combination->find('all', array(
                'conditions' => array('ProductCombination.product_id' => $product),
                'joins' => array(
                    array(
                        'alias' => 'ProductCombination',
                        'table' => 'product_combinations',
                        'type' => 'INNER',
                        'conditions' => 'Combination.id = ProductCombination.combination_id'
                        )
                    ),
                'fields' => array('Combination.all_products_in_combination'),
                'limit' => 1
                ));
            if (!empty($combination_list)) {
                $combined_product = $combination_list[0]['Combination']['all_products_in_combination'];
                $detail_val['combined_product'] = $combined_product;
            }
            $details_data[] = $detail_val;
        }
        $existing_record['CsaMemoDetail'] = $details_data;

        for ($i=0; $i < count($details_data); $i++) {
            $measurement_unit_id = $details_data[$i]['measurement_unit_id'];
            $this->loadModel('MeasurementUnit');
            $measurement_unit_name = $this->MeasurementUnit->find('all', array(
                'conditions'=>array('MeasurementUnit.id'=>$measurement_unit_id),
                'fields'=>array('name'),
                'recursive'=>-1
                ));
            $existing_record['CsaMemoDetail'][$i]['measurement_unit_name'] = $measurement_unit_name[0]['MeasurementUnit']['name'];
        }

        $existing_record['office_id'] = $existing_record['SalesPerson']['office_id'];
        $existing_record['territory_id'] = $existing_record['CsaMemo']['territory_id'];
        $existing_record['market_id'] = $existing_record['CsaMemo']['market_id'];
        $existing_record['outlet_id'] = $existing_record['CsaMemo']['outlet_id'];
        $existing_record['memo_date'] = date('d-m-Y',strtotime($existing_record['CsaMemo']['memo_date']));
		$existing_record['memo_time'] = date('d-m-Y',strtotime($existing_record['CsaMemo']['memo_time']));
        $existing_record['csa_memo_no'] = $existing_record['CsaMemo']['csa_memo_no'];
		$existing_record['memo_reference_no'] = $existing_record['CsaMemo']['memo_reference_no'];

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array();
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }

        $this->set('office_id', $existing_record['office_id']);
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

        $office_id = $existing_record['office_id'];
        $territory_id = $existing_record['territory_id'];
        $market_id = $existing_record['market_id'];
        $outlets = array();
        

        $this->loadModel('Territory');
        $territories = $this->Territory->find('list', array(
			'conditions' => array('Territory.office_id' => $office_id, 'User.user_group_id !=' => 1008),
			'joins' => array(
				array(
					'alias' => 'User',
					'table' => 'users',
					'type' => 'INNER',
					'conditions' => 'SalesPerson.id = User.sales_person_id'
				)
			),
			'order' => array('Territory.name' => 'asc'),
			'recursive' => 0
		));

        $markets = $this->Market->find('list', array(
            'conditions' => array('territory_id' => $territory_id),
            'order' => array('name' => 'asc')
            ));

        if ($market_id) {
            $outlets = $this->Outlet->find('list', array(
                'conditions' => array('market_id' => $market_id),
                'order' => array('name' => 'asc')
                ));
        }

        $this->loadModel('Store');
        $this->loadModel('Product');
        $this->loadModel('CurrentInventory');
        $store_info = $this->Store->find('first', array(
                'conditions' => array(
                'Store.territory_id' => $territory_id              
                ),
            'recursive'=>-1    
        ));
        $store_id = $store_info['Store']['id'];


        foreach ($existing_record['CsaMemoDetail'] as $key => $single_product) {

            $total_qty_arr = $this->CurrentInventory->find('all',array(
                'conditions' => array('store_id' => $store_id, 'product_id' => $single_product['product_id']),
                'fields'=>array('sum(qty) as total'),
                'recursive'=>-1
                ));
    
            $total_qty = $total_qty_arr[0][0]['total'];
          
            $sales_total_qty = $this->unit_convertfrombase($single_product['product_id'],$single_product['measurement_unit_id'],$total_qty);

            $existing_record['CsaMemoDetail'][$key]['stock_qty'] = $sales_total_qty;
        }

        
        $products_from_ci = $this->CurrentInventory->find('all', array('fields' => array('DISTINCT CurrentInventory.product_id'),
            'conditions' => array('CurrentInventory.store_id' => $store_id,'CurrentInventory.qty > ' => 0,'inventory_status_id'=>1)
        ));
        
        $product_ci=array();
        foreach ($products_from_ci as $each_ci) {
            $product_ci[]=$each_ci['CurrentInventory']['product_id'];
        }
        foreach ($existing_record['CsaMemoDetail'] as $value) {
            $product_ci[] = $value['product_id'];
        }

        $product_ci_in=implode(",",$product_ci);
  
        $product_list = $this->Product->find('list', array('conditions' => array('Product.id' => $product_ci),'order' => array('order' => 'asc')));
        
        /* ------- end get edit data -------- */


        /*-----------My Work--------------*/
        $this->loadModel('ProductPrice');
        $this->loadModel('ProductCombination');


        foreach ($existing_record['CsaMemoDetail'] as $key => $value) 
		{
            $existing_product_category_id_array = $this->Product->find('all',array(
                    'conditions'=>array('Product.id'=>$value['product_id']),
                    'fields'=>array('product_category_id'),
                    'recursive'=>-1
                ));

            $existing_product_category_id = $existing_product_category_id_array[0]['Product']['product_category_id'];

            if ($existing_product_category_id != 32) 
			{
                $individual_slab = array();
                $combined_slab = array();
                $all_combination_id = array();

                $retrieve_price_combination[$value['product_id']] = $this->ProductPrice->find('all',array(
                        'conditions'=>array('ProductPrice.product_id'=>$value['product_id'],'ProductPrice.has_combination'=>0)
                    ));

                foreach ($retrieve_price_combination[$value['product_id']][0]['ProductCombination'] as $key => $value2) {
                    $individual_slab[$value2['min_qty']]=$value2['price'];
                }

                
                $combination_info = $this->ProductCombination->find('first',array(
                        'conditions'=>array('ProductCombination.product_id'=>$value['product_id'],'ProductCombination.combination_id !=' => 0)
                    ));

                if (!empty($combination_info['ProductCombination']['combination_id'])) {
                    $combination_id = $combination_info['ProductCombination']['combination_id'];
                    $all_combination_id_info = $this->ProductCombination->find('all',array(
                            'conditions'=>array('ProductCombination.combination_id'=>$combination_id)
                        ));

                    $combined_product = '';
                    foreach ($all_combination_id_info as $key => $individual_combination_id) {
                        $all_combination_id[$individual_combination_id['ProductCombination']['product_id']]=$individual_combination_id['ProductCombination']['price'];

                        $individual_combined_product_id = $individual_combination_id['ProductCombination']['product_id'];

                        $combined_product = $combined_product . ',' . $individual_combined_product_id;
                    }
                    $trimmed_combined_product = ltrim($combined_product, ',');

                    $combined_slab[$combination_info['ProductCombination']['min_qty']]=$all_combination_id;

                    $matched_combined_product_id_array = explode(',', $trimmed_combined_product);
                    asort($matched_combined_product_id_array);
                    $matched_combined_product_id = implode(',', $matched_combined_product_id_array);
                }else{
                    $combined_slab=array();
                    $matched_combined_product_id='';
                }
                
                

                $edited_cart_data[$value['product_id']] = array(
                        'product_price'=>array(
                                'id'=>$retrieve_price_combination[$value['product_id']][0]['ProductPrice']['id'],
                                'product_id'=>$value['product_id'],
                                'general_price'=>$retrieve_price_combination[$value['product_id']][0]['ProductPrice']['general_price'],
                                'effective_date'=>$retrieve_price_combination[$value['product_id']][0]['ProductPrice']['effective_date']
                            ),
                        'individual_slab'=>$individual_slab,
                        'combined_slab'=>$combined_slab,
                        'combined_product'=>$matched_combined_product_id
                    );

                

                if (!empty($matched_combined_product_id)) {
                    $edited_matched_data[$matched_combined_product_id]=array(
                        'count'=>'4',
                        'is_matched_yet'=>'NO',
                        'matched_count_so_far'=>'2',
                        'matched_id_so_far'=>'63,65'
                    );

                    $edited_current_qty_data[$value['product_id']]=$value['sales_qty'];
                }
            }
         
        }

        if (!empty($edited_cart_data)) {
            $this->Session->write('cart_session_data', $edited_cart_data);
        }
        if (!empty($edited_matched_data)) {
            $this->Session->write('matched_session_data', $edited_matched_data);
        }
        if (!empty($edited_current_qty_data)) {
            $this->Session->write('combintaion_qty_data', $edited_current_qty_data);
        }


        $this->set('page_title', 'Edit Csa Memo');
        $this->CsaMemo->id = $id;
        if (!$this->CsaMemo->exists($id)) {
            throw new NotFoundException(__('Invalid CsaMemo'));
        }
        /* -------- create individual Product data --------- */

        

        global $cart_data, $matched_array;
        $cart_data = array();
        $matched_array = array();
        $qty_session_data = array();
        
		/* ---------creating prepare data---------- */
		$prepare_cart_data = array();
		if (!empty($filter_product['Product']['id'])) 
		{
			$prepare_cart_data[$filter_product['Product']['id']]['Product'] = $filter_product['Product'];
			$prepare_cart_data[$filter_product['Product']['id']]['ProductPrice'] = $filter_product['ProductPrice'];
			foreach ($filter_product['Individual_slab'] as $individual_slab_val) {
				$prepare_cart_data[$filter_product['Product']['id']]['Individual_slab'][$individual_slab_val['min_qty']] = $individual_slab_val['price'];
			}
			if (!empty($filter_product['Combination'])) {
				$prepare_cart_data[$filter_product['Product']['id']]['Combination'] = $filter_product['Combination'];
			}
			if (!empty($filter_product['Combination_id'])) {

				/* ---- start ------- */
				$this->Combination->recursive = 2;
				$condition_value1['Combination.id'] = $filter_product['Combination_id']; //$filter_product['Combination']['id'];
				$combination_slab_data_option = array(
					'conditions' => array($condition_value1),
					);
				$combination_slab_data = $this->Combination->find('all', $combination_slab_data_option);
				/* ----- end -------- */

				$combination_slab = array();
				foreach ($combination_slab_data as $combine_group) {
					foreach ($combine_group['ProductCombination'] as $combine_val) {
						$combination_slab[$combine_val['min_qty']][$combine_val['product_id']] = $combine_val['Childrel']['price'];
					}
				}
				if (!empty($combination_slab)) {
					$prepare_cart_data[$filter_product['Product']['id']]['Combined_slab'] = $combination_slab;
				}
			}
		}
            

        $user_office_id = $this->UserAuth->getOfficeId();

        /* ------ start code of sale type list ------ */
        $sale_type_list = array(
            //1 => 'SO Sales',
            2 => 'CSA Sales'
            );
		
		
		//start memo setting
		$this->loadModel('MemoSetting');
		$MemoSettings = $this->MemoSetting->find('all', array(
			//'conditions' => array('ChallanDetail.challan_id' => $challan['Challan']['id']),
			'order' => array('id'=>'asc'),
			'recursive' => 0,
			//'limit' => 100
			)
		);
		
		foreach($MemoSettings as $s_result)
		{
			//echo $s_result['MemoSetting']['name'].'<br>';
			if($s_result['MemoSetting']['name']=='stock_validation'){
				$stock_validation = $s_result['MemoSetting']['value'];	
			}
			if($s_result['MemoSetting']['name']=='stock_hit'){
				$stock_hit = $s_result['MemoSetting']['value'];	
			}
			
			if($s_result['MemoSetting']['name']=='ec_calculation'){
				$ec_calculation = $s_result['MemoSetting']['value'];	
			}
			if($s_result['MemoSetting']['name']=='oc_calculation'){
				$oc_calculation = $s_result['MemoSetting']['value'];	
			}
			
			if($s_result['MemoSetting']['name']=='sales_calculation'){
				$sales_calculation = $s_result['MemoSetting']['value'];	
			}
			if($s_result['MemoSetting']['name']=='stamp_calculation'){
				$stamp_calculation = $s_result['MemoSetting']['value'];	
			}
			//pr($MemoSetting);
		}
		
		$this->set(compact('stock_validation'));
		//end memo setting
		
        $user_office_id = $this->UserAuth->getOfficeId();

		$sales_person_list = $this->SalesPerson->find('list', array(
			'conditions' => array('SalesPerson.office_id' => $user_office_id)
			));

          
        if ($this->request->is('post')) {

            $csa_memo_no = $this->request->data['CsaMemo']['csa_memo_no'];

            $memo_id_arr = $this->CsaMemo->find('first',array(
                'conditions' => array(
                    'CsaMemo.csa_memo_no'=> $csa_memo_no                     
                    )
                ));

            $this->loadModel('Store');
            $store_id_arr = $this->Store->find('first',array(
                'conditions' => array(
                    'Store.territory_id'=> $memo_id_arr['CsaMemo']['territory_id']
                    )
                ));

            $store_id = $store_id_arr['Store']['id'];
			
            $csa_memo_id = $id;
			
			$this->admin_delete($csa_memo_id, 0);

            if ($this->request->data['CsaMemo']) 
			{
                $this->request->data['CsaMemo']['is_active'] = 1;
                //$this->request->data['CsaMemo']['status'] = ($this->request->data['CsaMemo']['credit_amount'] != 0) ? 1 : 2;
				
				if (array_key_exists('draft', $this->request->data)) {
					$this->request->data['CsaMemo']['status'] = 0;
					$message = "Memo Has Been Saved as Draft";
				}else{
					$message = "Memo Has Been Saved";
					$this->request->data['CsaMemo']['status'] = ($this->request->data['CsaMemo']['credit_amount'] != 0) ? 1 : 2;
				}
				
                $sales_person = $this->SalesPerson->find('list', array(
                    'conditions' => array('territory_id' => $this->request->data['CsaMemo']['territory_id']),
                    'order' => array('name' => 'asc')
                    ));
                
                $this->request->data['CsaMemo']['sales_person_id'] = key($sales_person);                    
                $this->request->data['CsaMemo']['memo_date'] = date('Y-m-d', strtotime($this->request->data['CsaMemo']['memo_date']));
				$this->request->data['CsaMemo']['entry_date'] = date('Y-m-d', strtotime($this->request->data['CsaMemo']['entry_date']));
                /*echo "<pre>";
                print_r($this->request->data['CsaMemo']);
                echo "</pre>";die();*/
				
				
				/*START ADD NEW*/
				//get office id 
				$office_id = $this->request->data['CsaMemo']['office_id'];
				
				//get thana id 
				$this->loadModel('Market');
				$market_info = $this->Market->find('first', array(
					'conditions' => array('Market.id' => $this->request->data['CsaMemo']['market_id']),
					'fields' => 'Market.thana_id',
					'order' => array('Market.id'=>'asc'),
					'recursive' => -1,
					//'limit' => 100
					)
				);
				$thana_id = $market_info['Market']['thana_id'];
				/*END ADD NEW*/
				

                $memoData['office_id'] = $this->request->data['CsaMemo']['office_id'];
                $memoData['territory_id'] = $this->request->data['CsaMemo']['territory_id'];
                $memoData['market_id'] = $this->request->data['CsaMemo']['market_id'];
                $memoData['outlet_id'] = $this->request->data['CsaMemo']['outlet_id'];
                $memoData['entry_date'] = $this->request->data['CsaMemo']['entry_date'];
                $memoData['memo_date'] = $this->request->data['CsaMemo']['memo_date'];
                $memoData['csa_memo_no'] = $this->request->data['CsaMemo']['csa_memo_no'];
                $memoData['gross_value'] = $this->request->data['CsaMemo']['gross_value'];
                $memoData['cash_recieved'] = $this->request->data['CsaMemo']['cash_recieved'];
                $memoData['credit_amount'] = $this->request->data['CsaMemo']['credit_amount'];
                $memoData['is_active'] = $this->request->data['CsaMemo']['is_active'];
                $memoData['status'] = $this->request->data['CsaMemo']['status'];
				
				$memoData['memo_time'] = $this->request->data['CsaMemo']['entry_date'];
				
				$memoData['entry_date'] = $this->request->data['CsaMemo']['entry_date'];
				
                $memoData['sales_person_id'] = $this->request->data['CsaMemo']['sales_person_id'];
                $memoData['id'] = $csa_memo_id;
                $memoData['action'] = 1;
                $memoData['from_app'] = 0;
				
				$memoData['is_program'] = 0;
				
				$memoData['memo_reference_no'] = $this->request->data['CsaMemo']['memo_reference_no'];
				
				$memoData['created_at'] = $this->current_datetime();
				$memoData['created_by'] = $this->UserAuth->getUserId();
				$memoData['updated_at'] = $this->current_datetime();
				$memoData['updated_by'] = $this->UserAuth->getUserId();
				
				$memoData['office_id'] = $office_id ? $office_id : 0;
				$memoData['thana_id'] = $thana_id ? $thana_id : 0;
				

                if ($this->CsaMemo->save($memoData)) 
				{
                    $csa_memo_id = $id;
					
					// EC Calculation 
					if($ec_calculation){
					$this->ec_calculation($memoData['gross_value'], $memoData['outlet_id'], $memoData['territory_id'], $memoData['memo_date'], 1);
					}

					// OC Calculation 
					if($oc_calculation){
					$this->oc_calculation($memoData['territory_id'], $memoData['gross_value'], $memoData['outlet_id'], $memoData['memo_date'], $memoData['memo_time'], 1);
					}
					
					$this->CsaMemoDetail->deleteAll(array('CsaMemoDetail.csa_memo_id'=>$csa_memo_id));
                    if ($csa_memo_id) {
                        if (!empty($this->request->data['CsaMemoDetail'])) {
                            $total_product_data = array();
                            $memo_details = array();
                            $memo_details['CsaMemoDetail']['csa_memo_id'] = $csa_memo_id;
                            foreach ($this->request->data['CsaMemoDetail']['product_id'] as $key => $val) {
                                $memo_details['CsaMemoDetail']['product_id'] = $val;
                                $memo_details['CsaMemoDetail']['measurement_unit_id'] = $this->request->data['CsaMemoDetail']['measurement_unit_id'][$key];
                                $memo_details['CsaMemoDetail']['price'] = $this->request->data['CsaMemoDetail']['Price'][$key];
                                $memo_details['CsaMemoDetail']['sales_qty'] = $this->request->data['CsaMemoDetail']['sales_qty'][$key];
                                $memo_details['CsaMemoDetail']['product_price_id'] = $this->request->data['CsaMemoDetail']['product_price_id'][$key];
                                $memo_details['CsaMemoDetail']['bonus_qty'] = $this->request->data['CsaMemoDetail']['bonus_product_qty'][$key];
								if ($this->request->data['CsaMemoDetail']['bonus_product_id'][$key] != 0) {
									$memo_details['CsaMemoDetail']['bonus_product_id'] = $this->request->data['CsaMemoDetail']['bonus_product_id'][$key];
								}else{
									$memo_details['CsaMemoDetail']['bonus_product_id'] = NULL;
								}
								
								//Start for bonus
								$memo_date = date('Y-m-d', strtotime($this->request->data['CsaMemo']['memo_date']));
								$bonus_product_id = $this->request->data['CsaMemoDetail']['bonus_product_id'];
								$bonus_product_qty = $this->request->data['CsaMemoDetail']['bonus_product_qty'];
								$memo_details['CsaMemoDetail']['bonus_id'] = 0;
								$memo_details['CsaMemoDetail']['bonus_scheme_id'] = 0;
								if($bonus_product_qty[$key] > 0)
								{
									//echo $bonus_product_id[$key].'<br>';
									//echo $bonus_product_id = $$bounus_result = $this->bouns_and_scheme_id_set($bonus_product_id, $memo_date);
									//echo '<pre>';
									$b_product_id = $bonus_product_id[$key];
									$bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $memo_date);
									$memo_details['CsaMemoDetail']['bonus_id'] = $bonus_result['bonus_id'];
								}
								//End for bouns
								
                                $total_product_data[] = $memo_details;
                            }
                            $this->CsaMemoDetail->saveAll($total_product_data);
                        }
                    }
                }
				
				
                $this->Session->setFlash(__('The Csa Memo has been Updated'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }                


        }

        $this->loadModel('ProductMeasurement');
        $product_measurement_units = $this->ProductMeasurement->find('list',array('fields'=>array('product_id','measurement_unit_id')));
        $product_category_id_list = $this->Product->find('list',array('fields'=>array('id','product_category_id')));
		  
        $this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'product_list', 'sale_type_list', 'existing_record', 'office_parent_id', 'product_measurement_units','product_category_id_list'));

      }
	  
	  
	  //for bonus and bouns schema
	public function bouns_and_scheme_id_set($b_product_id=0, $memo_date='')
	{
		$this->loadModel('Bonus');
		//$this->loadModel('OpenCombination');
		//$this->loadModel('OpenCombinationProduct');
		
		$bonus_result = array();
		
		$b_product_qty = 0;
		$bonus_id = 0;
		$bonus_scheme_id = 0;
		
		$bonus_info = $this->Bonus->find('first', array(
			'conditions' => array(
				'Bonus.effective_date <= ' => $memo_date,
				'Bonus.end_date >= ' => $memo_date,
				'Bonus.bonus_product_id' => $b_product_id
			),
			'recursive' => -1,
			)
		);
		
		//pr($bonus_info);
		
		if($bonus_info)
		{
			$bonus_table_id = $bonus_info['Bonus']['id'];
			$mother_product_id = $bonus_info['Bonus']['mother_product_id'];
			$mother_product_quantity = $bonus_info['Bonus']['mother_product_quantity'];
			
			$bonus_id = $bonus_table_id;
			
			//echo $bonus_id;
			//break;
		}
		
		
		/*echo 'Bonus = '.$bonus_id;
		echo '<br>';
		echo 'Bonus Scheme = '. $bonus_scheme_id;
		echo '<br>';
		echo '<br>';
		echo '<br>';*/
		
		$bonus_result['bonus_id'] = $bonus_id;
		$bonus_result['bonus_scheme_id'] = $bonus_scheme_id;
		
		return $bonus_result;
			
	}
	  
	  
	  // it will be called from memo not from memo_details 
	  public function ec_calculation($gross_value, $outlet_id, $terrority_id, $memo_date, $cal_type) {
        // from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
        // check gross_value >0

        if ($gross_value > 0) {
            $this->loadModel('Outlet');
            // from outlet_id, retrieve pharma or non-pharma
            $outlet_info = $this->Outlet->find('first', array(
                'conditions' => array(
                    'Outlet.id' => $outlet_id
                ),
                'recursive' => -1
            ));

            if (!empty($outlet_info)) {
                $is_pharma_type = $outlet_info['Outlet']['is_pharma_type'];
                // from memo_date , split month and get month name and compare month table with memo year
                $memoDate = strtotime($memo_date);
                $month = date("n", $memoDate);
                $year = date("Y", $memoDate);
                $this->loadModel('Month');

                // from outlet_id, retrieve pharma or non-pharma
                $fasical_info = $this->Month->find('first', array(
                    'conditions' => array(
                        'Month.month' => $month,
                        'Month.year' => $year
                    ),
                    'recursive' => -1
                ));


                if (!empty($fasical_info)) {
                    $this->loadModel('SaleTargetMonth');
                    if ($cal_type == 1) {
                        if ($is_pharma_type == 1) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_pharma_achievement' => "SaleTargetMonth.effective_call_pharma_achievement+1");
                        } else if ($is_pharma_type == 0) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_non_pharma_achievement' => "SaleTargetMonth.effective_call_non_pharma_achievement+1");
                        }
                    } else {
                        if ($is_pharma_type == 1) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_pharma_achievement' => "SaleTargetMonth.effective_call_pharma_achievement-1");
                        } else if ($is_pharma_type == 0) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_non_pharma_achievement' => "SaleTargetMonth.effective_call_non_pharma_achievement-1");
                        }
                    }

                    $conditions_arr = array('SaleTargetMonth.product_id' => 0, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'], 'SaleTargetMonth.month_id' => $fasical_info['Month']['id']);

                    $this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
                }
            }
        }
    }
	  
	  
	  
	  
	  // it will be called from csa_memo not from memo_details 
	  public function oc_calculation($terrority_id, $gross_value, $outlet_id, $memo_date, $memo_time, $cal_type) {
	
			// from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
			// check gross_value >0
			if ($gross_value > 0) {
				$this->loadModel('Memo');
				// this will be updated monthly , if done then increment else no action
				$month_first_date = date('Y-m-01', strtotime($memo_date));
				$count = $this->CsaMemo->find('count', array(
					'conditions' => array(
						'CsaMemo.outlet_id' => $outlet_id,
						'CsaMemo.memo_date >= ' => $month_first_date,
						'CsaMemo.memo_time < ' => $memo_time
					)
				));
	
				if ($count == 0) {
	
					$this->loadModel('Outlet');
					// from outlet_id, retrieve pharma or non-pharma
					$outlet_info = $this->Outlet->find('first', array(
						'conditions' => array(
							'Outlet.id' => $outlet_id
						),
						'recursive' => -1
					));
	
					if (!empty($outlet_info)) {
						$is_pharma_type = $outlet_info['Outlet']['is_pharma_type'];
						// from memo_date , split month and get month name and compare month table with memo year
						$memoDate = strtotime($memo_date);
						$month = date("n", $memoDate);
						$year = date("Y", $memoDate);
						$this->loadModel('Month');
						// from outlet_id, retrieve pharma or non-pharma
						$fasical_info = $this->Month->find('first', array(
							'conditions' => array(
								'Month.month' => $month,
								'Month.year' => $year
							),
							'recursive' => -1
						));
	
						if (!empty($fasical_info)) {
							$this->loadModel('SaleTargetMonth');
							if ($cal_type == 1) {
								if ($is_pharma_type == 1) {
									$update_fields_arr = array('SaleTargetMonth.outlet_coverage_pharma_achievement' => "SaleTargetMonth.outlet_coverage_pharma_achievement+1");
								} else if ($is_pharma_type == 0) {
									$update_fields_arr = array('SaleTargetMonth.outlet_coverage_non_pharma_achievement' => "SaleTargetMonth.outlet_coverage_non_pharma_achievement+1");
								}
							} else {
								if ($is_pharma_type == 1) {
									$update_fields_arr = array('SaleTargetMonth.outlet_coverage_pharma_achievement' => "SaleTargetMonth.outlet_coverage_pharma_achievement-1");
								} else if ($is_pharma_type == 0) {
									$update_fields_arr = array('SaleTargetMonth.outlet_coverage_non_pharma_achievement' => "SaleTargetMonth.outlet_coverage_non_pharma_achievement-1");
								}
							}
	
							$conditions_arr = array('SaleTargetMonth.product_id' => 0, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'], 'SaleTargetMonth.month_id' => $fasical_info['Month']['id']);
							$this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
							//pr($conditions_arr);
							//pr($update_fields_arr);
							//exit;
						}
					}
				}
			}
		}
	  

}
