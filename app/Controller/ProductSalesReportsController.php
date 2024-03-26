<?php
App::uses('AppController', 'Controller');
/**
 * ProductSettings Controller
 *
 * @property ProductSalesReport $ProductSalesReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProductSalesReportsController extends AppController
{

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
	public function admin_index($id = null)
	{
		
		

		$this->loadModel('Product');
		$this->loadModel('Memo');

		$this->set('page_title', 'Product Sales Report');

		if ($this->request->is('post') || $this->request->is('put')) {

			//pr($this->request->data);

			$product_list = $this->Product->find('all', array(
				'fields' => array(
					'Product.name',
					'Product.id',
					'Product.product_code',
					'Product.sales_measurement_unit_id',
					'MU.name as mes_name',
					'Product.product_category_id',
					'ProductCategory.name',
					'Brand.name'
				),
				'joins' => array(
					array(
						'table' => 'measurement_units',
						'alias' => 'MU',
						'type' => 'LEFT',
						'conditions' => array('MU.id= Product.sales_measurement_unit_id')
					),
					array(
						'table' => 'product_categories',
						'alias' => 'ProductCategory',
						'type' => 'inner',
						'conditions' => array('ProductCategory.id= Product.product_category_id')
					),
					array(
						'table' => 'brands',
						'alias' => 'Brand',
						'type' => 'inner',
						'conditions' => array('Brand.id= Product.brand_id')
					)
				),
				'conditions' => array('NOT' => array('Product.product_category_id' => 32), 'Product.product_type_id' => 1),
				'order' => 'Product.product_category_id,Brand.id,Product.order',
				'recursive' => -1
			));

			// pr($product_list);
			// exit;


			$requested_data = $this->request->data;

			$date_from = $this->data['ProductSalesReports']['date_from'];
			$date_to = $this->data['ProductSalesReports']['date_to'];

			$sales_data = $this->getProductSales($date_from, $date_to, $product_id = 27);

			$this->set(compact('requested_data', 'product_list', 'date_from', 'date_to'));
		}
		$unit_types = array(
			'1' => 'Sales Unit',
			'2' => 'Base Unit',
		);
		$this->set(compact('unit_types'));
	}



	/**
	 * admin_view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_view($id = null)
	{
	}

	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add($id = null)
	{
	}

	/**
	 * admin_edit method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function admin_edit($id = null)
	{
	}

	/**
	 * admin_delete method
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param string $id
	 * @return void
	 */
	public function admin_delete($id = null)
	{
	}




	public function getProductSales($date_from, $date_to, $product_id = 0)
	{

		$this->loadModel('Memo');

		$sales_data = array();

		if ($product_id) {
			$this->Memo->recursive = -1;
			$sales_people = $this->Memo->find('all', array(
				'fields' => array('DISTINCT(sales_person_id) as sales_person_id', 'SalesPerson.name'),
				'joins' => array(
					array(
						'table' => 'sales_people',
						'alias' => 'SalesPerson',
						'type' => 'INNER',
						'conditions' => array(
							' SalesPerson.id=Memo.sales_person_id',
							//'SalesPerson.office_id'=> $office_id
						)
					)
				),
				'conditions' => array(
					'Memo.memo_date BETWEEN ? and ?' => array(date('Y-m-d', strtotime($date_from)), date('Y-m-d', strtotime($date_to)))
				),
			));


			$sales_person = array();
			foreach ($sales_people as  $data) {
				$sales_person[] = $data['0']['sales_person_id'];
			}
			$sales_person = implode(',', $sales_person);


			if (!empty($sales_person)) {
				$sales_results = $this->Memo->query(" SELECT m.sales_person_id,md.product_id, 
				SUM(md.sales_qty) as pro_quantity,
				sum(md.sales_qty*(md.actual_price-(CASE WHEN dbp.is_discount_exclude_from_value=1 THEN md.discount_amount ELSE 0 END))) as pro_price,
				FROM memos m 
				RIGHT JOIN memo_details md on md.memo_id=m.id
				LEFT JOIN discount_bonus_policies dbp on md.policy_id = dbp.id
				WHERE (m.status!=0 AND m.memo_date BETWEEN  '" . date('Y-m-d', strtotime($date_from)) . "' AND '" . date('Y-m-d', strtotime($date_to)) . "') AND sales_person_id IN (" . $sales_person . ") AND md.price!=0 AND md.product_id=$product_id  GROUP BY m.sales_person_id,md.product_id");

//				SUM(md.price*md.sales_qty) as pro_price

				//pr($sales_results);

				$qty = 0;
				$val = 0;
				foreach ($sales_results as $sales_result) {
					$qty += $sales_result[0]['pro_quantity'];
					$val += $sales_result[0]['pro_price'];
				}

				$sales_data['product_id'] = $product_id;
				$sales_data['qty'] = $qty ? $qty : 0;
				$sales_data['val'] = $val ? $val : 0;

				//exit;

				return  $sales_data;
			}
		}
	}
}
