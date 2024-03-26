<?php
App::uses('AppController', 'Controller');
/**
 * AppVersions Controller
 *
 * @property AppVersion $AppVersion
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OutletDeleteBtnHideDateSettingController extends AppController {

	public $components = array('Paginator', 'Session');
	public $uses=array('AppVersion');

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_index($id = 2) {
		//print_r(Configure::version()); die();
		$this->loadModel('FiscalYear');
        $this->set('page_title','Edit Outlet Delete Button Hide Date');
		// $this->AppVersion->id = $id;
		if (!$this->AppVersion->exists($id)) {
			throw new NotFoundException(__('Invalid app version'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			//print_r($this->request->data['fiscal_year_id']); die();
			// $this->request->data['AppVersion']['updated_by'] = $this->UserAuth->getUserId();
			// $this->request->data['AppVersion']['fiscal_year_id_for_bonus_report'] = $this->request->data['fiscal_year_id_for_bonus_report'];
			$this->request->data['AppVersion']['outlet_delete_btn_hide_date'] = "'".$this->request->data['AppVersion']['outlet_delete_btn_hide_date']."'";
			// pr($this->request->data['AppVersion']);exit;
			if ($this->AppVersion->UpdateAll($this->request->data['AppVersion'])) {
				$this->Session->setFlash(__('The  Outlet Delete Button Hide Date has been & Fiscal Year For Bonus Party Report saved '), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The  Outlet Delete Button Hide Date  & Fiscal Year For Bonus Party Report could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$fiscalYears = $this->FiscalYear->find('list', array('fields' => array('year_code')));
			$this->set(compact('fiscalYears','current_year_code'));
			$options = array('conditions' => array('AppVersion.' . $this->AppVersion->primaryKey => $id));
			$this->request->data = $this->AppVersion->find('first', $options);
			// pr($this->request->data);exit;
		}
	}


}
