<?php
App::uses('AppController', 'Controller');
/**
 * EsalesSettings Controller
 *
 * @property MigSaleReport $MigSaleReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MigSaleReportsController extends AppController {
	
	

/**
 * Components
 *
 * @var array
 */
 
	public $uses = array('Memo', 'MemoDetail', 'Office', 'Territory', 'OutletCategory', 'Outlet', 'Product', 'SalesPerson', 'Division', 'District', 'Thana', 'Brand', 'ProductCategory', 'Program', 'TerritoryAssignHistory', 'NotundinProgram');
	public $components = array('Paginator', 'Session', 'Filter.Filter');	
	
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index($id = null) 
	{
		
		ini_set('memory_limit', '512M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes
		
		
		//update date field of exsiting table
		/*$sql = "select * from mig_target_by_area_by_products";
		$results = $this->Memo->query($sql);
		foreach($results as $result){
			$update = "update mig_target_by_area_by_products set date='".$result[0]['YY']."-".$result[0]['MM']."-01' where id='".$result[0]['id']."'";
			$this->Memo->query($update);
		}
		exit;*/
				
		
		$this->set('page_title', 'Mig Sales Report');
		
		$request_data = array();
				
		//for outlet category list
		/*$sql = "select distinct OutletCategory from mig_outlet_category_sales";
		$results = $this->Memo->query($sql);
		$outlet_categories = array();
		foreach($results as $result){
			$outlet_categories[$result[0]['OutletCategory']]=$result[0]['OutletCategory'];
		}
		//pr($outlet_categories);exit;*/
		

		$outlet_categories = array(
			'Pharma' 			=> 'Pharma',
			'NGMP' 				=> 'NGMP',
			'Cosmetics Shop'	=> 'Cosmetics Shop',
			'NGO'				=> 'NGO',
			'Grocery Sho'		=> 'Grocery Sho',
			'Pan Shop'			=> 'Pan Shop',
			'Distributor'		=> 'Distributor',
			'GD'				=> 'GD',
			'Others'			=> 'Others',
		);
		$this->set(compact('outlet_categories'));
		
		
		$outlet_category_ids = isset($this->request->data['MigSaleReports']['outlet_category_id']) != '' ? $this->request->data['MigSaleReports']['outlet_category_id'] : array();
		$outlet_categories2 = array();
		if($outlet_category_ids){
			foreach($outlet_category_ids as $outlet_category){
				$outlet_categories2[$outlet_category]=$outlet_category;
			}
		}else{
			$outlet_categories2 = $outlet_categories;
		}
		
		
		
		//products
		/*$sql = "select distinct ProductName from mig_product_sales";
		$results = $this->Memo->query($sql);
		$product_list = array();
		foreach($results as $result){
			$product_list[$result[0]['ProductName']] = $result[0]['ProductName'];
		}*/
		$product_list = array(
			'Amore Black 12' => 'Amore Black 12',
			'Amore Black 3' => 'Amore Black 3',
			'Amore Gold 12' => 'Amore Gold 12',
			'Amore Gold 3' => 'Amore Gold 3',
			'Bolt 200gm' => 'Bolt 200gm',
			'Bolt 25gm' => 'Bolt 25gm',
			'Bolt 400gm' => 'Bolt 400gm',
			'C3' => 'C3',
			'ECP' => 'ECP',
			'Femicon' => 'Femicon',
			'Femipil' => 'Femipil',
			'HCG' => 'HCG',
			'Hero' => 'Hero',
			'Hero 3s' => 'Hero 3s',
			'Implant' => 'Implant',
			'Joya Ultra Comfort' => 'Joya Ultra Comfort',
			'JOYA-5(Belt)' => 'JOYA-5(Belt)',
			'JOYA-8(Belt)' => 'JOYA-8(Belt)',
			'JOYA-Wings' => 'JOYA-Wings',
			'Minicon' => 'Minicon',
			'MoniMix' => 'MoniMix',
			'Mypill' => 'Mypill',
			'Nordette-28' => 'Nordette-28',
			'Noret-28' => 'Noret-28',
			'Norix' => 'Norix',
			'Norix 1' => 'Norix 1',
			'ORSaline-Fruity (M)' => 'ORSaline-Fruity (M)',
			'ORSaline-Fruity (O)' => 'ORSaline-Fruity (O)',
			'ORSaline-N' => 'ORSaline-N',
			'Ovacon Gold' => 'Ovacon Gold',
			'Panther Dotted' => 'Panther Dotted',
			'Raja (Plain)' => 'Raja (Plain)',
			'Raja 100' => 'Raja 100',
			'Raja Super' => 'Raja Super',
			'Relax' => 'Relax',
			'Safe Delivery Kit (SDK)' => 'Safe Delivery Kit (SDK)',
			'Sayana Press' => 'Sayana Press',
			'Sensation Classic' => 'Sensation Classic',
			'Sensation SD' => 'Sensation SD',
			'Sensation SR' => 'Sensation SR',
			'SMC Zinc' => 'SMC Zinc',
			'Smile L 24s Standard' => 'Smile L 24s Standard',
			'Smile L 4s Mini' => 'Smile L 4s Mini',
			'Smile M 26s Standard' => 'Smile M 26s Standard',
			'Smile M 5s Mini' => 'Smile M 5s Mini',
			'Smile S 28s Standard' => 'Smile S 28s Standard',
			'Smile S 5s Mini' => 'Smile S 5s Mini',
			'Smile XL 4s Mini' => 'Smile XL 4s Mini',
			'SOMA-JECT' => 'SOMA-JECT',
			'Taste Me (Lychee 200 gm)' => 'Taste Me (Lychee 200 gm)',
			'Taste Me (Lychee 25 gm)' => 'Taste Me (Lychee 25 gm)',
			'Taste Me (Mango 200 gm)' => 'Taste Me (Mango 200 gm)',
			'Taste Me (Mango 25gm)' => 'Taste Me (Mango 25gm)',
			'Taste Me (Orange 200 gm)' => 'Taste Me (Orange 200 gm)',
			'Taste Me (Orange 25gm)' => 'Taste Me (Orange 25gm)',
			'Taste Me (Pomegranate 200 gm)' => 'Taste Me (Pomegranate 200 gm)',
			'Taste Me (Pomegranate 25 gm)' => 'Taste Me (Pomegranate 25 gm)',
			'U&ME Color' => 'U&ME Color',
			'U&ME-AN' => 'U&ME-AN',
			'U&ME-LL' => 'U&ME-LL',
			'Xtreme 3 in 1' => 'Xtreme 3 in 1',
			'Xtreme Ultra Thin' => 'Xtreme Ultra Thin',
		);
		//pr($product_list);exit;
		$this->set(compact('product_list'));
		
		$product_ids = isset($this->request->data['MigSaleReports']['product_id']) != '' ? $this->request->data['MigSaleReports']['product_id'] : array();
		$product_list2 = array();
		if($product_ids)
		{
			foreach($product_ids as $product_id){
				$product_list2[$product_id]=$product_id;
			}
		}else{
			$product_list2 = $product_list;
		}
		
		
		//for brands list
		/*$sql = "select distinct ProductBrand from mig_product_sales";
		$results = $this->Memo->query($sql);
		$brands = array();
		foreach($results as $result){
			$brands[$result[0]['ProductBrand']]=$result[0]['ProductBrand'];
		}*/
		$brands = array(
			'Amore' => 'Amore',
			'Bolt' => 'Bolt',
			'C3' => 'C3',
			'ECP' => 'ECP',
			'Femicon' => 'Femicon',
			'Femipil' => 'Femipil',
			'HCG' => 'HCG',
			'Hero' => 'Hero',
			'Implant' => 'Implant',
			'Minicon' => 'Minicon',
			'MoniMix' => 'MoniMix',
			'Mypill' => 'Mypill',
			'Nordette-28' => 'Nordette-28',
			'Noret-28' => 'Noret-28',
			'Norix' => 'Norix',
			'Norix 1' => 'Norix 1',
			'ORSaline-Fruity' => 'ORSaline-Fruity',
			'ORSaline-N' => 'ORSaline-N',
			'Ovacon Gold' => 'Ovacon Gold',
			'Panther' => 'Panther',
			'Raja' => 'Raja',
			'Raja (Plain)' => 'Raja (Plain)',
			'Raja Super' => 'Raja Super',
			'Relax' => 'Relax',
			'Safe Delivery Kit (SDK)' => 'Safe Delivery Kit (SDK)',
			'Sanitary Napkin' => 'Sanitary Napkin',
			'Sayana Press' => 'Sayana Press',
			'Sensation' => 'Sensation',
			'SMC Zinc' => 'SMC Zinc',
			'Smile' => 'Smile',
			'SOMA-JECT' => 'SOMA-JECT',
			'Taste Me' => 'Taste Me',
			'U&ME' => 'U&ME',
			'Xtreme' => 'Xtreme',
		);
		//pr($brands);exit;
		$this->set(compact('brands'));
		
		$brand_ids = isset($this->request->data['MigSaleReports']['brand_id']) != '' ? $this->request->data['MigSaleReports']['brand_id'] : array();
		$brands2 = array();
		if($brand_ids)
		{
			foreach($brand_ids as $brand_id){
				$brands2[$brand_id]=$brand_id;
			}
		}else{
			$brands2 = $brands;
		}
		
				
		//for cateogry list
		/*$sql = "select distinct ProductCategory from mig_product_sales order by ProductCategory asc";
		$results = $this->Memo->query($sql);
		$categories = array();
		foreach($results as $result){
			$categories[$result[0]['ProductCategory']]=$result[0]['ProductCategory']."',";
		}*/
		
		$categories = array(
			'Baby Diaper' => 'Baby Diaper',
			'Condom' => 'Condom',
			'Emergency Contraceptive Pill' => 'Emergency Contraceptive Pill',
			'Food & Beverage' => 'Food & Beverage',
			'Implant' => 'Implant',
			'Injectable' => 'Injectable',
			'IUD' => 'IUD',
			'ORSaline' => 'ORSaline',
			'Pill' => 'Pill',
			'Pregnancy Test Device' => 'Pregnancy Test Device',
			'Safe Delivery Kit (SDK)' => 'Safe Delivery Kit (SDK)',
			'Sanitary Napkin' => 'Sanitary Napkin',
			'Sprinkles' => 'Sprinkles',
			'Zinc' => 'Zinc',
		);
		//pr($categories);exit;
		$this->set(compact('categories'));
		
		$product_category_ids = isset($this->request->data['MigSaleReports']['product_category_id']) != '' ? $this->request->data['MigSaleReports']['product_category_id'] : array();
		$category_list2 = array();
		if($product_category_ids)
		{
			foreach($product_category_ids as $product_category_id){
				$category_list2[$product_category_id]=$product_category_id;
			}
		}else{
			$category_list2 = $categories;
		}
		
		
		
		
		
		
		//report type
		$unit_types = array(
			'2' => 'Base Unit',
			'1' => 'Sales Unit',
			'3' => 'Cartoon',
		);
		$this->set(compact('unit_types'));
		
		//product targets
		$product_targets = array(
			'1' => 'Target By Area By Product',
		);
		$this->set(compact('product_targets'));
		
		
		//for rows
		$rows = array(
			'so' 			=> 'By SO',
			//'territory' 	=> 'By Territory',
			'area' 			=> 'By Area',
			'month' 		=> 'By Month',
			'year' 			=> 'By Year',
			'division' 		=> 'By Division',
			'district' 		=> 'By District',
			'thana' 		=> 'By Thana',
			'ngo' 			=> 'By NGO',
			'national' 		=> 'By National',
		);
		$this->set(compact('rows'));
		
		
		//for columns
		$columns = array(
			'product' 		=> 'By Product',
			'brand' 		=> 'By Brand',
			'category' 		=> 'By Category',
			'outlet_type' 	=> 'By Outlet Type',
			'national' 		=> 'By National',
		);
		$this->set(compact('columns'));
		
		
		//for indicator
		$indicators = array(
			'volume' 		=> 'Volume',
			'value' 		=> 'Value',
			'oc' 			=> 'OC',
			'ec' 			=> 'EC',
			'cyp' 			=> 'CYP',
			'bonus' 		=> 'Bonus',
		);
		$this->set(compact('indicators'));
		
		
		//Location Types
		$locationTypes = array(
			'urban' 		=> 'Urban',
			'rural' 		=> 'Rural',
		);
		$this->set(compact('locationTypes'));
		
		
		//Divisions List	
		$divisions = array(
			'barisal' 		=> 'Barisal',
			'chittagong' 	=> 'Chittagong',
			'dhaka' 		=> 'Dhaka',
			'khulna' 		=> 'Khulna',
			'rajshahi' 		=> 'Rajshahi',
			'sylhet' 		=> 'Sylhet',
		);
		$this->set(compact('divisions'));

		
		//product_measurement
		$product_measurement = $this->Product->find('list', array(
				//'conditions'=> $pro_conditions,
				'fields'=> array('Product.id', 'Product.sales_measurement_unit_id'),
				'order'=>  array('order'=>'asc'),
				'recursive'=> -1
			));
		$this->set(compact('product_measurement'));	
		
				
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		$this->set(compact('office_parent_id'));
		
		//for office list
		$offices = array(
			'barisal' 			=> 'Barisal',
			'Bogra' 			=> 'Bogra',
			'Chittagong' 		=> 'Chittagong',
			'Comilla' 			=> 'Comilla',
			'Dhaka East' 		=> 'Dhaka East',
			'Dhaka West' 		=> 'Dhaka West',
			'Khulna' 			=> 'Khulna',
			'Kushtia' 			=> 'Kushtia',
			'Mymensingh' 		=> 'Mymensingh',
			'Rajshahi' 			=> 'Rajshahi',
			'Rangpur' 			=> 'Rangpur',
			'Sylhet' 			=> 'Sylhet',
		);
		
		
		$report_esales_setting_id = array();
		
		$territories = array();
				
		if($this->request->is('post') || $this->request->is('put'))
		{
			//pr($this->request->data);	
			
			$request_data = $this->request->data;
			
			$office_id = $this->request->data['MigSaleReports']['office_id'] ? $this->request->data['MigSaleReports']['office_id'] : '';
						
			//Start so list
			$sos = array();
			if($office_id)
			{
				$sql = "select distinct SOName from mig_product_sales where AreaName='".$office_id."'";
				$results = $this->Memo->query($sql);
				$sos = array();
				foreach($results as $result){
					$sos[$result[0]['SOName']]=$result[0]['SOName'];
				}
			}
			$this->set(compact('sos'));
			//End so list
			
			
			//division list
			if($office_id)
			{
				$divisions = array();
				$sql = "select distinct DivName from mig_product_sales where AreaName='".$office_id."'";
				$results = $this->Memo->query($sql);
				foreach($results as $result){
					$divisions[$result[0]['DivName']]=$result[0]['DivName'];
				}
				$this->set(compact('divisions'));
			}
			
			
			//district list
			$districts = array();
			$division_id = isset($this->request->data['MigSaleReports']['division_id']) != '' ? $this->request->data['MigSaleReports']['division_id'] : 0;
			if($division_id)
			{
				$where = '';
				if($office_id)$where = " where AreaName='".$office_id."'";
				
				if($division_id){
					if($where){
						$where.= " and DivName='".$division_id."'";
					}else{
						$where = " where DivName='".$division_id."'";
					}
				}
				
				$sql = "select distinct DistName from mig_product_sales $where";
				$results = $this->Memo->query($sql);
				foreach($results as $result){
					$districts[strtolower($result[0]['DistName'])]=$result[0]['DistName'];
				}
			}
			$this->set(compact('districts'));	
			
			
			
			//thana list
			$thanas = array();
			
			$district_id = isset($this->request->data['MigSaleReports']['district_id']) != '' ? $this->request->data['MigSaleReports']['district_id'] : 0;
			if($district_id){
				$where = '';
				if($office_id)$where = " where AreaName='".$office_id."'";
				if($division_id){
					if($where){
						$where.= " and DivName='".$division_id."'";
					}else{
						$where = " where DivName='".$division_id."'";
					}
				}
				if($district_id){
					if($where){
						$where.= " and DistName='".$division_id."'";
					}else{
						$where = " where DistName='".$division_id."'";
					}
				}
				
				$sql = "select distinct ThaName from mig_product_sales $where";
				$results = $this->Memo->query($sql);
			
				foreach($results as $result){
					$thanas[strtolower($result[0]['ThaName'])]=$result[0]['ThaName'];
				}
			}
			$this->set(compact('thanas'));
				

		}
		
		
		
		if ($this->request->is('post'))
		{
			@$unit_type = $this->request->data['MigSaleReports']['unit_type'];
			//$this->set(compact('unit_type'));
			if($unit_type==1){
				$unit_type_text='Sales';
			}elseif($unit_type==2){
				$unit_type_text='Base';
			}elseif($unit_type==3){
				$unit_type_text='Cartoon';
			}
			$this->set(compact('unit_type_text'));
			
						
			if ($this->request->data['MigSaleReports']['indicators'] && @$this->request->data['MigSaleReports']['rows']) 
			{
				//pr($indicators);
				//pr($this->request->data);
				//exit;
								
				$request_data = $this->request->data;
				$date_from = explode('-', $request_data['MigSaleReports']['date_from']);
				$date_from[0] = date('m', strtotime($date_from[0]));
				$date_from = date('y-m-d', mktime(0,0,0,$date_from[0],1,$date_from[1]));
				$date_from = date("Y-m-01", strtotime($date_from));
				
				$date_to = explode('-', $request_data['MigSaleReports']['date_to']);
				$date_to[0] = date('m', strtotime($date_to[0]));
				$date_to = date('y-m-d', mktime(0,0,0,$date_to[0],1,$date_to[1]));
				$date_to = date("Y-m-t", strtotime($date_to));
				
				$this->set(compact('date_from', 'date_to', 'request_data'));
				
				
				$office_id = $this->request->data['MigSaleReports']['office_id'] ? $this->request->data['MigSaleReports']['office_id'] : '';
				@$so_id = $this->request->data['MigSaleReports']['so_id'] ? $this->request->data['MigSaleReports']['so_id'] : '';
				@$division_id = $this->request->data['MigSaleReports']['division_id'] ? $this->request->data['MigSaleReports']['division_id'] : '';
				@$district_id = $this->request->data['MigSaleReports']['district_id'] ? $this->request->data['MigSaleReports']['district_id'] : '';
				@$thana_id = $this->request->data['MigSaleReports']['thana_id'] ? $this->request->data['MigSaleReports']['thana_id'] : '';
				@$location_type_id = $this->request->data['MigSaleReports']['location_type_id'] ? $this->request->data['MigSaleReports']['location_type_id'] : '';
				$unit_type = $this->request->data['MigSaleReports']['unit_type'] ? $this->request->data['MigSaleReports']['unit_type'] : '';
				
				
				if($this->request->data['MigSaleReports']['product_id'])
				{
					$pro_total = count($this->request->data['MigSaleReports']['product_id']); 
					$p=1;
					$product_id = '';
					foreach($this->request->data['MigSaleReports']['product_id'] as $p_val)
					{
						if($p==$pro_total){
							$product_id.= "'".$p_val."'";
						}else{
							$product_id.= "'".$p_val."',";
						}
						$p++;
					}
				}else{
					$product_id = 0;
				}
				
				
				//for columns
				$columns_list = array();
				
				if($request_data['MigSaleReports']['columns']=='product' || $request_data['MigSaleReports']['columns']=='ngo')
				{
					//for products list
					/*if($request_data['MigSaleReports']['product_id']){
						$sql = "select distinct ProductName from mig_product_sales where LOWER(ProductName)='".strtolower($request_data['MigSaleReports']['product_id'])."'";
					}else{
						$sql = "select distinct ProductName from mig_product_sales";
					}
					
					$results = $this->Memo->query($sql);
					$product_list = array();
					foreach($results as $result){
						$product_list[$result[0]['ProductName']] = $result[0]['ProductName'];
					}*/
					$columns_list = $product_list2;
				}
				elseif($request_data['MigSaleReports']['columns']=='brand')
				{
					$columns_list = $brands2;
				}
				elseif($request_data['MigSaleReports']['columns']=='category')
				{
					$columns_list = $category_list2;
					
				}
				elseif($request_data['MigSaleReports']['columns']=='outlet_type')
				{
					$columns_list = $outlet_categories2;
				}
				else
				{
					$columns_list['national'] = 'National';
				}
				//pr($columns_list);
				
				$this->set(compact('columns_list'));
				//end for columns
				
				
				
				//for rows
				$rows_list = array();
				if($request_data['MigSaleReports']['rows']=='so')
				{
					$so_list = array();
					$where = '';
					if($office_id){
						$where = " where LOWER(AreaName)='".strtolower($office_id)."'";
					}
					
					if($so_id){
						$where.= " AND LOWER(SOName)='".strtolower($so_id)."'";
					}
					
					$sql = "select distinct SOName from mig_product_sales $where";
					
					$results = $this->Memo->query($sql);
					$so_list = array();
					foreach($results as $result){
						$so_list[$result[0]['SOName']]=$result[0]['SOName'];
					}
					
					$rows_list = $so_list;
				}
				elseif($request_data['MigSaleReports']['rows']=='area')
				{
					$area_list = array();
					
					if($office_id){
						$area_list[$office_id]=$office_id;
					}else{
						$area_list = $offices;	
					}
					
					$rows_list = $area_list;
				}
				elseif($request_data['MigSaleReports']['rows']=='month')
				{
					
					$d1 = new DateTime($date_from);
					$d2 = new DateTime($date_to);
					$months = 0;
					
					$d1->add(new \DateInterval('P1M'));
					while ($d1 <= $d2){
						$months ++;
						$d1->add(new \DateInterval('P1M'));
					}
					
					//print_r($months);
					
					//month count
					$date1 = $date_from;
					$date2 = $date_to;
					$output = [];
					$output2 = array();
					$time   = strtotime($date1);
					$last   = date('Y-m', strtotime($date2));
					do {
						$month = date('Y-m', $time);
						$total = date('t', $time);
					
						$output[] = [
							'month' => $month,
							'total_days' => $total,
						];
						
						$output2[$month] = date('M, Y', $time);
					
						$time = strtotime('+1 month', $time);
					} while ($month != $last);
					
					//$month_list = $output;
					
					//pr($output2);
					//exit;
					
					$rows_list = $output2;
				}
				elseif($request_data['MigSaleReports']['rows']=='year')
				{
					
					$d1 = new DateTime($date_from);
					$d2 = new DateTime($date_to);
					$months = 0;
					
					$start    = $date_from;
					$end      = $date_to;
					$getRangeYear   = range(gmdate('Y', strtotime($start)), gmdate('Y', strtotime($end)));
					
					
					$output = array();
					foreach($getRangeYear as $key => $val){
						$output[$val]=$val;
					}
					
					$rows_list = $output;
					//pr($rows_list);
				}
				elseif($request_data['MigSaleReports']['rows']=='division')
				{
					$division_list = array();
					if($division_id){
						$division_list[$division_id]=$division_id;
					}else{
						$division_list = $divisions;
					}
					$rows_list = $division_list;
				}
				elseif($request_data['MigSaleReports']['rows']=='district')
				{
					$district_list = array();
					if($district_id){
						$district_list[$district_id]=$district_id;
					}else{
						$district_list = $districts;
					}
					
					$rows_list = $district_list;
				}
				elseif($request_data['MigSaleReports']['rows']=='thana')
				{
					$thana_list = array();
					if($thana_id){
						$thana_list[$thana_id]=$thana_id;
					}else{
						$thana_list = $thanas;
					}
					
					$rows_list = $thana_list;
				}
				elseif($request_data['MigSaleReports']['rows']=='ngo')
				{
					$ngos = array();
					$district_id = isset($this->request->data['MigSaleReports']['district_id']) != '' ? $this->request->data['MigSaleReports']['district_id'] : 0;
					$where = '';
					if($office_id)$where = " where AreaName='".$office_id."'";
					if($division_id){
						if($where){
							$where.= " and DivName='".$division_id."'";
						}else{
							$where = " where DivName='".$division_id."'";
						}
					}
					if($district_id){
						if($where){
							$where.= " and DistName='".$division_id."'";
						}else{
							$where = " where DistName='".$division_id."'";
						}
					}
					if($thana_id){
						if($where){
							$where.= " and ThaName='".$thana_id."'";
						}else{
							$where = " where ThaName='".$thana_id."'";
						}
					}
					
					$sql = "select distinct NgoName from mig_ngo_sales $where";
					$results = $this->Memo->query($sql);
				
					foreach($results as $result){
						$ngos[strtolower($result[0]['NgoName'])]=$result[0]['NgoName'];
					}
					
					$rows_list = $ngos;
				}
				else
				{
					$rows_list = array('National');
				}
				$this->set(compact('rows_list'));
				
				//pr($rows_list);		
				//exit;
				//end for rows
				
						
	
				/*START FOR RESULT QUERY*/
				$rows 		= $request_data['MigSaleReports']['rows'];
				$columns 	= $request_data['MigSaleReports']['columns'];
				
				
				
				$q_results = array();
				$q_results2 = array();
				
				//for column
				$col_keys = array();
				foreach($columns_list as $col_key => $col_val)
				{
					array_push($col_keys, $col_key)	;
				}
				$row_keys = array();
				foreach($rows_list as $row_key => $row_val)
				{
					array_push($row_keys, $row_key)	;
				}
				//pr($row_keys);
				//exit;
				
				//pr($request_data);
				
				$from_yy 	= date('Y', strtotime($date_from));
				$from_mm 	= date('m', strtotime($date_from));
				
				$to_yy 		= date('Y', strtotime($date_to));
				$to_mm 		= date('m', strtotime($date_to));
				
				//$where = " (YY>='$from_yy' AND MM<='$from_mm') OR (YY<='$to_yy' AND MM<='$to_mm')";
				$where = " date between '$date_from' and '$date_to'";
				
				if($so_id)$where.=" AND SOName='".$so_id."'";
				if($office_id)$where.=" AND AreaName='".$office_id."'";
				if($division_id)$where.=" AND DivName='".$division_id."'";
				if($district_id)$where.=" AND DistName='".$district_id."'";
				if($thana_id)$where.=" AND ThaName='".$thana_id."'";
				if($location_type_id)$where.=" AND Location='".$location_type_id."'";
				if($product_id)$where.=" AND ProductName IN(".$product_id.")";
				//pr($rows_list);
				//exit;
				
				
							
				//for month row
				if($rows=='month' || $rows=='year' || $rows=='national')
				{
					$results = array();
					$n_where='';
					foreach($rows_list as $row_key => $row_val)
					{
						$row_key1 = $row_key.'-01';
						if($rows=='month')$n_where=" AND YY='".date('Y', strtotime($row_key1))."' AND MM='".date('m', strtotime($row_key1))."'";
						if($rows=='year')$n_where=" AND YY='".date('Y', strtotime($row_key1))."'";
						
						if(@$this->request->data['MigSaleReports']['product_targets']!=1)
						{
							if($request_data['MigSaleReports']['columns']=='product')
							{
								$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
								AreaName, ProductName from mig_product_sales WHERE $where  
								GROUP BY AreaName, ProductName";
								$q_results = $this->Memo->query($sql);
								
								foreach($q_results as $q_result)
								{
									if($unit_type==1){
										$volume = $q_result[0]['sale_qty_unit'];
									}elseif($unit_type==3){
										$volume = $q_result[0]['cartoon_qty_unit'];
									}else{
										$volume = $q_result[0]['total_qty'];
									}
									
									$results[$row_key][$q_result[0]['ProductName']] = array(
										'volume' => $volume,
										'value' => $q_result[0]['total_val'],
										'price' => $q_result[0]['total_price'],
										'bonus' => $q_result[0]['total_bonus'],
										'ec' => '',
										'oc' => '',
										'cyp' => $q_result[0]['cyp'],
									);
								}
								
								//pr($results);
								//exit;
								
							}
							elseif($request_data['MigSaleReports']['columns']=='category')
							{
								$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
								AreaName, ProductCategory from mig_product_sales WHERE $where   
								GROUP BY AreaName, ProductCategory";
								$q_results = $this->Memo->query($sql);
								//pr($q_results);
								//$results = array();
								foreach($q_results as $q_result)
								{
									if($unit_type==1){
										$volume = $q_result[0]['sale_qty_unit'];
									}elseif($unit_type==3){
										$volume = $q_result[0]['cartoon_qty_unit'];
									}else{
										$volume = $q_result[0]['total_qty'];
									}
									
									$results[$row_key][$q_result[0]['ProductCategory']] = array(
										'volume' => $volume,
										'value' => $q_result[0]['total_val'],
										'price' => $q_result[0]['total_price'],
										'bonus' => $q_result[0]['total_bonus'],
										'ec' => '',
										'oc' => '',
										'cyp' => $q_result[0]['cyp'],
									);
								}
							}
							elseif($request_data['MigSaleReports']['columns']=='brand')
							{
								$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
								AreaName, ProductBrand from mig_product_sales WHERE $where   
								GROUP BY AreaName, ProductBrand";
								$q_results = $this->Memo->query($sql);
								//pr($q_results);
								//$results = array();
								foreach($q_results as $q_result)
								{
									if($unit_type==1){
										$volume = $q_result[0]['sale_qty_unit'];
									}elseif($unit_type==3){
										$volume = $q_result[0]['cartoon_qty_unit'];
									}else{
										$volume = $q_result[0]['total_qty'];
									}
									
									$results[$row_key][$q_result[0]['ProductBrand']] = array(
										'volume' => $volume,
										'value' => $q_result[0]['total_val'],
										'price' => $q_result[0]['total_price'],
										'bonus' => $q_result[0]['total_bonus'],
										'ec' => '',
										'oc' => '',
										'cyp' => $q_result[0]['cyp'],
									);
								}
							}
							elseif($request_data['MigSaleReports']['columns']=='outlet_type')
							{
								
								$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
								AreaName, OutletCategory from mig_outlet_category_sales WHERE $where   
								GROUP BY AreaName, OutletCategory";
								$q_results = $this->Memo->query($sql);
								//pr($q_results);
								//$results = array();
								foreach($q_results as $q_result)
								{
									if($unit_type==1){
										$volume = $q_result[0]['sale_qty_unit'];
									}elseif($unit_type==3){
										$volume = $q_result[0]['cartoon_qty_unit'];
									}else{
										$volume = $q_result[0]['total_qty'];
									}
									
									$results[$row_key][$q_result[0]['OutletCategory']] = array(
										'volume' => $volume,
										'value' => $q_result[0]['total_val'],
										'price' => $q_result[0]['total_price'],
										'bonus' => $q_result[0]['total_bonus'],
										'ec' => '',
										'oc' => '',
										'cyp' => $q_result[0]['cyp'],
									);
								}
							}	
							elseif($request_data['MigSaleReports']['columns']=='national')
							{
								$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp from mig_product_sales WHERE $where $n_where 
								";
								$q_results = $this->Memo->query($sql);
								
								//pr($q_results);
								
								foreach($q_results as $q_result)
								{
									if($unit_type==1){
										$volume = $q_result[0]['sale_qty_unit'];
									}elseif($unit_type==3){
										$volume = $q_result[0]['cartoon_qty_unit'];
									}else{
										$volume = $q_result[0]['total_qty'];
									}
									
									$results[$row_key]['national'] = array(
										'volume' => $volume,
										'value' => $q_result[0]['total_val'],
										'price' => $q_result[0]['total_price'],
										'bonus' => $q_result[0]['total_bonus'],
										'ec' => '',
										'oc' => '',
										'cyp' => $q_result[0]['cyp'],
									);
								}
								
								//pr($results);
								//exit;
							}	
							
							
							//For OC and EC count
							//$ec_result = array();
							if(in_array('oc', $this->request->data['MigSaleReports']['indicators']) || in_array('ec', $this->request->data['MigSaleReports']['indicators']))
							{
								//pr($this->request->data['MigSaleReports']['indicators']);
								$and = '';
								if($office_id)$and.= " AND AreaName='".$office_id."'";
								//$ec_sql = "SELECT * FROM mig_ec_oc_revenues WHERE date between '$date_from' and '$date_to' $and";
								
								$row_key1 = $row_key.'-01';
								if($rows=='month')$and.=" AND YY='".date('Y', strtotime($row_key1))."' AND MM='".date('m', strtotime($row_key1))."'";
								if($rows=='year')$and.=" AND YY='".date('Y', strtotime($row_key1))."'";
								
								$ec_sql = "SELECT sum(EC) as total_ec, sum(OC) as total_oc FROM mig_ec_oc_revenues WHERE date between '$date_from' and '$date_to' $and";
								$ec_q_results = $this->Memo->query($ec_sql);
								
								foreach($ec_q_results as $ec_q_result)
								{
									$ec_result[$row_key] = array(
										'ec'=>$ec_q_result[0]['total_ec'],
										'oc'=>$ec_q_result[0]['total_oc'],
									);
								}
								//pr($ec_result);
							}
						//exit;		
						}
						else
						{
							if($rows=='month')
							{
								$and = '';
								if($office_id)$and = " AND AreaName='".$office_id."'";
								$sql = "SELECT sum(TargetQty) as total_qty, sum(TargetVal) as total_val, YY, MM, ProductName  FROM mig_target_by_area_by_products WHERE $where  GROUP BY YY, MM, ProductName";
								$q_results = $this->Memo->query($sql);
								
								$results = array();
								foreach($q_results as $q_result)
								{
									$results[$q_result[0]['YY'].'-'.sprintf('%02d', $q_result[0]['MM'])][$q_result[0]['ProductName']] = array(
										'volume' => $q_result[0]['total_qty'],
										'value' => $q_result[0]['total_val'],
										'price' => $q_result[0]['total_val'],
										//'date' => $q_result[0]['date'],
									);
								}
								//pr($results);
								//exit;
							}
							elseif($rows=='year')
							{
								$and = '';
								if($office_id)$and = " AND AreaName='".$office_id."'";
								$sql = "SELECT sum(TargetQty) as total_qty, sum(TargetVal) as total_val, YY, ProductName FROM mig_target_by_area_by_products WHERE $where  GROUP BY YY, ProductName";
								$q_results = $this->Memo->query($sql);
								//pr($q_results);
								$results = array();
								foreach($q_results as $q_result)
								{
									$results[$q_result[0]['YY']][$q_result[0]['ProductName']] = array(
										'volume' => $q_result[0]['total_qty'],
										'value' => $q_result[0]['total_val'],
										'price' => $q_result[0]['total_val'],
										//'date' => $q_result[0]['date'],
									);
								}
								
							}
							
							//pr($results);
							//exit;
						}

					}
				}
				else
				{
					//echo $unit_type;
					//exit;
					
					if($request_data['MigSaleReports']['rows']=='so')
					{
						if($request_data['MigSaleReports']['columns']=='product')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							SOName, AreaName, ProductName from mig_product_sales WHERE $where   
							GROUP BY SOName, AreaName, ProductName";
							$q_results = $this->Memo->query($sql);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[$q_result[0]['SOName']][$q_result[0]['ProductName']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
							
							//pr($results);
							//exit;
							
						}
						elseif($request_data['MigSaleReports']['columns']=='category')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							SOName, AreaName, ProductCategory from mig_product_sales WHERE $where   
							GROUP BY SOName, AreaName, ProductCategory";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[$q_result[0]['SOName']][$q_result[0]['ProductCategory']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
						}
						elseif($request_data['MigSaleReports']['columns']=='brand')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							SOName, AreaName, ProductBrand from mig_product_sales WHERE $where   
							GROUP BY SOName, AreaName, ProductBrand";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[$q_result[0]['SOName']][$q_result[0]['ProductBrand']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
						}
						elseif($request_data['MigSaleReports']['columns']=='national')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							SOName, AreaName from mig_product_sales WHERE $where   
							GROUP BY SOName, AreaName";
							$q_results = $this->Memo->query($sql);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[$q_result[0]['SOName']]['national'] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
							
							//pr($results);
							//exit;
							
						}
					}
					
					
					if($request_data['MigSaleReports']['rows']=='area')
					{
						if($request_data['MigSaleReports']['columns']=='product')
						{
							if(@$this->request->data['MigSaleReports']['product_targets']!=1)
							{
								$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
								AreaName, ProductName from mig_product_sales WHERE $where   
								GROUP BY AreaName, ProductName";
								$q_results = $this->Memo->query($sql);
								$results = array();
								foreach($q_results as $q_result)
								{
									if($unit_type==1){
										$volume = $q_result[0]['sale_qty_unit'];
									}elseif($unit_type==3){
										$volume = $q_result[0]['cartoon_qty_unit'];
									}else{
										$volume = $q_result[0]['total_qty'];
									}
									
									$results[strtolower($q_result[0]['AreaName'])][$q_result[0]['ProductName']] = array(
										'volume' => $volume,
										'value' => $q_result[0]['total_val'],
										'price' => $q_result[0]['total_price'],
										'bonus' => $q_result[0]['total_bonus'],
										'ec' => '',
										'oc' => '',
										'cyp' => $q_result[0]['cyp'],
									);
								}
								//pr($results);
								//exit;
								
								//For OC and EC count
								$ec_result = array();
								if(in_array('oc', $this->request->data['MigSaleReports']['indicators']) || in_array('ec', $this->request->data['MigSaleReports']['indicators']))
								{
									//pr($this->request->data['MigSaleReports']['indicators']);
									$and = '';
									if($office_id)$and = " AND AreaName='".$office_id."'";
									$ec_sql = "SELECT * FROM mig_ec_oc_revenues WHERE date between '$date_from' and '$date_to' $and";
									$ec_q_results = $this->Memo->query($ec_sql);
									
									foreach($ec_q_results as $ec_q_result){
										$ec_result[strtolower($ec_q_result[0]['AreaName'])] = array(
											'ec'=>$ec_q_result[0]['EC'],
											'oc'=>$ec_q_result[0]['OC'],
										);
									}
									//pr($ec_result);
								}
								//exit;
							}
							else
							{
								$and = '';
								if($office_id)$and = " AND AreaName='".$office_id."'";
								$sql = "SELECT sum(TargetQty) as total_qty, sum(TargetVal) as total_val, AreaName, ProductName FROM mig_target_by_area_by_products WHERE $where  GROUP BY AreaName, ProductName";
								$q_results = $this->Memo->query($sql);
								//pr($q_results);
								$results = array();
								foreach($q_results as $q_result)
								{
									$results[strtolower($q_result[0]['AreaName'])][$q_result[0]['ProductName']] = array(
										'volume' => $q_result[0]['total_qty'],
										'value' => $q_result[0]['total_val'],
										'price' => $q_result[0]['total_val'],
									);
								}
								
								//pr($results);
								//exit;
							}
							
						}
						elseif($request_data['MigSaleReports']['columns']=='category')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							AreaName, ProductCategory from mig_product_sales WHERE $where   
							GROUP BY AreaName, ProductCategory";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['AreaName'])][$q_result[0]['ProductCategory']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
						}
						elseif($request_data['MigSaleReports']['columns']=='brand')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							AreaName, ProductBrand from mig_product_sales WHERE $where   
							GROUP BY AreaName, ProductBrand";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['AreaName'])][$q_result[0]['ProductBrand']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
						}
						elseif($request_data['MigSaleReports']['columns']=='outlet_type')
						{
							
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							AreaName, OutletCategory from mig_outlet_category_sales WHERE $where   
							GROUP BY AreaName, OutletCategory";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['AreaName'])][$q_result[0]['OutletCategory']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
						}
						elseif($request_data['MigSaleReports']['columns']=='national')
						{
							if($this->request->data['MigSaleReports']['product_targets']!=1)
							{
								$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
								AreaName from mig_product_sales WHERE $where   
								GROUP BY AreaName";
								$q_results = $this->Memo->query($sql);
								$results = array();
								foreach($q_results as $q_result)
								{
									if($unit_type==1){
										$volume = $q_result[0]['sale_qty_unit'];
									}elseif($unit_type==3){
										$volume = $q_result[0]['cartoon_qty_unit'];
									}else{
										$volume = $q_result[0]['total_qty'];
									}
									
									$results[strtolower($q_result[0]['AreaName'])]['national'] = array(
										'volume' => $volume,
										'value' => $q_result[0]['total_val'],
										'price' => $q_result[0]['total_price'],
										'bonus' => $q_result[0]['total_bonus'],
										'ec' => '',
										'oc' => '',
										'cyp' => $q_result[0]['cyp'],
									);
								}
								//pr($results);
								//exit;
								
								//For OC and EC count
								$ec_result = array();
								if(in_array('oc', $this->request->data['MigSaleReports']['indicators']) || in_array('ec', $this->request->data['MigSaleReports']['indicators']))
								{
									//pr($this->request->data['MigSaleReports']['indicators']);
									$and = '';
									if($office_id)$and = " AND AreaName='".$office_id."'";
									$ec_sql = "SELECT * FROM mig_ec_oc_revenues WHERE date between '$date_from' and '$date_to' $and";
									$ec_q_results = $this->Memo->query($ec_sql);
									
									foreach($ec_q_results as $ec_q_result){
										$ec_result[strtolower($ec_q_result[0]['AreaName'])] = array(
											'ec'=>$ec_q_result[0]['EC'],
											'oc'=>$ec_q_result[0]['OC'],
										);
									}
									//pr($ec_result);
								}
								//exit;
							}
							else
							{
								$and = '';
								if($office_id)$and = " AND AreaName='".$office_id."'";
								$sql = "SELECT sum(TargetQty) as total_qty, sum(TargetVal) as total_val, AreaName, ProductName FROM mig_target_by_area_by_products WHERE $where  GROUP BY AreaName";
								$q_results = $this->Memo->query($sql);
								//pr($q_results);
								$results = array();
								foreach($q_results as $q_result)
								{
									$results[strtolower($q_result[0]['AreaName'])]['national'] = array(
										'volume' => $q_result[0]['total_qty'],
										'value' => $q_result[0]['total_val'],
										'price' => $q_result[0]['total_val'],
									);
								}
								
								//pr($results);
								//exit;
							}
							
						}
					}
					
					
					if($request_data['MigSaleReports']['rows']=='division')
					{
						if($request_data['MigSaleReports']['columns']=='product')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							DivName, ProductName from mig_product_sales WHERE $where  
							GROUP BY DivName, ProductName";
							$q_results = $this->Memo->query($sql);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['DivName'])][$q_result[0]['ProductName']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
							
							//pr($results);
							//exit;
							
						}
						elseif($request_data['MigSaleReports']['columns']=='category')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							DivName, ProductCategory from mig_product_sales WHERE $where   
							GROUP BY DivName, ProductCategory";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['DivName'])][$q_result[0]['ProductCategory']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
						}
						elseif($request_data['MigSaleReports']['columns']=='brand')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							DivName, ProductBrand from mig_product_sales WHERE $where   
							GROUP BY DivName, ProductBrand";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['DivName'])][$q_result[0]['ProductBrand']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
						}
						elseif($request_data['MigSaleReports']['columns']=='outlet_type')
						{
							
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							DivName, OutletCategory from mig_outlet_category_sales WHERE $where  
							GROUP BY DivName, OutletCategory";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['DivName'])][$q_result[0]['OutletCategory']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
							
						}
						elseif($request_data['MigSaleReports']['columns']=='national')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							DivName from mig_product_sales WHERE $where  
							GROUP BY DivName";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['DivName'])]['national'] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
							
							//pr($results);
							//exit;
							
						}
					}
					
					
					
					if($request_data['MigSaleReports']['rows']=='district')
					{
						if($request_data['MigSaleReports']['columns']=='product')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							DistName, ProductName from mig_product_sales WHERE $where   
							GROUP BY DistName, ProductName";
							$q_results = $this->Memo->query($sql);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['DistName'])][$q_result[0]['ProductName']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
							
							//pr($results);
							//exit;
							
						}
						elseif($request_data['MigSaleReports']['columns']=='category')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							DistName, ProductCategory from mig_product_sales WHERE $where   
							GROUP BY DistName, ProductCategory";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['DistName'])][$q_result[0]['ProductCategory']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
						}
						elseif($request_data['MigSaleReports']['columns']=='brand')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							DistName, ProductBrand from mig_product_sales WHERE $where   
							GROUP BY DistName, ProductBrand";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['DistName'])][$q_result[0]['ProductBrand']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
						}
						elseif($request_data['MigSaleReports']['columns']=='outlet_type')
						{
							
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							DistName, OutletCategory from mig_outlet_category_sales WHERE $where   
							GROUP BY DistName, OutletCategory";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['DistName'])][$q_result[0]['OutletCategory']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
							
						}
						elseif($request_data['MigSaleReports']['columns']=='national')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							DistName from mig_product_sales WHERE $where   
							GROUP BY DistName";
							$q_results = $this->Memo->query($sql);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['DistName'])]['national'] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
							
							//pr($results);
							//exit;
							
						}
					}
					
					
					
					if($request_data['MigSaleReports']['rows']=='thana')
					{
						if($request_data['MigSaleReports']['columns']=='product')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							ThaName, ProductName from mig_product_sales WHERE $where  
							GROUP BY ThaName, ProductName";
							$q_results = $this->Memo->query($sql);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['ThaName'])][$q_result[0]['ProductName']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
							
							//pr($results);
							//exit;
							
						}
						elseif($request_data['MigSaleReports']['columns']=='category')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							ThaName, ProductCategory from mig_product_sales WHERE $where   
							GROUP BY ThaName, ProductCategory";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['ThaName'])][$q_result[0]['ProductCategory']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
						}
						elseif($request_data['MigSaleReports']['columns']=='brand')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							ThaName, ProductBrand from mig_product_sales WHERE $where  
							GROUP BY ThaName, ProductBrand";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['ThaName'])][$q_result[0]['ProductBrand']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
						}
						elseif($request_data['MigSaleReports']['columns']=='outlet_type')
						{
							
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							ThaName, OutletCategory from mig_outlet_category_sales WHERE $where   
							GROUP BY ThaName, OutletCategory";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['ThaName'])][$q_result[0]['OutletCategory']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
							
						}
						elseif($request_data['MigSaleReports']['columns']=='national')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							ThaName from mig_product_sales WHERE $where  
							GROUP BY ThaName";
							$q_results = $this->Memo->query($sql);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['ThaName'])]['national'] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
							
							//pr($results);
							//exit;
							
						}
					}
					
					
					if($request_data['MigSaleReports']['rows']=='ngo')
					{
						if($request_data['MigSaleReports']['columns']=='product')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							NgoName, ProductName from mig_ngo_sales WHERE $where   
							GROUP BY NgoName, ProductName";
							$q_results = $this->Memo->query($sql);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['NgoName'])][$q_result[0]['ProductName']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
							
							//pr($results);
							//exit;
							
						}
						elseif($request_data['MigSaleReports']['columns']=='category')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							NgoName, ProductCategory from mig_ngo_sales WHERE $where   
							GROUP BY NgoName, ProductCategory";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['NgoName'])][$q_result[0]['ProductCategory']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
						}
						elseif($request_data['MigSaleReports']['columns']=='brand')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							NgoName, ProductBrand from mig_ngo_sales WHERE $where   
							GROUP BY NgoName, ProductBrand";
							$q_results = $this->Memo->query($sql);
							//pr($q_results);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['NgoName'])][$q_result[0]['ProductBrand']] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
						}
						elseif($request_data['MigSaleReports']['columns']=='national')
						{
							$sql = "select sum(Qty) as total_qty, sum(CYP) as cyp, sum(val) as total_val, sum(Price) as total_price, sum(Bonus) as total_bonus, sum(ConversionFactorForSale) as sale_qty_unit, sum(ConversionFactorForCartoon) as cartoon_qty_unit, sum(CYP) as cyp, 
							NgoName from mig_ngo_sales WHERE $where   
							GROUP BY NgoName";
							$q_results = $this->Memo->query($sql);
							$results = array();
							foreach($q_results as $q_result)
							{
								if($unit_type==1){
									$volume = $q_result[0]['sale_qty_unit'];
								}elseif($unit_type==3){
									$volume = $q_result[0]['cartoon_qty_unit'];
								}else{
									$volume = $q_result[0]['total_qty'];
								}
								
								$results[strtolower($q_result[0]['NgoName'])]['national'] = array(
									'volume' => $volume,
									'value' => $q_result[0]['total_val'],
									'price' => $q_result[0]['total_price'],
									'bonus' => $q_result[0]['total_bonus'],
									'ec' => '',
									'oc' => '',
									'cyp' => $q_result[0]['cyp'],
								);
							}
							
							//pr($results);
							//exit;
							
						}

					}
					
				}
				
				
				$this->set(compact('results'));
				
				
				//FOR OUTPUT
				$indicators_array = array();
				$indicators = array(
					'volume' 		=> 'Volume',
					'value' 		=> 'Value',
					'cyp' 			=> 'CYP',
					'bonus' 		=> 'Bonus',
				);
				if(empty($request_data['MigSaleReports']['indicators'])){	
					foreach($indicators as $key => $val){array_push($indicators_array, $key);}
				}else{
					$indicators_array = $request_data['MigSaleReports']['indicators'];
					/*foreach($request_data['MigSaleReports']['indicators'] as $key => $val){
						if($val!='ec' && $val!='oc')array_push($indicators_array, $val);
					}*/
				}
				$this->set(compact('indicators_array'));
				$indicators2=$indicators;
				$this->set(compact('indicators2'));
				
				
								
				$i=0;
				$output = '';
				$d_val=0;
				$g_col_total = array();
				$g_total = 0;
				$sub_total = 0;
				
				//pr($rows_list);
				//pr($columns_list);
				//pr($results);
				//exit;
				
				$oc_total = 0;
				$ec_total = 0;
				foreach($rows_list as $row_key => $row_val)
				{ 
					$row_key = strtolower($row_key);
					if(isset($results[$row_key]) && $results[$row_key])
					{
						
					$output .= '<tr>';
					$output.= '<td style="text-align:left;">'.str_replace('Sales Office', '', $row_val).'</td>';
					
					$c = 0;	
					
					foreach($columns_list as $col_key => $col_val)
					{															
						foreach($indicators as $in_key => $in_val)
						{ 
							if(in_array($in_key, $indicators_array))
							{
							   if($request_data['MigSaleReports']['rows']=='month')
							   {
								   /*echo $row_key;
								   echo '<br>';
								   echo $col_key;
								   echo '<br>';
								   echo $in_key;*/
								   
								   	$d_val = @sprintf("%01.2f", $results[$row_key][$col_key][$in_key]);
							   }
							   elseif($request_data['MigSaleReports']['rows']=='year')
							   {
									$d_val = @sprintf("%01.2f", $results[$row_key][$col_key][$in_key]);
							   }
							   else
							   { 
								   	$d_val = @sprintf("%01.2f", $results[$row_key][$col_key][$in_key]);
							   }
								
							   $output.= '<td><div>'.@$d_val.'</div></td>';
																  
							   @$g_col_total[$col_key][$in_key]+= $d_val;	
								  
							} 
						}
						$i++; 
						$c++;
					} 
					
					if(in_array('oc', $this->request->data['MigSaleReports']['indicators'])){
						$output.= '<td><div>'.@$ec_result[$row_key]['oc'].'</div></td>';
						$oc_total+=$ec_result[$row_key]['oc'];
					}
					if(in_array('ec', $this->request->data['MigSaleReports']['indicators'])){
						$output.= '<td><div>'.@$ec_result[$row_key]['ec'].'</div></td>';
						$ec_total+=$ec_result[$row_key]['ec'];
					}
						
					$output.= '</tr>';
					
					}
					
				}
				
				//pr($g_col_total);
				//exit;
				//for total cal
				$output.= '<tr><td style="text-align:right;"><b>Total :</b></td>';
				
				foreach($columns_list as $col_key => $col_val)
				{															
					foreach($indicators as $in_key => $in_val)
					{ 
						if(in_array($in_key, $indicators_array))
						{
							$output.= '<td><b>'.@sprintf("%01.2f", $g_col_total[$col_key][$in_key]).'</b></td>';
						}
					}
				}
				
				if(in_array('oc', $this->request->data['MigSaleReports']['indicators'])){
					$output.= '<td><div>'.@$oc_total.'</div></td>';
				}
				if(in_array('ec', $this->request->data['MigSaleReports']['indicators'])){
					$output.= '<td><div>'.@$ec_total.'</div></td>';
				}
				
				
				$output.= '</tr>';
				
				
				
				//exit;
				//echo $output;
				$this->set(compact('output'));
				
				//END OUTPUT
				
				
				/*END FOR RESULT QUERY*/
					
			}else{
				$this->Session->setFlash(__('Please select an Indicators or Rows!'), 'flash/error');
			}
		}
			
		
		//echo $office_id;
				
		
		
		
		$this->set(compact('offices', 'outlet_type', 'territories'));
		
	}
	
	
	



	

	
	public function get_office_so_list()
	{
		$view = new View($this);
				
        $form = $view->loadHelper('Form');	
		
		$office_id = $this->request->data['office_id'];
		
		$so_list = array();
		
		//$conditions = array('SalesPerson.territory_id >' => 0, 'User.user_group_id' => 4, 'User.active' => 1);
		if($office_id)
		{
			$sql = "select distinct SOName from mig_product_sales where LOWER(AreaName)='".strtolower($office_id)."'";
			$results = $this->Memo->query($sql);
			$so_list = array();
			foreach($results as $result){
				$so_list[$result[0]['SOName']]=$result[0]['SOName'];
			}
		}
		//pr($so_list);
		
			
		$form->create('MigSaleReports', array('role' => 'form', 'action'=>'index'))	;
		
		echo $form->input('so_id', array('label' => false, 'id' => 'so_id', 'class' => 'form-control so_id', 'required'=>false, 'options' => $so_list, 'empty'=>'---- All ----'));
		$form->end();
				
        $this->autoRender = false;
    }
	
	
		
		
	public function get_district_list()
	{
		$view = new View($this);
				
        $form = $view->loadHelper('Form');	
		
		$division_id = $this->request->data['division_id'];
		
		$dis_list = array();
		
		//$conditions = array('SalesPerson.territory_id >' => 0, 'User.user_group_id' => 4, 'User.active' => 1);
		if($division_id)
		{
			$sql = "select distinct DistName from mig_product_sales where LOWER(DivName)='".strtolower($division_id)."'";
			$results = $this->Memo->query($sql);
			$dis_list = array();
			foreach($results as $result){
				$dis_list[$result[0]['DistName']]=$result[0]['DistName'];
			}
		}
		//pr($dis_list);
		
		$output = '<option value="">---- All ----</option>';
		
		foreach($dis_list as $key => $name){
			$output.= '<option value="'.$key.'">'.$name.'</option>';
		}
		
		echo $output;
			
		/*$form->create('MigSaleReports', array('role' => 'form', 'action'=>'index'))	;		
		echo $form->input('district_id', array('label' => false, 'id' => 'district_id', 'class' => 'form-control district_id', 'required'=>false, 'options' => $dis_list, 'empty'=>'---- All ----'));
		$form->end();*/
		
		
        $this->autoRender = false;
	}
	
	public function get_thana_list()
	{
		$view = new View($this);
				
        $form = $view->loadHelper('Form');	
		
		$district_id = $this->request->data['district_id'];
		
		$thana_list = array();
		
		//$conditions = array('SalesPerson.territory_id >' => 0, 'User.user_group_id' => 4, 'User.active' => 1);
		if($district_id)
		{
			$sql = "select distinct ThaName from mig_product_sales where LOWER(DistName)='".strtolower($district_id)."'";
			$results = $this->Memo->query($sql);
			$thana_list = array();
			foreach($results as $result){
				$thana_list[$result[0]['ThaName']]=$result[0]['ThaName'];
			}
		}
		//pr($thana_list);
		
		$output = '<option value="">---- All ----</option>';
		
		foreach($thana_list as $key => $name){
			$output.= '<option value="'.$key.'">'.$name.'</option>';
		}
		
		echo $output;		        
		
		/*$form->create('MigSaleReports', array('role' => 'form', 'action'=>'index'))	;
		echo $form->input('thana_id', array('label' => false, 'id' => 'thana_id', 'class' => 'form-control thana_id', 'required'=>false, 'options' => $thana_list, 'empty'=>'---- All ----'));
		$form->end();*/

		
		$this->autoRender = false;
	}
	
	
	public function get_product_list()
	{
		$view = new View($this);
		
        $form = $view->loadHelper('Form');	
		
		$product_type = $this->request->data['product_type'];
		
		$conditions = array(
				'NOT' => array('Product.product_category_id'=>32),
				'is_active' => 1,
				'Product.product_type_id' => 1
			);
		
		if($product_type)$conditions['source'] = $product_type;
		
		//get SO list
		$list = $this->Product->find('all', array(
			'fields' => array('id', 'name'),
			'conditions' => $conditions,
			'recursive'=> -1
		));	
		
		
		$product_list = array();
		
		foreach($list as $key => $value)
		{
			$product_list[$value['Product']['id']] = $value['Product']['name'];
		}
		
		        
		if($product_list)
		{	
			$form->create('MigSaleReports', array('role' => 'form', 'action'=>'index'));
			
			echo $form->input('product_id', array('id' => 'product_id', 'label'=>false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required'=> true, 'options'=> $product_list));
			
			
			$form->end();
			
		}
		else
		{
			echo '';	
		}
		
		
        $this->autoRender = false;
    }
		
	
	
	
	public function admin_csv_import(){
		
		if(mysql_error()){
			echo(mysql_error());	
		}
		
		$msg = '';
		
		if($_POST){
			if (is_uploaded_file($_FILES['import']['tmp_name'])) {
				$filename = $_FILES['import']['tmp_name'];
			}else{
				$filename = false;	
			}
			
			$types = array('application/vnd.ms-excel', 'text/csv');
			
			if(in_array($_FILES['import']['type'], $types)){
				if ($filename) {
					$this->import($filename);
					$msg = '<h3 style="color:#0F0">Import Successfully!</h3>';
				} else {
					$msg = '<h3 style="color:#F00">Import file is empty!</h3>';
				}
			}else{
				$msg = '<h3 style="color:#F00">Upload file must be CSV format!</h3>';
			}
			
			$this->set(compact('msg'));
		}
		
	}
	
	private function import($filename)
	{
		$content = fopen("$filename", "r");				
		$row = 0;
		
		//Delete all date from selected table
		$sql = "truncate table mig_target_by_area_by_products";
		$this->Product->query($sql);
		
		while (($data = fgetcsv($content, 1000000, ",")) != FALSE)
		{
			/*echo '<pre>';
			print_r($data);
			echo '</pre>';*/
			
			if(count($data)!=0)
			{
				if($row > 0) 
				{
					//Product Sales Table
					/*$insert = "INSERT INTO mig_product_sales 
					(AreaName, DivName,DistName, ThaName, Market, Location, SOName, YY, MM, ProductCategory, ProductBrand, ProductVariant, ProductName, Qty, Bonus, val, Price, CYP, ConversionFactorForSale, ConversionFactorForCartoon) 
					VALUES
					('$data[0]', '$data[1]', '$data[2]', '$data[3]', '$data[4]', '$data[5]', '$data[6]', '$data[7]', '$data[8]', '$data[9]', '$data[10]', '$data[11]', '$data[12]', '$data[13]', '$data[14]', '$data[15]', '$data[15]', '$data[17]', '$data[18]', '$data[19]')";*/
					
					
					//Outlet Category Sales Table
					/*$insert = "INSERT INTO mig_outlet_category_sales 
					(AreaName, DivName,DistName, ThaName, Market, Location, OutletCategory, OutletType, YY, MM, ProductCategory, ProductBrand, ProductVariant, ProductName, Qty, Bonus, val, Price, CYP, ConversionFactorForSale, ConversionFactorForCartoon) 
					VALUES
					('$data[0]', '$data[1]', '$data[2]', '$data[3]', '$data[4]', '$data[5]', '$data[6]', '$data[7]', '$data[8]', '$data[9]', '$data[10]', '$data[11]', '$data[12]', '$data[13]', '$data[14]', '$data[15]', '$data[16]', '$data[17]', '$data[18]', '$data[19]', '$data[20]')";*/
					
										
					//EC, OC and Revenues Table
					/*$insert = "INSERT INTO mig_ec_oc_revenues 
					(AreaName, YY, MM, EC, OC, Rev, Cash, Credit) 
					VALUES
					('$data[0]', '$data[1]', '$data[2]', '$data[3]', '$data[4]', '$data[5]', '$data[6]', '$data[7]')";*/
					
					
					//NGO Sales Table
					/*$insert = "INSERT INTO mig_ngo_sales 
					(AreaName, DivName,DistName, ThaName, NgoName, YY, MM, ProductCategory, ProductBrand, ProductVariant, ProductName, Qty, Bonus, val, Price, CYP, ConversionFactorForSale, ConversionFactorForCartoon) 
					VALUES
					('$data[0]', '$data[1]', '$data[2]', '$data[3]', '$data[4]', '$data[5]', '$data[6]', '$data[7]', '$data[8]', '$data[9]', '$data[10]', '$data[11]', '$data[12]', '$data[13]', '$data[14]', '$data[15]', '$data[15]', '$data[17]')";*/
					
					
					//Target By Area By Products Table
					$insert = "INSERT INTO mig_target_by_area_by_products 
					(AreaName, YY, MM, ProductCategory, ProductBrand, ProductVariant, ProductName, TargetQty, TargetVal, date) 
					VALUES
					('$data[0]', '$data[1]', '$data[2]', '$data[3]', '$data[4]', '$data[5]', '$data[6]', '$data[7]', '$data[8]', '".$data[1].'-'.$data[2]."-01')";
					
					$this->Product->query($insert);
					//exit;	
				}
			}
			
			/*if($row==10){
				exit;
			}*/
			
			$row++;
		}
		
		return true;
		
	}
	
	
}
