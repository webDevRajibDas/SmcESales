<?php
App::uses('AppController', 'Controller');
/**
 * DistSrVisitPlanLists Controller
 *
 * @property DistSrVisitPlanList $DistSrVisitPlanList
 * @property PaginatorComponent $Paginator
 */
class DistSrVisitPlanListsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator','Filter.Filter');

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() 
	{
		$this->set('page_title','SR Visit Plan List');
		$this->loadModel('Office');
		$this->loadModel('DistDistributor');		
		$this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistAreaExecutive');
        
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        
		if($this->UserAuth->getOfficeParentId() !=0 ){
			if($user_group_id == 1029 || $user_group_id == 1028){
                if($user_group_id == 1028){
                    $dist_ae_info = $this->DistAreaExecutive->find('first',array(
                        'conditions'=>array('DistAreaExecutive.user_id'=>$user_id),
                        'recursive'=> -1,
                    ));
                    $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                    $dist_tso_info = $this->DistTso->find('list',array(
                        'conditions'=>array('dist_area_executive_id'=>$dist_ae_id),
                        'fields'=> array('DistTso.id','DistTso.dist_area_executive_id'),
                    ));
                    
                    $dist_tso_id = array_keys($dist_tso_info);
                }
                else{
                    $dist_tso_info = $this->DistTso->find('first',array(
                        'conditions'=>array('DistTso.user_id'=>$user_id),
                        'recursive'=> -1,
                    ));
                    $dist_tso_id = $dist_tso_info['DistTso']['id'];
                }
                
                $tso_dist_list = $this->DistTsoMapping->find('list',array(
                    'conditions'=> array(
                        'dist_tso_id' => $dist_tso_id,
                    ),
                    'fields'=>array('DistTsoMapping.dist_distributor_id','DistTsoMapping.dist_tso_id'),
                ));
                $conditions = array('DistSrVisitPlanList.dist_distributor_id' => array_keys($tso_dist_list));
            }
            elseif($user_group_id == 1034){
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first',array(
                    'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
                
                $conditions = array('DistSrVisitPlanList.dist_distributor_id' => $distributor_id);
            }
            else{
                $dist_conditions = array('office_type_id' => 2, 'DistDistributor.is_active'=>1,'Office.id' => $this->UserAuth->getOfficeId());
                $conditions = array('DistSrVisitPlanList.aso_id' => $this->UserAuth->getOfficeId());
            }
			
		}else{
			$conditions = array();
		}
		
		$this->paginate = array('conditions' => $conditions,
								/*'joins' => array(
									array(
										'alias' => 'DistDistributor',
										'table' => 'dist_distributors',
										'type' => 'INNER',
										'conditions' => 'DistRoute.dist_distributor_id = DistDistributor.id'
									),
									array(
										'alias' => 'Office',
										'table' => 'offices',
										'type' => 'INNER',
										'conditions' => 'DistDistributor.office_id = Office.id'
									)
								),
								'fields' => array(
								'DistSrVisitPlanList.*','So.name',
								'DistRoute.id','DistRoute.code','DistRoute.name','DistDistributor.id', 'DistDistributor.name','DistDistributor.office_id'),*/
								'order' => array('DistSrVisitPlanList.id' => 'DESC'),
								'recursive' => 0);
		
		//pr($this->paginate());	
			
		$this->set('visitPlanLists', $this->paginate());

		$this->set('office_id', $this->UserAuth->getOfficeId());
        $this->loadModel('Office');
		$office_parent_id = $this->UserAuth->getOfficeParentId();
		if ($office_parent_id == 0) {
            $office_conditions = array(
                'office_type_id' => 2,
                "NOT" => array("id" => array(30, 31, 37))
            );
        }
        else 
		{
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
		//pr($this->request->data);
		
		$office_id = isset($this->request->data['DistSrVisitPlanList']['office_id'])!='' ? $this->request->data['DistSrVisitPlanList']['office_id'] : 0;
		$distributor_id = isset($this->request->data['DistSrVisitPlanList']['distributor_id'])!='' ? $this->request->data['DistSrVisitPlanList']['distributor_id'] : 0;

		$dist_routes = '';
		$dist_distributors = array();
		if($office_id){
			$this->loadModel('DistDistributor');
			$this->loadModel('DistRoute');
			$dist_distributors = $this->DistDistributor->find('list',array('conditions'=>array('office_id'=>$office_id, 'is_active'=> 1)));
			$dist_routes = $this->DistRoute->find('list',array('conditions'=>array('office_id'=>$office_id)));
		}
		if($distributor_id){
			$this->loadModel('DistRouteMapping');
			$dist_routes = $this->DistRouteMapping->find('list', array(
                'conditions' => array('DistRouteMapping.dist_distributor_id' => $distributor_id),
                'fields'=>array('DistRoute.id','DistRoute.name'),
                'recursive' => 0
            ));

		}
		$market_id = isset($this->request->data['DistSrVisitPlanList']['market_id']) != '' ? $this->request->data['DistSrVisitPlanList']['market_id'] : 0;
        
        
        $sr_id = isset($this->request->data['DistSrVisitPlanList']['sr_id']) != '' ? $this->request->data['DistSrVisitPlanList']['sr_id'] : 0;
        $dist_route_id = isset($this->request->data['DistSrVisitPlanList']['dist_route_id']) != '' ? $this->request->data['DistSrVisitPlanList']['dist_route_id'] : 0;
		
		

		$srs = array();
		
        if($distributor_id)
        {
              $this->loadModel('DistSalesRepresentative');
				$srs = $this->DistSalesRepresentative->find('list', array(
					'conditions' => array(
						'DistSalesRepresentative.dist_distributor_id' => $distributor_id,
						'DistSalesRepresentative.is_active' => 1,
					),
				));  
        }
		
		$markets = array();
		if($dist_route_id)
        {
				$this->loadModel('DistMarket');
				$markets = $this->DistMarket->find('list', array('conditions' => array(
				'dist_route_id' => $dist_route_id
				), 
				'order' => array('name' => 'asc')
				));
				$markets;   
        }
		
		
		
		$this->set(compact('dist_distributors','dist_routes','offices'));
		
		 $this->set(compact('offices','distributors','distributor_id','srs','sr_id', 'markets', 'sr_names','market_id','dist_route_id'));

	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->set('page_title','Visit plan list Details');
		if (!$this->DistSrVisitPlanList->exists($id)) {
			throw new NotFoundException(__('Invalid visit plan list'));
		}
		$options = array('conditions' => array('DistSrVisitPlanList.' . $this->DistSrVisitPlanList->primaryKey => $id));
		$this->set('visitPlanList', $this->DistSrVisitPlanList->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Visit plan list');
		if ($this->request->is('post')) {
			//$this->DistSrVisitPlanList->create();
			if(!empty($this->request->data['market_id']))
			{
				$market_array = array();
				foreach($this->request->data['market_id'] as $val)
				{
					$data['aso_id'] = $this->UserAuth->getPersonId();
					$data['so_id'] = $this->request->data['DistSrVisitPlanList']['so_id'];
					$data['market_id'] = $val;
					$data['visit_plan_date'] = DATE('Y-m-d',strtotime($this->request->data['DistSrVisitPlanList']['visit_plan_date']));
					$data['is_out_of_plan'] = 0;
					$data['visit_status'] = 'Pending';
					$data['created_at'] = $this->current_datetime();
					$data['created_by'] = $this->UserAuth->getUserId();			
					$data['updated_at'] = $this->current_datetime();
					$market_array[] = $data;					
				}
				
				$this->DistSrVisitPlanList->saveAll($market_array);
				$this->Session->setFlash(__('The visit plan has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
				
			}else{
				$this->Session->setFlash(__('Please select at least one market'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
			
			
		}
		$so_list = $this->DistSrVisitPlanList->So->find('list',array(
					'conditions' => array('So.office_id' => $this->UserAuth->getOfficeId(),'User.user_group_id' => 4), 	
					'order' => array('So.name'=>'ASC'),
					'recursive' => 0
				));
		if(isset($this->request->data['so_id'])!='')
		{			
			$so_info = $this->SalesPerson->find('first',array(
				'fields' => array('SalesPerson.dist_distributor_id'), 	
				'conditions' => array('SalesPerson.id' => $this->request->data['so_id']), 	
				'recursive' => -1
			));		
			$dist_routes = $this->DistRoute->find('list',array(
				'conditions' => array('DistRoute.dist_distributor_id' => $so_info['SalesPerson']['dist_distributor_id']), 	
				'order' => array('DistRoute.name'=>'ASC'),
				'recursive' => -1
			));
		}else{
			$dist_routes = array();
		}
		$this->set(compact('dist_routes','so_list'));
	}
	
	
	public function admin_get_market_list() 
	{		
		$view = new View($this);
        $form = $view->loadHelper('Form');	
		$this->loadModel('SalesPerson');
		$this->loadModel('DistRoute');
		
		$so_id = $this->request->data['so_id'];
		if($so_id !='')
		{	
			$so_info = $this->SalesPerson->find('first',array(
						'fields' => array('SalesPerson.dist_distributor_id'), 	
						'conditions' => array('SalesPerson.id' => $so_id), 	
						'recursive' => -1
					));
					
			$market_list = $this->DistRoute->find('list',array(
				'conditions' => array('DistRoute.dist_distributor_id' => $so_info['SalesPerson']['dist_distributor_id']), 	
				'order' => array('DistRoute.name'=>'ASC'),
				'recursive' => -1
			));
				
			echo $form->input('market_id', array('label'=>false,'multiple' => 'checkbox', 'options' => $market_list,'required'=>true));
		}else{
			echo '';
		}
		$this->autoRender = false;		
	}
	

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
        $this->set('page_title','Edit Visit plan list');
		$this->DistSrVisitPlanList->id = $id;
		if (!$this->DistSrVisitPlanList->exists($id)) {
			throw new NotFoundException(__('Invalid visit plan list'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['DistSrVisitPlanList']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->DistSrVisitPlanList->save($this->request->data)) {
				$this->Session->setFlash(__('The visit plan list has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The visit plan list could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('DistSrVisitPlanList.' . $this->DistSrVisitPlanList->primaryKey => $id));
			$this->request->data = $this->DistSrVisitPlanList->find('first', $options);
		}
		$dist_routes = $this->DistSrVisitPlanList->DistRoute->find('list');
		$this->set(compact('dist_routes'));
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
		$this->DistSrVisitPlanList->id = $id;
		if (!$this->DistSrVisitPlanList->exists()) {
			throw new NotFoundException(__('Invalid visit plan list'));
		}
		if ($this->DistSrVisitPlanList->delete($id)) {
			$this->Session->setFlash(__('Visit plan list deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Visit plan list was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	
	
	
	public function add_visit_plan()
	{
		
		//$this->request->data;
		
		//pr($this->request->data);
		//exit;
		
		if($this->request->data['so_id'])
		{
			/*$this->loadModel('SalesPerson');
			$so_info = $this->SalesPerson->find('first',array(
					'fields' => array('SalesPerson.dist_distributor_id', 'SalesPerson.office_id'), 	
					'conditions' => array('SalesPerson.id' => $this->request->data['so_id']), 	
					'recursive' => -1
				));	
			pr($so_info);
			exit;*/
			
			$data['aso_id'] = $this->UserAuth->getPersonId();
			
			$data['so_id'] = $this->request->data['so_id'];
			$data['market_id'] = $this->request->data['market_id'];
			$data['visit_plan_date'] = $this->request->data['visit_plan_date'];
			
			
			$results = $this->DistSrVisitPlanList->find('all', array(
				'conditions' => array(
					'DistSrVisitPlanList.so_id' => $data['so_id'],
					'DistSrVisitPlanList.market_id' => $data['market_id'],
					'DistSrVisitPlanList.visit_plan_date' => $data['visit_plan_date'],
				),
				'recursive' => -1,
			));
			
			
			//pr($result);
			//exit;
			
			$data['is_out_of_plan'] = 0;
			$data['visit_status'] = 'Pending';
			$data['created_at'] = $this->current_datetime();
			$data['created_by'] = $this->UserAuth->getUserId();			
			$data['updated_at'] = $this->current_datetime();
			
			$data['type'] = $this->request->data['type'];
			
			if(!$results)
			{
				if($this->DistSrVisitPlanList->save($data)){
					echo $this->DistSrVisitPlanList->getLastInsertId();;
				}else{
					echo 0;
				}
			}
			else
			{
				echo 'fail';
			}
		}
		
		$this->autoRender = false; 
		
	}
	
	
	public function update_visit_plan()
	{
		//pr($this->request->data);
		//exit;
		if($this->request->data['id'])
		{
			$this->DistSrVisitPlanList->id = $this->request->data['id'];
			if ($this->DistSrVisitPlanList->id) {
				$this->DistSrVisitPlanList->saveField('visit_plan_date', $this->request->data['visit_plan_date']);
				$this->DistSrVisitPlanList->saveField('updated_at', $this->current_datetime());
				echo 1;
			}else{
				echo 0;
			}
		}
		$this->autoRender = false; 
	}
	
	public function delete_visit_plan()
	{
		//pr($this->request->data);
		//exit;
		if($this->request->data['id'])
		{
			$this->DistSrVisitPlanList->id = $this->request->data['id'];
			
			if (!$this->DistSrVisitPlanList->exists()){
				echo 0;
			}else{
				$this->DistSrVisitPlanList->delete(); 
				echo 1;
			}			
		}
		$this->autoRender = false; 
	}
	
	
	
	
	

	
}
