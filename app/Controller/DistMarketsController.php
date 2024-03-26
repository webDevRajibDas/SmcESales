<?php

App::uses('AppController', 'Controller');

/**
 * Markets Controller
 *
 * @property Market $Market
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistMarketsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $uses = array('DistMarket', 'District', 'Thana', 'DistRoute');
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index() {
        $this->set('page_title', 'Distributor Market List');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $office_id = (isset($this->request->data['DistMarket']['office_id']) ? $this->request->data['DistMarket']['office_id'] : 0);

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        if ($office_parent_id == 0) {
            $conditions = array();
            $office_conditions = array('Office.office_type_id' => 2);
            $territory_conditions = array('conditions' => array('Territory.office_id' => $office_id), 'order' => array('Territory.name' => 'ASC'));
            $route_conditions = array('conditions' => array('DistRoute.office_id' => $office_id), 'order' => array('DistRoute.name' => 'ASC'));
        } else {
            
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
            $territory_conditions = array('conditions' => array('Territory.office_id' => $office_id), 'order' => array('Territory.name' => 'ASC'));

            $this->loadModel('DistAreaExecutive');
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
               $route_list = $this->DistRouteMapping->find('list',array(
                'conditions'=>array('dist_distributor_id'=>array_keys($tso_dist_list)),
                'fields'=>array('DistRouteMapping.dist_route_id','DistRouteMapping.dist_distributor_id'),
               ));
               $route_conditions = array('conditions' =>array('DistRoute.id'=>array_keys($route_list)), 'order' => array('DistRoute.name' => 'ASC'));
               $conditions = array('DistMarket.dist_route_id' => array_keys($route_list));
            }
            elseif($user_group_id == 1034){
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first',array(
                    'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
                $route_list = $this->DistRouteMapping->find('list',array(
                'conditions'=>array('dist_distributor_id'=>$distributor_id),
                'fields'=>array('DistRouteMapping.dist_route_id','DistRouteMapping.dist_distributor_id'),
               ));
               $route_conditions = array('conditions' =>array('DistRoute.id'=>array_keys($route_list)), 'order' => array('DistRoute.name' => 'ASC'));
               $conditions = array('DistMarket.dist_route_id' => array_keys($route_list));

            }
            else{
                $conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId());
                $route_conditions = array('conditions' => array('DistRoute.office_id' => $office_id), 'order' => array('DistRoute.name' => 'ASC'));  
            }
            
        }
        $this->DistMarket->recursive = 0;
        $this->paginate = array(
            'joins' => array(
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Office.id = Territory.office_id'
                    )
                )
            ),
            'conditions' => $conditions,
            'fields' => array('DistMarket.*', 'LocationType.name', 'Thana.name', 'Territory.name', 'Office.office_name', 'DistRoute.name'),
            'order' => array('DistMarket.id' => 'desc')
        );
        $this->set('distmarkets', $this->paginate());

        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        $location_list = $this->DistMarket->LocationType->find('list');
        $district_list = $this->District->find('list', array('order' => array('District.name' => 'ASC')));
        $this->set('district_list', $district_list);
        $thana_list = $this->DistMarket->Thana->find('list', array(
            'conditions' => array('Thana.district_id' => (isset($this->request->data['DistMarket']['district_id']) ? $this->request->data['DistMarket']['district_id'] : 0)),
            'order' => array('Thana.name' => 'ASC')
        ));

        $route_list = $this->DistMarket->DistRoute->find('list', $route_conditions);

        $territory_list = $this->DistMarket->Territory->find('list', $territory_conditions);
        $this->set(compact('location_list', 'thana_list', 'offices', 'territory_list', 'route_list'));
    }

    public function get_thana_list() {
        $rs = array(array('id' => '', 'title' => '---- Select Thana -----'));
        $district_id = $this->request->data['district_id'];
        $thana = $this->Thana->find('all', array(
            'fields' => array('Thana.id as id', 'Thana.name as title'),
            'conditions' => array('Thana.district_id' => $district_id),
            'order' => array('Thana.name' => 'asc'),
            'recursive' => -1
        ));
        $data_array = Set::extract($thana, '{n}.0');
        if (!empty($thana)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_route_list() {
        $office_id = $this->request->data['office_id'];
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
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
               $route_list = $this->DistRouteMapping->find('list',array(
                'conditions'=>array('dist_distributor_id'=>array_keys($tso_dist_list)),
                'fields'=>array('DistRouteMapping.dist_route_id','DistRouteMapping.dist_distributor_id'),
               ));
               $route_conditions = array('DistRoute.id' => array_keys($route_list));
        }
        elseif($user_group_id == 1034){
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');
            $distributor = $this->DistUserMapping->find('first',array(
                'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
            $route_list = $this->DistRouteMapping->find('list',array(
            'conditions'=>array('dist_distributor_id'=>$distributor_id),
            'fields'=>array('DistRouteMapping.dist_route_id','DistRouteMapping.dist_distributor_id'),
           ));
           $route_conditions = array('DistRoute.id' => array_keys($route_list));
        }
        else{
            $route_conditions = array('DistRoute.office_id' => $office_id);
        }
        $output = "<option value=''>--- Select ---</option>";
        if ($office_id) {
            $route = $this->DistRoute->find('list', array(
                'conditions' => $route_conditions,
                'order' =>array('DistRoute.name ASC'),
            ));
            if ($route) {
                foreach ($route as $key => $data) {
                    $output .= "<option value='$key'>$data</option>";
                }
            }
        }
        echo $output;
        $this->autoRender = false;
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null) {
        $this->set('page_title', 'DistMarkets Details');
        if (!$this->DistMarket->exists($id)) {
            throw new NotFoundException(__('Invalid market'));
        }
        $options = array('conditions' => array('DistMarket.' . $this->DistMarket->primaryKey => $id));
        $this->set('market', $this->DistMarket->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add() {
        $this->set('page_title', 'Add Distributor Market');
        if ($this->request->is('post')) {
            $this->request->data['DistMarket']['created_at'] = $this->current_datetime();
            $this->request->data['DistMarket']['updated_at'] = $this->current_datetime();
            $this->request->data['DistMarket']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistMarket']['action'] = 1;
            $this->DistMarket->create();
            if ($this->DistMarket->save($this->request->data)) {
                $this->Session->delete('from_outlet');
                $this->Session->delete('from_market');
                $dist_outlet_id = $this->DistMarket->getLastInsertID();
                if (array_key_exists('tag', $this->request->data['DistMarket'])) {
                    /* --------------Add dist_distributor_id in index----------------- */
                    $this->request->data['DistMarket']['dist_distributor_id']=$this->request->data['DistMarket']['market_distributor_id'];
                    unset($this->request->data['DistMarket']['market_distributor_id']);  
                    
                    /*
                    $this->loadModel('DistRouteMapping');
                    $distRouteMappings = $this->DistRouteMapping->find('all', array(
                        'conditions' => array('DistRouteMapping.dist_route_id' => $this->request->data['DistMarket']['dist_route_id']),
                        'recursive' => -1
                    ));
                    if (count($distRouteMappings) > 0) {
                        $this->request->data['DistMarket']['dist_distributor_id'] = $distRouteMappings[0]['DistRouteMapping']['dist_distributor_id'];
                    }
                    */
                    /* --------------Add dist_sr_id in index----------------- */
                    $this->loadModel('DistSalesRepresentative');
                    
                    /*
                    $sr = $this->DistSalesRepresentative->find('all', array(
                        'conditions' => array(
                            'DistSalesRepresentative.office_id' => $this->request->data['DistMarket']['office_id'],
                            'DistSalesRepresentative.dist_distributor_id' => $distRouteMappings[0]['DistRouteMapping']['dist_distributor_id'],
                        )
                    ));
                    if (count($sr) > 0) {
                        $this->request->data['DistMarket']['dist_sales_representative_id'] = $sr[0]['DistSalesRepresentative']['id'];
                    }
                     */
                    
                    $this->request->data['DistMarket']['dist_sales_representative_id'] = $this->request->data['DistMarket']['market_sr_id'];
                    unset($this->request->data['DistMarket']['market_sr_id']);
                    
                    $this->request->data['DistMarket']['dist_route_id'] = $this->request->data['DistMarket']['market_route_id'];
                    unset($this->request->data['DistMarket']['market_route_id']);
                    
                    $this->request->data['DistMarket']['memo_date'] = $this->request->data['DistMarket']['market_memo_date'];
                    unset($this->request->data['DistMarket']['market_memo_date']);
                    
                    $this->request->data['DistMarket']['ae_id'] = $this->request->data['DistMarket']['market_ae_id'];
                    unset($this->request->data['DistMarket']['market_ae_id']);
                    
                    $this->request->data['DistMarket']['tso_id'] = $this->request->data['DistMarket']['market_tso_id'];
                    unset($this->request->data['DistMarket']['market_tso_id']);
                    
                    $this->request->data['DistMarket']['memo_reference_no'] = $this->request->data['DistMarket']['market_memo_reference_no'];
                    unset($this->request->data['DistMarket']['market_memo_reference_no']);

                    $this->request->data['DistMarket']['dist_market_id'] = $dist_outlet_id;
                    $this->request->data['DistMarket']['dist_outlet_id'] = '';
                    $this->request->data['DistMarket']['identity'] = 'from_market';
                    $data['DistOutlet']=$this->request->data['DistMarket'];
                    
                    $this->Session->write('from_outlet', $data);
                    die();
                }
                $this->Session->setFlash(__('The DistMarket has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
        }

        $office_id = (isset($this->request->data['Territory']['office_id']) ? $this->request->data['Territory']['office_id'] : 0);
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2);
        } else {
            $office_conditions = array('id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        $locationTypes = $this->DistMarket->LocationType->find('list');
        $thanas = $this->DistMarket->Thana->find('list', array('order' => array('name' => 'asc')));
        $territories = $this->DistMarket->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));
        $this->set(compact('locationTypes', 'thanas', 'territories', 'offices'));
    }

    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_edit($id = null) {
        $this->set('page_title', 'Edit Market');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $this->DistMarket->id = $id;
        if (!$this->DistMarket->exists($id)) {
            throw new NotFoundException(__('Invalid market'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['DistMarket']['updated_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistMarket']['updated_at'] = $this->current_datetime();
            $this->request->data['DistMarket']['action'] = 1;
            if ($this->DistMarket->save($this->request->data)) {
                $this->Session->setFlash(__('The Distributor Market has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                //$this->Session->setFlash(__('The market could not be saved. Please, try again.'), 'flash/error');
            }
        } else {
            $this->DistMarket->recursive = 0;
            $options = array('conditions' => array('DistMarket.' . $this->DistMarket->primaryKey => $id));
            $this->request->data = $this->DistMarket->find('first', $options);
        }

        $office_id = $this->request->data['Territory']['office_id'];
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2);
            $route_conditions = array('order' => array('DistRoute.name' => 'ASC'));  
        } else {
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
               $route_list = $this->DistRouteMapping->find('list',array(
                'conditions'=>array('dist_distributor_id'=>array_keys($tso_dist_list)),
                'fields'=>array('DistRouteMapping.dist_route_id','DistRouteMapping.dist_distributor_id'),
               ));
               $route_conditions = array('conditions' =>array('DistRoute.id'=>array_keys($route_list)), 'order' => array('DistRoute.name' => 'ASC'));
            }
            elseif($user_group_id == 1034){
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first',array(
                    'conditions'=>array('DistUserMapping.sales_person_id'=>$sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
                $route_list = $this->DistRouteMapping->find('list',array(
                'conditions'=>array('dist_distributor_id'=>$distributor_id),
                'fields'=>array('DistRouteMapping.dist_route_id','DistRouteMapping.dist_distributor_id'),
               ));
               $route_conditions = array('conditions' =>array('DistRoute.id'=>array_keys($route_list)), 'order' => array('DistRoute.name' => 'ASC'));
            }
            else{
                
                $route_conditions = array('conditions' => array('DistRoute.office_id' => $office_id), 'order' => array('DistRoute.name' => 'ASC'));  
            }
            $office_conditions = array('id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        $locationTypes = $this->DistMarket->LocationType->find('list');
        $thanas = $this->DistMarket->Thana->find('list', array('order' => array('name' => 'asc')));
        $territories = $this->DistMarket->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));
       
        $distRoutes = $this->DistMarket->DistRoute->find('list', $route_conditions);

        $this->set(compact('locationTypes', 'thanas', 'territories', 'offices', 'distRoutes'));
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
        $this->DistMarket->id = $id;
        if (!$this->DistMarket->exists()) {
            throw new NotFoundException(__('Invalid market'));
        }
        if ($this->DistMarket->delete()) {
            $this->Session->setFlash(__('DistMarket deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('DistMarket was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }

    public function get_market_list() {
        $this->LoadModel('DistMarket');
        $thana_id = $this->request->data['thana_id'];
        $territory_id = $this->request->data['territory_id'];
        $location_type_id = $this->request->data['location_type_id'];

        if ($territory_id > 0 AND $location_type_id > 0) {
            $conditions = array('territory_id' => $territory_id, 'location_type_id' => $location_type_id, 'is_active' => 1, 'thana_id' => $thana_id);
        } else {
            $conditions = array('territory_id' => $territory_id, 'is_active' => 1, 'thana_id' => $thana_id);
        }

        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $market_list = $this->DistMarket->find('all', array(
            'conditions' => $conditions,
            'order' => array('DistMarket.name' => 'ASC'),
            'recursive' => -1
        ));
        $data_array = Set::extract($market_list, '{n}.DistMarket');
        if (!empty($market_list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

}
