<?php
App::uses('AppController', 'Controller');

class AllUsersHistoyController extends AppController {

    public function admin_index(){
		$this->set('page_title', 'User Historys');
		$this->loadModel('UserHistory');
		$this->UserHistory->recursive = 0;
		$this->paginate = array('order' => array('UserHistory.order' => 'desc'), 'limit' => 20);
		$this->set('usersHistorys', $this->paginate());
		
	}





}