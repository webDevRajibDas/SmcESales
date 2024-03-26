<?php
App::uses('AppController', 'Controller');
/**
 * Brands Controller
 *
 * @property Brand $Brand
 * @property PaginatorComponent $Paginator
 */
class BonusCampaignsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $uses = array( 'BonusCampaign', 'BonusCampaignProductList');
	public $components = array('Paginator', 'Session', 'Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Bonus Campaign List');
		$this->BonusCampaign->recursive = 0;
		/* $this->paginate = array(			
			'order' => array('BonusCampaign.id' => 'DESC')
		); */
		$con=array();
		$this->paginate = array(
        	'fields'=>array('BonusCampaign.id', 'BonusCampaign.start_date', 'BonusCampaign.end_date', 'BonusCampaignProductList.bonus_campaign_id'),
			//'conditions'=>$con,
			'joins'=>array(
            	array(
            		'table'=>'bonus_campaign_product_lists',
            		'alias'=>'BonusCampaignProductList',
            		'conditions'=>'BonusCampaignProductList.bonus_campaign_id=BonusCampaign.id',
            		'type'=>'inner'
            	   ),
            	),
            'group'=>array('BonusCampaign.id', 'BonusCampaign.start_date', 'BonusCampaign.end_date', 'BonusCampaignProductList.bonus_campaign_id'),
            'order' => array('BonusCampaign.id' => 'desc'),
           // 'limit' => 100
            );
			

		$this->set('bonuscampaign', $this->paginate());

		$this->loadModel('Product');
		$products = $this->Product->find('list', array(
			'order'=>array('Product.order' => 'ASC'),
			'conditions'=>array('Product.product_type_id'=>1),
		));
		$this->set(compact('products'));

	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Bonus Campaign Details');
		if (!$this->BonusCampaign->exists($id)) {
			throw new NotFoundException(__('Invalid brand'));
		}
		$options = array('conditions' => array('BonusCampaign.' . $this->BonusCampaign->primaryKey => $id));
		$this->set('bonuscampaign', $this->BonusCampaign->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Bonus Campaign');
		if ($this->request->is('post')) {
			

			$sosrcheck = count($this->request->data['BonusCampaign']['create_for']);
			if($sosrcheck == 0){
				$this->Session->setFlash(__('The Create For So Or Sr can be empty. Please, try again.'), 'flash/error');
				$this->redirect(array('action' => 'add'));
			}
			
			if (!empty($this->request->data['BonusCampaign']['attachment']['name'])) {
				$file = $this->request->data['BonusCampaign']['attachment'];
				$ext = substr(strtolower(strrchr($file['name'], '.')), 1);
				$image_name=rand(99999,10000000).'.'.$ext;
				$arr_ext = array('jpg', 'jpeg','png', 'pdf');

				if (in_array($ext, $arr_ext)) 
				{
					$maxDimW = 1500;
					$maxDimH = 2500;
					list($width, $height, $type, $attr) = getimagesize(  $file['tmp_name'] );
					if ( $width > $maxDimW || $height > $maxDimH ) {
					    $target_filename = $file['tmp_name'];
					    $fn =$file['tmp_name'];
					    $size = getimagesize( $fn );
				    	$width = $maxDimW;
				        $height = $maxDimH;
					    $src = imagecreatefromstring(file_get_contents($fn));
					    $dst = imagecreatetruecolor( $width, $height );
					    imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1] );
					    if($ext== 'jpg' || $ext=='jpeg')
					    	imagejpeg($dst, $target_filename); // adjust format as needed
					    if($ext== 'png')
					    	imagepng($dst, $target_filename); // adjust format as needed
					}
					move_uploaded_file($file['tmp_name'], WWW_ROOT . 'img'.DS.'bonus_attachment'.DS. $image_name);
                	//prepare the filename for database entry
					$this->request->data['BonusCampaign']['attachment'] = $image_name;
				}else{
					$this->Session->setFlash(__('File Format is wrong. Please, try again.'), 'flash/error');
					$this->redirect(array('action' => 'add'));
				}

			}
			else{
				unset($this->request->data['BonusCampaign']['attachment']);
			}

			$so = 0;
			$sr = 0;

			foreach($this->request->data['BonusCampaign']['create_for'] as $sosr){
				if($sosr == 1){
					$so = 1;
				}
				if($sosr == 2){
					$sr = 2;
				}
			}
	
			//echo '<pre>';print_r($this->request->data['BonusCampaign']['product_id']);
			
			$this->request->data['BonusCampaign']['start_date'] = date('Y-m-d', strtotime($this->request->data['BonusCampaign']['date_from']));
			$this->request->data['BonusCampaign']['end_date'] = date('Y-m-d', strtotime($this->request->data['BonusCampaign']['date_to']));
			$this->request->data['BonusCampaign']['is_so'] = $so;
			$this->request->data['BonusCampaign']['is_sr'] = $sr;
			$this->request->data['BonusCampaign']['created_at'] = $this->current_datetime();
			$this->request->data['BonusCampaign']['updated_at'] = $this->current_datetime();
			$this->request->data['BonusCampaign']['created_by'] = $this->UserAuth->getUserId();

			$this->BonusCampaign->create();

			if ($this->BonusCampaign->save($this->request->data)) {

				$inserID = $this->BonusCampaign->getLastInsertId();

				if(!empty($this->request->data['BonusCampaign']['product_id'])){
					$rs = array();
					$this->loadModel('BonusCampaignProductList');
					foreach($this->request->data['BonusCampaign']['product_id'] as $v){
	
						$productArray['BonusCampaignProductList']['bonus_campaign_id'] = $inserID;
						$productArray['BonusCampaignProductList']['product_id'] = $v;
						$productArray['BonusCampaignProductList']['created_at'] = $this->current_datetime();
						$productArray['BonusCampaignProductList']['updated_at'] = $this->current_datetime();
						$productArray['BonusCampaignProductList']['created_by'] = $this->UserAuth->getUserId();
						$rs[]= $productArray;
						
					}
					
					//echo '<pre>';print_r($rs);exit;
					
					if(!empty($rs)){
						$this->BonusCampaignProductList->create();
						$this->BonusCampaignProductList->saveAll($rs);
					}
				}

				$this->Session->setFlash(__('The Bonus Campaign has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The BonusCampaign could not be saved. Please, try again.'), 'flash/error');
			}
		}

		$this->loadModel('Product');
		$products = $this->Product->find('list', array(
			'order'=>array('Product.order' => 'ASC'),
			'conditions'=>array('Product.product_type_id'=>1),
		));
		$this->html = '';
		foreach($products as $key=>$val)
		{
			$this->html .= '<option value="'.$key.'">'.addslashes($val).'</option>';		
		}
		$product_list = $this->html;
		$this->set(compact('products', 'product_list'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->set('page_title','Edit Bonus Campaign');
		$this->loadModel('BonusCampaignProductList');
        $this->BonusCampaign->id = $id;
		if (!$this->BonusCampaign->exists($id)) {
			throw new NotFoundException(__('Invalid Bonus Campaign'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {

			$sosrcheck = count($this->request->data['BonusCampaign']['create_for']);
			if($sosrcheck == 0){
				$this->Session->setFlash(__('The Create For So Or Sr can be empty. Please, try again.'), 'flash/error');
				$this->redirect(array('action' => 'edit/'. $id));
			}



			if (!empty($this->request->data['BonusCampaign']['attachment']['name'])) {
				$file = $this->request->data['BonusCampaign']['attachment'];
				$ext = substr(strtolower(strrchr($file['name'], '.')), 1);
				$image_name=rand(99999,10000000).'.'.$ext;
				$arr_ext = array('jpg', 'jpeg','png', 'pdf');

				if (in_array($ext, $arr_ext)) 
				{
					$maxDimW = 1500;
					$maxDimH = 2500;
					list($width, $height, $type, $attr) = getimagesize(  $file['tmp_name'] );
					if ( $width > $maxDimW || $height > $maxDimH ) {
					    $target_filename = $file['tmp_name'];
					    $fn =$file['tmp_name'];
					    $size = getimagesize( $fn );
				    	$width = $maxDimW;
				        $height = $maxDimH;
					    $src = imagecreatefromstring(file_get_contents($fn));
					    $dst = imagecreatetruecolor( $width, $height );
					    imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1] );
					    if($ext== 'jpg' || $ext=='jpeg')
					    	imagejpeg($dst, $target_filename); // adjust format as needed
					    if($ext== 'png')
					    	imagepng($dst, $target_filename); // adjust format as needed
					}
					move_uploaded_file($file['tmp_name'], WWW_ROOT . 'img'.DS.'bonus_attachment'.DS. $image_name);
                	//prepare the filename for database entry
					$this->request->data['BonusCampaign']['attachment'] = $image_name;
				}else{
					$this->Session->setFlash(__('File Format is wrong. Please, try again.'), 'flash/error');
					$this->redirect(array('action' => 'edit/'. $id));
				}

			}
			else{
				unset($this->request->data['BonusCampaign']['attachment']);
			}


			$so = 0;
			$sr = 0;

			foreach($this->request->data['BonusCampaign']['create_for'] as $sosr){
				if($sosr == 1){
					$so = 1;
				}
				if($sosr == 2){
					$sr = 2;
				}
			}

			$this->request->data['BonusCampaign']['is_so'] = $so;
			$this->request->data['BonusCampaign']['is_sr'] = $sr;

			$this->request->data['BonusCampaign']['start_date'] = date('Y-m-d', strtotime($this->request->data['BonusCampaign']['date_from']));
			$this->request->data['BonusCampaign']['end_date'] = date('Y-m-d', strtotime($this->request->data['BonusCampaign']['date_to']));
			$this->request->data['BonusCampaign']['updated_by'] = $this->UserAuth->getUserId();
			$this->request->data['BonusCampaign']['updated_at'] = $this->current_datetime();

			if ($this->BonusCampaign->save($this->request->data)) {

				$id = $this->request->data['BonusCampaign']['id'];

				$this->BonusCampaignProductList->deleteAll(array('BonusCampaignProductList.bonus_campaign_id'=>$id));

				if(!empty($this->request->data['BonusCampaign']['product_id'])){
					$rs = array();
					
					foreach($this->request->data['BonusCampaign']['product_id'] as $v){
	
						$productArray['BonusCampaignProductList']['bonus_campaign_id'] = $id;
						$productArray['BonusCampaignProductList']['product_id'] = $v;
						$productArray['BonusCampaignProductList']['created_at'] = $this->current_datetime();
						$productArray['BonusCampaignProductList']['updated_at'] = $this->current_datetime();
						$productArray['BonusCampaignProductList']['created_by'] = $this->UserAuth->getUserId();
						$rs[]= $productArray;
						
					}
					
					if(!empty($rs)){
						$this->BonusCampaignProductList->create();
						$this->BonusCampaignProductList->saveAll($rs);
					}
				}

				$this->Session->setFlash(__('The Bonus Campaign has been updated'), 'flash/success');
				$this->redirect(array('action' => 'index'));

			} else {

			}
		} else {

			$options = array('conditions' => array('BonusCampaign.' . $this->BonusCampaign->primaryKey => $id));
			$this->request->data = $this->BonusCampaign->find('first', $options);

			$this->request->data['BonusCampaign']['date_from'] = date('d-m-Y', strtotime($this->request->data['BonusCampaign']['start_date']));
			$this->request->data['BonusCampaign']['date_to'] = date('d-m-Y', strtotime($this->request->data['BonusCampaign']['end_date']));
			
			//echo '<pre>';print_r($this->request->data);exit;

			$selected_create_for = array();

			if($this->request->data['BonusCampaign']['is_so'] == 1){
				$selected_create_for[] = 1;
			}

			if($this->request->data['BonusCampaign']['is_sr'] == 2){
				$selected_create_for[] = 2;
			}


			$list = $this->BonusCampaignProductList->find('all', array(
				'conditions'=>array(
					'BonusCampaignProductList.bonus_campaign_id'=>$id
				),
			));
			$exitingproduct_id=array();
			foreach($list as $pval){
				$exitingproduct_id[] = $pval['BonusCampaignProductList']['product_id'];

			}

			$this->loadModel('Product');
			$products = $this->Product->find('list', array(
				'order'=>array('Product.order' => 'ASC'),
				'conditions'=>array('Product.product_type_id'=>1),
			));
			$this->html = '';
			foreach($products as $key=>$val)
			{
				$this->html .= '<option value="'.$key.'">'.addslashes($val).'</option>';		
			}
			$product_list = $this->html;
			$this->set(compact('products', 'product_list', 'exitingproduct_id', 'selected_create_for'));

			//echo '<pre>';print_r($selected_create_for);exit;


		}
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->loadModel('BonusCampaignProductList');
		$this->BonusCampaignProductList->deleteAll(array('BonusCampaignProductList.bonus_campaign_id'=>$id));

		$this->BonusCampaign->id = $id;
		if (!$this->BonusCampaign->exists()) {
			throw new NotFoundException(__('Invalid Bonus Campaign'));
		}
		if ($this->BonusCampaign->delete()) {
			$this->flash(__('Bonus Campaign deleted'), array('action' => 'index'));
		}
		$this->flash(__('Bonus Campaign was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}



	function get_product_name($id){

		$this->loadModel('Product');
		$this->loadModel('BonusCampaignProductList');

		$list = $this->BonusCampaignProductList->find('all', array(
			'conditions'=>array(
				'BonusCampaignProductList.bonus_campaign_id'=>$id
			),
			'joins' => array(
				array(
					'alias' => 'Product',
					'table' => 'products',
					'type' => 'INNER',
					'conditions' => 'Product.id = BonusCampaignProductList.product_id'
				)
			),
			'fields' => array(
				'Product.name'
			)
		));

		$name = '';

		if(!empty($list)){
			foreach($list as $v){
				$name .= $v['Product']['name']. ', ';
			}
			$name = rtrim($name, ", ");
		}else{
			$name = '';
		}

		return $name;
		
	}



}
