<?php
App::uses('AppController', 'Controller');
App::import('Vendor', 'php-excel-reader/excel_reader2');
/**
 * Weeks Controller
 *
 * @property VatexecutingProduct $VatexecutingProduct
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class VatexecutingProductsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Vat Executing Product List');
		$this->VatexecutingProduct->recursive = 0;
		$this->paginate = array('order' => array('VatexecutingProduct.id' => 'DESC'));		
		$this->set('vatexecutingproduct', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','VatexecutingProduct Details');
		if (!$this->VatexecutingProduct->exists($id)) {
			throw new NotFoundException(__('Invalid  VatexecutingProduct'));
		}
		$options = array('conditions' => array('VatexecutingProduct.' . $this->VatexecutingProduct->primaryKey => $id));
		$this->set('VatexecutingProduct', $this->VatexecutingProduct->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
        
		$this->set('page_title','Add Product Month');
		if ($this->request->is('post')) {

            $this->request->data['VatexecutingProduct']['effective_date'] = date('Y-m-d',  strtotime($this->request->data['VatexecutingProduct']['effectivedate']));
            $this->request->data['VatexecutingProduct']['created_at'] = $this->current_datetime();
			$this->request->data['VatexecutingProduct']['updated_at'] = $this->current_datetime();
			$this->request->data['VatexecutingProduct']['created_by'] = $this->UserAuth->getUserId();
			$this->request->data['VatexecutingProduct']['updated_by'] = 0;
           
			$this->VatexecutingProduct->create();
			if ($this->VatexecutingProduct->save($this->request->data)) {
				$this->Session->setFlash(__('The Vat Executing Product has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The VatexecutingProduct could not be saved. Please, try again.'), 'flash/error');
			}
		}
        
        $this->loadModel('ProductType');

        $productTypes = $this->ProductType->find('list', array('order' => 'id'));

        $this->set(compact('product_list', 'productTypes'));



	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */

 public function admin_admin_updateorcreate() {
	$this->loadModel('VatexecutingProduct');
	 if($this->request->data['type']=='create'){
		 if ($this->request->is('post')) {
            $this->request->data['effective_date'] = date('Y-m-d',  strtotime($this->request->data['effective_date']));
			$this->request->data['created_at'] = $this->current_datetime();
			$this->request->data['created_by'] = $this->UserAuth->getUserId();
			//$this->dd($this->request->data);exit();
			if ($this->VatexecutingProduct->save($this->request->data)) {
				$message ='Vat Executing Product Create Done.';
				echo json_encode(['messege' =>$message]);
				$this->autoRender = false;
			} else {
				$message ='Some thing went wrong. please!!';
				echo json_encode(['messege' =>$message]);
				$this->autoRender = false;
			}
		}
	}else{
		//$this->dd($this->request->data);exit();


		$this->request->data['id'];
		if (!$this->request->data['id']) {
			throw new NotFoundException(__('ID not found'));
		}
		$this->request->data['updated_at'] = $this->current_datetime();
		$this->request->data['updated_by'] =  $this->UserAuth->getUserId();
		$this->request->data['effective_date'] = date('Y-m-d',  strtotime($this->request->data['effective_date']));
		
		$this->VatexecutingProduct->save($this->request->data);
		$message ='Vat Executing Product Update Done.';
		echo json_encode(['messege' =>$message]);
		$this->autoRender = false;
		
	}


 }

	public function admin_edit($id = null) {
		$this->set('page_title','Edit VatexecutingProduct');
        $this->VatexecutingProduct->id = $id;
		if (!$this->VatexecutingProduct->exists($id)) {
			throw new NotFoundException(__('Invalid VatexecutingProduct'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
            
            $this->request->data['VatexecutingProduct']['effective_date'] = date('Y-m-d',  strtotime($this->request->data['VatexecutingProduct']['effectivedate']));
			$this->request->data['VatexecutingProduct']['updated_at'] = $this->current_datetime();
			$this->request->data['VatexecutingProduct']['updated_by'] =  $this->UserAuth->getUserId();

			if ($this->VatexecutingProduct->save($this->request->data)) {
				$this->Session->setFlash(__('The Vat Executing Product has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				//$this->Session->setFlash(__('The VatexecutingProduct could not be saved. Please, try again.'), 'flash/error');
			}
		} else {

			$options = array('conditions' => array('VatexecutingProduct.' . $this->VatexecutingProduct->primaryKey => $id), 'recursive'=>-1);
			$this->request->data = $this->VatexecutingProduct->find('first', $options);
			
			$this->loadModel('ProductType');

			$this->loadModel('Product');
			
            $product_list = $this->Product->find('list', array(
                'conditions' => array(
                    'Product.product_type_id' => $this->request->data['VatexecutingProduct']['product_type'],
                    'Product.is_virtual' => 0
                ),
                'order' => array('Product.order' => 'ASC'),
                'recursive' => -1
            )); 

            $productTypes = $this->ProductType->find('list', array('order' => 'id'));

            $this->set(compact('product_list', 'productTypes','id'));

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
		$this->VatexecutingProduct->id = $id;
		if (!$this->VatexecutingProduct->exists()) {
			throw new NotFoundException(__('Invalid VatexecutingProduct'));
		}
		if ($this->VatexecutingProduct->delete()) {
			$this->Session->setFlash(__('Product Month deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Product Month was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}


    public function get_product()
    {
        $this->loadModel('Product');
        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $type_id = $this->request->data['product_type_id'];
        if ($type_id == '') {
            $rs = array(array('id' => '', 'name' => '---- Select -----'));
        } else {
            $product = $this->Product->find('all', array(
                'conditions' => array(
                    'Product.product_type_id' => $type_id,
                    'Product.is_virtual' => 0
                ),
                'order' => array('Product.order' => 'ASC'),
                'recursive' => -1
            ));
            //pr($months);
            $data_array = Set::extract($product, '{n}.Product');
            if (!empty($product)) {
                echo json_encode(array_merge($rs, $data_array));
            } else {
                echo json_encode($rs);
            }
        }
        $this->autoRender = false;
    }


	public function admin_upload_xl()
    {
        ini_set('max_execution_time', 999999); //300 seconds = 5 minutes
        $this->autoRender=false;

      
        $this->loadModel('Product');
       
        if(!empty($_FILES["file"]["name"]))
        {
            $target_dir = WWW_ROOT.'files/';

            $target_file = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 30);
            $uploadOk = 1;
            $imageFileType = pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION);
            
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir.$target_file.'.'.$imageFileType)) 
            {
			
                $data_ex = new Spreadsheet_Excel_Reader($target_dir.$target_file.'.'.$imageFileType, true);
                $temp = $data_ex->dumptoarray();
                $i=0;
                $error=0;
                $ms = '';
			
                for($j=2;$j<=count($temp);$j++){
               
                    if($temp[$j][1]==''){
                        continue;
                    }

					//pr($temp[$j]);exit;

                    $pid = $temp[$j][1];
                    $pprice = $temp[$j][3];
                    $pvat = $temp[$j][4];

                  
                    $pinfo = $this->Product->find('first', array(
						'conditions'=>array('Product.id'=>$pid)
					));

                    if(!empty( $pinfo ) AND !empty($pprice) AND !empty($pvat) ){
                       
						$vatdata['VatexecutingProduct']['product_type'] = $pinfo['Product']['product_type_id'];
						$vatdata['VatexecutingProduct']['product_id'] = $pid;
						$vatdata['VatexecutingProduct']['price'] = $pprice;
						$vatdata['VatexecutingProduct']['vat'] = $pvat;
						$vatdata['VatexecutingProduct']['effective_date'] = date('Y-m-d');
						$vatdata['VatexecutingProduct']['created_at'] = $this->current_datetime();
						$vatdata['VatexecutingProduct']['updated_at'] = $this->current_datetime();
						$vatdata['VatexecutingProduct']['created_by'] = $this->UserAuth->getUserId();
						$vatdata['VatexecutingProduct']['updated_by'] = 0;

						$insert_data[] = $vatdata;

                    }else{

                        $error = 1;
                        if(empty( $pinfo )){
                            $ms = 'Product ID  Dose not match!. Line Number --' . $j;
                        }elseif(empty( $pprice )){
							$ms = 'Product Price  Dose not empty!. Line Number --' . $j;
						}elseif(empty( $pvat )){
							$ms = 'Product Vat  Dose not empty!. Line Number --' . $j;
						}

                        break;

                    } 

             	}

                if($error == 1){
                    $this->Session->setFlash(__($ms), 'flash/error');
                    $this->redirect(array('action' => 'add'));
                }else{

					if( !empty($insert_data) ){
						$this->VatexecutingProduct->saveAll($insert_data);
					}
					
				}

				$this->Session->setFlash(__('The Vat Executing Product file has been uploaded.'), 'flash/success');
                $this->redirect(array('action' => 'index'));
				
			}
		}
	}


}
