<?php
App::uses('AppController', 'Controller');
/**
 * DailySoActivitySummaries Controller
 *
 * @property DailySoActivitySummary $DailySoActivitySummary
 * @property PaginatorComponent $Paginator
 */
class DailySoActivitySummariesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->set('page_title','Daily so activity summary List');
		$this->DailySoActivitySummary->recursive = 0;
		$this->paginate = array('order' => array('DailySoActivitySummary.id' => 'DESC'));
		$this->set('dailySoActivitySummaries', $this->paginate());
	}

}
