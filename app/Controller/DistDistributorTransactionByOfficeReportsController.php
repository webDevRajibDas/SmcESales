<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistDistributorTransactionByOfficeReportsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $uses = array('DistDistributorBalance', 'DistDistributorBalanceHistory');
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index() {
        $this->set('page_title', 'Offic Wise Distributor Transaction Reports');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $designation_id=$this->Session->read('Office.designation_id');
        $office_set = 0; //flag
        $this->loadModel('Office');   
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
       
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_id = $this->UserAuth->getOfficeId();
        $conditions = array();
        $b_conditions = array();
        $dist_con = array();

       
        //==================25-6-19====//
        //echo $office_parent_id;
		$this->set(compact('office_parent_id','office_id'));
        if ($office_parent_id == 0) //for super user admin
        {
            $conditions = array('office_type_id' => 2);
            $b_conditions = array();
            $dist_con = array('DistDistributor.is_active' =>1);
			$region_office_condition = array('office_type_id'=>3);
        }
        else //for company office user admin
        {
            $conditions = array('office_type_id' => 2, 'Office.id' => $this->UserAuth->getOfficeId());
			$region_office_condition = array('office_type_id'=>3);
			$region_office_id = $this->UserAuth->getOfficeParentId();
			$this->set(compact('region_office_id'));
			$this->set(compact('office_parent_id','office_id'));
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
                $b_conditions = array('DistDistributorBalance.dist_distributor_id' =>array_keys($tso_dist_list));
                $dist_con = array('DistDistributor.id' =>array_keys($tso_dist_list),'DistDistributor.is_active' =>1);
            }
            elseif($user_group_id == 1034){
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first',array(
                    'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
        
                $b_conditions = array('DistDistributorBalance.dist_distributor_id' =>$distributor_id);
                $dist_con = array('DistDistributor.id' =>$distributor_id,'DistDistributor.is_active' =>1);
            }
            else{
                $b_conditions = array('DistDistributorBalance.office_id' =>$this->UserAuth->getOfficeId());
                $dist_con = array('DistDistributor.office_id' =>$this->UserAuth->getOfficeId(),'DistDistributor.is_active' =>1);
            }
        }
		$region_offices = $this->Office->find('list', array(
			'conditions' => $region_office_condition, 
			'order' => array('office_name' => 'asc')
			));

		$this->set(compact('region_offices'));
        /*pr($conditions);
        exit;*/
        
        $this->loadModel('Office');           
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        //================end==25-6-19====//
        $this->paginate = array(
            'fields'=> array('DistDistributorBalance.*','DistDistributor.id','DistDistributor.name','Office.office_name'),
            'conditions' => $b_conditions,
            'joins'=>array(
                array(
                    'table'=>'offices',
                    'alias'=>'Office',
                    'type' => 'LEFT',
                    'conditions'=>array('Office.id= DistDistributor.office_id')
                    )
                ),
            'recursive' => 0,
            //'group' => array('Office.id'),
            //'order' => array('DistDistributorBalance.effective_date' => 'DESC')
        );
        $this->set('dist_distributor_balances', $this->paginate());

        
        $this->loadModel('DistDistributor');
        
        $distDistributors = $this->DistDistributor->find('list',array(
                'conditions' => $dist_con
            ));
        //pr($distDistributors);die();
        $this->set(compact('offices'));
        $this->set(compact('distDistributors'));
     
        if($this->request->is('post')){
			
            //pr($this->request->data);die();
			$region_office_id = $this->request->data['DistDistributorBalance']['region_office_id'];
            $office_id = $this->request->data['DistDistributorBalance']['office_id'];
            $from_date = $this->request->data['DistDistributorBalance']['date_from'];
            $to_date = $this->request->data['DistDistributorBalance']['date_to'];
            $date_from = date('Y-m-d',strtotime($from_date));
            $date_to = date('Y-m-d',strtotime($to_date));
			$previous_date = date('Y-m-d',strtotime("$date_from -1 day"));
			$balance_conditions = array();
            if(empty($office_id)){//for region office and not office id selected
                $office_set = 1;// for only region office
                $opening_balance_store=array(); //for opening balance
                $debit_credit_balance_store=array(); //for credit, debit, balance store
                $offices_list = $this->Office->find('list', array(
					'conditions' =>array(
						'parent_office_id'=> $region_office_id
					), 
					'order' => array('office_name' => 'asc')
					));
                    // pr($offices_list);exit;
                    $this->set(compact('offices_list'));
                    $sql_condition_opening = ' ';

                
                if($region_office_id){
                    $sql_condition_opening .= ' And ofc.parent_office_id ='.$region_office_id;
                }
                if($date_from){
                    $sql_condition_opening .= " And ddbh.transaction_date <  "."'".$date_from."'";
                }
               
                $sql_opening = "Select SUM(case when balance_type = 1 then transaction_amount end) as debit, 
                                SUM(case when balance_type = 2 then transaction_amount end) as credit,ofc.id as office_id
                                from dist_distributor_balance_histories ddbh
                                inner join dist_distributors dd on dd.id=ddbh.dist_distributor_id
                                inner join offices ofc on ofc.id=dd.office_id
                                where balance_type in(1,2)".$sql_condition_opening.
                                "group by ofc.id,ofc.[order] order by ofc.[order] asc";
                // echo $sql_opening;exit;

                

                
                $opening_balance_arr = $this->DistDistributorBalanceHistory->query($sql_opening); //all db opening
                // pr($opening_balance_arr);exit;

                foreach ($opening_balance_arr as $key => $opening_balance_val) {
					
                    $debit = $opening_balance_val[0]['debit'];
                    $credit = $opening_balance_val[0]['credit'];
                    
					$office_id = $opening_balance_val[0]['office_id'];
                    $opening= $debit-$credit;
                    
					
					
					$opening_balance_store[$office_id]['opening_balance'] =  round($opening,2); // store opening here
                    
					
	
					
				}

                $condition_for_debit_credit = " ";

                if($region_office_id){
                    $condition_for_debit_credit .= ' And ofc.parent_office_id ='.$region_office_id;
                }
                if($date_from){
                    $condition_for_debit_credit .= " And ddbh.transaction_date >=  "."'".$date_from."'";
                }
                if($date_to){
                    $condition_for_debit_credit .= " And ddbh.transaction_date <=  "."'".$date_to."'";
                }

                $sql_for_debit_credit= "Select SUM(case when balance_type = 1 then transaction_amount end) as debit, 
                SUM(case when balance_type = 2 then transaction_amount end) as credit,ofc.id as office_id
                from dist_distributor_balance_histories ddbh
                inner join dist_distributors dd on dd.id=ddbh.dist_distributor_id
                inner join offices ofc on ofc.id=dd.office_id
                where balance_type in(1,2)".$condition_for_debit_credit.
                "group by ofc.id,ofc.[order] order by ofc.[order] asc";

                // echo $sql_for_debit_credit;exit;
                $debit_credit_balance_arr = $this->DistDistributorBalanceHistory->query($sql_for_debit_credit); //all db opening

                foreach ($debit_credit_balance_arr as $key => $debit_credit_val) {
					
                    if(isset($debit_credit_val[0]['debit']))$debit = $debit_credit_val[0]['debit'];else $debit =0;
                    //$debit = $debit_credit_val[0]['debit'];
                    if(isset($debit_credit_val[0]['credit']))$credit = $debit_credit_val[0]['credit'];else $credit =0;
                    //$credit = $debit_credit_val[0]['credit'];
                    
					$office_id = $debit_credit_val[0]['office_id'];

                    $opening_balance = $opening_balance_store[$office_id]['opening_balance'];
                    
                   
					
					
					
                    $debit_credit_balance_store[$office_id]['debit'] =  $debit;
                    $debit_credit_balance_store[$office_id]['credit'] =  $credit;
                   
					
	
					
				}
                    // pr($debit_credit_balance_store);exit;
                    $this->set(compact('opening_balance_store','debit_credit_balance_store'));

			}else{ //if office id seleceted
                $office_set = 2;// for distributors
				
                $distributor_list=array(); // for distributor
                $opening_balance_store=array(); //for opening balance
                $debit_credit_balance_store=array(); //for credit, debit, balance store
                $sql_condition = ' ';

                if($office_id){
                    $sql_condition .= ' And dd.office_id ='.$office_id;
                }

                $sql = "select dd.name as db, dd.id as db_ids,ofc.id as office_id
                        
                        from dist_tso_mapping_histories dtmh 
                        inner join dist_distributors dd on dd.id=dtmh.dist_distributor_id 
                        inner join offices ofc on ofc.id=dd.office_id 
                        where dtmh.is_change=1 
                        and dtmh.effective_date <= '".$date_to."'
                        and (dtmh.end_date is null or dtmh.end_date > '".$date_from."')
                        ".$sql_condition."
                        group by dd.name, dd.id,ofc.id order by dd.name";

                // echo $sql;exit;

                $distributor_list_arr = $this->DistDistributorBalanceHistory->query($sql); //all db opening
                // pr($distributor_list_arr);exit;
                $this->set(compact('distributor_list_arr'));
                
                $sql_condition_opening = ' ';

                
                if($office_id){
                    $sql_condition_opening .= ' And ddbh.office_id ='.$office_id;
                }
                if($date_from){
                    $sql_condition_opening .= " And ddbh.transaction_date <  "."'".$date_from."'";
                }
               
                $sql_opening = "Select SUM(case when balance_type = 1 then transaction_amount end) as debit, 
                                SUM(case when balance_type = 2 then transaction_amount end) as credit,dist_distributor_id,ofc.id as office_id
                                from dist_distributor_balance_histories ddbh
                                inner join dist_distributors dd on dd.id=ddbh.dist_distributor_id
                                inner join offices ofc on ofc.id=dd.office_id
                                where balance_type in(1,2)".$sql_condition_opening.
                                "group by ddbh.dist_distributor_id,ofc.id order by ddbh.dist_distributor_id";
                // echo $sql_opening;exit;

                
                $opening_balance_arr = $this->DistDistributorBalanceHistory->query($sql_opening); //all db opening
                // pr($opening_balance_arr);exit;

                foreach ($opening_balance_arr as $key => $opening_balance_val) {
					
                    $debit = $opening_balance_val[0]['debit'];
                    $credit = $opening_balance_val[0]['credit'];
                    $db_id = $opening_balance_val[0]['dist_distributor_id'];
					$office_id = $opening_balance_val[0]['office_id'];
                    $opening = $debit-$credit;
                    
					
					
					$opening_balance_store[$office_id][$db_id]['opening_balance'] =  $opening ;//store opening according to office_id & db_id
 
				}
                // pr($opening_balance_store);exit;

                $condition_for_debit_credit = " ";

                if($office_id){
                    $condition_for_debit_credit .= ' And ddbh.office_id ='.$office_id;
                }
                if($date_from){
                    $condition_for_debit_credit .= " And ddbh.transaction_date >=  "."'".$date_from."'";
                }
                if($date_to){
                    $condition_for_debit_credit .= " And ddbh.transaction_date <=  "."'".$date_to."'";
                }

                $sql_for_debit_credit= "Select SUM(case when balance_type = 1 then transaction_amount end) as debit, 
                SUM(case when balance_type = 2 then transaction_amount end) as credit,dist_distributor_id,ofc.id as office_id
                from dist_distributor_balance_histories ddbh
                inner join dist_distributors dd on dd.id=ddbh.dist_distributor_id
                inner join offices ofc on ofc.id=dd.office_id
                where balance_type in(1,2)".$condition_for_debit_credit.
                "group by ddbh.dist_distributor_id,ofc.id order by ddbh.dist_distributor_id";

                // echo $sql_for_debit_credit;exit;

                $debit_credit_balance_arr = $this->DistDistributorBalanceHistory->query($sql_for_debit_credit); //all db opening
                // pr( $debit_credit_balance_arr);exit;
                foreach ($debit_credit_balance_arr as $key => $debit_credit_val) {
					
                    if(isset($debit_credit_val[0]['debit'])) $debit = $debit_credit_val[0]['debit']; else $debit=0;
                    // $debit = $debit_credit_val[0]['debit'];
                    if(isset($debit_credit_val[0]['credit'])) $credit = $debit_credit_val[0]['credit']; else $credit=0;
                    // $credit = $debit_credit_val[0]['credit'];
                    
					$office_id = $debit_credit_val[0]['office_id'];
                    $db_id = $debit_credit_val[0]['dist_distributor_id'];
                    
                    $opening_balance = $opening_balance_store[$office_id][$db_id]['opening_balance'];
                    
                    
					
					
					
                    $debit_credit_balance_store[$office_id][$db_id]['debit'] =  $debit;
                    $debit_credit_balance_store[$office_id][$db_id]['credit'] =  $credit;
                    
					
	
					
				}
                // pr($debit_credit_balance_store);exit;

                $this->set(compact('opening_balance_store','debit_credit_balance_store'));
			
			}
			
  
            $this->set(compact('date_from','date_to','offices','office_set','dist_list','region_office_list'));
        }
        
        $this->set(compact('office_set'));
    }
	
	function get_office_list(){
		$region_office_id = $this->request->data['region_office_id'];
		$this->loadModel('Office');
		$rs = array(array('id' => '', 'name' => '---- All -----'));
		$offices = $this->Office->find('all', array(
			'conditions' => array('Office.parent_office_id' => $region_office_id),
			'order' => array('Office.office_name' => 'asc'),
			'recursive' => 0,
		));
		$data_array = array();
		foreach($offices as $key => $value)
		{
			$data_array[] = array(
				'id' => $value['Office']['id'],
				'name' => $value['Office']['office_name'],
			);
		}
		//echo $this->Office->getLastquery();exit;
		if(!empty($offices)){
			echo json_encode(array_merge($rs,$data_array));
		}else{
			echo json_encode($rs);
		} 
		$this->autoRender = false;
	}
}
