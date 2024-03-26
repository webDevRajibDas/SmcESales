<?php
App::uses('AppController', 'Controller');
/**
 * EsalesSettings Controller
 *
 * @property OutletSalesReport $OutletSalesReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OutletSalesReportsController extends AppController {
	
	

/**
 * Components
 *
 * @var array
 */
 
	public $uses = array('Memo', 'MemoDetail', 'Office', 'Territory', 'OutletCategory', 'Outlet', 'Product', 'SalesPerson');
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
		
		ini_set('memory_limit', '512M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes
				
		
		$this->set('page_title', 'Outlet Sales Summary Report');
		
		
		//for outlet category list
		$outlet_categories = $this->OutletCategory->find('list', array(
			'conditions' => array('is_active'=>1), 
			'order' => array('category_name' => 'asc')
		));
		$this->set(compact('outlet_categories'));
		$this->Session->write('outlet_categories', $outlet_categories);
		
		
		$report_type_list = array(
			'1' => 'Outlet Type Wise Sales Summary',
			'2' => 'Outlet Type Wise Sales Details'
		);
		$this->set(compact('report_type_list'));
		
		
		
		//for region office list
        $region_offices = $this->Office->find('list', array(
            'conditions' => array('Office.office_type_id'=>3),
            'order' => array('office_name' => 'asc')
        ));
		
		
		$so_list = array();
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		
		$this->set(compact('office_parent_id'));
		
		if ($office_parent_id == 0)
		{
			$office_conditions = array('Office.office_type_id'=>2, "NOT" => array( "id" => array(30, 31, 37)));
						
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
			
			$office_id = array_keys($offices);
			
		} 
		else 
		{
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id'=>2);
			$office_id = $this->UserAuth->getOfficeId();
			
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
		
		$this->set(compact('office_id'));
		
		$report_esales_setting_id = array();
		
		
		$this->Session->write('so_list', $so_list);
		
		$outlet_type = 1;		
		$ranks_2_conditions = array('type' => $outlet_type);	
		$region_offices_2_conditions = array('Office.office_type_id' => 3);	
						
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$request_data = $this->request->data;
			
			$office_id = isset($this->request->data['OutletSalesReports']['office_id']) != '' ? $this->request->data['OutletSalesReports']['office_id'] : $office_id;
			
			//get SO list
			/*$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id','SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => 4,
				),
				'recursive'=> 0
			));*/
			
			
			//NEW SO LIST GENERATE FROM MEMO TABLE
			$date_from = date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_to']));
			$so_list_r = $this->SalesPerson->find('all',array(
				'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
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
			
			//pr($this->request->data);
			//exit;
			
			foreach($so_list_r as $key => $value)
			{
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
			}
			
			
			$date_from = $request_data['OutletSalesReports']['date_from'];
			$date_to = $request_data['OutletSalesReports']['date_to'];
			$this->set(compact('date_from', 'date_to', 'request_data'));
			
			$this->Session->write('request_data', $request_data);
			
			//pr($request_data);
			//exit;
			
			$product_list = $this->Product->find('list', array(
				'conditions'=>array
					('NOT' => array('Product.product_category_id'=>32), 
					'is_active' => 1,
					'Product.product_type_id' => 1
				),
				'order'=>  array('order'=>'asc')
			));
			
			$this->set(compact('product_list'));
			$this->Session->write('product_list', $product_list);
			
			if($request_data['OutletSalesReports']['report_type']==1){
				//START DATA QUERY
				$date_from = date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_from']));
				$date_to = date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_to']));
				$conditions = array(
					'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					'Memo.gross_value >' => 0,
					'Memo.status !=' => 0,
					'Territory.office_id' => $request_data['OutletSalesReports']['office_id'],
					'MemoDetail.price >' => 0,
				);
	
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
							'alias' => 'dbp',
							'table' => 'discount_bonus_policies',
							'type' => 'left',
							'conditions' => 'MemoDetail.policy_id = dbp.id'
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
						)
					),
					'fields' => array(
						'sum(MemoDetail.sales_qty) AS volume,
						sum(MemoDetail.sales_qty*(MemoDetail.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN MemoDetail.discount_amount ELSE 0 END))) AS value,
						COUNT(Memo.memo_no) AS ec', 'sales_person_id', 'Outlet.category_id',
						'MemoDetail.product_id'
					),
					'group' => array('sales_person_id', 'Outlet.category_id', 'MemoDetail.product_id'),
					'recursive' => -1
				));	
				
				//sum(MemoDetail.sales_qty*MemoDetail.price) AS value,
				//pr($q_results);
				
				$datas = array();
				foreach($q_results as $result){
					$datas[$result['Memo']['sales_person_id']][$result['Outlet']['category_id']][$result['MemoDetail']['product_id']] = array(
						'volume' => $result[0]['volume'],
						'value' => $result[0]['value'],
						'ec' => $result[0]['ec'],
					);
				}
				$this->set(compact('datas'));
				$this->Session->write('s_datas', $datas);
				//pr($datas);
				
				//OC COUNT
				$conditions = array(
					'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					//'Memo.gross_value >' => 0,
					'Memo.status !=' => 0,
					'Territory.office_id' => $request_data['OutletSalesReports']['office_id'],
				);
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
						)
					),
					'fields' => array(
						'COUNT(DISTINCT Memo.outlet_id) as oc', 'sales_person_id',
						'Outlet.category_id', 'MemoDetail.product_id'
					),
					'group' => array(
						'sales_person_id', 'Outlet.category_id',
						'MemoDetail.product_id'
					),
					'recursive' => -1
				));	
				$datas2 = array();
				foreach($q_results as $result){
					$datas2[$result['Memo']['sales_person_id']][$result['Outlet']['category_id']][$result['MemoDetail']['product_id']] = array(
						'oc' => $result[0]['oc'],
					);
				}
				$this->set(compact('datas2'));
				$this->Session->write('s_datas2', $datas2);
				//exit;
				//END DATA QUERY
			}
			else{
				//START DATA QUERY
				$date_from = date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_from']));
				$date_to = date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_to']));
				$conditions = array(
					'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					'Memo.gross_value >' => 0,
					'Memo.status !=' => 0,
					'Territory.office_id' => $request_data['OutletSalesReports']['office_id'],
					'MemoDetail.price >' => 0,
				);
	
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
						)
					),
					//'fields' => array('sum(MemoDetail.sales_qty) AS volume, sum(MemoDetail.sales_qty*MemoDetail.price) AS value, COUNT(Memo.memo_no) AS ec', 'sales_person_id', 'Outlet.category_id', 'MemoDetail.product_id'),
					//'group' => array('sales_person_id', 'Outlet.category_id', 'MemoDetail.product_id'),
					
					'fields' => array(
						'Memo.id', 'Memo.memo_no', 'Memo.sales_person_id', 'Memo.memo_date',
						'Memo.gross_value', 'Outlet.category_id', 'MemoDetail.product_id',
						'MemoDetail.sales_qty', 'MemoDetail.price', 'Product.order',
						'Product.name', 'Outlet.name', 'Market.name', 'Thana.name'
					),
					'group' => array(
						'Memo.id', 'Memo.memo_no', 'Memo.sales_person_id', 'Memo.memo_date',
						'Memo.gross_value', 'Outlet.category_id', 'MemoDetail.product_id',
						'MemoDetail.sales_qty', 'MemoDetail.price', 'Product.order',
						'Product.name', 'Outlet.name', 'Market.name', 'Thana.name'
					),
					'order' => array('Memo.memo_no asc', 'Outlet.category_id asc', 'Product.order asc'),
					'recursive' => -1
				));	
				
				//pr($q_results);
				//exit;
				
				$datas = array();
				$i=0;
				foreach($q_results as $result){
					$datas[$result['Memo']['sales_person_id']][$result['Outlet']['category_id']][$result['Memo']['memo_no']][$result['MemoDetail']['product_id']] = array(
						'memo_no' 				=> $result['Memo']['memo_no'],
						'memo_date' 			=> $result['Memo']['memo_date'],
						'gross_value' 			=> $result['Memo']['gross_value'],
						
						'product_id' 			=> $result['MemoDetail']['product_id'],
						'product_name' 			=> $result['Product']['name'],
						'product_sales_qty' 	=> $result['MemoDetail']['sales_qty'],
						'product_price' 		=> $result['MemoDetail']['price'],
						
						'outlet_name' 			=> $result['Outlet']['name'],
						'outlet_category_id' 	=> $result['Outlet']['category_id'],
						
						'market_name' 			=> $result['Market']['name'],
						'thana_name' 			=> $result['Thana']['name'],
					);
					$i++;
				}
				$this->set(compact('datas'));
				$this->Session->write('d_datas', $datas);
				//pr($datas);
				//exit;
				
			}
			
			
						
		}
		//for office list
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		
		/*pr($offices);
		exit;*/
				
		$this->set(compact('offices', 'outlet_type', 'region_offices', 'so_list'));
		
	}

	
	public function getOutletECTotal($request_data=array(), $so_id=0, $outlet_category_id=0)
	{
		$date_from = date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_from']));
		$date_to = date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_to']));
			
		/*$conditions = array(
			'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
			'Memo.gross_value >' => 0,
			'Memo.status !=' => 0,
			'Memo.sales_person_id' => $so_id,
			'Outlet.category_id' => $outlet_category_id,
		);
		
		$result = $this->Memo->find('count', array(
			'conditions'=> $conditions,
			'fields' => 'COUNT(Memo.memo_no) as count',
			'recursive' => 0
		));
		return $result;*/
		
		$sql = "SELECT COUNT(Memo.memo_no) as count FROM [memos] AS [Memo] LEFT JOIN [sales_people] AS [SalesPerson] ON ([Memo].[sales_person_id] = [SalesPerson].[id]) LEFT JOIN [outlets] AS [Outlet] ON ([Memo].[outlet_id] = [Outlet].[id]) LEFT JOIN [territories] AS [Territory] ON ([Memo].[territory_id] = [Territory].[id]) LEFT JOIN [markets] AS [Market] ON ([Memo].[market_id] = [Market].[id]) WHERE [Memo].[memo_date] BETWEEN '".$date_from."' and '".$date_to."' AND [Memo].[gross_value] > 0 AND [Memo].[status] > 0 AND [Memo].[sales_person_id] = ".$so_id." AND [Outlet].[category_id] = ".$outlet_category_id."";
		
		$result = $this->Memo->query($sql);	
		
		return $result[0][0]['count'];	
	}
	

	public function getOutletOCTotal($request_data=array(), $so_id=0, $outlet_category_id=0)
	{
		$date_from = date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_from']));
		$date_to = date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_to']));
			
		/*$conditions = array(
			'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
			'Memo.status !=' => 0,
			'Memo.sales_person_id' => $so_id,
			'Outlet.category_id' => $outlet_category_id,
		);
		
		$result = $this->Memo->find('count', array(
			'conditions'=> $conditions,
			'fields' => 'COUNT(DISTINCT Memo.outlet_id) as count',
			'recursive' => 0
		));	
		//pr($result);
		return $result;	*/
		
		$sql = "SELECT COUNT(DISTINCT Memo.outlet_id) as count FROM [memos] AS [Memo] LEFT JOIN [sales_people] AS [SalesPerson] ON ([Memo].[sales_person_id] = [SalesPerson].[id]) LEFT JOIN [outlets] AS [Outlet] ON ([Memo].[outlet_id] = [Outlet].[id]) LEFT JOIN [territories] AS [Territory] ON ([Memo].[territory_id] = [Territory].[id]) LEFT JOIN [markets] AS [Market] ON ([Memo].[market_id] = [Market].[id]) WHERE [Memo].[memo_date] BETWEEN '".$date_from."' and '".$date_to."' AND [Memo].[status] > 0 AND [Memo].[sales_person_id] = ".$so_id." AND [Outlet].[category_id] = ".$outlet_category_id."";
		
		$result = $this->Memo->query($sql);	
		
		return $result[0][0]['count'];
		
	}
	
	
	/*public function getProdcutOCTotal($request_data=array(), $so_id=0, $outlet_category_id=0, $product_id=0)
	{
		$date_from = date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_from']));
		$date_to = date('Y-m-d', strtotime($request_data['OutletSalesReports']['date_to']));
			
		$conditions = array(
			'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
			'Memo.gross_value >' => 0,
			'Memo.status !=' => 0,
			'Memo.sales_person_id' => $so_id,
			'Outlet.category_id' => $outlet_category_id,
			'MemoDetail.product_id' => $product_id
		);
		
		$result = $this->Memo->find('all', array(
			'conditions'=> $conditions,
			'joins' => array(
				array(
					'alias' => 'MemoDetail',
					'table' => 'memo_details',
					'type' => 'INNER',
					'conditions' => 'Memo.id = MemoDetail.memo_id'
				)
			),
			'fields' => array('sum(MemoDetail.sales_qty) AS p_qty, sum(MemoDetail.sales_qty*MemoDetail.price) AS p_value, COUNT(Memo.memo_no) AS total_EC, COUNT(DISTINCT Memo.outlet_id) as o_count'),
			'recursive' => 0
		));	
			
		//pr($result);
		//exit;
		
		return $result;	
	}*/

	
	
	public function get_territory_so_list()
	{
		$view = new View($this);
		
        $form = $view->loadHelper('Form');	
		
		$office_id = $this->request->data['office_id'];
		 
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
		
		        
		if($so_list)
		{	
			$form->create('OutletSalesReports', array('role' => 'form', 'action'=>'index'))	;
			
			echo $form->input('so_id', array('label'=>false, 'class' => 'checkbox', 'multiple' => 'checkbox', 'options' => $so_list));
			$form->end();
			
		}
		else
		{
			echo '';	
		}
		
		
        $this->autoRender = false;
    }
		
		
	public function getOutletName($id=0) 
	{
		$this->loadModel('Outlet'); 
	
		$reuslt = $this->Outlet->find('first', array(
			'conditions' => array(
				'id' => $id
			),
			'recursive' => -1
		));
		
		return $reuslt['Outlet']['name'];
	}
	
	public function getOutletTypeName($id=0) 
	{
		$this->loadModel('OutletCategory'); 
	
		$reuslt = $this->OutletCategory->find('first', array(
			'conditions' => array(
				'id' => $id
			),
			'recursive' => -1
		));
		
		return $reuslt['OutletCategory']['name'];
	}
	

	
	
	public function getOfficeName($id=0) 
	{
		$this->loadModel('Office'); 
	
		$reuslt = $this->Office->find('first', array(
			'conditions' => array(
				'id' => $id
			),
			'recursive' => -1
		));
		
		return $reuslt['Office']['office_name'];
	}
	
	public function getSOName($id=0) 
	{
		$this->loadModel('SalesPerson'); 
	
		$reuslt = $this->SalesPerson->find('first', array(
			'conditions' => array(
				'id' => $id
			),
			'recursive' => -1
		));
		
		return $reuslt['SalesPerson']['name'];
	}
	
	
	
	//summary xls download
	public function admin_summary_xls() 
    {
		$request_data = $this->Session->read('request_data');
		$so_list = $this->Session->read('so_list');
		$outlet_categories = $this->Session->read('outlet_categories');
		$product_list = $this->Session->read('product_list');
		
		$datas = $this->Session->read('s_datas');
		$datas2 = $this->Session->read('s_datas2');
		
						
		$header="";
		$data1="";
				
		
		$data1 .= ucfirst("Sales Officer\t"); //for Tab Delimitated use \t
		$data1 .= ucfirst("Outlet Type\t");
		$data1 .= ucfirst("# Of Eff. Calls\t");
		$data1 .= ucfirst("# Of Outlet\t");
		$data1 .= ucfirst("Product\t");
		$data1 .= ucfirst("Qty\t");
		$data1 .= ucfirst("Value\t");
		$data1 .= ucfirst("Eff. Calls\t");
		$data1 .= ucfirst("Eff. Outlet\t");
		
		
		$data1 .= "\n";
		
		
		$so_array = array();
		if(empty($request_data['OutletSalesReports']['so_id'])){	
			foreach($so_list as $key => $val){array_push($so_array, $key);}
		}else{
			$so_array = $request_data['OutletSalesReports']['so_id'];
		}
		
		//pr($so_array);
		
		$outlet_cat_array = array();
		if(empty($request_data['OutletSalesReports']['outlet_category_id'])){	
			foreach($outlet_categories as $key => $val){array_push($outlet_cat_array, $key);}
		}else{
			$outlet_cat_array = $request_data['OutletSalesReports']['outlet_category_id'];
		}
		
		
		$s=1;
        foreach($so_list as $s_key => $s_val)
		{
			if(in_array($s_key, $so_array))
			{
				$o=1;
                foreach($outlet_categories as $o_key => $o_val)
				{ 
					if(in_array($o_key, $outlet_cat_array))
					{
						$p=1; 
                        foreach($product_list as $p_key => $p_val)
						{
							$data1 .= ucfirst(($o==1 && $p==1) ? $s_val."\t" : ''."\t"); //for Tab Delimitated use \t
		
							$data1 .= ucfirst(($p==1) ? $o_val."\t" : ''."\t");
							
							$data1 .= ucfirst(($p==1) ? $this->getOutletECTotal($request_data, $s_key, $o_key)."\t" : ''."\t");
							
							$data1 .= ucfirst(($p==1) ? $this->getOutletOCTotal($request_data, $s_key, $o_key)."\t" : ''."\t");
							
							$data1 .= ucfirst($p_val."\t");
							
							$data1 .= ucfirst(@$datas[$s_key][$o_key][$p_key]['volume'] ? intval($datas[$s_key][$o_key][$p_key]['volume'])."\t" : '0'."\t");
							
							$data1 .= ucfirst(@$datas[$s_key][$o_key][$p_key]['value'] ? sprintf("%01.2f",$datas[$s_key][$o_key][$p_key]['value'])."\t" : '0'."\t");
							
							$data1 .= ucfirst(@$datas[$s_key][$o_key][$p_key]['ec'] ? $datas[$s_key][$o_key][$p_key]['ec']."\t" : '0'."\t");
							$data1 .= ucfirst(@$datas2[$s_key][$o_key][$p_key]['oc'] ? $datas2[$s_key][$o_key][$p_key]['oc']."\t" : '0'."\t");
							
							$data1 .= "\n";
							
							$p++;
						}
						
						$o++;
					}
				}
			}

			$s++;
		}
		
		//exit;
		
		
		
		$data1 = str_replace("\r", "", $data1);
		if ($data1 == ""){
			$data1 = "\n(0) Records Found!\n";
		}
		
		
		
		header("Content-type: application/vnd.ms-excel; name='excel'");
		//header("Content-Disposition: attachment; filename=sales-analysis-reports-".date("jS-F-Y-H:i:s").".xls\"");
		header("Content-Disposition: attachment; filename=\"Outlet-Sales-Summary-Reports-".date("jS-F-Y-H:i:s").".xls\"");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		echo $data1; 
		
		exit;
		
		$this->autoRender = false;
	}
	
	//detail xls download
	public function admin_detail_xls() 
    {
		$request_data = $this->Session->read('request_data');
		$so_list = $this->Session->read('so_list');
		$outlet_categories = $this->Session->read('outlet_categories');
		$product_list = $this->Session->read('product_list');
		$datas = $this->Session->read('d_datas');

		
		$header="";
		$data1="";
		
		
		$data1 .= ucfirst("Memo No\t"); //for Tab Delimitated use \t
		$data1 .= ucfirst("Date\t");
		$data1 .= ucfirst("Product\t");
		$data1 .= ucfirst("Qty\t");
		$data1 .= ucfirst("Value\t");
		$data1 .= ucfirst("Outlet\t");
		$data1 .= ucfirst("Market\t");
		$data1 .= ucfirst("Thana\t");
		
		$data1 .= "\n";
		
		
		
		$so_array = array();
		if(empty($request_data['OutletSalesReports']['so_id'])){	
			foreach($so_list as $key => $val){array_push($so_array, $key);}
		}else{
			$so_array = $request_data['OutletSalesReports']['so_id'];
		}
				
		$outlet_cat_array = array();
		if(empty($request_data['OutletSalesReports']['outlet_category_id'])){	
			foreach($outlet_categories as $key => $val){array_push($outlet_cat_array, $key);}
		}else{
			$outlet_cat_array = $request_data['OutletSalesReports']['outlet_category_id'];
		}
		
		
		$s=1;
		foreach($so_list as $s_key => $s_val)
		{ 
		  if(in_array($s_key, $so_array))
		  {
			  	$data1 .= ucfirst("Sales Officer :- ".trim($s_val)."\t");
			  	$data1 .= "\n";
			  
			  	$o=1;
				foreach($outlet_categories as $o_key => $o_val)
				{ 
				   if(in_array($o_key, $outlet_cat_array))
				   {
						$data1 .= ucfirst("Report For :- ".trim($o_val)."\t");
						$data1 .= "\n";
						
						if(@$datas[$s_key][$o_key])
						{
							$memo_datas = $datas[$s_key][$o_key];
							foreach($memo_datas as $m_key => $m_data)
							{
								$product_list = $memo_datas[$m_key]; 
								$k=1;
								$total = count($product_list);
								foreach($product_list as $p_data)
								{ 
									$data1 .= ucfirst(($k==1) ? $p_data['memo_no']."\t" : ''."\t"); //for Tab Delimitated use \t
									$data1 .= ucfirst(($k==1) ? date('d-m-Y', strtotime($p_data['memo_date']))."\t" : ''."\t");
									$data1 .= ucfirst($p_data['product_name']."\t");
									
									$data1 .= ucfirst(intval($p_data['product_sales_qty'])."\t");
									
									$data1 .= ucfirst(sprintf("%01.2f", $p_data['product_sales_qty']*$p_data['product_price'])."\t");
									$data1 .= ucfirst(($k==1) ? $p_data['outlet_name']."\t" : ''."\t");
									$data1 .= ucfirst(($k==1) ? $p_data['market_name']."\t" : ''."\t");
									$data1 .= ucfirst(($k==1) ? $p_data['thana_name']."\t" : ''."\t");
									$data1 .= "\n";
									
									if($k==$total)
									{
										$data1 .= ucfirst("\t");
										$data1 .= ucfirst("\t");
										$data1 .= ucfirst("\t");
										$data1 .= ucfirst("Memo Total:\t");
										$data1 .= ucfirst(sprintf("%01.2f", $p_data['gross_value'])."\t");	
										$data1 .= ucfirst("\t");
										$data1 .= ucfirst("\t");
										$data1 .= ucfirst("\t");
										$data1 .= "\n";
									}
									
									$k++; 
								}
							
							}
						}
						else
						{
							$data1 .= ucfirst("\t");
							$data1 .= ucfirst("\t");
							$data1 .= ucfirst("\t");
							$data1 .= ucfirst("No Result Found!\t");
							$data1 .= "\n";
								 
						}
					  
				   }
				}
		  }
		}
		
		
		//exit;
		
		$data1 = str_replace("\r", "", $data1);
		if ($data1 == ""){
			$data1 = "\n(0) Records Found!\n";
		}
		
		header("Content-type: application/vnd.ms-excel; name='excel'");
		//header("Content-Disposition: attachment; filename=sales-analysis-reports-".date("jS-F-Y-H:i:s").".xls\"");
		header("Content-Disposition: attachment; filename=\"Outlet-Sales-Detail-Reports-".date("jS-F-Y-H:i:s").".xls\"");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		echo $data1; 
		
		exit;
		
		$this->autoRender = false;
	}
	
}
