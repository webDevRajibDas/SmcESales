<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class NgoInstituteSaleReportsController extends AppController {
	
	

/**
 * Components
 *
 * @var array
 */
 
	public $uses = array('Memo', 'Office', 'Territory', 'TerritoryAssignHistory', 'SalesPerson', 'Institute', 'Product', 'Brand', 'ProductCategory', 'ProductCategory');
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
		
		ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes
				
		
		$this->set('page_title', "NGO/Institute Sales Report");
		
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
		

        $institute_type= array(1=>'NGO', 2=>'Institute');
		$this->set(compact('institute_type'));

		
		
		
		//products
        $conditions = array(
            'NOT' => array('Product.product_category_id'=>32),
            'is_active' => 1,
			'product_type_id' => 1
        );
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
		
		//report type
		$unit_types = array(
			'1' => 'Sales Unit',
			'2' => 'Base Unit',
		);
		$this->set(compact('unit_types'));
		
		
		//product_measurement
		$product_measurement = $this->Product->find('list', array(
				//'conditions'=> $pro_conditions,
				'fields'=> array('Product.id', 'Product.sales_measurement_unit_id'),
				'order'=>  array('order'=>'asc'),
				'recursive'=> -1
			));
		$this->set(compact('product_measurement'));
		
		
		//product, brand, category
        $columns = array(
            'product' => 'By Product',
            'brand' => 'By Brand',
            'category' => 'By Category'
        );
        $this->set(compact('columns'));
		
		
		
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
			
			$districts = $this->District->find('list', array(
				'conditions'=> $conditions,
				'joins' => array(
					array(
						'alias' => 'Thana',
						'table' => 'thanas',
						'type' => 'INNER',
						'conditions' => 'District.id = Thana.district_id'
					),
					array(
						'alias' => 'ThanaTerritory',
						'table' => 'thana_territories',
						'type' => 'INNER',
						'conditions' => 'Thana.id = ThanaTerritory.thana_id'
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'ThanaTerritory.territory_id = Territory.id'
					),
					
				),
				'order'=>  array('District.name'=>'asc')
			));
			
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
					'User.user_group_id' => array(4,1008),
				),
				'recursive'=> 0
			));	
			foreach($so_list_r as $key => $value)
			{
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
			}			
			
			$conditions['Territory.office_id'] = $office_id;	
			
		}
		
		$institute_type_id = '';
		
		if ($this->request->is('post') || $this->request->is('put')){
			if(@$this->data['NgoInstituteSaleReports']['region_office_id'])$office_conditions = array('Office.parent_office_id'=>$this->data['NgoInstituteSaleReports']['region_office_id']);
			if(@$this->data['NgoInstituteSaleReports']['office_id'])$office_id = $this->data['NgoInstituteSaleReports']['office_id'];
			if(@$this->request->data['NgoInstituteSaleReports']['institute_type_id'])$institute_type_id = $this->request->data['NgoInstituteSaleReports']['institute_type_id'];
		}
		
		
		$ins_conditions = array();
		$ins_conditions['Institute.is_active'] = 1;
		if($office_id)$ins_conditions['Territory.office_id'] = $office_id;
		if($institute_type_id)$ins_conditions['Institute.type'] = $institute_type_id;
		//pr($ins_conditions);
		$institutes = $this->Institute->find('list', array(
                'conditions'=> $ins_conditions,
				
				'joins' => array(
					array(
						'alias' => 'Outlet',
						'table' => 'outlets',
						'type' => 'INNER',
						'conditions' => 'Institute.id = Outlet.institute_id'
					),
					array(
						'alias' => 'Market',
						'table' => 'markets',
						'type' => 'INNER',
						'conditions' => 'Outlet.market_id = Market.id'
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Market.territory_id = Territory.id'
					)
				),
				
                'order' => array('Institute.name' => 'asc'),
            ));
		
		
		
		
		
		//for office list
		$office_conditions['NOT']=array( "id" => array(30, 31, 37)); 
		$offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		
		
		
		$dis_con = array();
		
		
		
		if($this->request->is('post') || $this->request->is('put'))
		{
			$request_data = $this->request->data;
			
			$date_from = date('Y-m-d', strtotime($request_data['NgoInstituteSaleReports']['date_from']));
			$date_to = date('Y-m-d', strtotime($request_data['NgoInstituteSaleReports']['date_to']));
			$this->set(compact('date_from', 'date_to'));
			
			$type = $this->request->data['NgoInstituteSaleReports']['type'];
			$this->set(compact('type'));
			
			$region_office_id = isset($this->request->data['NgoInstituteSaleReports']['region_office_id']) != '' ? $this->request->data['NgoInstituteSaleReports']['region_office_id'] : $region_office_id;
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
			}else {
				$offices = $this->Office->find('list', array(
					'conditions' => array(
						'office_type_id' 	=> 2,
						"NOT" => array("id" => array(30, 31, 37))
					),
					'order' => array('office_name' => 'asc')
				));

				$office_ids = array_keys($offices);
			}

			//--------------institute check-----------\\

			$institute_empty = $this->request->data['NgoInstituteSaleReports']['institute_id'];
			$countinstitute = count($this->request->data['NgoInstituteSaleReports']['institute_id']);
			print_r($region_office_id);exit;
			if(empty($region_office_id)){
				if($countinstitute > 1 || empty($institute_empty)){
					$this->Session->setFlash(__('Please Select Only One Institute. If Search Nationally.'), 'flash/error');
					$this->redirect(array('action' => 'index'));
				}
			}

			
			//-----------------end-----------------\\

			
			$office_id = isset($this->request->data['NgoInstituteSaleReports']['office_id']) != '' ? $this->request->data['NgoInstituteSaleReports']['office_id'] : $office_id;
			$this->set(compact('office_id'));
			
			$territory_id = isset($this->request->data['NgoInstituteSaleReports']['territory_id']) != '' ? $this->request->data['NgoInstituteSaleReports']['territory_id'] : 0;
			$this->set(compact('territory_id'));
			
			$so_id = isset($this->request->data['NgoInstituteSaleReports']['so_id']) != '' ? $this->request->data['NgoInstituteSaleReports']['so_id'] : 0;
			$this->set(compact('so_id'));
			
			$institute_id = isset($this->request->data['NgoInstituteSaleReports']['institute_id']) != '' ? $this->request->data['NgoInstituteSaleReports']['institute_id'] : 0;
			$this->set(compact('institute_id'));

			
			$unit_type = $this->request->data['NgoInstituteSaleReports']['unit_type'];
			$this->set(compact('unit_type'));
			$unit_type_text = ($unit_type==2)?'Base':'Sales';
			$this->set(compact('unit_type_text'));
			
			$column = $columns = $this->request->data['NgoInstituteSaleReports']['columns'];
			$this->set(compact('column'));
			
			
			$product_ids = isset($this->request->data['NgoInstituteSaleReports']['product_id']) != '' ? $this->request->data['NgoInstituteSaleReports']['product_id'] : 0;
			$brand_ids = isset($this->request->data['NgoInstituteSaleReports']['brand_id']) != '' ? $this->request->data['NgoInstituteSaleReports']['brand_id'] : 0;
			$product_category_ids = isset($this->request->data['NgoInstituteSaleReports']['product_category_id']) != '' ? $this->request->data['NgoInstituteSaleReports']['product_category_id'] : 0;
			
			
			if($columns=='product' || $columns=='category')
			{
				$c_con = array(
					'NOT' => array('ProductCategory.id'=>32),
					
				);
				if($product_category_ids)$c_con['ProductCategory.id']=$product_category_ids;
				
				$p_con = array(
					'Product.is_active' => 1,
					'Product.product_type_id' => 1
				);
				if($product_ids)$p_con['Product.id']=$product_ids;
				
				/*$filter_list = $this->Product->find('list', array(
					'conditions'=> $p_con,
					'order'=>  array('order'=>'asc')
				));*/
							  
				$categories_products = $this->ProductCategory->find('all', array(
					 'conditions' => $c_con,
					 'contain' => array(
						 'Product'=>array(
							   'conditions'=> $p_con,
							   'order' => array('Product.order'=>'asc'),
						  ),
					  ),
					  //'order' => array('ProductCategory.name'=>'asc'),
					  'recursive' => 1,
					)
				);
			   
			   //pr($categories_products);
			   //exit;
			   
			   $this->set(compact('categories_products'));
				
			}
			
			
			
			if($columns=='brand')
			{
				$b_con = array();
				
				if($brand_ids)$b_con['Brand.id']=$brand_ids;
				
				$p_con = array(
					'Product.is_active' => 1,
					'Product.product_type_id' => 1
				);
				if($product_ids)$p_con['Product.id']=$product_ids;
				
				/*$filter_list = $this->Product->find('list', array(
					'conditions'=> $p_con,
					'order'=>  array('order'=>'asc')
				));*/
							  
				$categories_products = $this->Brand->find('all', array(
					 'conditions' => $b_con,
					 'contain' => array(
						 'Product'=>array(
							   'conditions'=> $p_con
						  ),
					  ),
					  'order' => array('Brand.name'=>'asc'),
					  'recursive' => 1,
					)
				);
			   
			   //pr($categories_products);
			   //exit;
			   
			   $this->set(compact('categories_products'));
				
			}
			
			
			$this->set(compact('filter_list'));
			
			
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
			$so_list_r = $this->SalesPerson->find('all', array(
				'fields' => array('SalesPerson.id','SalesPerson.name', 'Territory.name'),
				'conditions' => array(
					'SalesPerson.office_id' => $office_id,
					'SalesPerson.territory_id >' => 0,
					'User.user_group_id' => array(4,1008),
				),
				'recursive'=> 0
			));	
			
			foreach($so_list_r as $key => $value)
			{
				$so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'].' ('.$value['Territory']['name'].')';
			}
			
			//add old so from territory_assign_histories
			if($office_id)
			{
				$conditions = array('Territory.office_id' => $office_id, 'TerritoryAssignHistory.assign_type'=>2);
				// $conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
				$conditions['TerritoryAssignHistory.date >= '] = $date_from;
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

			
			$institute_type_id = isset($this->request->data['NgoInstituteSaleReports']['institute_type_id']) != '' ? $this->request->data['NgoInstituteSaleReports']['institute_type_id'] : $institute_type_id;
			

			
			
			//For Query Conditon
			$conditions = array(
				'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
				'Memo.gross_value >' => 0,
				'Memo.status !=' => 0,
				'MemoDetail.price >' => 0
			);
			
			if($office_ids)$conditions['Memo.office_id'] = $office_ids;
			if($office_id)$conditions['Memo.office_id'] = $office_id;	
			if($institute_type_id)$conditions['Institute.type'] = $institute_type_id;	
			if($institute_id)$conditions['Outlet.institute_id'] = $institute_id;	
			
			if($type=='so'){
				if($so_id)$conditions['Memo.sales_person_id'] = $so_id;
			}else{
				if($territory_id)$conditions['Memo.territory_id'] = $territory_id;
			}
			
			if($product_ids)$conditions['MemoDetail.product_id'] = $product_ids;
			if($brand_ids)$conditions['Product.brand_id'] = $brand_ids;
			if($product_category_ids)$conditions['Product.product_category_id'] = $product_category_ids;
							
			/*pr($conditions);
			exit;*/
			

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
						'alias' => 'Office',
						'table' => 'offices',
						'type' => 'INNER',
						'conditions' => 'Territory.office_id = Office.id'
					),
					array(
						'alias' => 'Outlet',
						'table' => 'outlets',
						'type' => 'INNER',
						'conditions' => 'Memo.outlet_id = Outlet.id'
					),
					array(
						'alias' => 'Institute',
						'table' => 'institutes',
						'type' => 'INNER',
						'conditions' => 'Outlet.institute_id = Institute.id'
					)
				),
			
				
				'fields' => array('SUM(MemoDetail.sales_qty) as sales_qty', 'SUM(MemoDetail.sales_qty*MemoDetail.price) as price', 'Office.office_name', 'MemoDetail.product_id', 'Institute.id', 'Institute.name'),
				
				'group' => array('Office.office_name', 'MemoDetail.product_id', 'Institute.id', 'Institute.name'),
				
				'order' => array('Office.office_name asc', 'Institute.name asc'),
				'recursive' => -1,
				//'limit' => 15
			));	
			
			//pr($q_results);
			//exit;
			
			//$unit_type=1;
			
			$results = array();
			$institute_list = array();
			foreach($q_results as $result)
			{
				$sales_qty = ($unit_type==1)?$result[0]['sales_qty']:$this->unit_convert($result['MemoDetail']['product_id'], $product_measurement[$result['MemoDetail']['product_id']], $result[0]['sales_qty']);
				
				$institute_list[$result['Institute']['id']] = $result['Institute']['name'];
				
				$results[$result['Office']['office_name']][$result['Institute']['id']][$result['MemoDetail']['product_id']] = 
				array(
					
					'product_id' 			=> $result['MemoDetail']['product_id'],
					//'sales_qty' 			=> $result[0]['sales_qty'],
					'sales_qty' 			=> $sales_qty,
					'price' 				=> $result[0]['price'],
					'institute_name' 		=> $result['Institute']['name'],
					//'outlet_id' 			=> $result['Outlet']['id'],
				);
			}
			//pr($results);
			//exit;
			
			$this->set(compact('results', 'institute_list'));
						
		}
						
				
		$this->set(compact('offices', 'territories', 'region_offices', 'office_id', 'request_data', 'so_list', 'institutes'));
		
	}
	
	

	
	
    public function get_institute_list(){

        $view = new View($this);

        $form = $view->loadHelper('Form');


        $ids = $this->request->data['institute_type_id'];
		
		$office_id = 0;
		
		
		if(@$this->request->data['office_id'])$office_id = $this->request->data['office_id'];
		
		
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if($office_parent_id && $office_parent_id!=14){
		$office_id = $this->UserAuth->getOfficeId();
		}

        $conditions = array();
		$conditions['Institute.is_active'] = 1;
		
        
		
		//pr($conditions);
		//exit;


		
		
		$ins_conditions = array();
		$ins_conditions['Institute.is_active'] = 1;
		if($office_id)$ins_conditions['Territory.office_id'] = $office_id;
		if($ids)
        {
            $ids_arr = explode(',', $ids);
            $ins_conditions['Institute.type'] = $ids_arr;
		}
		//pr($ins_conditions);
		$results = $this->Institute->find('list', array(
                'conditions'=> $ins_conditions,
				
				'joins' => array(
					array(
						'alias' => 'Outlet',
						'table' => 'outlets',
						'type' => 'INNER',
						'conditions' => 'Institute.id = Outlet.institute_id'
					),
					array(
						'alias' => 'Market',
						'table' => 'markets',
						'type' => 'INNER',
						'conditions' => 'Outlet.market_id = Market.id'
					),
					array(
						'alias' => 'Territory',
						'table' => 'territories',
						'type' => 'INNER',
						'conditions' => 'Market.territory_id = Territory.id'
					)
				),
				
                'order' => array('Institute.name' => 'asc'),
            ));

		if($results)
		{
			/*$form->create('OutletCharacteristicReports', array('role' => 'form', 'action'=>'index'))	;
			echo $form->input('thana_id', array('id' => 'thana_id', 'label'=>false, 'class' => 'checkbox', 'fieldset' => false, 'multiple' => 'checkbox', 'required'=> true, 'options'=> $thana_list));
			$form->end();*/

			$output = '<div class="input select">
<input type="hidden" name="data[NgoInstituteSaleReports][institute_id]" value="" id="institute_id"/>';
			foreach($results as $key => $val)
			{
				$output.= '<div class="checkbox">
					<input type="checkbox" name="data[NgoInstituteSaleReports][institute_id][]" value="'.$key.'" id="institute_id'.$key.'" />
					<label for="institute_id'.$key.'">'.$val.'</label>
				  </div>';
			}
			$output.='</div>';

			echo $output;
		}
		else
		{
			echo '';
		}
        


        $this->autoRender = false;
    }
	
	
	
	
}
