<?php
App::uses('AppController', 'Controller');
/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class WeeklySalesReportsController extends AppController {

/**
 * Components
 *
 * @var array
 */
    public $uses = array('Memo', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'Combination', 'MemoDetail', 'MeasurementUnit', 'ProductCategory', 'TerritoryAssignHistory', 'Office', 'Territory','ProductType');
    public $components = array('Paginator', 'Session', 'Filter.Filter');
    
/**
 * admin_index method
 *
 * @return void 
 */
    public function admin_index($id = null) {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600);
        $this->loadModel('Product');
        $this->loadModel('Memo');
		$this->loadModel('Month');
		$this->loadModel('FiscalYear');
        
        $this->set('page_title', 'Weekly Sales Report');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0)
        {
            $office_id = 0;
        } 
        else 
        {
            $office_id = $this->UserAuth->getOfficeId();
        }
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if($office_parent_id!=0)
        {
            $office_type = $this->Office->find('first',array(
                'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
                'recursive'=>-1
                ));
            $office_type_id = $office_type['Office']['office_type_id'];
        }


        if ($office_parent_id == 0) {
            $region_office_condition=array('office_type_id'=>3);
            $office_conditions = array('office_type_id'=>2, "NOT" => array( "id" => array(30, 31, 37)));
        } 
        else 
        {
            if($office_type_id==3)
            {
                $region_office_condition=array('office_type_id'=>3,'Office.id' => $this->UserAuth->getOfficeId());
                $office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId(), 'office_type_id'=>2);
            }
            elseif($office_type_id==2)
            {
                $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'office_type_id'=>2);
            }

        }

        $offices = $this->Office->find('list', array(
            'conditions'=> $office_conditions,
            'fields'=>array('office_name')
            ));

        if(isset($region_office_condition))
        {
            $region_offices = $this->Office->find('list', array(
                'conditions' => $region_office_condition, 
                'order' => array('office_name' => 'asc')
                ));

            $this->set(compact('region_offices'));
        }       
        if ($this->request->is('post') || $this->request->is('put')) {
            
            //pr($this->request->data);die();
            
            $product_list = $this->Product->find('all',array(
            'fields'=>array('Product.name','Product.id','MU.name as mes_name','Product.product_category_id'),
            'joins'=>array(
                array(
                    'table'=>'measurement_units',
                    'alias'=>'MU',
                    'type' => 'LEFT',
                    'conditions'=>array('MU.id= Product.sales_measurement_unit_id')
                    )
                ),
            'conditions'=>array('NOT'=>array('Product.product_category_id'=>32),'Product.product_type_id'=>1),
            'order'=>'Product.product_category_id',
            'recursive'=>-1
            ));
           
            $requested_data = $this->request->data;
            
            $date_from = '01-'.$this->data['WeeklySalesReports']['date_from'];
            $date_to = $this->data['WeeklySalesReports']['date_to'];
            $total_working_days = $this->data['WeeklySalesReports']['total_working_days'];
			$monthly_working_days = $this->data['WeeklySalesReports']['monthly_working_days'];
			$remaining_days = $monthly_working_days - $total_working_days;
			$month = date('F',strtotime($date_from));
			//pr($month);die();
			$month_info = $this->Month->find('first',array(
				'conditions'=>array('Month.name'=>$month),
				'recursive'=> -1
			));
			$month_id = $month_info['Month']['id'];
			//pr($month_info);die();
			$fiscal_year_info = $this->FiscalYear->find('first',array(
				'conditions' => array(
					'FiscalYear.start_date <=' =>$date_from,
					'FiscalYear.end_date >=' =>$date_from,
					),
				'recursive'=> -1
			));
			$fiscal_year_id = $fiscal_year_info['FiscalYear']['id'];
			//pr($fiscal_year_info);die();
            $this->loadModel('ProductSettingsForReport');
            
            $product_setting_list = $this->ProductSettingsForReport->find('all',array(
                'fields'=>array('ProductSettingsForReport.*','Product.name','Product.id','Product.product_category_id','Product.brand_id','Product.source'),
            ));
			$first_item = 0;
			if(!empty($product_setting_list)){
				$first_item = $product_setting_list[0]['ProductSettingsForReport']['item'];
			}
            $this->set(compact('product_setting_list'));
            foreach ($product_setting_list as $key => $value) {
                $product_sales_data[$value['Product']['id']] = $this->getProductSales($date_from, $date_to, $value['Product']['id'],$month_id,$fiscal_year_id);
				$product_sales_data[$value['Product']['id']]['ProductSettingsForReport'] = $value['ProductSettingsForReport'];
            }
			//pr($product_sales_data);die();
            $this->set(compact('requested_data', 'product_list', 'date_from', 'date_to','product_sales_data','first_item','total_working_days','monthly_working_days','remaining_days'));
            
            
        }
        
    }
/******************************* Filtering Areaa******************************************/ 
    public function getProductSales($date_from, $date_to, $product_id=0,$month_id = 0,$fiscal_year_id=0){
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600);
        $this->loadModel('Memo');
        $this->loadModel('Product');
		$this->loadModel('SaleTargetMonth');
        $sales_data = array();
        
        if ($product_id) 
        {
            $this->Memo->recursive=-1;
            
            /*$sales_results = $this->Memo->query(" SELECT o.office_name,m.office_id,md.product_id, SUM(md.sales_qty) as pro_quantity, SUM(md.price*md.sales_qty) as pro_price
               FROM memos m RIGHT JOIN memo_details md on md.memo_id=m.id JOIN offices o on m.office_id=o.id 
               WHERE (m.status!=0 AND m.memo_date BETWEEN  '".date('Y-m-d', strtotime($date_from))."' AND '". date('Y-m-d', strtotime($date_to))."') AND md.price!=0 AND md.product_id IN (".$product_id.") GROUP BY md.product_id,o.office_name,m.office_id ORDER BY m.office_id");*/

            $from_date = date('Y-m-d', strtotime($date_from));
            $to_date = date('Y-m-d', strtotime($date_to));
            $sales_results = $this->Memo->find('all',array(
            
                'fields'=>array(
                    'COUNT(Memo.id) as effective_call','Office.office_name','Memo.office_id','MemoDetail.product_id','Product.name','Product.product_category_id','Product.brand_id','Product.source', 'Product.cyp_cal as cyp', 'Product.cyp as cyp_v','Product.sales_measurement_unit_id as measurement_unit','SUM(MemoDetail.sales_qty) as pro_quantity','SUM(MemoDetail.price*MemoDetail.sales_qty) as pro_price','SaleTargetMonth.target_quantity','SaleTargetMonth.target_quantity_achievement'
                ),
                'joins'=>array(
                    array(
                        'alias' => 'MemoDetail',
                        'table' => 'memo_details',
                        'type' => 'RIGHT',
                        'conditions' => 'Memo.id = MemoDetail.memo_id'
                    ),
                    array(
                        'alias' => 'Office',
                        'table' => 'offices',
                        'type' => 'INNER',
                        'conditions' => 'Memo.office_id = Office.id'
                    ),
                    array(
                        'alias' => 'Product',
                        'table' => 'products',
                        'type' => 'INNER',
                        'conditions' => 'MemoDetail.product_id = Product.id'
                    ),
					array(
                        'alias' => 'SaleTargetMonth',
                        'table' => 'sale_target_months',
                        'type' => 'INNER',
                        'conditions' => 'SaleTargetMonth.product_id = Product.id',
                    )
                ),
                'conditions'=>array(
                    'Memo.status !=' =>0,
                    'Memo.memo_date >='=> $from_date,
                    'Memo.memo_date <='=> $to_date,
					'Memo.gross_value >' => 0,
                    'MemoDetail.price !='=> 0,
                    'MemoDetail.product_id'=> $product_id,
                    'SaleTargetMonth.month_id'=> $month_id,
                    'SaleTargetMonth.fiscal_year_id'=> $fiscal_year_id,
                ),
                'group'=>array(
                    'MemoDetail.product_id','Office.office_name','Memo.office_id','Product.name','Product.product_category_id','Product.brand_id','Product.source', 'Product.cyp_cal', 'Product.cyp','Product.sales_measurement_unit_id','SaleTargetMonth.target_quantity','SaleTargetMonth.target_quantity_achievement'
                ),
                'order'=> array('Memo.office_id')
            ));
            //pr($sales_results);die();
            $qty = 0;
            $val = 0;
            $sales_data = array();
            $product_info = array();

			$total_cyp=0;
            if(!empty($sales_results)){
                foreach ($sales_results as $key => $value) {
					$cyp=0;
                    $value[0]['office_name'] = $value['Office']['office_name'];
                    $value[0]['office_id'] = $value['Memo']['office_id'];
                    $product_info['Product']['product_id'] = $value[0]['product_id'] = $value['MemoDetail']['product_id'];
                    $product_info['Product']['product_name'] = $value[0]['product_name'] = $value['Product']['name'];
                    $product_info['Product']['product_category_id'] = $value[0]['product_category_id'] = $value['Product']['product_category_id'];
                    $product_info['Product']['brand_id'] = $value[0]['brand_id'] = $value['Product']['brand_id'];
                    $product_info['Product']['source'] = $value[0]['source'] = $value['Product']['source'];
					$product_info['Product']['target_quantity'] = $value['SaleTargetMonth']['target_quantity'];
					$product_info['Product']['target_quantity_achievement'] = $value['SaleTargetMonth']['target_quantity_achievement'];
					
					$pro_volume = $value[0]['pro_quantity']?$value[0]['pro_quantity']:0;
					$base_qty = $this->unit_convert($value[0]['product_id'], $value[0]['measurement_unit'], $pro_volume);
					$cyp_s = $value[0]['cyp']?$value[0]['cyp']:0;
					$cyp_v = $value[0]['cyp_v']?$value[0]['cyp_v']:0;
					
					$effective_call = $value[0]['effective_call'];
					if($cyp_s=='/'){
						@$cyp = $base_qty/$cyp_v;
					}elseif($cyp_s=='*'){
						@$cyp = $base_qty*$cyp_v;
					}elseif($cyp_s=='-'){
						@$cyp = $base_qty-$cyp_v;
					}elseif($cyp_s=='+'){
						@$cyp = $base_qty+$cyp_v;
					}else{
						$cyp = 0;
					}
					$total_cyp+=$cyp;
					
					$value[0]['cyp'] = $cyp;
					
                    $sales_data[$value[0]['office_id']] = $value[0];
                    $qty+= $value[0]['pro_quantity'];
                    $val+= $value[0]['pro_price'];
                }
            
                $sales_data['effective_call'] = $effective_call;
                $sales_data['total_qty'] = $qty;
                $sales_data['total_price'] = $val;
                $sales_data['Product'] = $product_info['Product'];
				$target =  $product_info['Product']['target_quantity'];
				if($target == 0)$target = 1;
				$achive = ($qty/$target)*100;
				$sales_data['achive'] =$achive;
            }else{
                $products = $this->Product->find('first',array(
                    'conditions'=>array('Product.id'=>$product_id),
                    'recursive'=> -1
                ));
                $sales_data['Product']['product_name'] = $products['Product']['name'];
                $sales_data['Product']['product_category_id'] = $products['Product']['product_category_id'];
                $sales_data['Product']['brand_id'] = $products['Product']['brand_id'];
                $sales_data['Product']['source'] = $products['Product']['source'];
                $sales_data['Product']['product_id'] = $products['Product']['id'];
				$sales_data['Product']['target_quantity'] = 0;
                $sales_data['Product']['target_quantity_achievement'] = 0;
				$sales_data['total_qty'] = 0;
                $sales_data['total_price'] = 0;
				$sales_data['achive'] = 0;
            }
            //pr($sales_data);die();
            return  $sales_data;
            
        }
    }
	
	public function getSettingsInfo($last_setting_info){
		$this->loadModel('ProductCategory');
		$this->loadModel('Brand');
		//pr($last_setting_info);
		if(!empty($last_setting_info)){
			if($last_setting_info['item_type'] == 0){
				$id = $last_setting_info['item'];
				$data_info = $this->ProductCategory->find('first',array(
				'conditions'=>array('ProductCategory.id'=>$id),
				'fields'=>array('ProductCategory.id','ProductCategory.name'),
				'recursive'=> -1));
				$data = $data_info['ProductCategory']['name'];
			}elseif($last_setting_info['item_type'] == 1){
				$id = $last_setting_info['item'];
				$data_info = $this->Brand->find('first',array(
				'conditions'=>array('Brand.id'=>$id),
				'fields'=>array('Brand.id','Brand.name'),
				'recursive'=> -1));
				$data = $data_info['Brand']['name'];
			}else{
				$data = 'SMC';
			}
		}
		//pr($data);die();
		return $data;
	}

}
