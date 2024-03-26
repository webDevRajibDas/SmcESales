<?php
App::uses('AppController', 'Controller');
/**
 * MonthlySoActivitySummaries Controller
 *
 * @property MonthlySoActivitySummary $MonthlySoActivitySummary
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class MonthlySoActivitySummariesController extends AppController {

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
		$this->set('page_title','Monthly so activity summary List');
		$this->MonthlySoActivitySummary->recursive = 0;
		$this->paginate = array('order' => array('MonthlySoActivitySummary.id' => 'DESC'));
		$this->set('monthlySoActivitySummaries', $this->paginate());
	}

}
