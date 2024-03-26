<?php

App::uses('AppController', 'Controller');

/**
 * Stores Controller
 *
 * @property Store $Store
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistStoresController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index() {
        $this->set('page_title', 'Distributor Store List');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistDistributor');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        if ($office_parent_id == 0) {
            $conditions = array('DistDistributor.is_active'=> 1);
            $office_conditions = array('Office.office_type_id' => 2);
        } else {
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

                $conditions = array('DistStore.dist_distributor_id' => array_keys($tso_dist_list));

            }else{
                $conditions = array('Office.office_type_id' => 2,'DistStore.office_id' => $this->UserAuth->getOfficeId(),'DistDistributor.is_active'=> 1);
            }
            
            $office_conditions = array('Office.office_type_id' => 2,'Office.id' => $this->UserAuth->getOfficeId());
        }
        $this->DistStore->recursive = 0;
        $this->paginate = array('conditions' => $conditions, 'order' => array('DistStore.id' => 'DESC'));
        $this->set('stores', $this->paginate());
        $offices = $this->DistStore->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        $store_types = $this->DistStore->StoreType->find('list');
        $this->set(compact('offices', 'store_types'));
    }

}
