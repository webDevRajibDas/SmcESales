<?php
App::uses('AppController', 'Controller');
/**
 * FiscalYears Controller
 *
 * @property FiscalYear $FiscalYear
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class FiscalYearsController extends AppController {

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
public function admin_index() 
{
	$this->set('page_title','Fiscal Year List');
	$this->FiscalYear->recursive = 0;
	$this->paginate = array(			
		'order' => array('FiscalYear.id' => 'DESC')
		);
	$this->set('fiscalYears', $this->paginate());
}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function admin_view($id = null) {
	if (!$this->FiscalYear->exists($id)) {
		throw new NotFoundException(__('Invalid fiscal year'));
	}
	$options = array('conditions' => array('FiscalYear.' . $this->FiscalYear->primaryKey => $id));
	$this->set('fiscalYear', $this->FiscalYear->find('first', $options));
}

/**
 * admin_add method
 *
 * @return void
 */
public function admin_add() 
{
	$this->set('page_title','Add Fiscal Year');
	$this->LoadModel('Month');
	$months = $this->Month->find('all',array('fields'=>array('id','name'),'recursive'=>-1));
	$months=Set::extract($months,'{n}.Month');
	$months=array_combine(array_map(function($elem){return $elem['name'];},$months),array_map(function($elem){return $elem['id'];},$months));
	if ($this->request->is('post')) {
		$this->FiscalYear->create();
		$this->request->data['FiscalYear']['start_date'] = date("Y-m-d",strtotime($this->request->data['FiscalYear']['start_date']));
		$this->request->data['FiscalYear']['end_date'] = date("Y-m-d",strtotime($this->request->data['FiscalYear']['end_date']));
		$this->request->data['FiscalYear']['updated_at'] = $this->current_datetime();
		$this->request->data['FiscalYear']['created_at'] = $this->current_datetime();
		$this->request->data['FiscalYear']['created_by'] = $this->UserAuth->getUserId();
		if ($this->FiscalYear->save($this->request->data)) {
			$start_date=date("Y-m-d",strtotime($this->request->data['FiscalYear']['start_date']));
			$end_date = date("Y-m-d",strtotime($this->request->data['FiscalYear']['end_date']));
			$data_array=array();
			for($m=date('Y-m',strtotime($start_date));$m<=$end_date;$m=date('Y-m',strtotime($m.' +1 Months')))
			{
				$week_start=date('Y-m-d',strtotime($m));
				$week_serial=1;
				while($week_start <= date('Y-m-t',strtotime($m)))
				{
					$data['start_date']=$week_start;
					$data['month_id']=$months[date('F',strtotime($week_start))];
					$data['week_name']=date('F',strtotime($week_start)).'_'.date('Y',strtotime($week_start)).'_Week_'.$week_serial;
					if(date('Y-m-d',strtotime($week_start.'+7days')) < date('Y-m-t',strtotime($m)))
					{
						if(date('d',strtotime($week_start))=='01')
						{
							$data['end_date']=date('Y-m-d',strtotime($week_start.'+6days'));
						}
						else
						{
							$data['end_date']=date('Y-m-d',strtotime($week_start.'+7days'));
						}

					}
					else
					{
						$data['end_date']=date('Y-m-t',strtotime($m));
					}
					$data['created_at'] = $this->current_datetime();
					$data['updated_at'] = $this->current_datetime();
					$data['created_by'] = $this->UserAuth->getUserId();
					$data_array[]=$data;
					if(date('d',strtotime($week_start))=='01')
					{
						$week_start=date('Y-m-d',strtotime($week_start.'+7days'));
					}
					else
					{
						$week_start=date('Y-m-d',strtotime($week_start.'+8days'));
					}
					$week_serial+=1;
				}
			}
			$this->loadModel('Week');
			$this->Week->saveAll($data_array);
			$this->Session->setFlash(__('The fiscal year has been saved'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__('The fiscal year could not be saved. Please, try again.'), 'flash/error');
		}
	}
}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
public function admin_edit($id = null) {
	$this->set('page_title','Edit Fiscal Year');
	$this->FiscalYear->id = $id;
	if (!$this->FiscalYear->exists($id)) {
		throw new NotFoundException(__('Invalid fiscal year'));
	}
	if ($this->request->is('post') || $this->request->is('put')) {
		$this->request->data['FiscalYear']['start_date'] = date("Y-m-d",strtotime($this->request->data['FiscalYear']['start_date']));
		$this->request->data['FiscalYear']['end_date'] = date("Y-m-d",strtotime($this->request->data['FiscalYear']['end_date']));
		$this->request->data['FiscalYear']['updated_at'] = $this->current_datetime();
		$this->request->data['FiscalYear']['updated_by'] = $this->UserAuth->getUserId();
		if ($this->FiscalYear->save($this->request->data)) {
			$this->Session->setFlash(__('The fiscal year has been saved'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash(__('The fiscal year could not be saved. Please, try again.'), 'flash/error');
		}
	} else {
		$options = array('conditions' => array('FiscalYear.' . $this->FiscalYear->primaryKey => $id));
		$this->request->data = $this->FiscalYear->find('first', $options);
	}
}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
public function admin_delete($id = null) {
	if (!$this->request->is('post')) {
		throw new MethodNotAllowedException();
	}
	$this->FiscalYear->id = $id;
	if (!$this->FiscalYear->exists()) {
		throw new NotFoundException(__('Invalid fiscal year'));
	}
	if ($this->FiscalYear->delete()) {
		$this->Session->setFlash(__('Fiscal year deleted'), 'flash/success');
		$this->redirect(array('action' => 'index'));
	}
	$this->Session->setFlash(__('Fiscal year was not deleted'), 'flash/error');
	$this->redirect(array('action' => 'index'));
}
}
