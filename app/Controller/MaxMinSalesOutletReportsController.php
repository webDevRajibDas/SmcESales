<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MaxMinSalesOutletReportsController extends AppController {
	
	

/**
 * Components
 *
 * @var array
 */
 
	public $uses = array('Product','ProductCategory','Memo', 'Office', 'Territory', 'OutletCategory', 'Outlet', 'SalesPerson', 'Brand', 'ProductCategory', 'TerritoryAssignHistory');
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
				
		
		$this->set('page_title', "Max/Min Sales Outlet Report");
		
		$territories = array();
		$districts = array();
		$thanas = array();
		$markets = array();
		$outlets = array();
		$request_data = array();
		$report_type = array();
		$so_list = array();
		$institute = array();
					
		//for region office list
		$region_offices = $this->Office->find('list', array(
			'conditions' => array('Office.office_type_id'=>3), 
			'order' => array('office_name' => 'asc')
		));
		
		//types
		$types = array(
			'territory' => 'By Terriotry',
			'so' => 'By SO',
		);
		$this->set(compact('types'));
		
		//sales_value
		$sales_values = array(
			'max' => 'Maximum',
			'min' => 'Minimum',
		);
		$this->set(compact('sales_values'));
		
		//sales
		$sales = array(
			'sales_qty' => 'Qty',
			'price' => 'Value',
		);
		$this->set(compact('sales'));

		//for outlet category list
		$outlet_categories = $this->OutletCategory->find('list', array(
			'conditions' => array('is_active'=>1),
			'order' => array('category_name' => 'asc')
		));
		$this->set(compact('outlet_categories'));


        /*$productCategories = $this->ProductCategory->find('list', array(
            'conditions'=>array('NOT' => array('ProductCategory.id'=>32)),
            'order'=>  array('name'=>'asc')
        ));
        $this->set(compact('productCategories'));*/
		
		$columns = array(
            'product' => 'By Product',
            'brand' => 'By Brand',
            'category' => 'By Category'
        );
        $this->set(compact('columns'));


		//for brands list
        $conditions = array(
            'NOT' => array('Brand.id'=>44)
        );
        $brands = $this->Brand->find('list', array(
            'conditions'=> $conditions,
            'order'=>  array('name'=>'asc')
        ));
        $this->set(compact('brands'));


        //for cateogry list
        $conditions = array(
            'NOT' => array('ProductCategory.id'=>32)
        );
        $categories = $this->ProductCategory->find('list', array(
            'conditions'=> $conditions,
            'order'=>  array('name'=>'asc')
        ));
        $this->set(compact('categories'));
		
		//for product list
        $product_list = $this->Product->find('list', array(
			'conditions'=> array(
				'NOT' => array('Product.product_category_id'=>32),
				'is_active' => 1,
				'Product.product_type_id' => 1,
				'Product.is_virtual' =>0
			),
			'order'=>  array('order'=>'asc')
		));
        $this->set(compact('product_list'));

		
		//report type
		$unit_types = array(
			'1' => 'Sales Unit',
			'2' => 'Base Unit',
		);
		$this->set(compact('unit_types'));
		
		
		$memo_values = array(
			'0' => 'No',
			'1' => 'Yes',
		);
        $this->set(compact('memo_values'));
		
		
		//product_measurement
		$product_measurement = $this->Product->find('list', array(
				//'conditions'=> $pro_conditions,
				'fields'=> array('Product.id', 'Product.sales_measurement_unit_id'),
				'order'=>  array('order'=>'asc'),
				'recursive'=> -1
			));
		$this->set(compact('product_measurement'));	
		
		
		$region_office_id = 0;
				
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		$this->set(compact('office_parent_id'));
		
		$office_conditions = array('Office.office_type_id'=>2);
		
		if ($office_parent_id == 0)
		{
			$office_id = 0;
		}
		elseif($office_parent_id == 14)
		{
			$region_office_id = $this->UserAuth->getOfficeId();
			$region_offices = $this->Office->find('list', array(
				'conditions' => array('Office.office_type_id'=>3, 'Office.id'=>$region_office_id), 
				'order' => array('office_name' => 'asc')
			));
			
			$office_conditions = array('Office.parent_office_id'=>$region_office_id);
			
			$office_id = 0;
			
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,
					
					"NOT" => array( "id" => array(30, 31, 37))
					), 
				'order'=>array('office_name'=>'asc')
			));
			
			$office_ids = array_keys($offices);
			
			if($office_ids)$conditions['Territory.office_id'] = $office_ids;
					
			//pr($conditions);
			//exit;
						
		}
		else 
		{
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id'=>2);
			$office_id = $this->UserAuth->getOfficeId();
			
			$offices = $this->Office->find('list', array(
				'conditions'=> array(
					'id' 	=> $office_id,
				),	 
				'order'=>array('office_name'=>'asc')
			));
			
			/*$territories = $this->Territory->find('list', array(
				'conditions' => array('Territory.office_id' => $office_id),
				'order' => array('Territory.name' => 'asc')
				));	*/
				
			$territory_list = $this->Territory->find('all', array(
				'conditions' => array(
					'Territory.office_id' => $office_id, 
					/* 'User.user_group_id !=' => 1008 */
				),
				'joins' => array(
					array(
						'alias' => 'User',
						'table' => 'users',
						'type' => 'LEFT',
						'conditions' => 'SalesPerson.id = User.sales_person_id'
					)
				),
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));
			
			$territories = array();
		
			foreach($territory_list as $key => $value)
			{
				$territories[$value['Territory']['id']] = $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
			}
			
			//get SO list
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id','SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => 4,
				),
				'recursive'=> 0
			));	
			foreach($so_list_r as $key => $value)
			{
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
			}						
		}
		
		//pr($offices);
		
		if($this->request->is('post') || $this->request->is('put'))
		{
						
			$request_data = $this->request->data;
			
			//pr($request_data);
			
			$offices_all = $this->Office->find('list', array(
					'conditions'=> array(
						'office_type_id' 	=> 2,
						//'parent_office_id' 	=> $region_office_id,
						
						"NOT" => array( "id" => array(30, 31, 37))
						), 
					'order'=>array('office_name'=>'asc')
				));
			$this->set(compact('offices_all'));
			
			$date_from = date('Y-m-d', strtotime($request_data['MaxMinSalesOutletReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['MaxMinSalesOutletReports']['date_to']));
			$this->set(compact('date_from', 'date_to'));
			
			$type = $this->request->data['MaxMinSalesOutletReports']['type'];
			$this->set(compact('type'));
			
			$region_office_id = isset($this->request->data['MaxMinSalesOutletReports']['region_office_id']) != '' ? $this->request->data['MaxMinSalesOutletReports']['region_office_id'] : $region_office_id;
			$this->set(compact('region_office_id'));
			$office_ids = array();
			if($region_office_id)
			{
				$offices = $this->Office->find('list', array(
					'conditions'=> array(
						'office_type_id' 	=> 2,
						'parent_office_id' 	=> $region_office_id,
						
						"NOT" => array( "id" => array(30, 31, 37))
						), 
					'order'=>array('office_name'=>'asc')
				));
				
				$office_ids = array_keys($offices);
			}
			else
			{
				$offices = $this->Office->find('list', array(
					'conditions'=> array(
						'office_type_id' 	=> 2,
						
						"NOT" => array( "id" => array(30, 31, 37))
						), 
					'order'=>array('office_name'=>'asc')
				));
				
				$office_ids = array_keys($offices);
			}
			
			$office_id = isset($this->request->data['MaxMinSalesOutletReports']['office_id']) != '' ? $this->request->data['MaxMinSalesOutletReports']['office_id'] : $office_id;
			$this->set(compact('office_id'));
			
			$territory_id = isset($this->request->data['MaxMinSalesOutletReports']['territory_id']) != '' ? $this->request->data['MaxMinSalesOutletReports']['territory_id'] : 0;
			$this->set(compact('territory_id'));
			
			$so_id = isset($this->request->data['MaxMinSalesOutletReports']['so_id']) != '' ? $this->request->data['MaxMinSalesOutletReports']['so_id'] : 0;
			$this->set(compact('so_id'));
			
			$unit_type = $this->request->data['MaxMinSalesOutletReports']['unit_type'];
			$this->set(compact('unit_type'));
			$unit_type_text = ($unit_type==2)?'Base':'Sales';
			$this->set(compact('unit_type_text'));
			
			$column = $this->request->data['MaxMinSalesOutletReports']['columns'];
			$this->set(compact('column'));
			
			$memo_value = $this->request->data['MaxMinSalesOutletReports']['memo_value'];
			$this->set(compact('memo_value'));
			
			//territory list
			$territory_list = $this->Territory->find('all', array(
				'conditions' => array('Territory.office_id' => $office_id,/*  'User.user_group_id !=' => 1008 */),
				'joins' => array(
					array(
						'alias' => 'User',
						'table' => 'users',
						'type' => 'LEFT',
						'conditions' => 'SalesPerson.id = User.sales_person_id'
					)
				),
				'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
				'order' => array('Territory.name' => 'asc'),
				'recursive' => 0
			));
			
			$territories = array();
		
			foreach($territory_list as $key => $value)
			{
				$territories[$value['Territory']['id']] = $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
			}	
			
			
			//get SO list
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id','SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => 4,
				),
				'recursive'=> 0
			));	
			
			foreach($so_list_r as $key => $value){
			  $so_list[$value['SalesPerson']['id']]=$value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
			}
			
			//add old so from territory_assign_histories
			if($office_id)
			{
				$conditions = array('Territory.office_id' => $office_id, 'TerritoryAssignHistory.assign_type'=>2);
				// $conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
				$conditions['TerritoryAssignHistory.date >='] = $date_from;
				//pr($conditions);
				$old_so_list = $this->TerritoryAssignHistory->find('all', array(
					'conditions' => $conditions,
					'order'=>  array('Territory.name'=>'asc'),
					'recursive'=> 0
				));
				if($old_so_list){
					foreach($old_so_list as $old_so){
						$so_list[$old_so['TerritoryAssignHistory']['so_id']] = $old_so['SalesPerson']['name'];
					}
				}
			}
			
			$outlet_category_id = isset($this->request->data['MaxMinSalesOutletReports']['outlet_category_id']) != '' ? $this->request->data['MaxMinSalesOutletReports']['outlet_category_id'] : 0;
			
			$brand_id = isset($this->request->data['MaxMinSalesOutletReports']['brand_id']) != '' ? $this->request->data['MaxMinSalesOutletReports']['brand_id'] : 0;
						
			$product_id = isset($this->request->data['MaxMinSalesOutletReports']['product_id']) != '' ? $this->request->data['MaxMinSalesOutletReports']['product_id'] : 0;
						
			$product_category_id = isset($this->request->data['MaxMinSalesOutletReports']['product_category_id']) != '' ? $this->request->data['MaxMinSalesOutletReports']['product_category_id'] : 0;
			
			$no_of_outlet = isset($this->request->data['MaxMinSalesOutletReports']['no_of_outlet']) != '' && (int)$this->request->data['MaxMinSalesOutletReports']['no_of_outlet'] ? $this->request->data['MaxMinSalesOutletReports']['no_of_outlet'] : 10;
			$this->set(compact('no_of_outlet'));
			
			$sales_value = $this->request->data['MaxMinSalesOutletReports']['sales_value'];
			$this->set(compact('sales_value'));
			$sales_type = $this->request->data['MaxMinSalesOutletReports']['sales'];
			$this->set(compact('sales_type'));
						
			//For Query Conditon
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				'MemoDetail.price >' => 0
			);
			
			
			if($office_ids)$conditions['Memo.office_id'] = $office_ids;
			if($office_id)$conditions['Memo.office_id'] = $office_id;	
						
			if($type=='so'){
				if($so_id)$conditions['Memo.sales_person_id'] = $so_id;
			}else{
				if($territory_id)$conditions['Memo.territory_id'] = $territory_id;
			}
			
			if($outlet_category_id)$conditions['Outlet.category_id'] = $outlet_category_id;
			if($product_id)$conditions['MemoDetail.product_id'] = $product_id;
			if($brand_id)$conditions['Product.brand_id'] = $brand_id;	
			if($product_category_id)$conditions['Product.product_category_id'] = $product_category_id;			
			
			//pr($conditions);
			//exit;
			
			$order = array();
			if($sales_value=='min'){
				$order = array($sales_type.' asc', 'Product.order', 'Market.name asc', 'Outlet.name asc');
			}else{
				$order = array($sales_type.' desc', 'Product.order', 'Market.name asc', 'Outlet.name asc');
			}

			$q_results = $this->Memo->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
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
						'conditions' => 'MemoDetail.product_id = Product.id'
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Memo.territory_id = Territory.id'
					),
					array(
						'alias' => 'SalesPeople',
						'table' => 'sales_people',
						'type' => 'INNER',
						'conditions' => 'Memo.sales_person_id = SalesPeople.id'
					),
					array(
						'alias' => 'Outlet',
						'table' => 'outlets',
						'type' => 'INNER',
						'conditions' => 'Memo.outlet_id = Outlet.id'
					),
					array(
						'alias' => 'Market',
						'table' => 'markets',
						'type' => 'INNER',
						'conditions' => 'Memo.market_id = Market.id'
					),
					array(
						'alias' => 'Thana',
						'table' => 'thanas',
						'type' => 'INNER',
						'conditions' => 'Market.thana_id = Thana.id'
					),
					array(
						'alias' => 'District',
						'table' => 'districts',
						'type' => 'INNER',
						'conditions' => 'Thana.district_id = District.id'
					)
				),
								
				'fields' => array('COUNT(Memo.memo_no) as ec', 'SUM(MemoDetail.sales_qty) as sales_qty', 'SUM(MemoDetail.sales_qty*MemoDetail.price) as price', 'Memo.outlet_id', 'Outlet.name', 'MemoDetail.product_id', 'Product.name', 'Product.order', 'Product.brand_id', 'Product.product_category_id', 'Market.name', 'Thana.name', 'District.name', 'Territory.name', 'Memo.office_id', 'SalesPeople.name'),
				
				'group' => array('Memo.outlet_id', 'Outlet.name', 'MemoDetail.product_id', 'Product.name', 'Product.order', 'Product.brand_id', 'Product.product_category_id', 'Market.name', 'Thana.name', 'District.name', 'Territory.name', 'Memo.office_id', 'SalesPeople.name'),
				
				//'order' => array('sales_qty desc', 'Market.name asc', 'Outlet.name asc'),
				
				'order' => $order,
				
				'recursive' => -1,
				//'limit' => 200
			));	
			
			/*pr($q_results);
			exit;*/
			
			$results = array();
			
			if($memo_value)
			{
				$type_results = array();					
				foreach($q_results as $result)
				{
					$type_results[$result['Memo']['memo_no']][$result['Memo']['outlet_id']][$result['MemoDetail']['product_id']] = array(
						'product_id' 			=> $result['MemoDetail']['product_id'],
						'brand_id' 				=> $result['Product']['brand_id'],
						'product_category_id' 	=> $result['Product']['product_category_id'],
						'sales_qty' 			=> $result[0]['sales_qty'],
						'price' 				=> $result[0]['price'],
						'ec' 					=> $result[0]['ec'],
						
						'territory_name' 		=> $result['Territory']['name'],
						'office_name' 			=> $offices[$result['Memo']['office_id']],
						'so_name' 				=> $result['SalesPeople']['name'],
						'outlet_name' 			=> $result['Outlet']['name'],
						'outlet_id' 			=> $result['Memo']['outlet_id'],
						'market_name' 			=> $result['Market']['name'],
						'thana_name' 			=> $result['Thana']['name'],
						'district_name' 		=> $result['District']['name'],
					);
				}
				
				//pr($type_results);
				//exit;

				$i=0;
				foreach($type_results as $key_name => $outlet_datas)
				{
					foreach($outlet_datas as $outlet_id => $product_datas)
					{
						$sales_qty = 0;
						$price = 0;
						
						foreach($product_datas as $product_id => $p_result)
						{
							$sales_qty+= ($unit_type==1)?$p_result['sales_qty']:$this->unit_convert($p_result['product_id'], $product_measurement[$p_result['product_id']], $p_result['sales_qty']);
							
							$price+=$p_result['price'];							
						}
						
						$results[$sales_type=='price'?$price:$sales_qty][$key_name][$outlet_id] = array(
							'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
							//'product_id' 			=> $p_result['product_id'],
							'brand_id' 				=> $p_result['brand_id'],
							'product_category_id' 	=> $p_result['product_category_id'],
							
							'price' 				=> sprintf("%01.2f", $price),
							'ec' 					=> $p_result['ec'],
							
							'territory_name' 		=> $p_result['territory_name'],
							'office_name' 			=> $p_result['office_name'],
							'so_name' 				=> $p_result['so_name'],
							
							'outlet_name' 			=> $p_result['outlet_name'],	
							'outlet_id' 			=> $p_result['outlet_id'],			
							'market_name' 			=> $p_result['market_name'],
							'thana_name' 			=> $p_result['thana_name'],
							'district_name' 		=> $p_result['district_name']
						);
					}
					$i++;
					if($i==$no_of_outlet)break;
				}
				($sales_value=='min')?ksort($results):krsort($results);
				//pr($results);
				//exit;
			}
			else
			{
				if($column=='product')
				{
					foreach($q_results as $result)
					{
						$sales_qty = ($unit_type==1)?$result[0]['sales_qty']:$this->unit_convert($result['MemoDetail']['product_id'], $product_measurement[$result['MemoDetail']['product_id']], $result[0]['sales_qty']);				
						
						$results[$result['Product']['name']][$result['Memo']['outlet_id']] = array(
							'product_id' 			=> $result['MemoDetail']['product_id'],
							'brand_id' 				=> $result['Product']['brand_id'],
							'product_category_id' 	=> $result['Product']['product_category_id'],
							'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
							'price' 				=> $result[0]['price'],
							'ec' 					=> $result[0]['ec'],
							
							'territory_name' 		=> $result['Territory']['name'],
							'office_name' 			=> $offices[$result['Memo']['office_id']],
							'so_name' 				=> $result['SalesPeople']['name'],
							'outlet_name' 			=> $result['Outlet']['name'],
							'outlet_id' 			=> $result['Memo']['outlet_id'],
							'market_name' 			=> $result['Market']['name'],
							'thana_name' 			=> $result['Thana']['name'],
							'district_name' 		=> $result['District']['name'],
						);
					}
					
					//pr($results);
					
				}
				
				if($column=='brand')
				{
					$type_results = array();					
					foreach($q_results as $result)
					{
						$type_results[$brands[$result['Product']['brand_id']]][$result['Memo']['outlet_id']][$result['MemoDetail']['product_id']] = array(
							'product_id' 			=> $result['MemoDetail']['product_id'],
							'brand_id' 				=> $result['Product']['brand_id'],
							'product_category_id' 	=> $result['Product']['product_category_id'],
							'sales_qty' 			=> $result[0]['sales_qty'],
							'price' 				=> $result[0]['price'],
							'ec' 					=> $result[0]['ec'],
							
							'territory_name' 		=> $result['Territory']['name'],
							'office_name' 			=> $offices[$result['Memo']['office_id']],
							'so_name' 				=> $result['SalesPeople']['name'],
							'outlet_name' 			=> $result['Outlet']['name'],
							'outlet_id' 			=> $result['Memo']['outlet_id'],
							'market_name' 			=> $result['Market']['name'],
							'thana_name' 			=> $result['Thana']['name'],
							'district_name' 		=> $result['District']['name'],
						);
					}
					
					//pr($type_results);
					//exit;
					
					
					foreach($type_results as $key_name => $outlet_datas)
					{
						$i=0;
						foreach($outlet_datas as $outlet_id => $product_datas)
						{
							$sales_qty = 0;
							$price = 0;
							
							foreach($product_datas as $product_id => $p_result)
							{
								$sales_qty+= ($unit_type==1)?$p_result['sales_qty']:$this->unit_convert($p_result['product_id'], $product_measurement[$p_result['product_id']], $p_result['sales_qty']);
								
								$price+=$p_result['price'];							
							}
							
							$results[$sales_type=='price'?$price:$sales_qty][$key_name][$outlet_id] = 
								array(
									'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
									//'product_id' 			=> $p_result['product_id'],
									'brand_id' 				=> $p_result['brand_id'],
									'product_category_id' 	=> $p_result['product_category_id'],
									
									'price' 				=> sprintf("%01.2f", $price),
									'ec' 					=> $p_result['ec'],
									
									'territory_name' 		=> $p_result['territory_name'],
									'office_name' 			=> $p_result['office_name'],
									'so_name' 				=> $p_result['so_name'],
									
									'outlet_name' 			=> $p_result['outlet_name'],	
									'outlet_id' 			=> $p_result['outlet_id'],			
									'market_name' 			=> $p_result['market_name'],
									'thana_name' 			=> $p_result['thana_name'],
									'district_name' 		=> $p_result['district_name']
								);
								
							$i++;
							if($i==$no_of_outlet)break;
							
						}
					}
					//($sales_value=='min')?ksort($results):krsort($results);
					//pr($results);
					//exit;
				}
				
				if($column=='category')
				{
					$type_results = array();					
					foreach($q_results as $result)
					{
						$type_results[$categories[$result['Product']['product_category_id']]][$result['Memo']['outlet_id']][$result['MemoDetail']['product_id']] = array(
							'product_id' 			=> $result['MemoDetail']['product_id'],
							'brand_id' 				=> $result['Product']['brand_id'],
							'product_category_id' 	=> $result['Product']['product_category_id'],
							'sales_qty' 			=> $result[0]['sales_qty'],
							'price' 				=> $result[0]['price'],
							'ec' 					=> $result[0]['ec'],
							
							'territory_name' 		=> $result['Territory']['name'],
							'office_name' 			=> $offices[$result['Memo']['office_id']],
							'so_name' 				=> $result['SalesPeople']['name'],
							'outlet_name' 			=> $result['Outlet']['name'],
							'outlet_id' 			=> $result['Memo']['outlet_id'],
							'market_name' 			=> $result['Market']['name'],
							'thana_name' 			=> $result['Thana']['name'],
							'district_name' 		=> $result['District']['name'],
						);
					}
					
					//pr($type_results);
					//exit;
					
					
					foreach($type_results as $key_name => $outlet_datas)
					{
						$i=0;
						foreach($outlet_datas as $outlet_id => $product_datas)
						{
							$sales_qty = 0;
							$price = 0;
							
							foreach($product_datas as $product_id => $p_result)
							{
								$sales_qty+= ($unit_type==1)?$p_result['sales_qty']:$this->unit_convert($p_result['product_id'], $product_measurement[$p_result['product_id']], $p_result['sales_qty']);
								
								$price+=$p_result['price'];							
							}
							
							$results[$sales_type=='price'?$price:$sales_qty][$key_name][$outlet_id] = 
								array(
									'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
									//'product_id' 			=> $p_result['product_id'],
									'brand_id' 				=> $p_result['brand_id'],
									'product_category_id' 	=> $p_result['product_category_id'],
									
									'price' 				=> sprintf("%01.2f", $price),
									'ec' 					=> $p_result['ec'],
									
									'territory_name' 		=> $p_result['territory_name'],
									'office_name' 			=> $p_result['office_name'],
									'so_name' 				=> $p_result['so_name'],
									
									'outlet_name' 			=> $p_result['outlet_name'],	
									'outlet_id' 			=> $p_result['outlet_id'],			
									'market_name' 			=> $p_result['market_name'],
									'thana_name' 			=> $p_result['thana_name'],
									'district_name' 		=> $p_result['district_name']
								);
								
							$i++;
							if($i==$no_of_outlet)break;
							
						}
					}
					//($sales_value=='min')?ksort($results):krsort($results);
					//pr($results);
					//exit;
				}
			}
			
			
			//pr($results);
			//exit;
			
			$this->set(compact('results'));
			
						
		}
						
				
		$this->set(compact('offices', 'territories', 'outlet_type', 'region_offices', 'office_id', 'request_data', 'so_list'));
		
	}
	
	
	
	
	
}
