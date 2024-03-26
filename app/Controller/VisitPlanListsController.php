<?php
App::uses('AppController', 'Controller');
/**
 * VisitPlanLists Controller
 *
 * @property VisitPlanList $VisitPlanList
 * @property PaginatorComponent $Paginator
 */
class VisitPlanListsController extends AppController {

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
		$this->set('page_title','Visit Plan List');
		$this->loadModel('Office');
		$this->loadModel('Territory');		
		
		if($this->UserAuth->getOfficeParentId() !=0 ){
			$conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId());
		}else{
			$conditions = array();
		}
		
		$this->paginate = array('conditions' => $conditions,
								'joins' => array(
									array(
										'alias' => 'Territory',
										'table' => 'territories',
										'type' => 'INNER',
										'conditions' => 'Market.territory_id = Territory.id'
									),
									array(
										'alias' => 'Office',
										'table' => 'offices',
										'type' => 'INNER',
										'conditions' => 'Territory.office_id = Office.id'
									)
								),
								'fields' => array(
								'VisitPlanList.*','So.name',
								'Market.id','Market.code','Market.name','Territory.id', 'Territory.name','Territory.office_id'),
								'order' => array('VisitPlanList.id' => 'DESC'),
								'recursive' => 0);
						
		$this->set('visitPlanLists', $this->paginate());

		$offices = $this->Office->find('list',array(
			'conditions' => array('id'=>$this->UserAuth->getOfficeId()),
			'order' => array('Office.office_name'=>'asc')
		));
		$office_id = isset($this->request->data['Doctor']['office_id'])!='' ? $this->request->data['Doctor']['office_id'] : 0;
		$territory_id = isset($this->request->data['Doctor']['territory_id'])!='' ? $this->request->data['Doctor']['territory_id'] : 0;
		$territories = $this->Territory->find('list',array(
			'conditions' => array('Territory.office_id'=>$office_id),
			'order' => array('Territory.name'=>'asc')
		));
		$markets = $this->VisitPlanList->Market->find('list',array(
			'conditions' => array('Market.territory_id'=> $territory_id),
			'order' => array('Market.name'=>'asc')
		));
		$this->set(compact('territories','markets','offices'));

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
		if (!$this->VisitPlanList->exists($id)) {
			throw new NotFoundException(__('Invalid visit plan list'));
		}
		$options = array('conditions' => array('VisitPlanList.' . $this->VisitPlanList->primaryKey => $id));
		$this->set('visitPlanList', $this->VisitPlanList->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('page_title','Add Visit plan list');
		if ($this->request->is('post')) {
			//$this->VisitPlanList->create();
			if(!empty($this->request->data['market_id']))
			{
				$market_array = array();
				foreach($this->request->data['market_id'] as $val)
				{
					$data['aso_id'] = $this->UserAuth->getPersonId();
					$data['so_id'] = $this->request->data['VisitPlanList']['so_id'];
					$data['market_id'] = $val;
					$data['visit_plan_date'] = DATE('Y-m-d',strtotime($this->request->data['VisitPlanList']['visit_plan_date']));
					$data['is_out_of_plan'] = 0;
					$data['visit_status'] = 'Pending';
					$data['created_at'] = $this->current_datetime();
					$data['created_by'] = $this->UserAuth->getUserId();			
					$data['updated_at'] = $this->current_datetime();
					$market_array[] = $data;					
				}
				
				$this->VisitPlanList->saveAll($market_array);
				$this->Session->setFlash(__('The visit plan has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
				
			}else{
				$this->Session->setFlash(__('Please select at least one market'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}
			
			
		}
		$so_list = $this->VisitPlanList->So->find('list',array(
					'conditions' => array('So.office_id' => $this->UserAuth->getOfficeId(),'User.user_group_id' => 4), 	
					'order' => array('So.name'=>'ASC'),
					'recursive' => 0
				));
		if(isset($this->request->data['so_id'])!='')
		{			
			$so_info = $this->SalesPerson->find('first',array(
				'fields' => array('SalesPerson.territory_id'), 	
				'conditions' => array('SalesPerson.id' => $this->request->data['so_id']), 	
				'recursive' => -1
			));		
			$markets = $this->Market->find('list',array(
				'conditions' => array('Market.territory_id' => $so_info['SalesPerson']['territory_id']), 	
				'order' => array('Market.name'=>'ASC'),
				'recursive' => -1
			));
		}else{
			$markets = array();
		}
		$this->set(compact('markets','so_list'));
	}
	
	
	public function admin_get_market_list() 
	{		
		$view = new View($this);
        $form = $view->loadHelper('Form');	
		$this->loadModel('SalesPerson');
		$this->loadModel('Market');
		
		$so_id = $this->request->data['so_id'];
		if($so_id !='')
		{	
			$so_info = $this->SalesPerson->find('first',array(
						'fields' => array('SalesPerson.territory_id'), 	
						'conditions' => array('SalesPerson.id' => $so_id), 	
						'recursive' => -1
					));
					
			$market_list = $this->Market->find('list',array(
				'conditions' => array('Market.territory_id' => $so_info['SalesPerson']['territory_id']), 	
				'order' => array('Market.name'=>'ASC'),
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
		$this->VisitPlanList->id = $id;
		if (!$this->VisitPlanList->exists($id)) {
			throw new NotFoundException(__('Invalid visit plan list'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->request->data['VisitPlanList']['updated_by'] = $this->UserAuth->getUserId();
			if ($this->VisitPlanList->save($this->request->data)) {
				$this->Session->setFlash(__('The visit plan list has been saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The visit plan list could not be saved. Please, try again.'), 'flash/error');
			}
		} else {
			$options = array('conditions' => array('VisitPlanList.' . $this->VisitPlanList->primaryKey => $id));
			$this->request->data = $this->VisitPlanList->find('first', $options);
		}
		$markets = $this->VisitPlanList->Market->find('list');
		$this->set(compact('markets'));
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
		$this->VisitPlanList->id = $id;
		if (!$this->VisitPlanList->exists()) {
			throw new NotFoundException(__('Invalid visit plan list'));
		}
		if ($this->VisitPlanList->delete($id)) {
			$this->Session->setFlash(__('Visit plan list deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Visit plan list was not deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
	
	
	public function admin_set_visit_plan() 
	{
		
		$this->set('page_title','Set Visit Plan List');
		
		ini_set('memory_limit', '512M');
		ini_set('max_execution_time', 600); //300 seconds = 5 minutes
		
		$this->loadModel('SalesPerson');
		$this->loadModel('Market');
		//$this->loadModel('visitPlanLists');
		
		
		$so_list = $this->VisitPlanList->So->find('list',array(
					'conditions' => array(
						'So.office_id' => $this->UserAuth->getOfficeId(), 
						'So.territory_id >' => 0,
						'User.user_group_id' => 4
					), 	
					'order' => array('So.name'=>'ASC'),
					'recursive' => 0
				));
		
		$types = array(
			'1' => 'A',
			'2' => 'B',
			'3' => 'C',
			'4' => 'D'
		);
		$this->set(compact('types'));
		
		$months = array(
			date('Y',strtotime('-1 Year')).'-01'=>'January, '.date('Y',strtotime('-1 Year')),
			date('Y',strtotime('-1 Year')).'-02' => 'February, '.date('Y',strtotime('-1 Year')),
			date('Y',strtotime('-1 Year')).'-03' => 'March, '.date('Y',strtotime('-1 Year')),
			date('Y',strtotime('-1 Year')).'-04' => 'April, '.date('Y',strtotime('-1 Year')),
			date('Y',strtotime('-1 Year')).'-05' => 'May, '.date('Y',strtotime('-1 Year')),
			date('Y',strtotime('-1 Year')).'-06' => 'June, '.date('Y',strtotime('-1 Year')),
			date('Y',strtotime('-1 Year')).'-07' => 'July, '.date('Y',strtotime('-1 Year')),
			date('Y',strtotime('-1 Year')).'-08' => 'August, '.date('Y',strtotime('-1 Year')),
			date('Y',strtotime('-1 Year')).'-09' => 'September, '.date('Y',strtotime('-1 Year')),
			date('Y',strtotime('-1 Year')).'-10' => 'October, '.date('Y',strtotime('-1 Year')),
			date('Y',strtotime('-1 Year')).'-11' => 'November, '.date('Y',strtotime('-1 Year')),
			date('Y',strtotime('-1 Year')).'-12' => 'December, '.date('Y',strtotime('-1 Year')),
			date('Y').'-01' => 'January, '.date('Y'),
			date('Y').'-02' => 'February, '.date('Y'),
			date('Y').'-03' => 'March, '.date('Y'),
			date('Y').'-04' => 'April, '.date('Y'),
			date('Y').'-05' => 'May, '.date('Y'),
			date('Y').'-06' => 'June, '.date('Y'),
			date('Y').'-07' => 'July, '.date('Y'),
			date('Y').'-08' => 'August, '.date('Y'),
			date('Y').'-09' => 'September, '.date('Y'),
			date('Y').'-10' => 'October, '.date('Y'),
			date('Y').'-11' => 'November, '.date('Y'),
			date('Y').'-12' => 'December, '.date('Y')
		);
		$this->set(compact('months'));
		
		
		$markets = array();
		$so_id = '';
		$visitPlanLists = array();
		$marekt_sales = array();
		
		if ($this->request->is('get'))
		{ 
			
			if(isset($this->params['url']['so_id'])!='')
			{		
				$so_id = $this->params['url']['so_id'];	
				
				
				$visitPlanLists = $this->VisitPlanList->find('all',array(
					'conditions' => array('so_id' => $so_id),
					'order' => array('VisitPlanList.id'=>'desc'),
					'recursive' => 1
				));
				
				//pr($visitPlanLists);
				
				
				$so_info = $this->SalesPerson->find('first',array(
					'fields' => array('SalesPerson.territory_id'), 	
					'conditions' => array('SalesPerson.id' => $so_id), 	
					'recursive' => -1
				));	
				
				$markets = $this->Market->find('all',array(
					'conditions' => array('Market.territory_id' => $so_info['SalesPerson']['territory_id']), 	
					'order' => array('Market.name'=>'ASC'),
					'recursive' => 1
				));
				//pr(count($markets));
				//pr($markets);
				
				$date_from = date("Y-m-01", strtotime("-1 month"));
				$date_to = date("Y-m-t", strtotime("-1 month"));
					
				$markets_sales_results = $this->Market->find('all', array(
					'conditions' => array(
					'Market.territory_id' => $so_info['SalesPerson']['territory_id'],
					'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
					'Memo.gross_value >' => 0,
					'Memo.status !=' => 0,
					), 
					'joins' => array(
							array(
								'alias' => 'Outlet',
								'table' => 'outlets',
								'type' => 'INNER',
								'conditions' => 'Market.id = Outlet.market_id'
							),
							array(
								'alias' => 'Memo',
								'table' => 'memos',
								'type' => 'INNER',
								'conditions' => 'Outlet.id = Memo.outlet_id'
							),
						),	
					'fields' => array('Market.id as market_id, Market.name as market_name, SUM(Memo.gross_value) AS total_sales'),
					'group' => array('Market.id, Market.name'),
					'order' => array('Market.name'=>'ASC'),
					'recursive' => -1
				));
				
				
				foreach($markets_sales_results as $markets_sales_result){
					//pr($markets_sales_result);
					$marekt_sales[$markets_sales_result[0]['market_id']] = $this->priceSetting($markets_sales_result[0]['total_sales']);
				}
				
				/*$this->loadModel('Memo');
				$q_results = $this->Memo->find('all', array(
							'conditions'=> $conditions,
							'joins' => array(
								array(
									'alias' => 'Outlet',
									'table' => 'outlets',
									'type' => 'INNER',
									'conditions' => 'Memo.outlet_id = Outlet.id'
								),
								array(
									'alias' => 'Market',
									'table' => 'markets',
									'type' => 'INNER',
									'conditions' => 'Outlet.market_id = Market.id'
								)
							),
							'fields' => array('Market.id, count(Outlet.id) AS total_outlet'),
							'group' => array('Market.id, Market.name'),
							'recursive' => -1
						));*/
				
			}
		}
		
		//pr($marekt_sales);
		//exit;
		
		$this->set(compact('markets','so_list', 'so_id', 'visitPlanLists', 'marekt_sales'));
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
					'fields' => array('SalesPerson.territory_id', 'SalesPerson.office_id'), 	
					'conditions' => array('SalesPerson.id' => $this->request->data['so_id']), 	
					'recursive' => -1
				));	
			pr($so_info);
			exit;*/
			
			$data['aso_id'] = $this->UserAuth->getPersonId();
			
			$data['so_id'] = $this->request->data['so_id'];
			$data['market_id'] = $this->request->data['market_id'];
			$data['visit_plan_date'] = $this->request->data['visit_plan_date'];
			
			
			$results = $this->VisitPlanList->find('all', array(
				'conditions' => array(
					'VisitPlanList.so_id' => $data['so_id'],
					'VisitPlanList.market_id' => $data['market_id'],
					'VisitPlanList.visit_plan_date' => $data['visit_plan_date'],
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
				if($this->VisitPlanList->save($data)){
					echo $this->VisitPlanList->getLastInsertId();;
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
			$this->VisitPlanList->id = $this->request->data['id'];
			if ($this->VisitPlanList->id) {
				$this->VisitPlanList->saveField('visit_plan_date', $this->request->data['visit_plan_date']);
				$this->VisitPlanList->saveField('updated_at', $this->current_datetime());
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
			$this->VisitPlanList->id = $this->request->data['id'];
			
			if (!$this->VisitPlanList->exists()){
				echo 0;
			}else{
				$this->VisitPlanList->delete(); 
				echo 1;
			}			
		}
		$this->autoRender = false; 
	}
	
	private function priceSetting($amount=0)
	{
		/*if($amount >= 1000 && $amount < 100000){
			$amount = sprintf("%01.2f", $amount/1000).' TH';
		}elseif($amount >= 100000 && $amount < 1000000){
			$amount = sprintf("%01.2f", $amount/100000).' Lac';
		}elseif($amount >= 1000000){
			$amount = sprintf("%01.2f", $amount/1000000).' M';
		}else{
			$amount = sprintf("%01.2f", $amount);
		}*/
		
		//$amount = sprintf("%01.2f", $amount/1000000).' M';
		$amount = sprintf("%01.2f", $amount/100000).' L';
		
		return $amount ;
	}
	
	
	public function copy_month()
	{
		//pr($this->request->data);
		//exit;
		
		if($this->request->data['copy_month'] && $this->request->data['calendar_select_month'])
		{
			
			$so_id = $this->request->data['so_id'];			
			$copy_month = $this->request->data['copy_month'];
			$calendar_select_month = $this->request->data['calendar_select_month'];
			
			//exit;
			
			//get all copy month data
			$y_m = explode('-', $copy_month);
			$year = $y_m[0];
			$month = $y_m[1];
			$copy_data_lists = $this->VisitPlanList->find('all',array(
					'conditions' => array(
					'so_id' => $so_id,
					'YEAR(visit_plan_date)' => $year, 
					'MONTH(visit_plan_date)' => $month, 
					), 	
					'order' => array('VisitPlanList.id'=>'ASC'),
					'recursive' => -1
				));
			
			
			
			if($copy_data_lists)
			{
				$this->VisitPlanList->deleteAll(
					array( 
					'so_id' => $so_id,
					'YEAR(visit_plan_date)' => date('Y', strtotime($calendar_select_month)), 
					'MONTH(visit_plan_date)' => date('m', strtotime($calendar_select_month)), 
					)   //condition
				);
				
				$last_day_of_calendar = date('t', strtotime($calendar_select_month));
				
				$data_array = array();
				foreach($copy_data_lists as $result)
				{
					$visit_plan_date = explode('-', $result['VisitPlanList']['visit_plan_date']);
					
					$visit_plan_date_day = $visit_plan_date[2];
					
					if($last_day_of_calendar>=$visit_plan_date_day)
					{
						$visit_plan_date = date('Y-m', strtotime($calendar_select_month)).'-'.$visit_plan_date_day;
						$data['VisitPlanList']['aso_id'] = $result['VisitPlanList']['aso_id'];
						$data['VisitPlanList']['so_id'] = $result['VisitPlanList']['so_id'];
						$data['VisitPlanList']['market_id'] = $result['VisitPlanList']['market_id'];
						$data['VisitPlanList']['visit_plan_date'] = $visit_plan_date;
						$data['VisitPlanList']['is_out_of_plan'] = 0;
						$data['VisitPlanList']['visit_status'] = 'Pending';
						$data['VisitPlanList']['created_at'] = $this->current_datetime();
						$data['VisitPlanList']['created_by'] = $this->UserAuth->getUserId();			
						$data['VisitPlanList']['updated_at'] = $this->current_datetime();
						$data['VisitPlanList']['type'] = $result['VisitPlanList']['type'];
						
						$data_array[] = $data;
					}
				}
				
				
				/*pr($data_array);
				exit;*/
				
				/*$data['aso_id'] = $this->UserAuth->getPersonId();
				$data['so_id'] = $this->request->data['so_id'];
				$data['market_id'] = $this->request->data['market_id'];
				$data['visit_plan_date'] = $this->request->data['visit_plan_date'];
				$data['is_out_of_plan'] = 0;
				$data['visit_status'] = 'Pending';
				$data['created_at'] = $this->current_datetime();
				$data['created_by'] = $this->UserAuth->getUserId();			
				$data['updated_at'] = $this->current_datetime();
				$data['type'] = $this->request->data['type'];
				*/	
								
				if($this->VisitPlanList->saveAll($data_array)){
					echo 1;
				}else{
					echo 0;
				}
			}
			else
			{
				
			}
					
		}
		$this->autoRender = false; 
	}
	
}
