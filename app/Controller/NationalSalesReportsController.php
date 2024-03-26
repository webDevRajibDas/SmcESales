<?php
App::uses('AppController', 'Controller');
/**
 * ProductSettings Controller
 *
 * @property NationalSalesReport $NationalSalesReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class NationalSalesReportsController extends AppController {
	
	var $uses = false;

/**
 * Components
 *
 * @var array
 */
	//public $components = array('Paginator', 'Session','Filter.Filter');
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index($id = null) {
		
		$this->loadModel('Office');
		$this->loadModel('Product');
		$this->loadModel('ProductCategory');
		$this->loadModel('Memo');
		
		$this->set('page_title', 'National Sales Volume and Value Report');
		
		
		$category_name = '';
		
		$offices = $this->Office->find('list', array(
		'conditions'=> array(
			'office_type_id' => 2,
			"NOT" => array( "id" => array(30, 31, 37))
		),
		'order'=>array('office_name'=>'asc')
		));
		
		//pr($offices);
		
		$productCategories = $this->ProductCategory->find('list', array(
					'conditions'=>array('NOT' => array('ProductCategory.id'=>32)),
					'order'=>  array('name'=>'asc')
		));
		
		$outlet_type_list = array(
			'1' => 'Pharma',
			'0' => 'Non Pharma',			
			'2' => 'Both',
		);
		
		$this->set(compact('offices', 'productCategories', 'outlet_type_list'));
		
		
		
		if ($this->request->is('post') || $this->request->is('put')) {
			
			//pr($this->request->data);
			
			foreach($productCategories as $cata_id => $product_cata)
			{
				  if($cata_id==$this->request->data['NationalSalesReports']['product_categories_id'])
				  {
					$category_name = $product_cata;  
				  }
			}
			
			//$this->getCategoryName(20);
			
			
			
			if (!isset($this->data['NationalSalesReports']['product_categories_id']) || empty($this->data['NationalSalesReports']['product_categories_id']))
			{
				$request_data = array();
				$product_list = array();
				$this->Session->setFlash(__('Please select at least one Category!'), 'flash/error');
				$this->redirect(array('action' => 'index'));
				
			}
			elseif(!isset($this->data['NationalSalesReports']['office_id']) || empty($this->data['NationalSalesReports']['office_id']))
			{
				$request_data = array();
				$product_list = array();
				$this->Session->setFlash(__('Please select at least one Area Office!'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$request_data = $this->request->data;
				$this->set(compact('request_data'));
				
				$this->Session->write('request_data', $request_data);
				
				
				/*$product_list = $this->Product->find('all',array(
					'fields'=>array('Product.name', 'Product.id', 'Product.product_category_id'),
					'joins'=>array(
						array(
							'table'=>'measurement_units',
							'alias'=>'MU',
							'type' => 'LEFT',
							'conditions'=>array('MU.id= Product.sales_measurement_unit_id')
							)
						),
					'conditions'=>array('Product.product_category_id'=>$this->data['NationalSalesReports']['product_categories_id']),
					'order'=>  array('Product.product_category_id'=>'asc', 'Product.order'=>'asc'),
					'recursive'=>-1
					));*/
				
				//pr($request_data);
					
				$categories_products = $this->ProductCategory->find('all', array(
					'conditions' => array('ProductCategory.id' => $this->data['NationalSalesReports']['product_categories_id']),
					//'fields' => 'name',
					'order' => array('ProductCategory.id'=>'asc'),
					'recursive' => 1,
					)
				);
				
				//pr($categories_products);
				
				
				
				

				$date_from = $this->data['NationalSalesReports']['date_from'];
				$date_to = $this->data['NationalSalesReports']['date_to'];
				$this->set(compact('date_from', 'date_to'));
				
				
				//for data query
				if($request_data['NationalSalesReports']['outlet_type']==2){
					$outlet_type = '0, 1';
				}else{
					$outlet_type = $request_data['NationalSalesReports']['outlet_type'];
				}
				
				
				
				$sql = "SELECT 
							t.office_id, 
							md.product_id, 
							SUM(
								ROUND((ROUND(
										(md.sales_qty) * 
										(CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END)
									,0)) / 
									(CASE WHEN ProductMeasurementSales.qty_in_base is null then 1 ELSE ProductMeasurementSales.qty_in_base END)
									,2,1)
							) AS qty, 
							sum(md.sales_qty*(md.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN md.discount_amount ELSE 0 END))) as val 
						FROM memos m 
						INNER JOIN memo_details md on md.memo_id=m.id
						LEFT JOIN discount_bonus_policies dbp on md.policy_id = dbp.id
						INNER JOIN [products] AS [Product]
						  ON ([md].[product_id] = [Product].[id])
						LEFT JOIN [product_measurements] AS [ProductMeasurement]
						  ON ([Product].[id] = [ProductMeasurement].[product_id]
						  AND
							 CASE
							   WHEN ([md].[measurement_unit_id] IS NULL OR
								 [md].[measurement_unit_id] = 0) THEN [Product].[sales_measurement_unit_id]
							   ELSE [md].[measurement_unit_id]
							 END = [ProductMeasurement].[measurement_unit_id])
						LEFT JOIN [product_measurements] AS [ProductMeasurementSales]
						  ON ([Product].[id] = [ProductMeasurementSales].[product_id]
						  AND [Product].[sales_measurement_unit_id] = [ProductMeasurementSales].[measurement_unit_id])
						INNER JOIN territories t on m.territory_id=t.id
						INNER JOIN outlets o on m.outlet_id=o.id
						WHERE (m.status!=0 AND m.memo_date BETWEEN  '".date('Y-m-d', strtotime($date_from))."' AND '". date('Y-m-d', strtotime($date_to))."') AND m.gross_value > 0 AND m.status > 0 AND o.is_pharma_type IN(".$outlet_type.") GROUP BY t.office_id, md.product_id";
						//exit;
						
				$results = $this->Memo->query($sql);
				
				
				
				$q_data = array();
				foreach($results as $result)
				{
					//pr($result);
					$q_data[$result[0]['office_id']][$result[0]['product_id']] = array(
						'qty' => $result[0]['qty']>0?sprintf("%01.2f", $result[0]['qty']):0,
						'val' => $result[0]['val']>0?sprintf("%01.2f", $result[0]['val']):0,
					);
				}
				//pr($q_data);
				//exit;
				$this->set(compact('q_data'));
				
				$this->Session->write('q_data', $q_data);
				$this->Session->write('offices', $offices);
				$this->Session->write('categories_products', $categories_products);
				
				//$this->getProductSales($office_id=26, $date_from, $date_to, $product_id=27);
				//exit;
			}
			
			$this->set(compact('request_data', 'categories_products', 'productCategories', 'outlet_type_list', 'category_name'));
			
			
		}
		
	}

	
	public function get_category_list()
	{
		$view = new View($this);
		
        $form = $view->loadHelper('Form');	
		 
		$this->loadModel('ProductCategory');
              
	    $outlet_type = $this->request->data['outlet_type'];
		
		if($outlet_type==2){		
			$conditions = array('is_pharma_product' => 0);
		}elseif($outlet_type==3){	
			$conditions = array('is_pharma_product' => 1);
		}else{	
			$conditions = array();
		}
				
		$productCategories = $this->ProductCategory->find('list', array(
			'conditions' => $conditions,
			
		 	)
		);
		        
		if($productCategories){	
			$form->create('NationalSalesReports', array('role' => 'form', 'action'=>'index'))	;
			//echo $form->input('data[NationalSalesReports][product_categories_id]', array('class' => 'form-control', 'empty'=>'---- Select Status ----', 'options' => $productCategories, 'required'=>true));
			
			echo $form->input('product_categories_id', array('id' => 'product_category_id', 'label'=>false, 'class' => 'checkbox simple', 'multiple' => 'checkbox', 'options' => $productCategories, 'required' => true));
			$form->end();
			
			
		}else{
			echo '';	
		}
		
		
        $this->autoRender = false;
    }
	
	
	public function getProductSales($office_id=0, $date_from, $date_to, $product_id=0){
		
		$this->loadModel('Memo');
		
		$sales_data = array();
		
		if ($office_id && $product_id) 
		{
                /*$this->Memo->recursive=-1;
                $sales_people = $this->Memo->find('all',array(
                    'fields'=>array('DISTINCT(sales_person_id) as sales_person_id','SalesPerson.name'),
                    'joins'=>array(
                        array('table' => 'sales_people',
                            'alias' => 'SalesPerson',
                            'type' => 'INNER',
                            'conditions' => array(
                                ' SalesPerson.id=Memo.sales_person_id',
                                'SalesPerson.office_id'=> $office_id
                                )
                            )
                        ),
                    'conditions'=>array(
                        'Memo.memo_date BETWEEN ? and ?'=>array(date('Y-m-d', strtotime($date_from)), date('Y-m-d', strtotime($date_to)))
                        ),
                    ));
					
				
				
                $sales_person=array();
                foreach ($sales_people as  $data) {
                    $sales_person[]=$data['0']['sales_person_id'];
                }
                $sales_person=implode(',', $sales_person);
       			//pr($sales_person);
				
				
				   
					   
				
                if (!empty($sales_person)) 
				{
					$sql = "SELECT m.sales_person_id,md.product_id, SUM(md.sales_qty) as pro_quantity, SUM(md.sales_qty*md.price) as pro_price
                       FROM memos m RIGHT JOIN memo_details md on md.memo_id=m.id
                       WHERE (m.status!=0 AND m.memo_date BETWEEN  '".date('Y-m-d', strtotime($date_from))."' AND '". date('Y-m-d', strtotime($date_to))."') AND sales_person_id IN (".$sales_person.") AND md.price!=0 AND md.product_id=$product_id  GROUP BY m.sales_person_id,md.product_id";
                    $sales_results = $this->Memo->query($sql);
                    
					//pr($sales_results);
					
					
					$qty = 0;
					$val = 0;
					foreach($sales_results as $sales_result){
						$qty +=$sales_result[0]['pro_quantity'];
						$val +=$sales_result[0]['pro_price'];
					}
					
					
					
					$sales_data['office_id'] = $office_id;
					$sales_data['product_id'] = $product_id;
					$sales_data['qty'] = $qty?$qty:0;
					$sales_data['val'] = $val?$val:0;
                	
					//pr($sales_data);
					//exit;
					
					//exit;
					
					return  $sales_data;
					
                }*/
				
				$sql = "SELECT t.office_id, md.product_id, SUM(md.sales_qty) as qty,
				sum(md.sales_qty*(md.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN md.discount_amount ELSE 0 END))) as val 
				FROM memos m 
				INNER JOIN memo_details md on md.memo_id=m.id
				LEFT JOIN discount_bonus_policies dbp on md.policy_id = dbp.id
				INNER JOIN territories t on m.territory_id=t.id
				WHERE (m.status!=0 AND m.memo_date BETWEEN  '".date('Y-m-d', strtotime($date_from))."' AND '". date('Y-m-d', strtotime($date_to))."') AND t.office_id=$office_id AND m.gross_value > 0 AND m.status > 0 AND md.product_id=".$product_id."  GROUP BY t.office_id, md.product_id";
				//pr($sales_data);
				return $sales_data = $this->Memo->query($sql);
					
				
            }
	}
	
	
	
	//xls download
	public function admin_dwonload_xls() 
    {
		$request_data = $this->Session->read('request_data');
		$q_data = $this->Session->read('q_data');
		$offices = $this->Session->read('offices');
		$categories_products = $this->Session->read('categories_products');
		
		
		
						
		$header="";
		$data1="";
		
		
		$total_products = 0;
		$data1 .= ucfirst("Sales Area\t");
	    foreach ($categories_products as $c_value)
	    {
		  	$total_products+=count($c_value['Product']); 
			
			foreach ($c_value['Product'] as $p_value){
				$data1 .= ucfirst($p_value['name']."\t");
				$data1 .= ucfirst(" \t");
			}
			$data1 .= ucfirst("Total ".$c_value['ProductCategory']['name']."\t");
			$data1 .= ucfirst(" \t");
		}
		$data1 .= ucfirst("Total Sales\t");
		$data1 .= ucfirst(" \t");
		$data1 .= "\n";
		
		
		$data1 .= ucfirst(" \t");
	    foreach ($categories_products as $c_value)
	    {
		  	$total_products+=count($c_value['Product']); 
			
			foreach ($c_value['Product'] as $p_value){
				$data1 .= ucfirst("Qty\t");
				$data1 .= ucfirst("Val\t");
			}
			$data1 .= ucfirst("Qty\t");
			$data1 .= ucfirst("Val\t");
		}
		$data1 .= ucfirst("Qty\t");
		$data1 .= ucfirst("Val\t");
		$data1 .= "\n";
		
		
		foreach ($offices as $key => $value)
		{ 
           if(in_array($key, $request_data['NationalSalesReports']['office_id'])) 
		   {
				$data1 .= ucfirst(str_replace('Sales Office', '', $value)."\t");
				$f_t_qty = 0; 
                $f_t_val = 0;
				foreach ($categories_products as $c_value)
				{
					$t_qty = 0; 
					$t_val = 0;
					$office_id = $key;
					foreach ($c_value['Product'] as $p_value)
					{
						$product_id = $p_value['id'];
						$qty = @$q_data[$office_id][$product_id]['qty']?$q_data[$office_id][$product_id]['qty']:'0.00';
						$val = @$q_data[$office_id][$product_id]['val']?$q_data[$office_id][$product_id]['val']:'0.00';
						$t_qty+=$qty;
						$t_val+=$val;
						
						$data1 .= ucfirst($qty."\t");
						$data1 .= ucfirst($val."\t");
					}
					$data1 .= ucfirst($t_qty."\t");
					$data1 .= ucfirst($t_val."\t");
					
					$f_t_qty+=$t_qty;
					$f_t_val+=$t_val;
				}
				$data1 .= ucfirst($f_t_qty."\t");
				$data1 .= ucfirst($f_t_val."\t");
				$data1 .= "\n";
		   }
		}

		
		//exit;
		
		/*$data1 .= ucfirst("Order Date,"); //for Tab Delimitated use \t
		$data1 .= ucfirst("Order ID,");
		$data1 .= ucfirst("Before Discount,");
		$data1 .= ucfirst("Discount,");
		$data1 .= ucfirst("Net Product Price,");
		$data1 .= ucfirst("Shipping Cost,");
		$data1 .= ucfirst("Sub Total,");
		$data1 .= ucfirst("7% Tax Collected,");
		$data1 .= ucfirst("3.5% Tax Collected,");
		$data1 .= ucfirst("Total,");
		
		$data1 .= ucfirst("7% Taxable Total,");
		$data1 .= ucfirst("3.5% Taxable Total,");
		$data1 .= ucfirst("Tax Exempt Total,");*/
		
		/*$data1 .= "\n";
		
		foreach($this->data['e_orders'] as $row1){
			$line = '';
			foreach($row1 as $value){
				if ((!isset($value)) OR ($value == "")){
					$value = "\t"; //for Tab Delimitated use \t
				}else{
					$value = str_replace('"', '""', $value);
					$value = '"' . $value . '"' . "\t"; //for Tab Delimitated use \t
				}
				$line .= $value;
			}
			$data1 .= trim($line)."\n";
		}*/
		
		
		
		//exit;
		
		$data1 = str_replace("\r", "", $data1);
		if ($data1 == ""){
			$data1 = "\n(0) Records Found!\n";
		}
		
		header("Content-type: application/vnd.ms-excel; name='excel'");
		header("Content-Disposition: attachment; filename=\"National-Sales-Volume-Value-Reports-".date("jS-F-Y-H:i:s").".xls\"");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		echo $data1; 
		
		exit;
		
		$this->autoRender = false;
	}
	
}
