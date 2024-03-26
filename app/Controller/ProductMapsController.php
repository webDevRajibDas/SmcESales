<?php
App::uses('AppController', 'Controller');
/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class ProductMapsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator','Filter.Filter');
	
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		
		$this->set('page_title','Product Map');
		$product_map="test";		
		$this->set('product_map',$product_map);
		
		
	}
	
	
}
