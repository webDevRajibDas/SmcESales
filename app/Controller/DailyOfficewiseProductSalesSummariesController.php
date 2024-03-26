<?php
App::uses('AppController', 'Controller');
/**
 * DailyOfficewiseProductSalesSummaries Controller
 *
 * @property DailyOfficewiseProductSalesSummary $DailyOfficewiseProductSalesSummary
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DailyOfficewiseProductSalesSummariesController extends AppController {

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
		$this->set('page_title','Daily officewise product sales summary List');
		$this->DailyOfficewiseProductSalesSummary->recursive = 0;
		$this->paginate = array('order' => array('DailyOfficewiseProductSalesSummary.id' => 'DESC'));
		$this->set('dailyOfficewiseProductSalesSummaries', $this->paginate());
	}

}
