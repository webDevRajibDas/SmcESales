<?php
App::uses('AppController', 'Controller');
/**
 * Designations Controller
 *
 * @property Designation $Designation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class AeSoMappingsController extends AppController {

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
		$this->set('page_title','Ae So Mapping List');
		$this->AeSoMapping->recursive = 0;
		$this->paginate = array(
			'joins'=>array(
				array(
					'alias'=>'AE',
					'table'=>'sales_people',
					'type'=>'left',
					'conditions'=>'AE.id=AeSoMapping.ae_id'
				),
				array(
					'alias'=>'SO',
					'table'=>'sales_people',
					'type'=>'left',
					'conditions'=>'SO.id=AeSoMapping.so_id'
				),
				array(
					'alias'=>'Office',
					'table'=>'offices',
					'type'=>'left',
					'conditions'=>'Office.id=So.office_id'
				)
			),		
			'fields'=>array('AeSoMapping.*', 'AE.name', 'SO.name', 'Office.office_name'),	
			'order' => array('AeSoMapping.id' => 'DESC')
		);
		$this->set('aesomappings', $this->paginate());
	}

}
