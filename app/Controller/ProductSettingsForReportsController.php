<?php
App::uses('AppController', 'Controller');
/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class ProductSettingsForReportsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $uses = array('ProductSettingsForReport','Memo', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'Combination', 'MemoDetail', 'MeasurementUnit', 'ProductCategory', 'TerritoryAssignHistory', 'Office', 'Territory','ProductType');
    public $components = array('Paginator', 'Session', 'Filter.Filter');
	
/**
 * admin_index method
 *
 * @return void 
 */
	public function admin_index() {
		ini_set('max_execution_time', 1000);   
        ini_set('memory_limit', '-1');
		$this->loadModel('Product');
		
		$item_type = array(
			'0' => 'Category',
			'1' => 'Brand',
			'2' => 'Company'
		);
		$items = array();
		$this->set('page_title', 'Product Settings for Report');
		/****************** Option Start ***********************/
		/***************Product Categoris***************/
		$category_options = "<option id=''>---- Select -----</option>";
		$this->loadModel('ProductCategory');
		$product_categories = $this->ProductCategory->find('all',array());
		foreach($product_categories as $key => $value)
		{
			$id = $value['ProductCategory']['id'];
			$name = $value['ProductCategory']['name'];
			$category_options = $category_options."<option id=".$id.">".$name."</option>";
		}
		/***************** Brand ******************/
		$brand_options = "<option id=''>---- Select -----</option>";
		$this->loadModel('Brand');
		$brands = $this->Brand->find('all',array());
		foreach($brands as $key => $value)
		{
			$id = $value['Brand']['id'];
			$name = $value['Brand']['name'];
			$brand_options = $brand_options."<option id=".$id.">".$name."</option>";
		}
		/****************** Company ***********************/
		$company_options = "<option id=''>---- Select -----</option>";
		$company_options = $company_options."<option id= 1> SMCEL </option><option id= 2> SMC </option>";
    	$this->set(compact('category_options','brand_options','company_options'));
    	/****************** Option End ***********************/
		$product_list = $this->Product->find('list', array(
			'conditions' => array('Product.product_type_id'=>1),
            'order' => array('order'=>'asc'),
			'recursive'=> -1));
		$this->set(compact('product_list','item_type','items'));
		
		if ($this->request->is('post') || $this->request->is('put')) {
			//pr($this->request->data);die();
			$settings_data = $this->request->data;
			$products = $this->request->data['product_id'];
			$total_settings = array();
			if(!empty($products)){
				$this->ProductSettingsForReport->query("delete from product_settings_for_reports");
				foreach ($products as $key => $value) {
					$data = array();

					$data['product_id'] =  $products[$key];
					$data['item_type']  =  $this->request->data['item_type'][$key]; 
					$data['item']  =  $this->request->data['group_item'][$key]; 
					//$data['orders']  =  $this->request->data['order'][$key]; 

					$total_settings[] = $data;
				}
			}
			$this->ProductSettingsForReport->create();
			if($this->ProductSettingsForReport->saveAll($total_settings)){			
				$this->Session->setFlash(__('The Product Settings has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));		
			}else{
				$this->Session->setFlash(__('The Product Settings is not saved'), 'flash/warning');
			}
		}else{
			$exist_data = $this->ProductSettingsForReport->find('all',array(
				'fields'=>array('ProductSettingsForReport.*','Product.id','Product.name','Product.product_category_id','Product.brand_id','Product.source','ProductCategory.id','ProductCategory.name','Brand.id','Brand.name'),
				'joins'=>array(
					array(
						'table'=>'product_categories',
						'alias'=>'ProductCategory',
						'type' => 'LEFT',
						'conditions'=>array('ProductCategory.id= Product.product_category_id')
					),
					array(
						'table'=>'brands',
						'alias'=>'Brand',
						'type' => 'LEFT',
						'conditions'=>array('Brand.id= Product.brand_id')
					)
                ),
			));
			$this->set(compact('exist_data'));
			//pr($exist_data);die();
		}
	}



	public function admin_product_details() 
    {
        $this->loadModel('ProductPrice');
        $this->loadModel('Product');
        $this->autoRender = false;

        $id = $this->request->data['product_id'];
        $options = array('conditions' => array('Product.' . $this->Product->primaryKey => $id),'recursive'=>0);
        $product = $this->Product->find('first', $options);
        echo json_encode(array_merge($product));
    }

    public function get_items(){
    	$item_type = $this->request->data['item_type'];
    	//$rs = array(array('id' => '', 'name' => '---- Select -----'));
    	//$data_array = array();
    	$options = "<option value=''>---- Select -----</option>";
    	if($item_type == 0){
    		$this->loadModel('ProductCategory');
    		$product_categories = $this->ProductCategory->find('all',array());
    		foreach($product_categories as $key => $value)
			{
				$id = $value['ProductCategory']['id'];
				$name = $value['ProductCategory']['name'];
				$options = $options."<option value=".$id.">".$name."</option>";
			}
    	}
    	elseif($item_type == 1){
    		$this->loadModel('Brand');
    		$brands = $this->Brand->find('all',array());
    		foreach($brands as $key => $value)
			{
				$id = $value['Brand']['id'];
				$name = $value['Brand']['name'];
				$options = $options."<option value=".$id.">".$name."</option>";
			}
    	}else{
    		
    		$options = $options."<option value='SMCEL'> SMCEL </option>";
    		$options = $options."<option value='SMC'> SMC </option>";
    	}
    	//echo json_encode(array_merge($rs,$data_array));
    	echo $options;
    	$this->autoRender = false;
    }

    public function get_products(){
    	$group_item = $this->request->data['group_item'];
    	$item_type = $this->request->data['item_type'];
		//pr($this->request->data);die();
    	if($item_type == 0){
    		$conditions = array('Product.product_category_id' => $group_item);
    	}elseif($item_type == 1){
    		$conditions = array('Product.brand_id' => $group_item);
    	}else{
    		$conditions = array('Product.source' => $group_item);
    	}
    	$data_array = array();
    	
		$this->loadModel('Product');
		$products = $this->Product->find('all',array(
			'conditions'=>$conditions,
			'order'=>array('Product.order'),
			'recursive'=> -1
		));
		$options = "<option value=''>---- Select -----</option>";
		foreach($products as $key => $value)
		{
			//$data_array[] = array(
				$id = $value['Product']['id'];
				$name = $value['Product']['name'];
			//);*/
			$options = $options."<option value=".$id.">".$name."</option>";
		}
    	
    	echo $options;
    	//echo json_encode($data_array);
    	$this->autoRender = false;
    }
	public function product_rearrange_update(){
		$this->autoRender = false;
	}

}
