<?php
App::uses('AppController', 'Controller');
/**
 * ProjectionAchievementSettings Controller
 *
 * @property ProjectionAchievementReport $ProjectionAchievementReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ConsolidateStatementOfSalesController extends AppController {


    /**
     * Components
     *
     * @var array
     */

    public $uses = array('Product','ProductCategory','Memo', 'Office', 'Territory', 'Division', 'District', 'Thana', 'Market', 'OutletCategory', 'Outlet', 'TerritoryAssignHistory', 'SalesPerson', 'Brand');
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


        $this->set('page_title', 'Consolidate Statement Of Sales');
        $territories = array();
        $request_data = array();
        $report_type = array();
        $so_list = array();


        //types
        $types = array(
            'territory' => 'By Terriotry',
            'so' => 'By SO',
        );
        $this->set(compact('types'));

        $columns = array(
            'product' => 'By Product',
            'brand' => 'By Brand',
            'category' => 'By Category'
        );
        $this->set(compact('columns'));



        // For SO Wise or Territory Wise

        $territoty_selection = array(
            '1' => 'Territory Wise',
            '2' => 'SO Wise',
        );
        $this->set(compact('territoty_selection'));
		
		
		
		//report type
		$unit_types = array(
			'1' => 'Sales Unit',
			'2' => 'Base Unit',
		);
		$this->set(compact('unit_types'));
		


        //for region office list
        $region_offices = $this->Office->find('list', array(
            'conditions' => array('Office.office_type_id'=>3),
            'order' => array('office_name' => 'asc')
        ));

        //products
        $conditions = array(
            'NOT' => array('Product.product_category_id'=>32),
            'is_active' => 1,
			'product_type_id' => 1
        );
		$conditions['is_virtual']= 0;
        $product_list = $this->Product->find('list', array(
            'conditions'=> $conditions,
            'order'=>  array('order'=>'asc')
        ));
        $this->set(compact('product_list'));

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
				'conditions' => array('Territory.office_id' => $office_id),
				'joins' => array(
					array(
						'alias' => 'User',
						'table' => 'users',
						'type' => 'INNER',
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



        if($this->request->is('post') || $this->request->is('put'))
		{
			$request_data = $this->request->data;
			
			$date_from = date('Y-m-d', strtotime($request_data['ConsolidateStatementOfSales']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['ConsolidateStatementOfSales']['date_to']));
			$this->set(compact('date_from', 'date_to'));
			
			$type = $this->request->data['ConsolidateStatementOfSales']['type'];
			$this->set(compact('type'));
			
			$region_office_id = isset($this->request->data['ConsolidateStatementOfSales']['region_office_id']) != '' ? $this->request->data['ConsolidateStatementOfSales']['region_office_id'] : $region_office_id;
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
			
			$office_id = isset($this->request->data['ConsolidateStatementOfSales']['office_id']) != '' ? $this->request->data['ConsolidateStatementOfSales']['office_id'] : $office_id;
			$this->set(compact('office_id'));
			
			$territory_id = isset($this->request->data['ConsolidateStatementOfSales']['territory_id']) != '' ? $this->request->data['ConsolidateStatementOfSales']['territory_id'] : 0;
			$this->set(compact('territory_id'));
			
			$so_id = isset($this->request->data['ConsolidateStatementOfSales']['so_id']) != '' ? $this->request->data['ConsolidateStatementOfSales']['so_id'] : 0;
			$this->set(compact('so_id'));
			
			
			$unit_type = $this->request->data['ConsolidateStatementOfSales']['unit_type'];
			$unit_type_text = ($unit_type==2)?'Base':'Sales';
			$this->set(compact('unit_type_text'));
			
			
			$product_ids = isset($this->request->data['ConsolidateStatementOfSales']['product_id']) != '' ? $this->request->data['ConsolidateStatementOfSales']['product_id'] : 0;
			$brand_ids = isset($this->request->data['ConsolidateStatementOfSales']['brand_id']) != '' ? $this->request->data['ConsolidateStatementOfSales']['brand_id'] : 0;
			$product_category_ids = isset($this->request->data['ConsolidateStatementOfSales']['product_category_id']) != '' ? $this->request->data['ConsolidateStatementOfSales']['product_category_id'] : 0;
			
			
			
			//for product type selection
			$columns = $this->request->data['ConsolidateStatementOfSales']['columns'];
			
			
			
			
			//territory list
			$territory_list = $this->Territory->find('all', array(
				'conditions' => array('Territory.office_id' => $office_id),
				'joins' => array(
					array(
						'alias' => 'User',
						'table' => 'users',
						'type' => 'INNER',
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
			/*$so_list_r = $this->SalesPerson->find('all', array(
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
				$conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
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
			*/
			
			//NEW SO LIST GENERATE FROM MEMO TABLE
			$so_list_r = $this->SalesPerson->find('all',array(
				'fields' => array('distinct (Memo.sales_person_id) as so_id', 'SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
				'joins' => array(
						array('table' => 'memos',
							'alias' => 'Memo',
							'type' => 'INNER',
							'conditions' => 'SalesPerson.id=Memo.sales_person_id',
						),
						array(
							'alias' => 'Territory',
							'table' => 'territories',
							'type' => 'INNER',
							'conditions' => 'Memo.territory_id = Territory.id'
						),
					),
				'conditions'=>array(
					'Memo.memo_date BETWEEN ? and ?'=>array($date_from, $date_to),
					'Memo.office_id' => $office_id
					),
				'recursive'=> -1
				));
			
			foreach($so_list_r as $key => $value)
			{
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
			}
			
			
			
			//for report so list
			$con = array(
				'Memo.memo_date BETWEEN ? and ?'=>array($date_from, $date_to),
				'Memo.office_id' => $office_id
				);
			if($so_id)$con['Memo.sales_person_id']=$so_id;
			if($territory_id)$con['Memo.territory_id']=$territory_id;
			
			$so_list_r = $this->SalesPerson->find('all',array(
				'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Memo.territory_id', 'Territory.name', 'Memo.office_id',),
				'joins' => array(
						array('table' => 'memos',
							'alias' => 'Memo',
							'type' => 'INNER',
							'conditions' => 'SalesPerson.id=Memo.sales_person_id',
						),
						array(
							'alias' => 'Territory',
							'table' => 'territories',
							'type' => 'INNER',
							'conditions' => 'Memo.territory_id = Territory.id'
						),
					),
				'conditions'=>$con,
				'recursive'=> -1
				));
			$report_so_list = array();
			foreach($so_list_r as $key => $value)
			{
				$report_so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
			}
			$this->set(compact('report_so_list'));
			//end for report so list
			
			
			
			$outlet_category_id = isset($this->request->data['ConsolidateStatementOfSales']['outlet_category_id']) != '' ? $this->request->data['ConsolidateStatementOfSales']['outlet_category_id'] : 0;
									
			
			
			if($columns=='category')
			{
				//for cateogry list
				$c_conditions = array(
					'NOT' => array('ProductCategory.id'=>32)
				);
				if($product_category_ids)$c_conditions['ProductCategory.id'] = $product_category_ids;
				$f_list = $this->ProductCategory->find('list', array(
					'conditions'=> $c_conditions,
					'order'=>  array('name'=>'asc')
				));

			}
			elseif($columns=='brand')
			{
				
				//for brands list
				$b_conditions = array(
					'NOT' => array('Brand.id'=>44)
				);
				if($brand_ids)$b_conditions['Brand.id'] = $brand_ids;
				$f_list = $this->Brand->find('list', array(
					'conditions'=> $b_conditions,
					'order'=>  array('name'=>'asc')
				));
				
			}
			else
			{
				$p_conditions = array(
					'NOT' => array('Product.product_category_id'=>32),
					'is_active' => 1,
					'product_type_id' => 1
				);
				if($product_ids)$p_conditions['Product.id'] = $product_ids;
				$f_list = $this->Product->find('list', array(
					'conditions'=> $p_conditions,
					'order'=>  array('order'=>'asc')
				));
								
			}
			
			$this->set(compact('f_list'));
			
			
						
			//For Query Conditon
			
			//For Stokist Price
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				'MemoDetail.price >' => 0,
				'Memo.is_distributor' => 0
			);
			
			
			if($office_ids)$conditions['Memo.office_id'] = $office_ids;
			if($office_id)$conditions['Memo.office_id'] = $office_id;	
						
			if($type=='so'){
				if($so_id)$conditions['Memo.sales_person_id'] = $so_id;
			}else{
				if($territory_id)$conditions['Memo.territory_id'] = $territory_id;
			}
			
			
			
			if($product_ids)$conditions['MemoDetail.product_id'] = $product_ids;
			if($brand_ids)$conditions['Product.brand_id'] = $brand_ids;
			if($product_category_ids)$conditions['Product.product_category_id'] = $product_category_ids;
			
			$conditions['ProductCombinations.min_qty >'] = 1;
			
			
			//pr($conditions);
			
			
			
			$fields = array('SUM(MemoDetail.sales_qty) as sales_qty', 'SUM(MemoDetail.sales_qty*MemoDetail.price) as price', 'MemoDetail.product_id', 'Product.name', 'Product.sales_measurement_unit_id', 'Product.product_category_id', 'Product.brand_id', 'Product.order', 'SalesPeople.id', 'Territory.name');
				
			$group = array('MemoDetail.product_id', 'Product.name', 'Product.sales_measurement_unit_id', 'Product.order', 'Product.product_category_id', 'Product.brand_id', 'SalesPeople.id', 'Territory.name');
			
			$order = array('SalesPeople.id asc', 'Product.order asc');
			
			//pr($conditions);
			
			$s_q_results = $this->Memo->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
					array(
						'alias' => 'MemoDetail',
						'table' => 'memo_details',
						'type' => 'INNER',
						'conditions' => 'Memo.id = MemoDetail.memo_id'
					),
					array(
						'alias' => 'ProductCombinations',
						'table' => 'product_combinations',
						'type' => 'INNER',
						'conditions' => 'ProductCombinations.id = MemoDetail.product_price_id'
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
				),
								
				'fields' => $fields,
				
				'group' => $group,
				
				'order' => $order,
				
				'recursive' => -1,

			));	
			
			//pr($s_q_results);
			//exit;
						
			$s_results = array();
			
			if($columns=='category')
			{	
				$type_results = array();					
				foreach($s_q_results as $result)
				{
					$type_results[$result['SalesPeople']['id']][$result['Product']['product_category_id']][$result['MemoDetail']['product_id']] = 
					array(
						//'min_qty' 					=> $result['ProductCombinations']['min_qty'],
						'product_id' 				=> $result['MemoDetail']['product_id'],
						'product_name' 				=> $result['Product']['name'],
						'sales_qty' 				=> sprintf("%01.2f", $result[0]['sales_qty']),
						'price' 					=> sprintf("%01.2f", $result[0]['price']),
						'sales_measurement_unit_id' => $result['Product']['sales_measurement_unit_id'],
						'product_category_id' 		=> $result['Product']['product_category_id'],
					);
				}
				//pr($c_results);
				//exit;
				
				foreach($type_results as $so_te_name => $category_ids)
				{
					foreach($category_ids as $category_id => $product_datas)
					{
						$sales_qty = 0;
						$price = 0;
						foreach($product_datas as $product_id => $p_result)
						{
							$sales_qty+= ($unit_type==1)?$p_result['sales_qty']:$this->unit_convert($p_result['product_id'], $p_result['sales_measurement_unit_id'], $p_result['sales_qty']);
							$price+=$p_result['price'];
							
							$s_results[$so_te_name][$p_result['product_category_id']] = 
							array(
								'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
								'price' 				=> sprintf("%01.2f", $price),
							);
						}
					}
				}
				//exit;
				
			}
			elseif($columns=='brand')
			{	
				$type_results = array();					
				foreach($s_q_results as $result)
				{
					$type_results[$result['SalesPeople']['name']][$result['Product']['brand_id']][$result['MemoDetail']['product_id']] = 
					array(
						//'min_qty' 					=> $result['ProductCombinations']['min_qty'],
						'product_id' 				=> $result['MemoDetail']['product_id'],
						'product_name' 				=> $result['Product']['name'],
						'sales_qty' 				=> sprintf("%01.2f", $result[0]['sales_qty']),
						'price' 					=> sprintf("%01.2f", $result[0]['price']),
						'sales_measurement_unit_id' => $result['Product']['sales_measurement_unit_id'],
						'brand_id' 					=> $result['Product']['brand_id'],
					);
				}
				//pr($c_results);
				//exit;
				
				foreach($type_results as $so_te_name => $category_ids)
				{
					foreach($category_ids as $category_id => $product_datas)
					{
						$sales_qty = 0;
						$price = 0;
						foreach($product_datas as $product_id => $p_result)
						{
							$sales_qty+= ($unit_type==1)?$p_result['sales_qty']:$this->unit_convert($p_result['product_id'], $p_result['sales_measurement_unit_id'], $p_result['sales_qty']);
							$price+=$p_result['price'];
							
							$s_results[$so_te_name][$p_result['brand_id']] = 
							array(
								'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
								'price' 				=> sprintf("%01.2f", $price),
							);
						}
					}
				}
				//exit;
				
			}
			else
			{
				foreach($s_q_results as $result)
				{
					$sales_qty = ($unit_type==1)?$result[0]['sales_qty']:$this->unit_convert($result['MemoDetail']['product_id'], $result['Product']['sales_measurement_unit_id'], $result[0]['sales_qty']);
					
					$s_results[$result['SalesPeople']['id']][$result['MemoDetail']['product_id']] = 
					array(
						//'min_qty' 				=> $result['ProductCombinations']['min_qty'],
						'product_name' 			=> $result['Product']['name'],
						'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
						'price' 				=> sprintf("%01.2f", $result[0]['price']),
					);
				}
			}
			//break;
			
			
			//pr($s_results);
			//exit;
			
			$this->set(compact('s_results'));
			
			//For Query Conditon
			
			//For distribution Price 
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				'MemoDetail.price >' => 0,
				'Memo.is_distributor' => 1
			);
			
			
			if($office_ids)$conditions['Memo.office_id'] = $office_ids;
			if($office_id)$conditions['Memo.office_id'] = $office_id;	
						
			if($type=='so'){
				if($so_id)$conditions['Memo.sales_person_id'] = $so_id;
			}else{
				if($territory_id)$conditions['Memo.territory_id'] = $territory_id;
			}
			
			
			
			if($product_ids)$conditions['MemoDetail.product_id'] = $product_ids;
			if($brand_ids)$conditions['Product.brand_id'] = $brand_ids;
			if($product_category_ids)$conditions['Product.product_category_id'] = $product_category_ids;
			
			// $conditions['DistProductCombinations.min_qty >'] = 1;
			
			
			//pr($conditions);
			
			
			
			$fields = array('SUM(MemoDetail.sales_qty) as sales_qty', 'SUM(MemoDetail.sales_qty*MemoDetail.price) as price', 'MemoDetail.product_id', 'Product.name', 'Product.sales_measurement_unit_id', 'Product.product_category_id', 'Product.brand_id', 'Product.order', 'SalesPeople.id', 'Territory.name');
				
			$group = array('MemoDetail.product_id', 'Product.name', 'Product.sales_measurement_unit_id', 'Product.order', 'Product.product_category_id', 'Product.brand_id', 'SalesPeople.id', 'Territory.name');
			
			$order = array('SalesPeople.id asc', 'Product.order asc');
			
			//pr($conditions);
			
			$dist_q_results = $this->Memo->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
					array(
						'alias' => 'MemoDetail',
						'table' => 'memo_details',
						'type' => 'INNER',
						'conditions' => 'Memo.id = MemoDetail.memo_id'
					),
					array(
						'alias' => 'DistProductCombinations',
						'table' => 'dist_product_combinations',
						'type' => 'INNER',
						'conditions' => 'DistProductCombinations.id = MemoDetail.product_price_id'
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
				
				),
								
				'fields' => $fields,
				
				'group' => $group,
				
				'order' => $order,
				
				'recursive' => -1,

			));	
			
			//pr($s_q_results);
			//exit;
						
			$dist_results = array();
			
			if($columns=='category')
			{	
				$type_results = array();					
				foreach($dist_q_results as $result)
				{
					$type_results[$result['SalesPeople']['id']][$result['Product']['product_category_id']][$result['MemoDetail']['product_id']] = 
					array(
						//'min_qty' 					=> $result['ProductCombinations']['min_qty'],
						'product_id' 				=> $result['MemoDetail']['product_id'],
						'product_name' 				=> $result['Product']['name'],
						'sales_qty' 				=> sprintf("%01.2f", $result[0]['sales_qty']),
						'price' 					=> sprintf("%01.2f", $result[0]['price']),
						'sales_measurement_unit_id' => $result['Product']['sales_measurement_unit_id'],
						'product_category_id' 		=> $result['Product']['product_category_id'],
					);
				}
				//pr($c_results);
				//exit;
				
				foreach($type_results as $so_te_name => $category_ids)
				{
					foreach($category_ids as $category_id => $product_datas)
					{
						$sales_qty = 0;
						$price = 0;
						foreach($product_datas as $product_id => $p_result)
						{
							$sales_qty+= ($unit_type==1)?$p_result['sales_qty']:$this->unit_convert($p_result['product_id'], $p_result['sales_measurement_unit_id'], $p_result['sales_qty']);
							$price+=$p_result['price'];
							
							$dist_results[$so_te_name][$p_result['product_category_id']] = 
							array(
								'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
								'price' 				=> sprintf("%01.2f", $price),
							);
						}
					}
				}
				//exit;
				
			}
			elseif($columns=='brand')
			{	
				$type_results = array();					
				foreach($dist_q_results as $result)
				{
					$type_results[$result['SalesPeople']['name']][$result['Product']['brand_id']][$result['MemoDetail']['product_id']] = 
					array(
						//'min_qty' 					=> $result['ProductCombinations']['min_qty'],
						'product_id' 				=> $result['MemoDetail']['product_id'],
						'product_name' 				=> $result['Product']['name'],
						'sales_qty' 				=> sprintf("%01.2f", $result[0]['sales_qty']),
						'price' 					=> sprintf("%01.2f", $result[0]['price']),
						'sales_measurement_unit_id' => $result['Product']['sales_measurement_unit_id'],
						'brand_id' 					=> $result['Product']['brand_id'],
					);
				}
				//pr($c_results);
				//exit;
				
				foreach($type_results as $so_te_name => $category_ids)
				{
					foreach($category_ids as $category_id => $product_datas)
					{
						$sales_qty = 0;
						$price = 0;
						foreach($product_datas as $product_id => $p_result)
						{
							$sales_qty+= ($unit_type==1)?$p_result['sales_qty']:$this->unit_convert($p_result['product_id'], $p_result['sales_measurement_unit_id'], $p_result['sales_qty']);
							$price+=$p_result['price'];
							
							$dist_results[$so_te_name][$p_result['brand_id']] = 
							array(
								'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
								'price' 				=> sprintf("%01.2f", $price),
							);
						}
					}
				}
				//exit;
				
			}
			else
			{
				foreach($dist_q_results as $result)
				{
					$sales_qty = ($unit_type==1)?$result[0]['sales_qty']:$this->unit_convert($result['MemoDetail']['product_id'], $result['Product']['sales_measurement_unit_id'], $result[0]['sales_qty']);
					
					$dist_results[$result['SalesPeople']['id']][$result['MemoDetail']['product_id']] = 
					array(
						//'min_qty' 				=> $result['ProductCombinations']['min_qty'],
						'product_name' 			=> $result['Product']['name'],
						'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
						'price' 				=> sprintf("%01.2f", $result[0]['price']),
					);
				}
			}
			//break;
			
			
			//pr($dist_results);
			//exit;
			
			$this->set(compact('dist_results'));
			
			
			//For Retailer price
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				'MemoDetail.price >' => 0,
				'Memo.is_distributor' => 0
			);
			
			if($office_ids)$conditions['Memo.office_id'] = $office_ids;
			if($office_id)$conditions['Memo.office_id'] = $office_id;	
						
			if($type=='so'){
				if($so_id)$conditions['Memo.sales_person_id'] = $so_id;
			}else{
				if($territory_id)$conditions['Memo.territory_id'] = $territory_id;
			}
			
						
			if($product_ids)$conditions['MemoDetail.product_id'] = $product_ids;
			if($brand_ids)$conditions['Product.brand_id'] = $brand_ids;
			if($product_category_ids)$conditions['Product.product_category_id'] = $product_category_ids;
			
			$conditions['ProductCombinations.min_qty'] = 1;
			
			
			
			$r_q_results = $this->Memo->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
					array(
						'alias' => 'MemoDetail',
						'table' => 'memo_details',
						'type' => 'INNER',
						'conditions' => 'Memo.id = MemoDetail.memo_id'
					),
					array(
						'alias' => 'ProductCombinations',
						'table' => 'product_combinations',
						'type' => 'INNER',
						'conditions' => 'ProductCombinations.id = MemoDetail.product_price_id'
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
									
				),
								
				'fields' => array('SUM(MemoDetail.sales_qty) as sales_qty', 'SUM(MemoDetail.sales_qty*MemoDetail.price) as price', 'MemoDetail.product_id', 'Product.name', 'Product.sales_measurement_unit_id', 'Product.product_category_id', 'Product.brand_id', 'Product.order', 'SalesPeople.id', 'Territory.name', 'ProductCombinations.min_qty'),
				
				'group' => array('MemoDetail.product_id', 'Product.name', 'Product.sales_measurement_unit_id', 'Product.product_category_id', 'Product.brand_id', 'Product.order', 'SalesPeople.id', 'Territory.name', 'ProductCombinations.min_qty'),
				
				'order' => array('Product.order asc'),
				
				//'order' => $order,
				
				'recursive' => -1,
				//'limit' => 200
			));	
			//pr($r_q_results);
			//exit;
			
			$r_results = array();
			
			if($columns=='category')
			{	
				$type_results = array();					
				foreach($r_q_results as $result)
				{
					$type_results[$result['SalesPeople']['id']][$result['Product']['product_category_id']][$result['MemoDetail']['product_id']] = 
					array(
						//'min_qty' 					=> $result['ProductCombinations']['min_qty'],
						'product_id' 				=> $result['MemoDetail']['product_id'],
						'product_name' 				=> $result['Product']['name'],
						'sales_qty' 				=> sprintf("%01.2f", $result[0]['sales_qty']),
						'price' 					=> sprintf("%01.2f", $result[0]['price']),
						'sales_measurement_unit_id' => $result['Product']['sales_measurement_unit_id'],
						'product_category_id' 		=> $result['Product']['product_category_id'],
					);
				}
				//pr($c_results);
				//exit;
				
				foreach($type_results as $so_te_name => $category_ids)
				{
					foreach($category_ids as $category_id => $product_datas)
					{
						$sales_qty = 0;
						$price = 0;
						foreach($product_datas as $product_id => $p_result)
						{
							$sales_qty+= ($unit_type==1)?$p_result['sales_qty']:$this->unit_convert($p_result['product_id'], $p_result['sales_measurement_unit_id'], $p_result['sales_qty']);
							$price+=$p_result['price'];
							
							$r_results[$so_te_name][$p_result['product_category_id']] = 
							array(
								'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
								'price' 				=> sprintf("%01.2f", $price),
							);
						}
					}
				}
				//exit;
				
			}
			elseif($columns=='brand')
			{	
				$type_results = array();					
				foreach($r_q_results as $result)
				{
					$type_results[$result['SalesPeople']['id']][$result['Product']['brand_id']][$result['MemoDetail']['product_id']] = 
					array(
						//'min_qty' 					=> $result['ProductCombinations']['min_qty'],
						'product_id' 				=> $result['MemoDetail']['product_id'],
						'product_name' 				=> $result['Product']['name'],
						'sales_qty' 				=> sprintf("%01.2f", $result[0]['sales_qty']),
						'price' 					=> sprintf("%01.2f", $result[0]['price']),
						'sales_measurement_unit_id' => $result['Product']['sales_measurement_unit_id'],
						'brand_id' 					=> $result['Product']['brand_id'],
					);
				}
				//pr($c_results);
				//exit;
				
				foreach($type_results as $so_te_name => $brand_ids)
				{
					foreach($brand_ids as $brand_id => $product_datas)
					{
						$sales_qty = 0;
						$price = 0;
						foreach($product_datas as $product_id => $p_result)
						{
							$sales_qty+= ($unit_type==1)?$p_result['sales_qty']:$this->unit_convert($p_result['product_id'], $p_result['sales_measurement_unit_id'], $p_result['sales_qty']);
							$price+=$p_result['price'];
							
							$r_results[$so_te_name][$p_result['brand_id']] = 
							array(
								'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
								'price' 				=> sprintf("%01.2f", $price),
							);
						}
					}
				}
				//exit;
				
			}
			else
			{
				//pr($r_q_results);	
				foreach($r_q_results as $result)
				{
					$sales_qty = ($unit_type==1)?$result[0]['sales_qty']:$this->unit_convert($result['MemoDetail']['product_id'], $result['Product']['sales_measurement_unit_id'], $result[0]['sales_qty']);
									
					$r_results[$result['SalesPeople']['id']][$result['MemoDetail']['product_id']] = 
					array(
						//'min_qty' 				=> $result['ProductCombinations']['min_qty'],
						'product_name' 			=> $result['Product']['name'],
						'sales_qty' 			=> sprintf("%01.2f", $sales_qty),
						'price' 				=> sprintf("%01.2f", $result[0]['price']),
					);
					
					
				}
			}
			//pr($r_results);		
			//exit;
			$this->set(compact('r_results'));
			
			
			
			//Get total cahs and credit sales
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				//'MemoDetail.price >' => 0
			);
			
			if($office_ids)$conditions['Memo.office_id'] = $office_ids;
			if($office_id)$conditions['Memo.office_id'] = $office_id;	
						
			if($type=='so'){
				if($so_id)$conditions['Memo.sales_person_id'] = $so_id;
			}else{
				if($territory_id)$conditions['Memo.territory_id'] = $territory_id;
			}
						
			
			$sales_results = $this->Memo->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
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
				),
								
				'fields' => array('SUM(Memo.gross_value) as total_sales', 'SUM(Memo.cash_recieved) as cash_sales', 'SUM(Memo.credit_amount) as credit_sales', 'SalesPeople.id', 'Territory.name'),
				
				'group' => array('SalesPeople.id', 'Territory.name'),
				
				//'order' => array('Product.order asc'),
				
				'recursive' => -1,
				//'limit' => 200
			));	
			
			$cash_credit_sales = array();
			foreach($sales_results as $sales_data){
				$cash_credit_sales[$sales_data['SalesPeople']['id']] = 
					array(
						'total_sales' 			=> sprintf("%01.2f", $sales_data[0]['total_sales']),
						'cash_sales' 			=> sprintf("%01.2f", $sales_data[0]['cash_sales']),
						'credit_sales' 			=> sprintf("%01.2f", $sales_data[0]['credit_sales']),
					);
			}
			
			$this->set(compact('cash_credit_sales'));
			//pr($sales_total);
			//exit;
			
			
			
			//Get total credit collection
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				'Collection.is_credit_collection' => 1
			);
			
			if($office_ids)$conditions['Memo.office_id'] = $office_ids;
			if($office_id)$conditions['Memo.office_id'] = $office_id;	
						
			if($type=='so'){
				if($so_id)$conditions['Memo.sales_person_id'] = $so_id;
			}else{
				if($territory_id)$conditions['Memo.territory_id'] = $territory_id;
			}
			$credit_collection_results = $this->Memo->find('all', array(
				'conditions'=> $conditions,
				'joins' => array(
					array(
						'alias' => 'Collection',
						'table' => 'collections',
						'type' => 'INNER',
						'conditions' => 'Memo.memo_no = Collection.memo_no'
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
				),
								
				'fields' => array('SUM(Collection.collectionAmount) as creadit_collection', 'SalesPeople.id', 'Territory.name'),
				
				'group' => array('SalesPeople.id', 'Territory.name'),
				
				//'order' => array('Product.order asc'),
				
				'recursive' => -1,
				//'limit' => 200
			));	
			
			//pr($credit_collection_results);
			
			$credit_collections = array();
			foreach($credit_collection_results as $credit_collection_result){
				$credit_collections[$credit_collection_result['SalesPeople']['id']]= 
					array(
						'creadit_collection'=> sprintf("%01.2f", $credit_collection_result[0]['creadit_collection']),
					);
			}
			
			$this->set(compact('credit_collections'));
			//pr($credit_collections);
			//exit;
						
						
		}
				
		$this->set(compact('offices', 'territories', 'outlet_type', 'region_offices', 'office_id', 'request_data', 'so_list'));


    }



}
