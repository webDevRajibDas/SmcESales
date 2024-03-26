<?php
App::uses('AppController', 'Controller');
/**
 * MonthlyOfficewiseProductSalesSummaries Controller
 *
 * @property MonthlyOfficewiseProductSalesSummary $MonthlyOfficewiseProductSalesSummary
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MonthlyOfficewiseProductSalesSummariesController extends AppController {

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
		$this->set('page_title','Monthly officewise product sales summary List');
		$this->MonthlyOfficewiseProductSalesSummary->recursive = 0;
		$this->paginate = array('order' => array('MonthlyOfficewiseProductSalesSummary.id' => 'DESC'));
		$this->set('monthlyOfficewiseProductSalesSummaries', $this->paginate());
	}

}
