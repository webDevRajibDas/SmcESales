<?php
App::uses('AppController', 'Controller');
/**
 * VisitedOutlets Controller
 *
 * @property VisitedOutlet $VisitedOutlet
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class VisitedOutletsController extends AppController {

/**
 * Components
 *
 * @var array
 */
public $components = array('Paginator', 'Session','Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
public function admin_index() {
	$this->set('page_title','Visited outlet List');
	$conditions = array();
	$office_parent_id = $this->UserAuth->getOfficeParentId();
	$territory_conditions=array();
	$thana_conditions=array();
	if($office_parent_id !=0)
	{
		$this->LoadModel('Office');
		$office_type = $this->Office->find('first',array(
			'conditions' => array('Office.id' => $this->UserAuth->getOfficeId()),
			'recursive'=>-1
			));
		$office_type_id = $office_type['Office']['office_type_id'];
	}


	if ($office_parent_id == 0) {
		$region_office_condition=array('office_type_id'=>3);
		$office_conditions = array('office_type_id'=>2, "NOT" => array( "id" => array(30, 31, 37)));
	} 
	else 
	{
		if($office_type_id==3)
		{
			$territory_conditions=array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			$thana_conditions=array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			$conditions[] = array('Office.parent_office_id' => $this->UserAuth->getOfficeId());
			$region_office_condition=array('office_type_id'=>3,'Office.id' => $this->UserAuth->getOfficeId());
			$office_conditions = array('Office.parent_office_id' => $this->UserAuth->getOfficeId(), 'office_type_id'=>2);
		}
		elseif($office_type_id==2)
		{

			$territory_conditions=array('Territory.office_id' => $this->UserAuth->getOfficeId());
			$thana_conditions=array('Territory.office_id' => $this->UserAuth->getOfficeId());
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(),'office_type_id'=>2);
			$conditions[] = array('Office.id' => $this->UserAuth->getOfficeId());
		}

	}
	$this->LoadModel('Territory');
	$this->LoadModel('ThanaTerritory');
	$thanas=$this->ThanaTerritory->find('all',
		array(
			'conditions'=>$thana_conditions,
			'joins'=>array(
				array(
					'table'=>'territories',
					'alias'=>'Territory',
					'type'=>'inner',
					'conditions'=>'Territory.id=ThanaTerritory.territory_id'
					),
				array(
					'table'=>'offices',
					'alias'=>'Office',
					'type'=>'inner',
					'conditions'=>'Office.id=Territory.office_id'
					),
				),
			'fields'=>array('Thana.id','Thana.name')
			)
		);
	$thana_list=array();
	foreach($thanas as $key => $value)
	{
		$thana_list[$value['Thana']['id']]=$value['Thana']['name'];
	}
	/*$this->Territory->unbindModel(
		array(
			'belongsTo'=>array('Office')
			)
			);*/
			$territory_list_r = $this->Territory->find('all', array(
				'fields' => array('Territory.id','Territory.name','SalesPerson.name'),
				'conditions' => $territory_conditions,
				'recursive'=>0
				)); 
			foreach($territory_list_r as $key => $value)
			{
				$territory_list[$value['Territory']['id']] = $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
			}
			$this->set(compact('territory_list','thana_list'));
			$this->VisitedOutlet->recursive = 0;

			$this->paginate = array(
				'fields'=>array('Territory.name','Territory.id','Outlet.name','Outlet.id','SalesPerson.name','SalesPerson.id','VisitedOutlet.longitude','VisitedOutlet.latitude','VisitedOutlet.visited_at','VisitedOutlet.id','Thana.name','Market.name','Market.id','Thana.id'),
				'order' => array('VisitedOutlet.id' => 'DESC'),
				'joins'=>array(
					array(
						'table'=>'territories',
						'alias'=>'Territory',
						'conditions'=>'Territory.id=SalesPerson.territory_id'
						),
					array(
						'table'=>'markets',
						'alias'=>'Market',
						'conditions'=>'Market.id=Outlet.market_id'
						),
					array(
						'table'=>'thanas',
						'alias'=>'Thana',
						'conditions'=>'Thana.id=Market.thana_id'
						),
					array(
						'table'=>'offices',
						'alias'=>'Office',
						'conditions'=>'Office.id=Territory.office_id'
						)
					),
				'conditions'=>$conditions
				);
			$this->set('visitedOutlets', $this->paginate());
			$this->LoadModel('Office');

			$offices = $this->Office->find('list', array(
				'conditions'=> $office_conditions,
				'fields'=>array('office_name')
				));

			if(isset($region_office_condition))
			{
				$region_offices = $this->Office->find('list', array(
					'conditions' => $region_office_condition, 
					'order' => array('office_name' => 'asc')
					));

				$this->set(compact('region_offices'));
			}
			$this->set(compact('offices', 'office_id'));

		}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	/*public function admin_view($id = null) {
		$this->set('page_title','Visited outlet Details');
		if (!$this->VisitedOutlet->exists($id)) {
			throw new NotFoundException(__('Invalid visited outlet'));
		}
		$options = array('conditions' => array('VisitedOutlet.' . $this->VisitedOutlet->primaryKey => $id));
		$this->set('visitedOutlet', $this->VisitedOutlet->find('first', $options));
	}
*/
/**
 * admin_add method
 *
 * @return void
 */
	/*public function admin_add() {
		$this->set('page_title','Add Visited outlet');
		if ($this->request->is('post')) {
			$this->VisitedOutlet->create();
			$this->request->data['VisitedOutlet']['created_at'] = $this->current_datetime();
			$this->request->data['VisitedOutlet']['created_by'] = $this->UserAuth->getUserId();			
			if ($this->VisitedOutlet->save($this->request->data)) {
				$this->Session->setFlash(__('The visited outlet has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The visited outlet could not be saved. Please, try again.'), 'flash/error');
			}
		}
		$outlets = $this->VisitedOutlet->Outlet->find('list');
		$sos = $this->VisitedOutlet->So->find('list');
		$this->set(compact('outlets', 'sos'));
	}*/

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	/*public function admin_edit($id = null) {
        $this->set('page_title','Edit Visited outlet');
		$this->VisitedOutlet->id = $id;
		if (!$this->VisitedOutlet->exists($id)) {
			throw new NotFoundException(__('Invalid visited outlet'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['VisitedOutlet']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->VisitedOutlet->save($this->request->data)) {
				$this->Session->setFlash(__('The visited outlet has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The visited outlet could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('VisitedOutlet.' . $this->VisitedOutlet->primaryKey => $id));
			$this->request->data = $this->VisitedOutlet->find('first', $options);
		}
		$outlets = $this->VisitedOutlet->Outlet->find('list');
		$sos = $this->VisitedOutlet->So->find('list');
		$this->set(compact('outlets', 'sos'));
	}*/

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	/*public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->VisitedOutlet->id = $id;
		if (!$this->VisitedOutlet->exists()) {
			throw new NotFoundException(__('Invalid visited outlet'));
		}
		if ($this->VisitedOutlet->delete()) {
			$this->Session->setFlash(__('Visited outlet deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Visited outlet was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}*/
	public function get_office_list() {
		$this->LoadModel('Office');
        $rs = array(array('id' => '', 'name' => '---- All -----'));

        $parent_office_id = $this->request->data['region_office_id'];

        $office_conditions = array('Office.parent_office_id' => $parent_office_id, 'Office.office_type_id' => 2);

        $offices = $this->Office->find('all', array(
            'fields' => array('id', 'office_name'),
            'conditions' => $office_conditions,
            'order' => array('office_name' => 'asc'),
            'recursive' => -1
            )
        );

        $data_array = array();
        foreach ($offices as $office) {
            $data_array[] = array(
                'id' => $office['Office']['id'],
                'name' => $office['Office']['office_name'],
                );
        }

        //$data_array = Set::extract($offices, '{n}.Office');

        if (!empty($offices)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }

        $this->autoRender = false;
    }
	public function get_territory_list()
	{
		$this->LoadModel('Office');
		$this->LoadModel('Territory');
		$view = new View($this);

		$form = $view->loadHelper('Form');  

		$office_id = $this->request->data['office_id'];

        //get territory list
		$this->Territory->unbindModel(
			array(
				'belongsTo'=>array('Office')
				)
			);
		$territory_list_r = $this->Territory->find('all', array(
			'fields' => array('Territory.id','Territory.name','SalesPerson.name'),
			'conditions' => array(
				'Territory.office_id' => $office_id,
				),
			'recursive'=>0
			)); 
		foreach($territory_list_r as $key => $value)
		{
			$territory_list[$value['Territory']['id']] = $value['Territory']['name'].' ('.$value['SalesPerson']['name'].')';
		}


		if(isset($territory_list))
		{   
			$form->create('VisitedOutlet', array('role' => 'form', 'action'=>'filter'))    ;

			echo $form->input('territory_id', array('class' => 'form-control territory_id','empty'=>'--- Select---','options' => $territory_list));
			$form->end();

		}
		else
		{
			echo '';    
		}


		$this->autoRender = false;
	}
	public function get_thana_list()
	{
		
		$view = new View($this);

		$form = $view->loadHelper('Form');  

		$territory_id = $this->request->data['territory_id'];

        //get territory list
		
		$this->LoadModel('ThanaTerritory');
		$thanas=$this->ThanaTerritory->find('all',
			array(
				'conditions'=>array('ThanaTerritory.territory_id'=>$territory_id),
				'fields'=>array('Thana.id','Thana.name')
				)
			);
		$thana_list=array();
		foreach($thanas as $key => $value)
		{
			$thana_list[$value['Thana']['id']]=$value['Thana']['name'];
		}

		if(isset($thana_list))
		{   
			$form->create('VisitedOutlet', array('role' => 'form', 'action'=>'filter'))    ;

			echo $form->input('thana_id', array('class' => 'form-control thana_id','empty'=>'--- Select---','options' => $thana_list));
			$form->end();

		}
		else
		{
			echo '';    
		}


		$this->autoRender = false;
	}
}
