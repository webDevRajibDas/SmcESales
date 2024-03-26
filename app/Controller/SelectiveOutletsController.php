<?php
App::uses('AppController', 'Controller');
/**
 * Brands Controller
 *
 * @property Brand $Brand
 * @property PaginatorComponent $Paginator
 */
class SelectiveOutletsController extends AppController {

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
		$this->set('page_title','Selective Outlet List');
		$this->SelectiveOutlet->recursive = 0;
		$this->paginate = array(			
			'order' => array('SelectiveOutlet.id' => 'DESC')
		);
		$this->set('selectiveoutlets', $this->paginate());

		$categories = $this->SelectiveOutlet->OutletCategory->find('list', array('conditions'=>array('is_active'=>1)));
		
		$this->set(compact('categories'));


	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Brand');
		if ($this->request->is('post')) {

			//echo "<pre>";print_r($this->request->data);exit;

			if(!empty($this->request->data['SelectiveOutlet']['outlet_category_id'])){
				$savealloption = array();
				foreach($this->request->data['SelectiveOutlet']['outlet_category_id'] as $key => $val){
					$insertData['SelectiveOutlet']['outlet_category_id'] = $val;
					$insertData['SelectiveOutlet']['created_at'] = $this->current_datetime();
					$insertData['SelectiveOutlet']['updated_at'] = $this->current_datetime();
					$insertData['SelectiveOutlet']['created_by'] = $this->UserAuth->getUserId();
					$insertData['SelectiveOutlet']['updated_by'] = 0;
					$savealloption[] = $insertData;
				}

				$this->SelectiveOutlet->create();
				if ($this->SelectiveOutlet->saveAll($savealloption)) {
					$this->Session->setFlash(__('The Selective Outlet has been saved'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}


			}else{
				$this->Session->setFlash(__('The Outlet Category is can not be empty. Please, try again.'), 'flash/error');
			}
			
		}

		$exitingoutletcatid = $this->SelectiveOutlet->find('all');

		$ol_cat_id = array();

		if(!empty($exitingoutletcatid)){
			foreach($exitingoutletcatid as $ol_val){
				$ol_cat_id[$ol_val['SelectiveOutlet']['outlet_category_id']] = $ol_val['SelectiveOutlet']['outlet_category_id'];
			}
		}
		/* $Collection_conditions[] = array(
			'NOT'=>array('Collection.id' => $exist_permited_Collection_list
		)); */

		//echo '<pre>';print_r($ol_cat_id);exit;

		$categories = $this->SelectiveOutlet->OutletCategory->find('list', array(
			'conditions'=>array('is_active'=>1,
			'NOT'=>array('id' => $ol_cat_id)
			),
		));

		$output = '';
		
		foreach($categories as $key => $v)
		{
				$output .= '<div  style="position: relative;width: 100%;float: left;" class="checkbox">
				<div style="position: relative;width: 100%;float: left;">
					<input name="data[SelectiveOutlet][outlet_category_id][]" value="'.$key.'" id="SelectiveOutletoutlet_category_id'.$key.'" type="checkbox">
				</div>
				<label for="SelectiveOutletoutlet_category_id'.$key.'" style="width:auto;margin: 0; padding: 0;" class="">'.$v.'</label>
				</div>';
		}
		
		
		$this->set(compact('output'));
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
		$this->SelectiveOutlet->id = $id;
		if (!$this->SelectiveOutlet->exists()) {
			throw new NotFoundException(__('Invalid Selective Outlet'));
		}
		if ($this->SelectiveOutlet->delete()) {
			$this->flash(__('SelectiveOutlet deleted'), array('action' => 'index'));
		}
		$this->flash(__('Selective Outlet was not deleted'), array('action' => 'index'));
		$this->redirect(array('action' => 'index'));
	}
}
