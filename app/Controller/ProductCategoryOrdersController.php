<?php
App::uses('AppController', 'Controller');

/**
 * Memos Controller
 *
 * @property Memo $Memo
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ProductCategoryOrdersController extends AppController {
    
   public $components = array('Paginator', 'Session', 'Filter.Filter');
    public function admin_index() {
        ini_set('max_execution_time', 1000);   
        $this->set('page_title', 'Product Category Orders');
    }

    public function admin_create_order() {
        ini_set('max_execution_time', 1000);   
        $this->set('page_title', 'Product Category Orders for Weekly Sales');
        $this->loadModel('ProductCategory');
        $category_list = $this->ProductCategory->find('list',array(
            'order'=>array('ProductCategory.id ASC')
        ));
        $this->set(compact('category_list'));
        if($this->request->is('post')){
            pr($this->request->data);die();
        }
    }
    public function admin_edit(){
        ini_set('max_execution_time', 1000);   
        $this->set('page_title', 'Memo Recommendations');
    }

    public function admin_get_product(){
        $product_category_id = $this->request->data['category_id'];
        $this->loadModel('Product');
        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $products = $this->Product->find('all',array(
            'conditions'=>array(
                'Product.product_category_id' => $product_category_id,
                'Product.product_type_id' => 1
            ),
            'order'=>array('Product.name ASC')
        ));
        $data_array = array();
        if(!empty($products)){
            foreach ($products as $key => $value) {
                $data_array[] = array(
                    'id' => $value['Product']['id'],
                    'name' => $value['Product']['name']
                );
            }
        }

        if(!empty($products)){
            echo json_encode(array_merge($rs,$data_array));
        }else{
            echo json_encode($rs);
        }

        $this->autoRender = false;
    }
    
}



