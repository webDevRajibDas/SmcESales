<?php

App::uses('AppController', 'Controller');

/**
 * CurrentInventories Controller
 *
 * @property DistDistribu $DistDistribu
 * @property PaginatorComponent $Paginator
 */
class DistDistribuReportsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
	 
    public $components = array('Paginator', 'Filter.Filter');
    public $uses = array('Product', 'Division', 'District', 'ProductCategory', 'Challan', 'ChallanDetail', 'ProductCategory', 'Memo', 'MemoDetail', 'MeasurementUnit');


    public function admin_index() 
	{
        $this->set('page_title', 'Distric & Division Wise Distribution Report');
		
		ini_set('memory_limit', '512M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes
				
		//report type
		$unit_types = array(
			'1' => 'Sales Unit',
			'2' => 'Base Unit',
		);
		$this->set(compact('unit_types'));
		
		//for product type
		$product_types = $this->Product->ProductType->find('list', array('order'=>array('name'=>'asc')));
		$this->set(compact('product_types'));
		
		//$productCategories = $this->ProductCategory->find('list');
							
		$productCategories = $this->ProductCategory->find('list', array(
					'conditions'=>array('NOT' => array('ProductCategory.id'=>32)),
					'order'=>  array('name'=>'asc')
		));	
		
		//products
		$conditions = array(
				'NOT' => array('Product.product_category_id'=>32),
				'is_active' => 1
			);
		if($this->request->is('post') && $this->data['search']['product_categories_id']){
			$conditions['Product.product_category_id'] = $this->data['search']['product_categories_id'];
		}

		$conditions['is_virtual'] = 0;
		
		$product_list = $this->Product->find('list', array(
					'conditions'=> $conditions,
					'order'=>  array('order'=>'asc')
		));
		$this->set(compact('product_list'));
		
				
		//for divisions
		$divisions = $this->Division->find('list', array(
			//'conditions'=> $conditions,
			'order'=>  array('name'=>'asc')
		));	
		
		$conditions = array();
		if(!empty($this->data['search']['division_id'])){
			$conditions['Division.id'] = $this->data['search']['division_id'];
		}
		$divisions_2 = $this->Division->find('all', array(
			'conditions'=> $conditions,
			'order'=>  array('name'=>'asc')
		));
		// pr($divisions_2);exit;
		$this->Session->write('divisions_2', $divisions_2);	
		$divisions_3 = $this->Division->find('list', array(
			'conditions'=> $conditions,
			'order'=>  array('name'=>'asc')
		));
		$this->Session->write('divisions_3', $divisions_3);	
		//end for divisions
		
		$request_data = array();
		
		
		//product_measurement
		$product_measurement = $this->Product->find('list', array(
				//'conditions'=> $pro_conditions,
				'fields'=> array('Product.id', 'Product.sales_measurement_unit_id'),
				'order'=>  array('order'=>'asc'),
				'recursive'=> -1
			));
		$this->set(compact('product_measurement'));
		
						
        if($this->request->is('post'))
		{
			$request_data = $this->request->data;
			$this->Session->write('request_data', $request_data);
			
			//pr($request_data);
				
			
			
			$unit_type = $this->request->data['search']['unit_type'];
			//$this->set(compact('unit_type'));
			$unit_type_text = ($unit_type==2)?'Base':'Sales';
			$this->set(compact('unit_type_text'));
			
			$product_type_id = isset($this->request->data['search']['product_type_id']) != '' ? $this->request->data['search']['product_type_id'] : 0;
			
			
			//for products
			$conditions = 	array('NOT' => array('ProductCategory.id'=>32));	
			if($this->data['search']['product_categories_id']){
				$conditions['ProductCategory.id'] = $this->data['search']['product_categories_id'];
			}
			
			$p_conditions = array();
			if($product_type_id)$p_conditions['Product.product_type_id'] = $product_type_id;
			if($this->data['search']['product_id'])$p_conditions['Product.id']=$this->data['search']['product_id'];			
			
			if($p_conditions)
			{
				$categories_products = $this->ProductCategory->find('all', array(
					 'conditions' => $conditions,
					 'contain' => array(
						 'Product'=>array(
							   'conditions'=> $p_conditions
						  ),
					  ),
					  'order' => array('ProductCategory.id'=>'asc'),
					  'recursive' => 1,
					)
				);
			}
			else
			{
				$categories_products = $this->ProductCategory->find('all', array(
					 'conditions' => $conditions,
					  'order' => array('ProductCategory.id'=>'asc'),
					  'recursive' => 1,
					)
				);
			}
			$this->Session->write('categories_products', $categories_products);
			//pr($categories_products);
			//exit;	
				

			$date_from = $this->data['search']['date_from'];
			$date_to = $this->data['search']['date_to'];
			$this->set(compact('date_from', 'date_to'));
			
			
			//START FOR RESULT QUERY
			//for district ids
			
			$division_ids = array();
			foreach($divisions_2 as $division_r){
				array_push($division_ids,  $division_r['Division']['id']);
			}


			$distict_ids = array();
			foreach($divisions_2 as $division_r){
				foreach($division_r['District'] as $dis_r){
					//echo $dis_r['id'].'<br>';
					array_push($distict_ids,  $dis_r['id']);
				}
			}
			//pr($distict_ids);
			//for products ids
			
			$product_ids = array();
			foreach ($categories_products as $c_value)
            {
				foreach ($c_value['Product'] as $p_value){
					array_push($product_ids,  $p_value['id']);
				}
				
			}
			//pr($product_ids);
			
			$date_from = date('Y-m-d', strtotime($request_data['search']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['search']['date_to']));
			
			
			//For all without National
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0
			);
			
			$conditions['MemoDetail.product_id'] = $product_ids;
			
			$conditions['Division.id'] = $division_ids;
			$conditions['District.id'] = $distict_ids;
			// $conditions['MemoDetail.price >']=0;
			//pr($conditions);
			//exit;
			
			$results = $this->Memo->find('all', array(
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
					'alias' => 'ProductMeasurement',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => 'Product.id = ProductMeasurement.product_id AND 
							CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
							ELSE 
								MemoDetail.measurement_unit_id
							END =ProductMeasurement.measurement_unit_id'
				),
				array(
					'alias' => 'ProductMeasurementSales',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => '
							Product.id = ProductMeasurementSales.product_id 
							AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
				),
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'Memo.territory_id = Territory.id'
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
					'conditions' => 'Outlet.market_id = Market.id'
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
				),
				array(
					'alias' => 'Division',
					'table' => 'divisions',
					'type' => 'INNER',
					'conditions' => 'District.division_id = Division.id'
				)
			),
			//'fields' => array('Challan.id', 'ChallanDetail.id', 'ChallanDetail.product_id', 'Store.id', 'Store.office_id', 'Office.id', 'Territory.id', 'Territory.office_id', 'ThanaTerritory.thana_id', 'Thana.id', 'District.id', 'District.division_id'),
			'fields' => array(
				//'sum(MemoDetail.sales_qty) AS volume',
				'ROUND(SUM(
					(ROUND(
							(CASE WHEN MemoDetail.price >0 THEN MemoDetail.sales_qty END) * 
							(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
						,0)) / 
						(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
				),2,1) AS volume',
				'ROUND(SUM(
				(ROUND(
						(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
						(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
					,0)) / 
					(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
				),2,1) AS bonus',				
				'District.id', 
				'MemoDetail.product_id'
			),
			'group' => array('District.id', 'MemoDetail.product_id'),
			'recursive' => -1
			));
			
			// pr($results);
			//exit;
			
			$datas = array();
			foreach($results as $result)
			{
				$sales_qty = ($unit_type==1)?$result[0]['volume']:$this->unit_convert($result['MemoDetail']['product_id'], $product_measurement[$result['MemoDetail']['product_id']], $result[0]['volume']);
				
				$bonus_qty = ($unit_type==1)?$result[0]['bonus']:$this->unit_convert($result['MemoDetail']['product_id'], $product_measurement[$result['MemoDetail']['product_id']], $result[0]['bonus']);
					
				$total_qty = $sales_qty + $bonus_qty;
				
				$datas[$result['District']['id']][$result['MemoDetail']['product_id']] = array(
					'volume' => $total_qty,
				);
			}
			
			//pr($datas);
			
			$this->set(compact('datas'));
			$this->Session->write('datas', $datas);	
			
			//exit;
			//End for all
			
			
			//For National
			$conditions_2 = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
			);
			$conditions_2['MemoDetail.product_id'] = $product_ids;
			$conditions_2['District.division_id'] = $division_ids;
			$conditions2['Division.id'] = $division_ids;
			
		
			$results_2 = $this->Memo->find('all', array(
			'conditions'=> $conditions_2,
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
					'alias' => 'ProductMeasurement',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => 'Product.id = ProductMeasurement.product_id AND 
							CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
							ELSE 
								MemoDetail.measurement_unit_id
							END =ProductMeasurement.measurement_unit_id'
				),
				array(
					'alias' => 'ProductMeasurementSales',
					'table' => 'product_measurements',
					'type' => 'LEFT',
					'conditions' => '
							Product.id = ProductMeasurementSales.product_id 
							AND Product.sales_measurement_unit_id=ProductMeasurementSales.measurement_unit_id'
				),
				array(
					'alias' => 'Territory',
					'table' => 'territories',
					'type' => 'INNER',
					'conditions' => 'Memo.territory_id = Territory.id'
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
					'conditions' => 'Outlet.market_id = Market.id'
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
				),
				array(
					'alias' => 'Division',
					'table' => 'divisions',
					'type' => 'INNER',
					'conditions' => 'District.division_id = Division.id'
				)
			),
			//'fields' => array('Challan.id', 'ChallanDetail.id', 'ChallanDetail.product_id', 'Store.id', 'Store.office_id', 'Office.id', 'Territory.id', 'Territory.office_id', 'ThanaTerritory.thana_id', 'Thana.id', 'District.id', 'District.division_id'),
			'fields' => array(
				//'sum(MemoDetail.sales_qty) AS volume', 
				'ROUND(SUM(
					(ROUND(
							(CASE WHEN MemoDetail.price >0 THEN MemoDetail.sales_qty END) * 
							(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
						,0)) / 
						(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
				),2,1) AS volume',
				'ROUND(SUM(
				(ROUND(
						(CASE WHEN MemoDetail.price=0 THEN MemoDetail.sales_qty END) * 
						(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
					,0)) / 
					(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
				),2,1) AS bonus',
				'District.division_id', 
				'MemoDetail.product_id'
			),
			'group' => array('District.division_id', 'MemoDetail.product_id'),
			'recursive' => -1
			));
			
			//echo '<pre>';print_r($results_2);exit;
			
			// pr($results_2);exit;
			$datas_2 = array();
			foreach($results_2 as $result_2)
			{
				$sales_qty = ($unit_type==1)?$result_2[0]['volume']:$this->unit_convert($result_2['MemoDetail']['product_id'], $product_measurement[$result_2['MemoDetail']['product_id']], $result_2[0]['volume']);
				$bonus_qty = ($unit_type==1)?$result_2[0]['bonus']:$this->unit_convert($result_2['MemoDetail']['product_id'], $product_measurement[$result_2['MemoDetail']['product_id']], $result_2[0]['bonus']);
				
				$totalqty = $sales_qty + $bonus_qty;
				
				$datas_2[$result_2['District']['division_id']][$result_2['MemoDetail']['product_id']] = array(
					'volume' => $totalqty,
				);
			}
			
			//echo '<pre>';print_r($datas_2);exit;
			
			//pr($datas_2);
			//exit;
			$this->set(compact('datas_2'));
			$this->Session->write('datas_2', $datas_2);	
			//End for National
			
			//END FOR RESULT QUERY
			
          
    	}
		
		$this->set(compact('request_data', 'divisions', 'divisions_2', 'divisions_3', 'productCategories', 'categories_products'));
		
    
	}
	
	
	public function getProductSales($request_data, $district_id=0, $product_id=0, $division_id=0){
		
		$this->loadModel('Memo');
		
		$sales_data = array();
		
		//return 1;
		
		if($product_id) 
		{
			$date_from = date('Y-m-d', strtotime($request_data['search']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['search']['date_to']));
						
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status >' => 0,
			);
			
			$conditions['MemoDetail.product_id'] = $product_id;
			
			if($district_id > 0){
				$conditions['District.id'] = $district_id;
			}
			
			if($division_id > 0){
				$conditions['District.division_id'] = $division_id;
			}
			
			//pr($conditions);
			//exit;
			
            $result = $this->Memo->find('all', array(
			'conditions'=> $conditions,
			'joins' => array(
				array(
					'alias' => 'MemoDetail',
					'table' => 'memo_details',
					'type' => 'INNER',
					'conditions' => 'Memo.id = MemoDetail.memo_id'
				),
				array(
					'alias' => 'ThanaTerritory',
					'table' => 'thana_territories',
					'type' => 'INNER',
					'conditions' => 'ThanaTerritory.territory_id = Memo.territory_id'
				),
				array(
					'alias' => 'Thana',
					'table' => 'Thanas',
					'type' => 'INNER',
					'conditions' => 'ThanaTerritory.thana_id = Thana.id'
				),
				array(
					'alias' => 'District',
					'table' => 'districts',
					'type' => 'INNER',
					'conditions' => 'Thana.district_id = District.id'
				),
				/*array(
					'alias' => 'Division',
					'table' => 'divisions',
					'type' => 'INNER',
					'conditions' => 'District.division_id = Division.id'
				)*/
			),
			//'fields' => array('Challan.id', 'ChallanDetail.id', 'ChallanDetail.product_id', 'Store.id', 'Store.office_id', 'Office.id', 'Territory.id', 'Territory.office_id', 'ThanaTerritory.thana_id', 'Thana.id', 'District.id', 'District.division_id'),
			'fields' => array('sum(MemoDetail.sales_qty) AS volume', 'District.id', 'MemoDetail.product_id'),
			'group' => array('District.id', 'MemoDetail.product_id'),
			'recursive' => -1
			));
			
			
		
			//pr($result);
			//exit;
			
			return $result;	  
		}
	}
	
	
	//get product list
	public function get_product_list()
	{
		$view = new View($this);
		
        $form = $view->loadHelper('Form');	
		
		$this->loadModel('Product');
		
	    $product_categories_id = $this->request->data['product_categories_id'];
		
		$conditions = array(
			'NOT' => array('Product.product_category_id'=>32),
			'is_active' => 1
		);
		
		if($product_categories_id)
		{
			$ids = explode(',', $product_categories_id);
			$conditions['Product.product_category_id'] = $ids;
		}
		
		//pr($conditions);
		//exit;

		$conditions['Product.is_virtual'] = 0;
		
		$product_list = $this->Product->find('list', array(
			'conditions'=> $conditions,
			'order'=>  array('order'=>'asc')
		));
		
		
		if($product_list){	
			$form->create('search', array('role' => 'form', 'action'=>'index'))	;
			echo $form->input('product_id', array('id' => 'product_id', 'label'=>false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required'=> true, 'options'=> $product_list));
			$form->end();
		}else{
			echo '';	
		}
		
		
        $this->autoRender = false;
    }
	
	
	
	//xls download
	public function admin_dwonload_xls() 
    {
		$divisions_2 = $this->Session->read('divisions_2');
		$divisions_3 = $this->Session->read('divisions_3');
		
		$request_data = $this->Session->read('request_data');
		$categories_products = $this->Session->read('categories_products');
				
		$datas = $this->Session->read('datas');
		$datas_2 = $this->Session->read('datas_2');
				
						
		$header="";
		$data1="";
		
		//title row
		$total_products = '';
		$data1 .= ucfirst("District\t");
		foreach ($categories_products as $c_value)
		{
			$total_products+=count($c_value['Product']); 
			
			 if($c_value['Product']) 
			 {
				 foreach ($c_value['Product'] as $p_value)
				 {
				 	$data1 .= ucwords($p_value['name']."\t"); 
				 }
				 $data1 .= ucfirst('Total '.$c_value['ProductCategory']['name']."\t");
			 }
		}
		$data1 .= ucfirst("Total Sales\t"); 	
		//end title row
		
		
		$data1 .= "\n";
		
		
		foreach ($divisions_2 as $value)
	    { 
			//data rows
	    	$division_id = $value['Division']['id'];
			$data1 .= strtoupper($value['Division']['name']."\t"); 
			$data1 .= "\n";
			
			$division_row_total = 0;
			
			$c_total = array();
			$p_total = array();
			foreach($value['District'] as $dis_val)
			{ 
				 $district_id = $dis_val['id'];
				 $data1 .= ucwords($dis_val['name']."\t"); 
				 $f_t_qty = 0; 
				 foreach($categories_products as $c_value)
				 { 
					 $t_qty = 0; 
					 if($c_value['Product'])
					 {
						 foreach ($c_value['Product'] as $p_value)
						 {
						   $product_id = $p_value['id'];
						   $sales_qty = @$datas[$district_id][$product_id]['volume']?@$datas[$district_id][$product_id]['volume']:'0.00';
						   $t_qty+=$sales_qty;
						   $data1 .= ucfirst($sales_qty."\t"); 
						   @$p_total[$product_id]+=$sales_qty;
						 }
						 $data1 .= ucfirst(sprintf("%01.2f", $t_qty)."\t");
						 $f_t_qty+=$t_qty;
						 @$c_total[$c_value['ProductCategory']['id']]+=$t_qty;
					 }
				}
				
				$data1 .= ucfirst(sprintf("%01.2f", $f_t_qty)."\t");
				$data1 .= "\n";
				
				
				$division_row_total+=$f_t_qty;
				
			}
			
			
			//pr($p_total);
			//exit;
			
			//total row
			$data1 .= ucfirst("TOTAL :\t");
			$t_f = $division_row_total;
			foreach ($categories_products as $c_value)
			{
				if($c_value['Product'])
				{
					$t_c = $c_total[$c_value['ProductCategory']['id']];
					
					foreach ($c_value['Product'] as $p_value)
					{
						$t_p = $p_total[$p_value['id']];
						$data1 .= ucfirst(sprintf("%01.2f", $t_p)."\t");
					}
					$data1 .= ucfirst(sprintf("%01.2f", $t_c)."\t");
				}
			}
			$data1 .= ucfirst(sprintf("%01.2f", $t_f)."\t");
			$data1 .= "\n";
		}
		
		
		
		
		//NATIONAL WISE
		//title row
		$total_products = '';
		$data1 .= ucwords("Division\t");
		foreach ($categories_products as $c_value)
		{
			$total_products+=count($c_value['Product']); 
			
			 if($c_value['Product']) 
			 {
				 foreach ($c_value['Product'] as $p_value)
				 {
				 	$data1 .= ucwords($p_value['name']."\t"); 
				 }
				 $data1 .= ucfirst('Total '.$c_value['ProductCategory']['name']."\t");
			 }
		}
		$data1 .= ucfirst("Total Sales\t"); 	
		//end title row
		
		
		$data1 .= "\n";
		
		$c_total = array();
		$p_total = array();
		$division_row_total = 0;
		foreach ($divisions_3 as $key => $value)
	    { 
			//data rows
	    	//$division_id = $value['Division']['id'];
			$data1 .= strtoupper(str_replace('Sales Office', '', $value)."\t"); 
			//$data1 .= "\n";
						 
			 $f_t_qty = 0; 
			 foreach($categories_products as $c_value)
			 { 
				 $t_qty = 0; 
				 if($c_value['Product'])
				 {
					 foreach ($c_value['Product'] as $p_value)
					 {
					   $product_id = $p_value['id'];
					   $division_id = $key;
					   $sales_qty = @$datas_2[$division_id][$product_id]['volume']?@$datas_2[$division_id][$product_id]['volume']:'0.00';
					   $t_qty+=$sales_qty;
					   $data1 .= ucfirst($sales_qty."\t"); 
					   @$p_total[$product_id]+=$sales_qty;
					 }
					 $data1 .= ucfirst(sprintf("%01.2f", $t_qty)."\t");
					 $f_t_qty+=$t_qty;
					 @$c_total[$c_value['ProductCategory']['id']]+=$t_qty;
				 }
			}
			
			$data1 .= ucfirst(sprintf("%01.2f", $f_t_qty)."\t");
			$data1 .= "\n";
			
			
			$division_row_total+=$f_t_qty;
		}
			
			
			
		//pr($p_total);
		//exit;
		
		//total row
		$data1 .= ucfirst("TOTAL :\t");
		$t_f = $division_row_total;
		foreach ($categories_products as $c_value)
		{
			if($c_value['Product'])
			{
				$t_c = $c_total[$c_value['ProductCategory']['id']];
				
				foreach ($c_value['Product'] as $p_value)
				{
					$t_p = $p_total[$p_value['id']];
					$data1 .= ucfirst(sprintf("%01.2f", $t_p)."\t");
				}
				$data1 .= ucfirst(sprintf("%01.2f", $t_c)."\t");
			}
		}
		$data1 .= ucfirst(sprintf("%01.2f", $t_f)."\t");
		$data1 .= "\n";
		//END NATIONAL WISE
		
		
		
		
		
		$data1 = str_replace("\r", "", $data1);
		if ($data1 == ""){
			$data1 = "\n(0) Records Found!\n";
		}
		
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: attachment; filename=\"Distric-&-Division-Wise-Distribution-Reports-".date("jS-F-Y-H:i:s").".xls\"");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		echo $data1; 
		exit;
		
		$this->autoRender = false;
	}
}


