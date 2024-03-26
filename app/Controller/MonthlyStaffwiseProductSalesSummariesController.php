<?php
App::uses('AppController', 'Controller');
/**
 * MonthlyStaffwiseProductSalesSummaries Controller
 *
 * @property MonthlyStaffwiseProductSalesSummary $MonthlyStaffwiseProductSalesSummary
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MonthlyStaffwiseProductSalesSummariesController extends AppController {

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
		$this->set('page_title','Monthly staffwise product sales summary List');
		$this->MonthlyStaffwiseProductSalesSummary->recursive = 0;
		$this->paginate = array('order' => array('MonthlyStaffwiseProductSalesSummary.id' => 'DESC'));
		$this->set('monthlyStaffwiseProductSalesSummaries', $this->paginate());
	}

}
