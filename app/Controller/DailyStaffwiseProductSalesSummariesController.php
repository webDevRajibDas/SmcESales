<?php
App::uses('AppController', 'Controller');
/**
 * DailyStaffwiseProductSalesSummaries Controller
 *
 * @property DailyStaffwiseProductSalesSummary $DailyStaffwiseProductSalesSummary
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DailyStaffwiseProductSalesSummariesController extends AppController {

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
		$this->set('page_title','Daily staffwise product sales summary List');
		$this->DailyStaffwiseProductSalesSummary->recursive = 0;
		$this->paginate = array('order' => array('DailyStaffwiseProductSalesSummary.id' => 'DESC'));
		$this->set('dailyStaffwiseProductSalesSummaries', $this->paginate());
	}

}
