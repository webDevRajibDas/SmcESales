<?php
App::uses('AppController', 'Controller');
/**
 * DistSrVisitPlans Controller
 *
 * @property DistSrVisitPlan $DistSrVisitPlan
 * @property PaginatorComponent $Paginator
 */
class DistSrVisitPlansController extends AppController {

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
	
        $this->loadModel('User');
        $this->loadModel('Office');
		
		$this->loadModel('DistSrVisitPlanDetail');
        

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
        
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
      	
      	$user_types = array( 1=>'SR',2=>'DM');
		$week_days_options = array('1'=>'1st week','2'=>'2nd week','3'=>'3rd week','4'=>'4th week');
		
		$this->loadmodel('DistSrVisitPlan');
		
		$request_data = array();
		
		if($this->request->is('post'))
		{
			//pr($this->request->data);
			
			$request_data = $this->request->data;
			
			$office_id=$this->request->data['DistSrVisitPlans']['office_id'];
			$distributor_id=$this->request->data['DistSrVisitPlans']['distributor_id'];
			$sr_id=$this->request->data['DistSrVisitPlans']['sr_id'];
			$dm_id=$this->request->data['DistSrVisitPlans']['dm_id'];
			$user_type=$this->request->data['DistSrVisitPlans']['user_type'];
			$week_id=$this->request->data['DistSrVisitPlans']['week_id'];
			$effective_date=date('Y-m-d',strtotime($this->request->data['effective_date']));
			
			
			$this->loadmodel('DistDistributor');
			$distributors = $this->DistDistributor->find('list', array(
				'fields' => array('DistDistributor.id', 'DistDistributor.name'),
				'conditions' => array('DistDistributor.office_id' => $office_id,'DistDistributor.is_active'=> 1),
				'order' => array('DistDistributor.name' => 'asc'),
				'recursive' => 0
			));
			$this->set(compact('distributors'));
			
			
			
			$conditions= array(
				'DistSalesRepresentative.office_id'=>$office_id,
				'DistSalesRepresentative.dist_distributor_id'=>$distributor_id,
				'DistSalesRepresentative.is_active'=>1
			);
			$this->loadModel('DistSalesRepresentative');
			$srs = $this->DistSalesRepresentative->find('list',array(
				'conditions'=>$conditions,
				)
			);
			$this->set(compact('srs'));
			
			$conditions= array(
				'DistDeliveryMan.office_id'=>$office_id,
				'DistDeliveryMan.dist_distributor_id'=>$distributor_id,
				'DistDeliveryMan.is_active'=>1
			);
			$this->loadModel('DistDeliveryMan');
			$dms = $this->DistDeliveryMan->find('list',array(
				'conditions'=>$conditions,
				)
			);
			$this->set(compact('dms'));
			
			
			if($user_type==1)
			{
				$this->loadmodel('DistSalesRepresentative');
				$user_info =$this->DistSalesRepresentative->find('first',array(
					'conditions'=>array(
						'DistSalesRepresentative.id'=>$sr_id
					),
					'recursive' => -1,
				));
			}
			elseif($user_type==2)
			{
				$this->loadmodel('DistDeliveryMan');
				$user_info =$this->DistDeliveryMan->find('first',array(
					'conditions'=>array(
						'DistDeliveryMan.id'=>$dm_id
					),
					'recursive' => -1,
				));
			}
			//pr($user_info);die();
			$exist_visit_plan = $this->DistSrVisitPlan->find('first',array(
				'conditions'=>array(
					'DistSrVisitPlan.office_id'=>$office_id,
					'DistSrVisitPlan.distributor_id'=>$distributor_id,
					'DistSrVisitPlan.sr_id'=>$sr_id,
					'DistSrVisitPlan.user_type'=>$user_type,
					'DistSrVisitPlan.effective_date'=>$effective_date,
					'DistSrVisitPlan.week_id'=>$week_id,
				)));
			
			$data = array();
			
			//pr($exist_visit_plan);
			//exit;
			
				if(!empty($exist_visit_plan))
				{
					
					$this->DistSrVisitPlan->id = $exist_visit_plan['DistSrVisitPlan']['id'];
					$this->DistSrVisitPlan->delete(); 
					
					$this->DistSrVisitPlanDetail->deleteAll(
						array( 'DistSrVisitPlanDetail.dist_sr_visit_plan_id' => $exist_visit_plan['DistSrVisitPlan']['id'])   //condition
					); 
					
					$data['id'] = $exist_visit_plan['DistSrVisitPlan']['id'];
					
					//$this->Session->setFlash(__('Visit Plan Not Saved, Please Change Effective Date First'), 'flash/error');
					//$this->redirect(array('controller' => 'DistSrVisitPlans', 'action' => 'index'));
				}
				if($user_type == 1){
					$code = $user_info['DistSalesRepresentative']['code'];
					$sr_id = $this->request->data['DistSrVisitPlans']['sr_id'];
				}else{
					$code = $user_info['DistDeliveryMan']['code'];
					$sr_id = $this->request->data['DistSrVisitPlans']['dm_id'];
				}
				$data['office_id'] = $this->request->data['DistSrVisitPlans']['office_id'];
				$data['distributor_id'] = $this->request->data['DistSrVisitPlans']['distributor_id'];
				$data['sr_id'] = $sr_id;
				$data['user_type'] = $this->request->data['DistSrVisitPlans']['user_type'];
				$data['week_id'] = $this->request->data['DistSrVisitPlans']['week_id'];
				$data['effective_date'] = date('Y-m-d',strtotime($this->request->data['effective_date']));
				$data['code'] = $code;
				
				$data['created_at'] = $this->current_datetime();
				$data['created_by'] = $this->UserAuth->getUserId();
				$data['updated_at'] = $this->current_datetime();
				$data['updated_by'] = $this->UserAuth->getUserId();
				
				
				$this->DistSrVisitPlan->create();
				if($this->DistSrVisitPlan->save($data)){
					
					//pr($this->request->data['route']);
					
					$last_iinsert_id = $this->DistSrVisitPlan->getLastInsertID();
					$visit_plan = array();
					foreach ($this->request->data['route'] as $key => $value) {
						
						$visit_plan['route_id'] = $key;
						$visit_plan['dist_sr_visit_plan_id'] = $last_iinsert_id;
						for($i =0; $i<count($value); $i++){
	
							if(array_key_exists('Sat',$value)){$visit_plan['sat']=1;}else{$visit_plan['sat']=0;}
							if(array_key_exists('Sun',$value)){$visit_plan['sun']=1;}else{$visit_plan['sun']=0;}
							if(array_key_exists('Mon',$value)){$visit_plan['mon']=1;}else{$visit_plan['mon']=0;}
							if(array_key_exists('Tue',$value)){$visit_plan['tue']=1;}else{$visit_plan['tue']=0;}
							if(array_key_exists('Wed',$value)){$visit_plan['wed']=1;}else{$visit_plan['wed']=0;}
							if(array_key_exists('Thu',$value)){$visit_plan['thu']=1;}else{$visit_plan['thu']=0;}
							if(array_key_exists('Fri',$value)){$visit_plan['fri']=1;}else{$visit_plan['fri']=0;}
							//echo $value; echo "<br>";
						}
						
						$visit_plan['office_id'] =$office_id ;
						$visit_plan['distributor_id'] =$distributor_id ;
						$visit_plan['sr_id'] =$sr_id ;
						$visit_plan['week_id'] =$week_id ;
						$total_visit_plan [] = $visit_plan;
					}
					
					$this->DistSrVisitPlanDetail->create();
					if($this->DistSrVisitPlanDetail->saveAll($total_visit_plan)){
						$this->Session->setFlash(__('SR Visit Plan has been Saved'), 'flash/success');
						//$this->redirect(array('controller' => 'DistSrVisitPlans', 'action' => 'index'));
					}
				
			}
			
			
			
		}
		
		$this->set(compact('request_data'));
		
        
        $this->set(compact('user_types'));
        $this->set(compact('offices'));
        $this->set(compact('week_days_options'));
       
	}


	public function get_sr_list(){

		$office_id = $this->request->data['office_id'];
		$distributor_id = $this->request->data['distributor_id'];
		$user_type = $this->request->data['user_type'];

		$data_array=array();

		if($user_type == 1){
			$conditions= array(
				'DistSalesRepresentative.office_id'=>$office_id,
				'DistSalesRepresentative.dist_distributor_id'=>$distributor_id,
				//'DistSalesRepresentative.user_type'=>0,
				'DistSalesRepresentative.is_active'=>1
			);
			$this->loadModel('DistSalesRepresentative');
			$results = $this->DistSalesRepresentative->find('list',array(
				'conditions'=>$conditions,
				)
			);
		}
		else
		{
			$conditions= array(
				'DistDeliveryMan.office_id'=>$office_id,
				'DistDeliveryMan.dist_distributor_id'=>$distributor_id,
				'DistDeliveryMan.is_active'=>1
				//'DistSalesRepresentative.user_type'=>1,
			);
			$this->loadModel('DistDeliveryMan');
			$results = $this->DistDeliveryMan->find('list',array(
				'conditions'=>$conditions,
				//'fields'=>array('sp.id','sp.name'),
				)
			);
		}
		
		//pr($sr_data);die();
        $rs = '<option value="">---- Select ----</option>';
		foreach ($results as $key => $value) {
				$rs = $rs."<option value='".$key."'>".$value."</option>";
			
		}

		/*if(!empty($sr_data)){
			echo json_encode($data_array);
		}
		else{
			echo json_encode(array());
		}
*/
		echo $rs;
		$this->autoRender = false;
	}

	public function get_route(){

		$office_id = $this->request->data['office_id'];
		$sr_id = $this->request->data['sr_id'];
		$distributor_id=$this->request->data['distributor_id'];
		$user_type=$this->request->data['user_type'];
		$week_id=$this->request->data['week_id'];

		/*pr($office_id);
		pr($sr_id);
		pr($distributor_id);
		pr($week_id);
		pr($user_type);die();*/
		$data_array =array();
		$this->loadmodel('DistSrRouteMappingHistory');
		$this->loadmodel('DistSrRouteMapping');
		$this->loadmodel('DistRouteMapping');
		
		$this->loadmodel('DistRoute');
		$this->loadmodel('DistSrVisitPlan');
		$this->loadmodel('DistSrVisitPlanDetail');
 
		/*$route_list = $this->DistSrRouteMappingHistory->find('all',array(
			'conditions'=>array(
				'DistSrRouteMappingHistory.office_id'=>$office_id,
				'DistSrRouteMappingHistory.dist_distributor_id'=>$distributor_id,
				'DistSrRouteMappingHistory.dist_sr_id'=>$sr_id,
				'DistSrRouteMappingHistory.end_date'=>null,
				'DistSrRouteMappingHistory.is_assign'=>1,
			),
			'fields'=>array('DistSrRouteMappingHistory.dist_route_id'),
			'recursive' => -1,
		));*/

		if($user_type == 1){
			$route_conditions = array(
                'DistSrRouteMapping.office_id' => $office_id,
                'DistSrRouteMapping.dist_distributor_id' => $distributor_id,
                'DistSrRouteMapping.dist_sr_id !=' => $sr_id
            );
            $distSrRouteMappings_conditions = array(
                'DistSrRouteMapping.office_id' => $office_id,
                'DistSrRouteMapping.dist_sr_id' => $sr_id
            );
		}
		else{
			$route_conditions = array(
                'DistSrRouteMapping.office_id' => $office_id,
                'DistSrRouteMapping.dist_distributor_id' => $distributor_id,
                'DistSrRouteMapping.dist_dm_id !=' => $sr_id
            );
            $distSrRouteMappings_conditions = array(
                'DistSrRouteMapping.office_id' => $office_id,
                'DistSrRouteMapping.dist_dm_id' => $sr_id
            );
		}
		$distRouteMappings_except = $this->DistSrRouteMapping->find('all', array(
            'fields'=>array('dist_route_id'),
            'conditions' => $route_conditions,
            'recursive' => -1
        ));
       
        $route_except_ids=array();
        foreach ($distRouteMappings_except as $k => $v) {
            $route_except_ids[]=$v['DistSrRouteMapping']['dist_route_id'];
        }
		
		
		
		
		$distSrRouteMappings = $this->DistSrRouteMapping->find('all', array(
            'conditions' => $distSrRouteMappings_conditions,
            'recursive' => -1
        ));

        $mappingData = array();
        foreach ($distSrRouteMappings as $key => $value) {
            $mappingData[$value['DistSrRouteMapping']['dist_route_id']]['id'] = $value['DistSrRouteMapping']['id'];
            $mappingData[$value['DistSrRouteMapping']['dist_route_id']]['dist_route_id'] = $value['DistSrRouteMapping']['dist_route_id'];
            $mappingData[$value['DistSrRouteMapping']['dist_route_id']]['office_id'] = $value['DistSrRouteMapping']['office_id'];
            $mappingData[$value['DistSrRouteMapping']['dist_route_id']]['dist_distributor_id'] = $value['DistSrRouteMapping']['dist_distributor_id'];
            $mappingData[$value['DistSrRouteMapping']['dist_route_id']]['dist_sr_id'] = $value['DistSrRouteMapping']['dist_sr_id'];
            $mappingData[$value['DistSrRouteMapping']['dist_route_id']]['created_at'] = date("Y-m-d", strtotime($value['DistSrRouteMapping']['created_at']));
            $mappingData[$value['DistSrRouteMapping']['dist_route_id']]['effective_date'] = date("d-m-Y", strtotime($value['DistSrRouteMapping']['effective_date']));
        }
		//pr($route_except_ids);die();
		$route_list = $this->DistRouteMapping->find('all', array('conditions' => array('DistRouteMapping.office_id' => $office_id,'DistRouteMapping.dist_distributor_id' => $distributor_id,'NOT'=>array('DistRouteMapping.dist_route_id'=>$route_except_ids))));
		
		/*$route_list = $this->DistRouteMapping->find('all', array(
			'conditions' => array(
			'DistRouteMapping.office_id' => $office_id,
			'DistRouteMapping.dist_distributor_id' => $distributor_id,
			'NOT'=>array('DistRouteMapping.dist_route_id'=>$route_except_ids)
			),
			'recursive' => -1
		));*/
		//pr($mappingData);
		$lists=array();
		foreach ($route_list as $key => $value) {
			$dis_route_id = $value['DistRoute']['id'];
			if (array_key_exists($dis_route_id, $mappingData)) {
				$lists[$key]=$value['DistRouteMapping']['dist_route_id'];
			}
		}
		//pr($lists);die();
		//pr(count($lists));die();
		$routes=array();
		$previous_visit_plan=array();
		$count = count($lists);
		
		$effective_date = '';
		
		if($count > 0){
			if($count == 1){
				$visit_conditions = array(
					'DistSrVisitPlan.sr_id'=>$sr_id ,
					//'DistSrVisitPlan.user_type'=> $lists[0],
					'DistSrVisitPlan.user_type'=> $user_type,
					'DistSrVisitPlan.week_id'=> $week_id,
				);
			}
			else{
				$visit_conditions = array(
					'DistSrVisitPlan.sr_id'=>$sr_id ,
					//'DistSrVisitPlanDetail.user_type IN'=> $lists,
					'DistSrVisitPlan.week_id'=> $week_id,
					'DistSrVisitPlan.user_type'=> $user_type,
				);
			}
			
			//1st week info
			$visit_1st_conditions = array(
					'DistSrVisitPlan.sr_id'=>$sr_id ,
					//'DistSrVisitPlanDetail.user_type IN'=> $lists,
					'DistSrVisitPlan.week_id'=> 1,
					'DistSrVisitPlan.user_type'=> $user_type,
				);
			$first_visit_info = $this->DistSrVisitPlan->find('first',
				array(
				'conditions' => $visit_1st_conditions,
				'limit' => 1,
				'order' => array('id' => 'desc'),
				'recursive' => 2
				)
			);
			
			if($first_visit_info){
				$effective_date = $first_visit_info['DistSrVisitPlan']['effective_date'];
			}else{
				$effective_date = date('Y-m-d');
			}
			
			$previous_visit_plans = $this->DistSrVisitPlan->find('first',
				array(
				'conditions' => $visit_conditions,
				'limit' => 1,
				'order' => array('id' => 'desc'),
				'recursive' => 2
				)
			);
//pr($previous_visit_plans);die();
			$previous_visit_plan = array();
			if(!empty($previous_visit_plans)){
				$dist_sr_visit_plan_id = $previous_visit_plans['DistSrVisitPlan']['id'];
				
				
				$previous_visit_plan_details = $this->DistSrVisitPlanDetail->find('all',
					array(
					'conditions' => array(
						'DistSrVisitPlanDetail.dist_sr_visit_plan_id'=>$dist_sr_visit_plan_id ,
						'DistSrVisitPlanDetail.week_id' => $week_id
					),
					'order' => array('id' => 'asc'),
					'recursive' => -1
					)
				);
				foreach ($previous_visit_plan_details as $key => $value) {
					//pr($value['DistSrVisitPlanDetail']['route_id']);
					$previous_visit_plan[$value['DistSrVisitPlanDetail']['route_id']]=$value;
				}
			}
			//pr($previous_visit_plan);
			//exit;
		}
		 $msg="";

		
		
		if(!empty($route_list)){
			if($count == 1){
				$routes = $this->DistRoute->find('list',array('conditions'=>array('id'=>$lists[0])));
			}
			else{
				$routes = $this->DistRoute->find('list',array('conditions'=>array('id IN'=>$lists)));
			}
		 	
		}else{
			$msg='Please Mapping SR/DM to Route';
			
		}
		$week_days_options = array('1'=>'1st week','2'=>'2nd week','3'=>'3rd week','4'=>'4th week');
		$this->set(compact('routes','office_id','sr_id','distributor_id','previous_visit_plan','msg','effective_date','week_days_options','week_id'));
	}


	public function sr_visit_plan(){
		
		
		$this->loadmodel('DistSrVisitPlan');

		if($this->request->is('post')){
			
			$office_id=$this->request->data['DistSrVisitPlans']['office_id'];
			$distributor_id=$this->request->data['DistSrVisitPlans']['distributor_id'];
			$sr_id=$this->request->data['DistSrVisitPlans']['sr_id'];
			$routes = $this->request->data['route'];
			$lists =array_keys($routes);
			//pr($lists);die();
			$this->loadmodel('SalesPerson');
			$user_info =$this->SalesPerson->find('first',array(
				'conditions'=>array(
					'SalesPerson.dist_sales_representative_id'=>$sr_id
				),
				'recursive' => -1,
			));
			
			if(count($lists)==1){
				$this->DistSrVisitPlan->deleteAll(array('DistSrVisitPlan.office_id'=>$office_id,'DistSrVisitPlan.distributor_id'=>$distributor_id,'DistSrVisitPlan.sr_id'=>$sr_id,'DistSrVisitPlan.route_id'=>$lists[0]));
			}
			else{
				$this->DistSrVisitPlan->deleteAll(array('DistSrVisitPlan.office_id'=>$office_id,'DistSrVisitPlan.distributor_id'=>$distributor_id,'DistSrVisitPlan.sr_id'=>$sr_id,'DistSrVisitPlan.route_id IN'=>$lists));
			}
			

			$total_visit_plan = array();
			$user_id= $user_info['SalesPerson']['id'];
			
			foreach ($routes as $key => $value) {
				$visit_plan['route_id'] = $key;
				$visit_plan['user_id'] = $user_id;
				for($i =0; $i<count($value); $i++){

					if(array_key_exists('Sat',$value)){$visit_plan['sat']=1;}else{$visit_plan['sat']=0;}
					if(array_key_exists('Sun',$value)){$visit_plan['sun']=1;}else{$visit_plan['sun']=0;}
					if(array_key_exists('Mon',$value)){$visit_plan['mon']=1;}else{$visit_plan['mon']=0;}
					if(array_key_exists('Tue',$value)){$visit_plan['tue']=1;}else{$visit_plan['tue']=0;}
					if(array_key_exists('Wed',$value)){$visit_plan['wed']=1;}else{$visit_plan['wed']=0;}
					if(array_key_exists('Thu',$value)){$visit_plan['thu']=1;}else{$visit_plan['thu']=0;}
					if(array_key_exists('Fri',$value)){$visit_plan['fri']=1;}else{$visit_plan['fri']=0;}
					//echo $value; echo "<br>";
				}
				
				$visit_plan['office_id'] =$office_id ;
				$visit_plan['distributor_id'] =$distributor_id ;
				$visit_plan['sr_id'] =$sr_id ;
				$total_visit_plan [] = $visit_plan;
			}
			//pr($total_visit_plan);die();
			$this->DistSrVisitPlan->create();
			if($this->DistSrVisitPlan->saveAll($total_visit_plan)){
				//$this->flash(__('SR Visit Plan has been Saved');
				$this->Session->setFlash(__('SR Visit Plan has been Saved'), 'flash/success');
				$this->redirect(array('controller' => 'DistSrVisitPlans', 'action' => 'index'));
			}
			else{
				$this->redirect(array('controller' => 'DistSrVisitPlans', 'action' => 'index'));
			}
		}
		
	}
}
