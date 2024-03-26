<?php
App::uses('AppController', 'Controller');
/**
 * ProductSettings Controller
 *
 * @property ProductSalesReport $ProductSalesReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class PrimaryProductSalesReportsController extends AppController {
	
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
		// Configure::write('debug', 2);
		$this->loadModel('Product');
		$this->loadModel('Memo');
		
		$this->set('page_title', 'Product Sales Report');
				
		if ($this->request->is('post') || $this->request->is('put')) {
			$product_results = $this->Product->find('all', array(
				 'conditions'=>array('NOT'=>array('Product.product_category_id'=>32),'Product.product_type_id'=>1),

				'fields'=> array('Product.id', 'Product.name', 'Product.product_type_id', 'Product.source'),
				'recursive' => -1,
	            'order'=>  array('Product.product_type_id'=>'asc', 'Product.source'=>'desc', 'Product.order'=>'asc', )
	        ));
			
			

	        foreach($product_results as $product_result){
				$product_list[$product_result['Product']['source']][$product_result['Product']['id']] = array(
					'product_id'			=> $product_result['Product']['id'],
					'product_name'			=> $product_result['Product']['name'],
					'product_type_id'		=> $product_result['Product']['product_type_id'],
					'Product'				=> $product_result['Product']['source'],
				);
			}


			$product_results_primary = $this->Product->find('all', array(
				'conditions'=>array('NOT'=>array('Product.product_category_id'=>32),'Product.product_type_id'=>1,'Product.id'=>array('47', '48','339')),
				'fields'=> array('Product.id', 'Product.name', 'Product.product_type_id','Product.source'),
				'recursive' => -1,
	            'order'=>  array('Product.product_type_id'=>'asc', 'Product.order'=>'asc', )
	        ));
			
			//echo '<pre>';print_r($product_results_primary);exit;
	        foreach($product_results_primary as $product_result){
				$product_list_primary['SMCEL'][$product_result['Product']['id']] = array(
					'product_id'			=> $product_result['Product']['id'],
					'product_name'			=> $product_result['Product']['name'],
					'product_type_id'		=> $product_result['Product']['product_type_id'],
					'Product'				=> $product_result['Product']['source'],
				);
			}
			
			$requested_data = $this->request->data;
			
			$date_from = $this->data['PrimaryProductSalesReports']['date_from'];
			$date_to = $this->data['PrimaryProductSalesReports']['date_to'];
			
			$secondary_data=$this->Memo->query(
					"SELECT
						md.product_id,
						SUM(md.sales_qty) as pro_quantity,
						SUM(md.price*md.sales_qty) as pro_price
                    FROM memos m 
                    INNER JOIN memo_details md on md.memo_id=m.id
                    WHERE 
                    	m.status!=0 
                    	AND m.memo_date BETWEEN  '".date('Y-m-d', strtotime($date_from))."' 
                    	AND '". date('Y-m-d', strtotime($date_to))."'
                    	AND md.price!=0
                    GROUP BY md.product_id"
                );
			$secondary_sales_data=array();
			foreach($secondary_data as $sdata)
			{
				$secondary_sales_data[$sdata[0]['product_id']]=array(
					'qty'=>$sdata[0]['pro_quantity'],
					'val'=>$sdata[0]['pro_price']
					);
			}

				
				
			$primary_data=$this->Memo->query(
					"SELECT
						md.product_id,
						SUM(md.challan_qty) as pro_quantity,
						SUM(Cast(md.product_price As Varchar)*md.challan_qty) as pro_price
                    FROM primary_memos m 
                    INNER JOIN primary_memo_details md on md.primary_memo_id=m.id
                    WHERE 
                    	m.status!=0 
                    	AND m.challan_date BETWEEN  '".date('Y-m-d', strtotime($date_from))."' 
                    	AND '". date('Y-m-d', strtotime($date_to))."'
						AND Cast(md.product_price As Varchar)  !='0'
                    GROUP BY md.product_id"
                );
			$primary_sales_data=array();
			//AND md.product_price!=0///SUM(md.product_price*md.challan_qty) as pro_price
			
			foreach($primary_data as $sdata)
			{
				$primary_sales_data[$sdata[0]['product_id']]=array(
					'qty'=>$sdata[0]['pro_quantity'],
					'val'=>$sdata[0]['pro_price']
					);
			}
			
			//echo '<pre>';print_r($primary_sales_data);exit;

			$this->set(compact('requested_data', 'product_list', 'date_from', 'date_to','secondary_sales_data','primary_sales_data','product_list_primary'));
			
			
		}
		
	}



/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add($id = null) {}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {}
	
	
	
	
	public function getProductSales($date_from, $date_to, $product_id=0){
		
		$this->loadModel('Memo');
		
		$sales_data = array();
		
		if ($product_id) 
		{
                $this->Memo->recursive=-1;
                $sales_people = $this->Memo->find('all',array(
                    'fields'=>array('DISTINCT(sales_person_id) as sales_person_id','SalesPerson.name'),
                    'joins'=>array(
                        array('table' => 'sales_people',
                            'alias' => 'SalesPerson',
                            'type' => 'INNER',
                            'conditions' => array(
                                ' SalesPerson.id=Memo.sales_person_id',
                                //'SalesPerson.office_id'=> $office_id
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
       			

                if (!empty($sales_person)) {
                    $sales_results = $this->Memo->query(" SELECT m.sales_person_id,md.product_id, SUM(md.sales_qty) as pro_quantity, SUM(md.price*md.sales_qty) as pro_price
                       FROM memos m RIGHT JOIN memo_details md on md.memo_id=m.id
                       WHERE (m.status!=0 AND m.memo_date BETWEEN  '".date('Y-m-d', strtotime($date_from))."' AND '". date('Y-m-d', strtotime($date_to))."') AND sales_person_id IN (".$sales_person.") AND md.price!=0 AND md.product_id=$product_id  GROUP BY m.sales_person_id,md.product_id");
                    
					
					//pr($sales_results);
					
					$qty = 0;
					$val = 0;
					foreach($sales_results as $sales_result){
						$qty +=$sales_result[0]['pro_quantity'];
						$val +=$sales_result[0]['pro_price'];
					}
					
					$sales_data['product_id'] = $product_id;
					$sales_data['qty'] = $qty?$qty:0;
					$sales_data['val'] = $val?$val:0;
                
					//exit;
					
					return  $sales_data;
					
                }
            }
	}
	

	
}
