<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistSrLoginReportsController extends AppController {
    
    

/**
 * Components
 *
 * @var array
 */
 
    public $uses = array('DistRouteMappingHistory','Office','DistSalesRepresentatives');
    public $components = array('Paginator', 'Session', 'Filter.Filter');    
    
/**
 * admin_index method
 *
 * @return void
 */
    public function admin_index($id = null) 
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes
                
        
        $this->set('page_title', "SR Login Report");

                    
        //for region office list
        $region_offices = $this->Office->find('list', array(
            'conditions' => array('Office.office_type_id'=>3), 
            'order' => array('office_name' => 'asc')
        ));
        
        
        
        //report type
        $present_status_array=array('absent'=>'Absent','present'=>'Present');
        $this->set(compact('present_status_array'));
                
        
        
        $region_office_id = 0;
                
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        
        $this->set(compact('office_parent_id'));
        
        $office_conditions = array('Office.office_type_id'=>2);
        
        if ($office_parent_id == 0)
        {
            $office_id = 0;
        }
        elseif($office_parent_id == 14)
        {
            $region_office_id = $this->UserAuth->getOfficeId();
            $region_offices = $this->Office->find('list', array(
                'conditions' => array('Office.office_type_id'=>3, 'Office.id'=>$region_office_id), 
                'order' => array('office_name' => 'asc')
            ));
            
            $office_conditions = array('Office.parent_office_id'=>$region_office_id);
            
            $office_id = 0;
            
            $offices = $this->Office->find('list', array(
                'conditions'=> array(
                    'office_type_id'    => 2,
                    'parent_office_id'  => $region_office_id,
                    
                    "NOT" => array( "id" => array(30, 31, 37))
                    ), 
                'order'=>array('office_name'=>'asc')
            ));
            
            $office_ids = array_keys($offices);
            
            if($office_ids)$conditions['Territory.office_id'] = $office_ids;

        }
        else 
        {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id'=>2);
            $office_id = $this->UserAuth->getOfficeId();
            
            $offices = $this->Office->find('list', array(
                'conditions'=> array(
                    'id'    => $office_id,
                ),   
                'order'=>array('office_name'=>'asc')
            ));
            
        }
        
        
        
        $dis_con = array();
        
        
        
        if($this->request->is('post') || $this->request->is('put'))
        {
            
            $request_data = $this->request->data;
            $date = date('Y-m-d', strtotime($request_data['DistSrLoginReports']['date']));
           
            $this->set(compact( 'date'));
            
            $present_status = $this->request->data['DistSrLoginReports']['present_status'];
            $this->set(compact('present_status'));
            
            $region_office_id = isset($this->request->data['DistSrLoginReports']['region_office_id']) != '' ? $this->request->data['DistSrLoginReports']['region_office_id'] : $region_office_id;
            $this->set(compact('region_office_id'));
            $office_ids = array();
            if($region_office_id)
            {
                $offices = $this->Office->find('list', array(
                    'conditions'=> array(
                        'office_type_id'    => 2,
                        'parent_office_id'  => $region_office_id,
                        
                        "NOT" => array( "id" => array(30, 31, 37))
                        ), 
                    'order'=>array('office_name'=>'asc')
                ));
                
                $office_ids = array_keys($offices);
            }
            
            $office_id = isset($this->request->data['DistSrLoginReports']['office_id']) != '' ? $this->request->data['DistSrLoginReports']['office_id'] : $office_id;
            $this->set(compact('office_id'));
            $db_id = isset($this->request->data['DistSrLoginReports']['db_id']) != '' ? $this->request->data['DistSrLoginReports']['db_id'] : 0;
            $tso_id = isset($this->request->data['DistSrLoginReports']['tso_id']) != '' ? $this->request->data['DistSrLoginReports']['tso_id'] : 0;
            $date = date('Y-m-d', strtotime($request_data['DistSrLoginReports']['date']));
            $this->set(compact( 'date'));
            $present_status = $this->request->data['DistSrLoginReports']['present_status'];
            $this->set(compact('present_status'));
            $this->set(compact('db_id','tso_id'));
            $conditions=array();
            
            $condition_order=array();
            if($office_id)
            {
                $conditions['DistSalesRepresentatives.office_id']=$office_id;
            }
            elseif($office_ids)
            {
                $conditions['DistSalesRepresentatives.office_id']=$office_ids;
            }
            if($tso_id)
            {
                $conditions['DistTso.id']=$tso_id;
            }
            if($db_id)
            {
                $conditions['DistDistributor.id']=$db_id;
            }

            if($present_status== 'absent')
            {
                $conditions['DistSrCheckInOut.id']=NULL;
            }
            elseif($present_status== 'present')
            {
                $conditions['DistSrCheckInOut.id <>']=NULL;
            }

            $conditions['DistSalesRepresentatives.is_active']=1;
            $conditions['User.active']=1;

            $result=$this->DistSalesRepresentatives->find('all',array(
                'conditions'=>$conditions,
                'joins'=>array(
                    array(
                        'table'=>'dist_distributors',
                        'alias'=>'DistDistributor',
                        'conditions'=>'DistDistributor.id=DistSalesRepresentatives.dist_distributor_id'
                        ),
                    array(
                        'table'=>'dist_tso_mappings',
                        'alias'=>'DistTsoMapping',
                        'conditions'=>'DistTsoMapping.dist_distributor_id=DistDistributor.id'
                        ),
                    array(
                        'table'=>'dist_tsos',
                        'alias'=>'DistTso',
                        'type'=>'Left',
                        'conditions'=>'DistTsoMapping.dist_tso_id=DistTso.id'
                        ),
                    array(
                        'table'=>'offices',
                        'alias'=>'Office',
                        'conditions'=>'DistSalesRepresentatives.office_id=Office.id'
                        ),
                    array(
                        'table'=>'dist_sr_check_in_out',
                        'alias'=>'DistSrCheckInOut',
                        'type'=>'Left',
                        'conditions'=>'DistSrCheckInOut.sr_id=DistSalesRepresentatives.id AND DistSrCheckInOut.date=\''.$date.'\''
                        ),
                    array(
                        'table'=>'sales_people',
                        'alias'=>'SalesPerson',
                        'conditions'=>'DistSalesRepresentatives.id=SalesPerson.dist_sales_representative_id'
                        ),
                     array(
                        'table'=>'users',
                        'alias'=>'User',
                        'conditions'=>'User.sales_person_id=SalesPerson.id'
                        ),
                     
                    ),
                'fields'=>array(
                    'DistDistributor.name',
                    'DistTso.name',
                    'DistTso.mobile_number',
                    'DistSalesRepresentatives.id',
                    'DistSalesRepresentatives.name',
                    'SalesPerson.last_data_push_time',
                    'DistSalesRepresentatives.mobile_number',
                    'DistSrCheckInOut.check_in_time',
                    'DistSrCheckInOut.check_out_time',
                    'Office.office_name',
                    ),
                'order'=>array('Office.order','DistTso.id','DistDistributor.id'),
                'recursive'=>-1
                ));
              $this->set(compact('result'));
              $sr_ids=array();
              foreach($result as $data)
              {
                $sr_ids[$data['DistSalesRepresentatives']['id']]=$data['DistSalesRepresentatives']['id'];
              }
              $this->LoadModel('DistOrder');
              $total_order=$this->DistOrder->find('all',array(
                    'conditions'=>array(
                        'DistOrder.sr_id'=>array_keys($sr_ids),
                        'DistOrder.order_date'=>$date,
                        ),
                    'fields'=>array(
                        'DistOrder.sr_id',
                        'COUNT(DISTINCT DistOrder.id) as total_order',
                        'sum(DistOrder.gross_value) as order_value'
                        ),
                    'group'=>'DistOrder.sr_id',
                    'recursive'=>-1
                ));
              // pr($total_order);exit;
              $sr_wise_order=array();
              foreach($total_order as $data)
              {
                $sr_wise_order[$data['DistOrder']['sr_id']]['total_order']=$data[0]['total_order'];
                $sr_wise_order[$data['DistOrder']['sr_id']]['order_value']=$data[0]['order_value'];
              }
            $this->set(compact('sr_wise_order'));
        }
        $this->set(compact('offices', 'region_offices', 'office_id', 'request_data'));
        
    }
    
    //Office List
    public function get_office_list()
    {
        $rs = array(array('id' => '', 'name' => '---- All -----'));
        
        $parent_office_id = $this->request->data['region_office_id'];
        
        $office_conditions = array('NOT' => array( "id" => array(30, 31, 37)), 'Office.office_type_id'=>2);
        //$office_conditions = array('Office.parent_office_id' => $parent_office_id, 'Office.office_type_id'=>2);
        
        if($parent_office_id)$office_conditions['Office.parent_office_id'] = $parent_office_id;
        
        $offices = $this->Office->find('all', array(
            'fields' => array('id', 'office_name'),
            'conditions' => $office_conditions, 
            'order' => array('office_name' => 'asc'),
            'recursive' => -1
            )
        );
        
        
        $data_array = array();
        foreach($offices as $office){
            $data_array[] = array(
                'id'=>$office['Office']['id'],
                'name'=>$office['Office']['office_name'],
            );
        }
                
        //$data_array = Set::extract($offices, '{n}.Office');
                        
        if(!empty($offices)){
            echo json_encode(array_merge($rs, $data_array));
        }else{
            echo json_encode($rs);
        } 
        
        $this->autoRender = false;
    }

    function get_tso_list(){
        $office_id = $this->request->data['office_id'];
        
        $output = "<option value=''>---- Select TSO ----</option>";

        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        if($user_group_id == 1029){
            $dist_tso_conditions = array('DistTso.user_id'=>$user_id);
        }elseif ($user_group_id == 1028) {
            $dist_ae_info = $this->DistAreaExecutive->find('first',array(
                'conditions'=>array('DistAreaExecutive.user_id'=>$user_id),
                'recursive'=> -1,
            ));
            $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
            $dist_tso_conditions = array('DistTso.dist_area_executive_id'=>$dist_ae_id);
        }
        elseif($user_group_id == 1034){
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');
            $distributor = $this->DistUserMapping->find('first',array(
                'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
            
            $tsos_info = $this->DistTsoMapping->find('first',array(
                'conditions'=>array(
                    'DistTsoMapping.dist_distributor_id' => $distributor_id
                )
            ));
            $dist_tso_conditions = array('DistTso.id'=>$tsos_info['DistTso']['id']);
        }
        else{
            $dist_tso_conditions = array('DistTso.office_id'=>$office_id);
        }

        $dist_tso_info = $this->DistTso->find('all',array(
            'conditions'=>$dist_tso_conditions,
        )); 
        if ($dist_tso_info) {
            foreach ($dist_tso_info as $key => $data) {
                $k = $data['DistTso']['id'];
                $v = $data['DistTso']['name'];
                $output .= "<option value='$k'>$v</option>";
            }
        }
        echo $output;
        $this->autoRender = false;
    }

    function get_db_list(){
        $tso_id = $this->request->data['tso_id'];
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $this->loadModel('DistTsoMapping');

        $output = "<option value=''>--- Select Distributor ---</option>";
        if($user_group_id == 1034)
        {
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');
           
            $distributor = $this->DistUserMapping->find('first',array(
                'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
            $dist_conditions = array(
                'DistTsoMapping.dist_tso_id' => $tso_id,
                'DistTsoMapping.dist_distributor_id' => $distributor_id,
                'DistDistributor.is_active'=> 1
            );
        }
        else
        {
            $dist_conditions = array(
                'DistTsoMapping.dist_tso_id' => $tso_id,
                'DistDistributor.is_active'=> 1
                
            );
        }
        
        $distDistributors = $this->DistTsoMapping->find('all',array(
            'conditions'=>$dist_conditions,
            'fields'=>array('DistDistributor.id','DistDistributor.name'),
        ));
        if ($distDistributors) {
            $selected="";
            foreach ($distDistributors as $key => $data) {
                $k = $data['DistDistributor']['id'];
                $v = $data['DistDistributor']['name'];
                $output .= "<option $selected value='$k'>$v</option>";
            }
        }
        echo $output;
        $this->autoRender = false;
    
    }

      
}
