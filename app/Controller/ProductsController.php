
<?php
App::uses('AppController', 'Controller');
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
/**
 * Products Controller
 *
 * @property Product $Product
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProductsController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Session', 'Filter.Filter');



	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$this->set('page_title', 'Product List');
		$this->Product->recursive = 0;
		//$this->paginate = array('order' => array('Product.id' => 'DESC'));
		$this->paginate = array('order' => array('Product.order' => 'ASC'), 'limit' => 50);
		$this->set('products', $this->paginate());
		$productCategories = $this->Product->ProductCategory->generateTreeList(null, null, null, '__');
		$brands = $this->Product->Brand->find('list', array('order' => array('Brand.name' => 'asc')));
		$variants = $this->Product->Variant->find('list', array('order' => array('Variant.name' => 'asc')));
		$base_measurement_unit = $this->Product->BaseMeasurementUnit->find('list', array('order' => array('BaseMeasurementUnit.name' => 'asc')));
		$productTypes = $this->Product->ProductType->find('list', array('order' => array('name' => 'asc')));
		$groups = $this->Product->Group->find('list', array('order' => array('name' => 'asc')));
		$this->set(compact('productCategories', 'brands', 'variants', 'base_measurement_unit', 'productTypes', 'groups'));
	}


	public function admin_product_rearrange(){
		if ($this->request->is('post') || $this->request->is('put')) {
			// for update data
			$data_array = array();
			foreach ($this->request->data['product_id'] as $key => $val) {
				$data_array[] = array(
					'Product'	=> array(
						'id'		=> $val,
						'order'		=> $this->request->data['order'][$key]
					)
				);
			}
		
			//$this->Product->saveAll($data_array);
			if ($this->Product->saveMany($data_array)) {
				$this->Session->setFlash(__('The product has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Order must be unique.'), 'flash/warning');
			}
		}
		
		$this->set('page_title', 'Product Rearrange');
		/* disable after category wise product sorting*/
		//$this->Product->recursive = 0;
		//
		//$product_list = $this->Product->find('all', array(
		//	'order' => array(
		//	//'Product.product_category_id' => 'asc',
		//	//'Product.product_type_id' => 'asc',
		//	'Product.order' => 'asc')
		//));
		//
		//$this->set('products', $product_list);
	}
	public function product_auto_rearrange(){
		$this->loadModel('ProductCategory');
		$this->loadModel('Product');
		if($this->request->data['userID']==1){
			$product_list = $this->Product->find('all', array(
				'fields' => array('Product.product_category_id','Product.product_type_id','ProductCategory.order'),
				'order' => array('ProductCategory.order' => 'asc', 'Product.product_type_id' => 'asc', 'Product.id' => 'asc'),
				'recursive' => 0
			));
			
			$i=0;
			$lastCat=$product_list[0]['Product']['product_category_id'];
			foreach($product_list as $Products){
				$i++;
				if($lastCat != $Products['Product']['product_category_id']){
					$lastCat = $Products['Product']['product_category_id'];
					$i=1;
				}
				$order_sl = $Products['ProductCategory']['order'].'.'.sprintf("%04s", $i);
				$data_array[] = array(
					'Product'	=> array(
						//'CatId'		=>$Products['Product']['product_category_id'],
						'id'			=> $Products['Product']['id'],
						'order'			=> $order_sl,
						'updated_by'	=> $this->UserAuth->getUserId(),
						'updated_at'	=> $this->current_datetime()
					)
				);
			}
			
			if ($this->Product->saveMany($data_array,array('validate' => false))) {
				$message = 'Successfully all product auto arranged by category wise!';
			} else {
				$message = "Somethings error!";
			}
		}else{
			$message = "Access forbidden!";
		}
		
		echo json_encode(['messege' =>$message]);
		//echo "<pre>";
		//print_r($data_array);
		//echo "</pre>";
		//exit;
		$this->autoRender = false;
	}
	public function product_rearrange_update(){
		$this->loadModel('Product');
		$this->loadModel('ProductCategory');
		$CategoryID = $this->request->data['CategoryID'];
		$item_list = $this->request->data['item'];
		/*Get order index position of $CategoryID*/
		$order_index = $this->ProductCategory->find('all', array(
			'fields' 		=> array('ProductCategory.order'),
			'conditions'	=> array('ProductCategory.id' => $CategoryID),
			'recursive'		=> 0
		));
		$Category_Order = $order_index[0]['ProductCategory']['order'];
		
		$i = 0;
		$data_array = array();
		foreach ($item_list as $value) {
			$i++;
			$order_sl = $Category_Order.'.'.sprintf("%04s", $i);
			$data_array[] = array(
				'Product'	=> array(
					'id'			=> $value,
					'order'			=> $order_sl,
					'updated_by'	=> $this->UserAuth->getUserId(),
					'updated_at'	=> $this->current_datetime()
				)
			);
		}
		if ($this->Product->saveMany($data_array,array('validate' => false))) {
			echo 'The product has been changed position';
		} else {
			echo "Somethings error!";
		}
		$this->autoRender = false;
	}
	public function get_product_list(){
		if ($this->request->is('post')){
			$this->loadModel('Product');
			$product_list = $this->Product->find('all', array(
				'fields' => array(
					'Product.id',
					'Product.name',
					'ProductCategory.name',
					'ProductType.name',
					'BaseMeasurementUnit.name',
					'Brand.name',
				),
				'conditions'	=> array('Product.product_category_id'	=> $this->request->data['CategoryId']),
				'order' => array(
				'Product.order' => 'asc'),
				'recursive' => 0
			));
			//print_r($product_list);
			echo json_encode($product_list);
		}
		$this->autoRender = false;
	}
	private function product_orderIndex($condition){
		$OrderIndex = $this->Product->find('all', array(
			'fields' => array(
				'max([Product]."order")+0.0001 as LastOrder'
			),
			'conditions'	=> $condition,
			'group' => array(
				'Product.product_category_id'
			),
			'recursive' => -1
		));
		if(count($OrderIndex[0]>0)){
			return $OrderIndex[0][0]['LastOrder'];
		}else{
			return false;
		}
	}
	public function get_product_order(){
		if ($this->request->is('post')
			&& isset($this->request->data['CategoryId'])){
			$this->loadModel('Product');
			$this->loadModel('ProductCategory');
			$condition = array(
				'Product.product_category_id'	=> $this->request->data['CategoryId']
			);
			if(isset($this->request->data['ProductTypeId'])){
				$condition['Product.product_type_id'] = $this->request->data['ProductTypeId'];
			}
			$orderIndex = $this->product_orderIndex($condition);
			if($orderIndex>0){
				echo json_encode(['order' => $orderIndex]);
			}else{
				unset($condition['Product.product_type_id']);
				$orderIndex = $this->product_orderIndex($condition);
				if($orderIndex>0){
					echo json_encode(['order' => $orderIndex]);
				}else{
					$order_index = $this->ProductCategory->find('all', array(
						'fields' 		=> array('ProductCategory.order'),
						'conditions'	=> array('ProductCategory.id' => $this->request->data['CategoryId']),
						'recursive'		=> 0
					));
					$Category_Order = $order_index[0]['ProductCategory']['order'];
					echo json_encode(['order' => $Category_Order.".0001"]);
				}
			}
			
		}
		$this->autoRender = false;
	}


	/**
	 * admin_add method
	 *
	 * @return void
	 */
	public function admin_add(){
		$this->set('page_title', 'Add Product');
		$product_last = $this->Product->find('first', array(
			'conditions'	=> array('Product.product_category_id' => $CategoryID),
			'order' => array('order' => 'desc'),
			'recursive' => -1
			)
		);

  
		$order = $product_last['Product']['order'] + 1;
		//for product type
		$sql = "SELECT * FROM product_sources";
		$sources_datas = $this->Product->query($sql);
		$sources = array();
		foreach ($sources_datas as $sources_data) {
			$sources[$sources_data[0]['name']] = $sources_data[0]['name'];
		}
		/*$sources = array(
			'SMCEL' 		=> 'SMCEL',
			'SMC' 			=> 'SMC',
		);*/
		$this->set(compact('sources'));

		$parent_products = $this->Product->find('list', array(
			'conditions' => array(
				'Product.parent_id' => 0
			),
			'recursive' => -1
		));
  
		$giftsampleprodutct = $this->Product->find('list', array(
			'conditions' => array(
				'Product.product_type_id' => 1
			),
			'recursive' => -1
		));

		$this->set(compact('parent_products', 'giftsampleprodutct'));

		
		/* ------------- product group ----------------- */
		$this->LoadModel('ProductGroup');
		$groups = $this->ProductGroup->find('list', array(

			'recursive' => -1
		));
		$this->set(compact('groups'));

		//cyp_cal
		$cyp_cals = array(
			'*' 		=> 'X (Multiple)',
			'/' 		=> '÷ (Divide)',
			'+' 		=> '+ (Plus)',
			'-' 		=> '- (Minus)',
		);
		$this->set(compact('cyp_cals'));

		if ($this->request->is('post')) {
			$this->Product->create();



			$this->request->data['Product']['created_at'] = $this->current_datetime();
			$this->request->data['Product']['updated_at'] = $this->current_datetime();
			$this->request->data['Product']['created_by'] = $this->UserAuth->getUserId();
			$this->request->data['Product']['updated_by'] = 0;

			//-------------gift sample parent product add----------\\
			/*
			$product_type_id = $this->request->data['Product']['product_type_id'];

			if($product_type_id == 1){
				$this->request->data['Product']['gift_sample_product_id'] = 0;
			}else{
				if(empty($this->request->data['Product']['gift_sample_product_id'])){
					$this->request->data['Product']['gift_sample_product_id'] = 0;
				}
			}

			if ($this->request->data['Product']['is_virtual'] == 0) {
				$this->request->data['Product']['parent_id'] = 0;
			}
			*/
			//=====================end=================\\


			if (!empty($this->request->data['Product']['product_image']['name'])) {
				$file = $this->request->data['Product']['product_image'];
				$ext = substr(strtolower(strrchr($file['name'], '.')), 1);
				$image_name = $id . '.' . $ext;;
				$arr_ext = array('jpg', 'jpeg', 'png');

				if (in_array($ext, $arr_ext)) {
					$maxDimW = 512;
					$maxDimH = 512;
					list($width, $height, $type, $attr) = getimagesize($file['tmp_name']);
					if ($width > $maxDimW || $height > $maxDimH) {
						$target_filename = $file['tmp_name'];
						$fn = $file['tmp_name'];
						$size = getimagesize($fn);
						$width = $maxDimW;
						$height = $maxDimH;
						$src = imagecreatefromstring(file_get_contents($fn));
						$dst = imagecreatetruecolor($width, $height);
						imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
						if ($ext == 'jpg' || $ext == 'jpeg')
							imagejpeg($dst, $target_filename); // adjust format as needed
						if ($ext == 'png')
							imagepng($dst, $target_filename); // adjust format as needed
					}
					move_uploaded_file($file['tmp_name'], WWW_ROOT . 'img' . DS . 'product_img' . DS . $image_name);
					//prepare the filename for database entry
					$this->request->data['Product']['product_image'] = $image_name;
				}
			} else {
				unset($this->request->data['Product']['product_image']);
			}
			if ($this->Product->save($this->request->data)) {

				// update product code
				$udata['id'] = $this->Product->id;
				$udata['product_code'] = 'PD' . (1000 + $this->Product->id);
				$this->Product->save($udata);

				// add measurement unit
				$this->loadModel('ProductMeasurement');
				$this->loadModel('ProductFractionSlab');
				if (!empty($this->request->data['ProductMeasurement'])) {
					$data_array = array();
					foreach ($this->request->data['ProductMeasurement']['measurement_unit_id'] as $key => $val) {
						$data['ProductMeasurement']['product_id'] = $this->Product->id;
						$data['ProductMeasurement']['measurement_unit_id'] = $val;
						$data['ProductMeasurement']['qty_in_base'] = $this->request->data['ProductMeasurement']['qty_in_base'][$key];
						$data['ProductMeasurement']['created_at'] = $this->current_datetime();
						$data['ProductMeasurement']['updated_at'] = $this->current_datetime();
						$data['ProductMeasurement']['created_by'] = $this->UserAuth->getUserId();
						$data['ProductMeasurement']['updated_by'] = 0;
						$data_array[] = $data;
					}
					$this->ProductMeasurement->saveAll($data_array);
				}
				/*------------- Add product fraction slab : start-----------------------------*/
				if (isset($this->request->data['ProductFractionSlab']['fraction_sales_unit'])) {
					$data_array = array();
					foreach ($this->request->data['ProductFractionSlab']['fraction_sales_unit'] as $key => $val) {

						$fraction_data = array();

						$fraction_data['ProductFractionSlab']['sales_qty'] = $val;
						$fraction_data['ProductFractionSlab']['base_qty'] = $this->request->data['ProductFractionSlab']['fraction_base_unit'][$key];
						$fraction_data['ProductFractionSlab']['use_for_sales'] = $this->request->data['ProductFractionSlab']['fraction_is_sales'][$key];
						$fraction_data['ProductFractionSlab']['use_for_bonus'] = $this->request->data['ProductFractionSlab']['fraction_is_bonus'][$key];
						if (
							$fraction_data['ProductFractionSlab']['sales_qty']
							&& $fraction_data['ProductFractionSlab']['base_qty']
							&& ($fraction_data['ProductFractionSlab']['use_for_sales']
								|| $fraction_data['ProductFractionSlab']['use_for_bonus']
							)
						) {
							$fraction_data['ProductFractionSlab']['product_id'] = $this->Product->id;
							$fraction_data['ProductFractionSlab']['created_at'] = $this->current_datetime();
							$fraction_data['ProductFractionSlab']['created_by'] = $this->UserAuth->getUserId();
							$fraction_data['ProductFractionSlab']['updated_by'] = $this->UserAuth->getUserId();
							$fraction_data['ProductFractionSlab']['updated_at'] = $this->current_datetime();
							$data_array[] = $fraction_data;
						} else {
							continue;
						}
					}

					$this->ProductFractionSlab->saveAll($data_array);
				}
				/*------------- Add product fraction slab : end-----------------------------*/
				$this->Session->setFlash(__('The product has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		}

		// form data
		$productCategories = $this->Product->ProductCategory->generateTreeList(null, null, null, '__');

		$productTypes = $this->Product->ProductType->find('list', array('order' => array('name' => 'asc')));
		$brands = $this->Product->Brand->find('list', array('order' => array('name' => 'asc')));
		$variants = $this->Product->Variant->find('list', array('order' => array('name' => 'asc')));
		$baseMeasurementUnits = $this->Product->BaseMeasurementUnit->find('list', array('order' => array('name' => 'asc')));
		$salesMeasurementUnits = $this->Product->SalesMeasurementUnit->find('list', array('order' => array('name' => 'asc')));
		$challanMeasurementUnits = $this->Product->ChallanMeasurementUnit->find('list', array('order' => array('name' => 'asc')));
		$returnMeasurementUnits = $this->Product->ReturnMeasurementUnit->find('list', array('order' => array('name' => 'asc')));
		$this->html = '';
		foreach ($baseMeasurementUnits as $key => $val) {
			$this->html .= '<option value="' . $key . '">' . $val . '</option>';
		}
		$units = $this->html;
		$this->set(compact('productCategories', 'subCategories', 'brands', 'variants', 'baseMeasurementUnits', 'salesMeasurementUnits', 'challanMeasurementUnits', 'returnMeasurementUnits', 'units', 'productTypes', 'order'));
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
		ini_set('max_execution_time', 99999);
		ini_set('memory_limit', '-1');
		$this->set('page_title', 'Edit Product');
		$this->loadModel('ProductMeasurement');
		$this->loadModel('ProductFractionSlab');
		$this->Product->id = $id;
		if (!$this->Product->exists($id)) {
			throw new NotFoundException(__('Invalid product'));
		}

		//for product type
		$sql = "SELECT * FROM product_sources";
		$sources_datas = $this->Product->query($sql);
		$sources = array();
		foreach ($sources_datas as $sources_data) {
			$sources[$sources_data[0]['name']] = $sources_data[0]['name'];
		}
		/*$sources = array(
			'SMCEL' 		=> 'SMCEL',
			'SMC' 			=> 'SMC',
		);*/
		$this->set(compact('sources'));
		$parent_products = $this->Product->find('list', array(
			'conditions' => array(
				'Product.parent_id' => 0,
				//'Product.is_virtual' => 1,
			),
			'recursive' => -1
		));
        //$this->dd($parent_products);exit;
		$giftsampleprodutct = $this->Product->find('list', array(
			'conditions' => array(
				'Product.product_type_id' => 1
			),
			'recursive' => -1
		));

		$this->set(compact('parent_products', 'giftsampleprodutct'));

		
		/* -----product group ------- */
		$this->LoadModel('ProductGroup');
		$groups = $this->ProductGroup->find('list', array(

			'recursive' => -1
		));
		$this->set(compact('groups'));
		//cyp_cal
		$cyp_cals = array(
			'*' 		=> 'X (Multiple)',
			'/' 		=> '÷ (Divide)',
			'+' 		=> '+ (Plus)',
			'-' 		=> '- (Minus)',
		);
		$this->set(compact('cyp_cals'));


		if ($this->request->is('post') || $this->request->is('put')) {
			// pr($this->request->data);exit;

			$this->request->data['Product']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['Product']['updated_at'] = $this->current_datetime();

			//---------------------gift sample parent product add---------------\\
			/*
			$product_type_id = $this->request->data['Product']['product_type_id'];

			if($product_type_id == 1){
				$this->request->data['Product']['gift_sample_product_id'] = 0;
			}else{
				if(empty($this->request->data['Product']['gift_sample_product_id'])){
					$this->request->data['Product']['gift_sample_product_id'] = 0;
				}
			}
			*/
			//===================end=================\\

			if ($this->request->data['Product']['is_virtual'] == 0) {
				$this->request->data['Product']['parent_id'] = 0;
			}


			if (!empty($this->request->data['Product']['product_image']['name'])) {
				$file = $this->request->data['Product']['product_image'];
				$ext = substr(strtolower(strrchr($file['name'], '.')), 1);
				$ext;
				$image_name = $id . '.' . $ext;;
				$arr_ext = array('jpg', 'jpeg', 'png');

				if (in_array($ext, $arr_ext)) {
					$maxDimW = 512;
					$maxDimH = 512;
					list($width, $height, $type, $attr) = getimagesize($file['tmp_name']);
					if ($width > $maxDimW || $height > $maxDimH) {
						$target_filename = $file['tmp_name'];
						$fn = $file['tmp_name'];
						$size = getimagesize($fn);
						$width = $maxDimW;
						$height = $maxDimH;
						$src = imagecreatefromstring(file_get_contents($fn));
						$dst = imagecreatetruecolor($width, $height);
						imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
						if ($ext == 'jpg' || $ext == 'jpeg')
							imagejpeg($dst, $target_filename); // adjust format as needed
						if ($ext == 'png')
							imagepng($dst, $target_filename); // adjust format as needed
					}
					if ($this->request->data['Product']['prev_product_image']) {
						unlink(WWW_ROOT . DS . 'img' . DS . 'product_img' . DS . $this->request->data['Product']['prev_product_image']);
					}
					move_uploaded_file($file['tmp_name'], WWW_ROOT . 'img' . DS . 'product_img' . DS . $image_name);
					//prepare the filename for database entry
					// $img_url='img/product_img/'.$image_name;
					$this->request->data['Product']['product_image'] = $image_name;
					
				}
			} else {
				unset($this->request->data['Product']['product_image']);
			}

			if ($this->Product->save($this->request->data)) {

				// for update data
				if (isset($this->request->data['ProductMeasurement']['product_measurement_unit_id'])) {
					$data_array = array();
					foreach ($this->request->data['ProductMeasurement']['product_measurement_unit_id'] as $ukey => $uval) {
						$update_data['ProductMeasurement']['id'] = $this->request->data['ProductMeasurement']['product_measurement_unit_id'][$ukey];
						$update_data['ProductMeasurement']['product_id'] = $id;
						$update_data['ProductMeasurement']['measurement_unit_id'] = $this->request->data['ProductMeasurement']['update_measurement_unit_id'][$ukey];
						$update_data['ProductMeasurement']['qty_in_base'] = $this->request->data['ProductMeasurement']['update_qty_in_base'][$ukey];
						$update_data['ProductMeasurement']['updated_by'] = $this->UserAuth->getUserId();
						$update_data['ProductMeasurement']['updated_at'] = $this->current_datetime();
						$data_array[] = $update_data;
					}

					/*pr($data_array);
					exit;*/


					$this->ProductMeasurement->saveAll($data_array);
				}

				// for add more unit
				if (isset($this->request->data['ProductMeasurement']['measurement_unit_id'])) {
					$insert_data_array = array();
					foreach ($this->request->data['ProductMeasurement']['measurement_unit_id'] as $key => $val) {
						$insert_data['ProductMeasurement']['product_id'] = $id;
						$insert_data['ProductMeasurement']['measurement_unit_id'] = $val;
						$insert_data['ProductMeasurement']['qty_in_base'] = $this->request->data['ProductMeasurement']['qty_in_base'][$key];
						$insert_data['ProductMeasurement']['created_at'] = $this->current_datetime();
						$insert_data['ProductMeasurement']['created_by'] = $this->UserAuth->getUserId();
						$insert_data['ProductMeasurement']['updated_by'] = $this->UserAuth->getUserId();
						$insert_data['ProductMeasurement']['updated_at'] = $this->current_datetime();
						$insert_data_array[] = $insert_data;
					}
					$this->ProductMeasurement->saveAll($insert_data_array);
				}
				$product_prev_fraction_slab = $this->ProductFractionSlab->find('list', array(
					'conditions' => array('ProductFractionSlab.product_id' => $id)
				));
				if (isset($this->request->data['ProductFractionSlab']['fraction_sales_unit'])) {
					$data_array = array();
					foreach ($this->request->data['ProductFractionSlab']['fraction_sales_unit'] as $key => $val) {

						$fraction_data = array();

						$fraction_data['ProductFractionSlab']['sales_qty'] = $val;
						$fraction_data['ProductFractionSlab']['base_qty'] = $this->request->data['ProductFractionSlab']['fraction_base_unit'][$key];
						$fraction_data['ProductFractionSlab']['use_for_sales'] = $this->request->data['ProductFractionSlab']['fraction_is_sales'][$key];
						$fraction_data['ProductFractionSlab']['use_for_bonus'] = $this->request->data['ProductFractionSlab']['fraction_is_bonus'][$key];
						if (
							$fraction_data['ProductFractionSlab']['sales_qty']
							&& $fraction_data['ProductFractionSlab']['base_qty']
							&& ($fraction_data['ProductFractionSlab']['use_for_sales']
								|| $fraction_data['ProductFractionSlab']['use_for_bonus']
							)
						) {
							if (isset($this->request->data['ProductFractionSlab']['id'][$key])) {
								$fraction_data['ProductFractionSlab']['id'] = $this->request->data['ProductFractionSlab']['id'][$key];
								unset($product_prev_fraction_slab[$fraction_data['ProductFractionSlab']['id']]);
							}
							$fraction_data['ProductFractionSlab']['product_id'] = $id;
							$fraction_data['ProductFractionSlab']['created_at'] = $this->current_datetime();
							$fraction_data['ProductFractionSlab']['created_by'] = $this->UserAuth->getUserId();
							$fraction_data['ProductFractionSlab']['updated_by'] = $this->UserAuth->getUserId();
							$fraction_data['ProductFractionSlab']['updated_at'] = $this->current_datetime();
							$data_array[] = $fraction_data;
						} else {
							continue;
						}
					}
					// pr($product_prev_fraction_slab);exit;
					if ($product_prev_fraction_slab) {
						$this->ProductFractionSlab->DeleteAll(array('ProductFractionSlab.id' => $product_prev_fraction_slab), false);
					}
					$this->ProductFractionSlab->saveAll($data_array);
				}

				$this->Session->setFlash(__('The product has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		} else {
			$this->Product->unbindModel(array('hasMany' => array('SaleTarget', 'MemoDetail', 'PriceOpenProduct')));
			$options = array('conditions' => array('Product.' . $this->Product->primaryKey => $id));
			$this->request->data = $this->Product->find('first', $options);
		}

		// pr($this->request->data);exit;

		// form data
		$productTypes = $this->Product->ProductType->find('list', array('order' => array('name' => 'asc')));
		$productCategories = $this->Product->ProductCategory->generateTreeList(null, null, null, '__');

		$brands = $this->Product->Brand->find('list', array('order' => array('name' => 'asc')));
		$variants = $this->Product->Variant->find('list', array('order' => array('name' => 'asc')));
		$baseMeasurementUnits = $this->Product->BaseMeasurementUnit->find('list', array('order' => array('name' => 'asc')));
		$salesMeasurementUnits = $this->Product->SalesMeasurementUnit->find('list', array('order' => array('name' => 'asc')));
		$challanMeasurementUnits = $this->Product->ChallanMeasurementUnit->find('list', array('order' => array('name' => 'asc')));
		$returnMeasurementUnits = $this->Product->ReturnMeasurementUnit->find('list', array('order' => array('name' => 'asc')));
		$this->html = '';
		foreach ($baseMeasurementUnits as $key => $val) {
			$this->html .= '<option value="' . $key . '">' . $val . '</option>';
		}
		$units = $this->html;
		$this->set(compact('productCategories', 'subCategories', 'brands', 'variants', 'baseMeasurementUnits', 'salesMeasurementUnits', 'challanMeasurementUnits', 'returnMeasurementUnits', 'units', 'productTypes'));
	}
    
    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    
    
    public function admin_view($id = null){
        $this->set('page_title', 'Product Details');
        if (!$this->Product->exists($id)) {
            throw new NotFoundException(__('Invalid product'));
        }
        $parent_products = $this->Product->find('list', array(
            'conditions' => array(
                'Product.parent_id' => 0,
                //'Product.is_virtual' => 1,
            ),
            'recursive' => -1
        ));


        $this->set(compact('parent_products'));
        $options = array(
            'conditions' => array('Product.' . $this->Product->primaryKey => $id),
            'joins' => array(
                array(
                    'alias' => 'ProductCategory',
                    'table' => 'product_categories',
                    'type' => 'INNER',
                    'conditions' => 'ProductCategory.id = Product.product_category_id'
                ),
                array(
                    'alias' => 'ProductType',
                    'table' => 'product_type',
                    'type' => 'INNER',
                    'conditions' => 'ProductType.id = Product.product_type_id'
                ),
                array(
                    'alias' => 'Brand',
                    'table' => 'brands',
                    'type' => 'INNER',
                    'conditions' => 'Brand.id = Product.brand_id'
                ),
                array(
                    'alias' => 'Variant',
                    'table' => 'variants',
                    'type' => 'INNER',
                    'conditions' => 'Variant.id = Product.variant_id'
                ),
                array(
                    'alias' => 'Group',
                    'table' => 'product_groups',
                    'type' => 'INNER',
                    'conditions' => 'Group.id = Product.group_id'
                ),
                array(
                    'alias' => 'BaseMeasurementUnit',
                    'table' => 'measurement_units',
                    'type' => 'INNER',
                    'conditions' => 'BaseMeasurementUnit.id = Product.base_measurement_unit_id'
                ),
                array(
                    'alias' => 'SalesMeasurementUnit',
                    'table' => 'measurement_units',
                    'type' => 'INNER',
                    'conditions' => 'SalesMeasurementUnit.id = Product.sales_measurement_unit_id'
                ),
                array(
                    'alias' => 'ChallanMeasurementUnit',
                    'table' => 'measurement_units',
                    'type' => 'INNER',
                    'conditions' => 'ChallanMeasurementUnit.id = Product.challan_measurement_unit_id'
                ),
                array(
                    'alias' => 'ReturnMeasurementUnit',
                    'table' => 'measurement_units',
                    'type' => 'INNER',
                    'conditions' => 'ReturnMeasurementUnit.id = Product.return_measurement_unit_id'
                )
            ),
            'fields'=>array(
                'Product.*',
                'ProductCategory.name',
                'ProductType.name',
                'Brand.name',
                'Variant.name',
                'Group.name',
                'BaseMeasurementUnit.name',
                'SalesMeasurementUnit.name',
                'ChallanMeasurementUnit.name',
                'ReturnMeasurementUnit.name',
            ),
            'recursive'=>-1
        );
        
        
       //$result = $this->Product->find('first', $options);
       //$this->dd($result); exit;
        
        $this->set('product', $this->Product->find('first', $options));
        $this->loadModel('ProductMeasurement');
        $product_measurement_unit = $this->ProductMeasurement->find(
            'all',
            array(
                'conditions' => array('ProductMeasurement.product_id' => $id),
                'fields' => 'ProductMeasurement.id,ProductMeasurement.qty_in_base,MeasurementUnit.name',
                'recursive' => 0
            )
        );
        $this->set(compact('product_measurement_unit'));

        if ($this->request->is('post') || $this->request->is('put')) {
             pr($this->request->data);exit;
        }
    }
    
    public function admin_view2($id = null){
        $this->set('page_title', 'Product Details');
        if (!$this->Product->exists($id)) {
            throw new NotFoundException(__('Invalid product'));
        }
        
        $options = array('conditions' => array('Product.' . $this->Product->primaryKey => $id));
        //$this->dd($options);exit;
        $this->set('product', $this->Product->find('first', $options));
        $this->loadModel('ProductMeasurement');
        $product_measurement_unit = $this->ProductMeasurement->find(
            'all',
            array(
                'conditions' => array('ProductMeasurement.product_id' => $id),
                'fields' => 'ProductMeasurement.id,ProductMeasurement.qty_in_base,MeasurementUnit.name',
                'recursive' => 0
            )
        );
        $this->set(compact('product_measurement_unit'));
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
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Product->id = $id;
		if (!$this->Product->exists()) {
			throw new NotFoundException(__('Invalid product'));
		}
		if ($this->Product->delete()) {
			$this->Session->setFlash(__('Product deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Product was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}


	public function admin_price_open_list($id = null)
	{
		$this->Product->id = $id;
		if (!$this->Product->exists()) {
			throw new NotFoundException(__('Invalid product'));
		}

		$this->loadModel('PriceOpenProduct');

		$this->set('product_id', $id);

		$this->set('page_title', 'Price Open List');

		//$this->PriceOpenProduct->paginate();

		//$this->set('products', $this->PriceOpenProduct->paginate());

		//pr($this->PriceOpenProduct->paginate());
		//exit;

		$this->Paginator->settings = array(
			'conditions' => array('PriceOpenProduct.product_id' => $id),
			'limit' => 20,
			'order' => array('PriceOpenProduct.id' => 'desc'),
		);
		$data = $this->Paginator->paginate('PriceOpenProduct');

		$this->set('products', $data);


		/*$daa = @$this->paginate['PriceOpenProduct'] = array(
		'conditions' => array('PriceOpenProduct.product_id' => $id), 
			'limit' => 2, 
			'order' => 'id' 
		);
		pr($this->paginate('PriceOpenProduct'));
		$this->set( 'products', $this->paginate('PriceOpenProduct') );*/



		$options = array('conditions' => array('Product.' . $this->Product->primaryKey => $id));
		$product_info = $this->Product->find('first', $options);
		$this->set(compact('product_info'));

		//$this->redirect(array('action' => 'index'));
	}


	public function admin_price_open_add($id = null)
	{
		$this->Product->id = $id;
		if (!$this->Product->exists()) {
			throw new NotFoundException(__('Invalid product'));
		}

		$this->set('product_id', $id);

		$this->set('page_title', 'Price Open Add');

		$this->loadModel('PriceOpenProduct');

		if ($this->request->is('post')) {
			$this->PriceOpenProduct->create();

			$user_id = $this->UserAuth->getUserId();

			$this->request->data['PriceOpenProduct']['created_at'] = $this->current_datetime();
			$this->request->data['PriceOpenProduct']['updated_at'] = $this->current_datetime();
			$this->request->data['PriceOpenProduct']['created_by'] = $user_id;
			$this->request->data['PriceOpenProduct']['updated_by'] = $user_id;

			/*pr($this->request->data);
			exit;*/

			if ($this->PriceOpenProduct->save($this->request->data))  //for single save
			{
				$this->Session->setFlash(__('The open price has been saved!'), 'flash/success');
				$this->redirect(array('action' => 'price_open_list', $id));
			}
		}



		//$this->redirect(array('action' => 'index'));
	}

	public function admin_price_open_edit($id = null)
	{
		$this->loadModel('PriceOpenProduct');

		//$this->set('product_id', $id);

		$this->set('page_title', 'Price Open Edit');

		/*if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}*/

		$this->PriceOpenProduct->id = $id;

		if (!$this->PriceOpenProduct->exists()) {
			throw new NotFoundException(__('Invalid product price!'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			$this->request->data['PriceOpenProduct']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['PriceOpenProduct']['updated_at'] = $this->current_datetime();

			if ($this->PriceOpenProduct->save($this->request->data)) {
				$this->Session->setFlash(__('The open price has been updated!'), 'flash/success');
				$this->redirect(array('action' => 'price_open_list', $this->request->data['PriceOpenProduct']['product_id']));
			}
		} else {
			$options = array('conditions' => array('PriceOpenProduct.' . $this->PriceOpenProduct->primaryKey => $id));
			$this->request->data = $this->PriceOpenProduct->find('first', $options);
		}

		//$this->redirect(array('action' => 'index'));
	}

	public function admin_price_open_delete($id = null, $product_id = null)
	{
		$this->loadModel('PriceOpenProduct');

		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->PriceOpenProduct->id = $id;
		if (!$this->PriceOpenProduct->exists()) {
			throw new NotFoundException(__('Invalid product price!'));
		}
		if ($this->PriceOpenProduct->delete()) {
			$this->Session->setFlash(__('Product open price deleted!'), 'flash/success');
			$this->redirect(array('action' => 'price_open_list', $product_id));
		}
		$this->Session->setFlash(__('Product open price was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'price_open_list', $product_id));
	}

	/**
	 * admin_delete method
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param string $id
	 * @return void
	 */
	public function admin_delete_measurement_unit($id = null, $product_id = null)
	{
		$this->loadModel('ProductMeasurement');
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ProductMeasurement->id = $id;
		if (!$this->ProductMeasurement->exists()) {
			throw new NotFoundException(__('Invalid ProductMeasurement'));
		}
		if ($this->ProductMeasurement->delete()) {
			$this->Session->setFlash(__('Product Measurement unit deleted'), 'flash/success');
			$this->redirect(array('action' => 'view/' . $product_id));
		}
		$this->Session->setFlash(__('Product Measurement was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'view/' . $product_id));
	}

	/**
	 * product_details method
	 *
	 * @throws NotFoundException
	 * @throws MethodNotAllowedException
	 * @param string $id
	 * @return void
	 */
	public function admin_product_details()
	{
		$this->loadModel('ProductPrice');
		$this->autoRender = false;
		$id = $this->request->data['product_id'];
		$options = array('conditions' => array('Product.' . $this->Product->primaryKey => $id), 'recursive' => 0);
		$product = $this->Product->find('first', $options);
		$product_price = $this->ProductPrice->find(
			'first',
			array(
				'fields' => 'general_price',
				'conditions' => array('product_id' => $id, 'target_custommer' => 0),
				'recursive' => -1
			)
		);
		if (empty($product_price)) {
			$product_price['ProductPrice']['general_price'] = '';
		}
        //$this->dd(array_merge($product, $product_price));
		echo json_encode(array_merge($product, $product_price));
	}
}
