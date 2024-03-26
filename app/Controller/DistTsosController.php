<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session 
 */
class DistTsosController extends AppController
{

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
    public function admin_index()
    {
        $this->set('page_title', 'Tso List');
        $this->loadModel('Office');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_parent_id = $this->UserAuth->getOfficeParentId();

        if ($office_parent_id == 0) {
            $tso_conditions = $conditions = array('Office.office_type_id' => 2);
        } else {
            if ($user_group_id == 1029 || $user_group_id == 1028) {
                $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
                if ($user_group_id == 1028) {
                    $dist_ae_info = $this->DistAreaExecutive->find('first', array(
                        'conditions' => array('DistAreaExecutive.user_id' => $user_id, 'DistAreaExecutive.is_active' => 1),
                        'recursive' => -1,
                    ));
                    $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];

                    $tso_conditions = array('DistTso.dist_area_executive_id' => $dist_ae_id);
                } else {
                    $tso_conditions = array('DistTso.user_id' => $user_id, 'DistTso.is_active' => 1);
                }
            } else {
                $tso_conditions = $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
            }
        }
        $this->DistTso->virtualFields = array(
			
			'office_order' => 'Office.order',
			
		);
        $this->paginate = array(
            'conditions' => $tso_conditions,
            'recursive' => 0,
            'order' => array( 'office_order' => 'ASC','DistTso.name' => 'ASC')
            // 'order' => array('DistTso.name' => 'ASC')
        );

        $this->set('tsos', $this->paginate());
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $this->set(compact('offices'));
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null)
    {
        $this->set('page_title', 'TSO Details');
        if (!$this->DistTso->exists($id)) {
            throw new NotFoundException(__('Invalid TSO'));
        }
        $options = array('conditions' => array('DistTso.' . $this->DistTso->primaryKey => $id));
        $this->set('tso', $this->DistTso->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add()
    {
        $this->set('page_title', 'Add TSO');

        $users = array();
        $aes = array();
        $existing_ae_data = $this->get_available_tso_list();

        foreach ($existing_ae_data as $ae_key => $ae_value) {
            $user_id = $ae_value[0]['id'];
            $users[$user_id]['id'] = $user_id;
            $users[$user_id]['user_group_id'] = $ae_value[0]['user_group_id'];
            $users[$user_id]['sales_person_id'] = $ae_value[0]['sales_person_id'];
            $users[$user_id]['username'] = $ae_value[0]['username'];
            $users[$user_id]['name'] = $ae_value[0]['name'];
            $users[$user_id]['office_id'] = $ae_value[0]['office_id'];
            $aes[$user_id] = $ae_value[0]['name'];
        }

        $this->set(compact('users'));
        $this->set(compact('aes'));



        if ($this->request->is('post')) {

            $ae_name = "";
            $user_id = $this->request->data['DistTso']['user_id'];

            if ($user_id && array_key_exists($user_id, $aes)) {
                $ae_name = $users[$user_id]['name'];
                $this->request->data['DistTso']['name'] = $ae_name;
            } else {
                $this->Session->setFlash(__('Please select Valid TSO.'), 'flash/error');
                $this->redirect(array('action' => 'add'));
            }


            $this->request->data['DistTso']['is_added'] = 1;
            $this->request->data['DistTso']['created_at'] = $this->current_datetime();
            $this->request->data['DistTso']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistTso']['updated_at'] = $this->current_datetime();
            $this->request->data['DistTso']['updated_by'] = $this->UserAuth->getUserId();

            $this->request->data['DistTso']['pre_office_id'] = $this->request->data['DistTso']['office_id'];
            $this->request->data['DistTso']['effective_date'] = date("Y-m-d H:i:s", strtotime($this->request->data['DistTso']['effective_date']));
            $this->request->data['DistTso']['pre_dist_area_executive_id'] = $this->request->data['DistTso']['dist_area_executive_id'];

            //pr($this->request->data);exit;

            $this->DistTso->create();
            if ($this->DistTso->save($this->request->data)) {
                $this->loadModel('DistTsoHistory');
                $this->request->data['DistTso']['tso_id'] = $this->DistTso->getLastInsertID();
                $DistTsoHistory['DistTsoHistory'] = $this->request->data['DistTso'];
                $this->DistTsoHistory->save($DistTsoHistory);

                $this->Session->setFlash(__('The TSO has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                //echo $this->DistTso->getLastQuery();exit;
                $this->Session->setFlash(__('The TSO could not be saved. Please, try again.'), 'flash/error');
                //$this->redirect(array('action' => 'add'));
            }
        }
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $this->set(compact('offices'));
    }

    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_edit($id = null)
    {
        $this->set('page_title', 'Edit TSO');
        $this->DistTso->id = $id;
        if (!$this->DistTso->exists($id)) {
            throw new NotFoundException(__('Invalid Tso'));
        }

        $this->loadModel('DistTsoHistory');

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['DistTso']['updated_by'] = $this->UserAuth->getUserId();
            $transfer = 0;

            //pr($this->request->data); exit;
            /*             * ************ Check valid date ******************* */
            $this->request->data['DistTso']['effective_date'] = date("Y-m-d H:i:s", strtotime($this->request->data['DistTso']['effective_date']));
            $this->request->data['DistTso']['pre_effective_date'] = date("Y-m-d H:i:s", strtotime($this->request->data['DistTso']['pre_effective_date']));

            if (new DateTime($this->request->data['DistTso']['pre_effective_date']) > new DateTime($this->request->data['DistTso']['effective_date'])) {
                $this->Session->setFlash(__('New Effective should not be less than the existing effective date'), 'flash/error');
                $this->redirect(array('action' => "edit/$id"));
            }

            // $curr_effective_date=date("Y-m-d");
            /*                    
            if (new DateTime($curr_effective_date) > new DateTime($this->request->data['DistTso']['effective_date'])) {
               $this->Session->setFlash(__('Effective date can not be any past date'), 'flash/error');
               $this->redirect(array('action' => "edit/$id"));
              }
            */

            $this->loadModel('DistTsoHistory');
            $DistTsoHistory['DistTsoHistory'] = $this->request->data['DistTso'];
            if (($this->request->data['DistTso']['office_id'] != $this->request->data['DistTso']['pre_office_id']) || ($this->request->data['DistTso']['effective_date'] != $this->request->data['DistTso']['pre_effective_date']) || ($this->request->data['DistTso']['dist_area_executive_id'] != $this->request->data['DistTso']['pre_dist_area_executive_id'])) {
                $DistTsoHistory['DistTsoHistory']['is_transfer'] = 1;
                $transfer = 1;
                /*                 * ***************  Set start and end effective date  *********************** */

                if (new DateTime($this->request->data['DistTso']['pre_effective_date']) >= new DateTime($this->request->data['DistTso']['effective_date'])) {
                    $this->Session->setFlash(__('New Effective should not be less than the existing effective date'), 'flash/error');
                    $this->redirect(array('action' => "edit/$id"));
                }

                $effective_end_date = date('Y-m-d', strtotime($this->request->data['DistTso']['effective_date'] . ' -1 day'));
                $this->DistTsoHistory->query("update dist_tso_histories set active=0,effective_end_date='$effective_end_date' where tso_id=$id and active=1 and effective_end_date is NULL");
            } else {
                $DistTsoHistory['DistTsoHistory']['active'] = 0;
            }

            /*
            if($transfer==1)
            {
                $DistTsoHistory['DistTso']['effective_end_date']=date('Y-m-d', strtotime($this->request->data['DistTso']['effective_date']));
            }
            */

            /******* make inactive  *****/

            if ($this->request->data['DistTso']['is_active'] == 0 && $this->request->data['DistTso']['pre_is_active'] == 1) {
                $curr_effective_date = date("Y-m-d");
                $this->request->data['DistTso']['effective_end_date'] = date('Y-m-d', strtotime($curr_effective_date));
                unset($this->request->data['DistTso']['pre_is_active']);
            }

            if ($this->DistTso->save($this->request->data)) {

                $DistTsoHistory['DistTsoHistory']['created_at'] = $this->current_datetime();
                $DistTsoHistory['DistTsoHistory']['tso_id'] = $id;
                $DistTsoHistory['DistTsoHistory']['created_by'] = $this->UserAuth->getUserId();
                $DistTsoHistory['DistTsoHistory']['updated_at'] = $this->current_datetime();
                unset($DistTsoHistory['DistTsoHistory']['id']);
                $this->DistTsoHistory->save($DistTsoHistory);

                $this->Session->setFlash(__('The TSO has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $options = array('conditions' => array('DistTso.' . $this->DistTso->primaryKey => $id));
            $this->request->data = $this->DistTso->find('first', $options);
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
        $this->loadModel('Office');
        $this->loadModel('DistAreaExecutive');
        $this->loadModel('Territory');



        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        //$area_executives = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $this->set(compact('offices'));

        $office_id = $this->request->data['DistTso']['office_id'];
        $distAreaExecutives = $this->DistAreaExecutive->find('list', array(
            'conditions' => array(
                'DistAreaExecutive.office_id' => $office_id, 'DistAreaExecutive.is_active' => 1
            )
        ));
        $territories = $this->Territory->find('list', array(
            'conditions' => array('Territory.office_id' => $office_id, 'Territory.is_active' => 1),
            'order' => array('name' => 'asc'),
            'recursive' => -1
        ));
        $this->set(compact('distAreaExecutives', 'territories'));

        $has_node = 0;
        if ($this->check_tso_child($id)) {
            $has_node = 1;
        }

        $is_active = $this->DistTso->find('count', array(
            'conditions' => array(
                'DistTso.user_id' => $this->request->data['DistTso']['user_id'],
                'DistTso.id !=' => $id,
                'DistTso.is_active' => 1,
            ),
            'recursive' => -1
        ));

        if ($is_active > 0) {
            $has_node = 1;
        }

        $this->set(compact('has_node'));
    }

    /**
     * admin_delete method
     *
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
    public function admin_delete($id = null)
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->DistTso->id = $id;
        if (!$this->DistTso->exists()) {
            throw new NotFoundException(__('Invalid Tso'));
        }

        /*         * ********** Check foreign key ********** */

        if ($this->DistTso->checkForeignKeys("dist_tsos", $id)) {
            $this->Session->setFlash(__('This TSO has transactions'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }

        if ($this->DistTso->delete()) {
            $this->Session->setFlash(__('TSO deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('TSO was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }

    public function get_tso_list()
    {
        $office_id = $this->request->data['office_id'];
        $dist_distributor_id = $this->request->data['dist_distributor_id'];
        $this->loadModel('DistTso');
        $conditions = array(
            'DistTso.office_id' => $office_id,
            'DistTsoMapping.dist_distributor_id' => $dist_distributor_id
        );
        $joins = array(
            array(
                'table' => 'dist_tso_mappings',
                'alias' => 'DistTsoMapping',
                'type' => 'Inner',
                'conditions' => 'DistTsoMapping.dist_tso_id=DistTso.id'
            )
        );
        $distTsos = $this->DistTso->find('all', array(
            'conditions' => $conditions,
            'joins' => $joins,
            'order' => array('DistTso.name' => 'ASC'),
            'recursive' => -1
        ));
        echo json_encode($distTsos);
        $this->autoRender = false;
    }

    public function check_tso_child($tso_id)
    {
        $this->loadModel('DistTsoMapping');
        $conditions = array('DistTsoMapping.dist_tso_id' => $tso_id);
        $existingCount = $this->DistTsoMapping->find('count', array('conditions' => $conditions, 'recursive' => -1));
        return $existingCount;
    }

    public function get_tso_info()
    {
        $this->loadModel('DistAreaExecutive');
        $rs = array();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_id = $this->request->data['office_id'];
        if ($user_group_id == 1029 || $user_group_id == 1028) {
            if ($user_group_id == 1029) {
                $conditions = array('DistTso.user_id' => $user_id, 'DistTso.is_active' => 1);
            } else {
                $dist_ae_info = $this->DistAreaExecutive->find('first', array(
                    'conditions' => array('DistAreaExecutive.user_id' => $user_id),
                    'recursive' => -1,
                ));
                $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                $conditions = array('DistTso.dist_area_executive_id' => $dist_ae_id);
            }
        } else {
            $conditions = array('DistTso.office_id' => $office_id, 'DistTso.is_active' => 1);
        }

        $distAreaExecutives = $this->DistTso->find('list', array(
            'conditions' => $conditions
        ));
        if (!empty($distAreaExecutives)) {
            echo json_encode($distAreaExecutives);
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_available_tso_list()
    {

        $this->loadModel('DistTso');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        /*if ($office_parent_id == 0) {
            $conditions = array();
        } else {
            $conditions = array('DistTso.office_id' => $this->UserAuth->getOfficeId());
        }*/

        $dist_ae_except = $this->DistTso->find('all', array(
            'conditions' => array('DistTso.is_active' => 1),
            'recursive' => -1
        ));


        $ae_except_ids = array();
        foreach ($dist_ae_except as $k => $v) {
            if ($v['DistTso']['user_id'])
                $ae_except_ids[] = $v['DistTso']['user_id'];
        }

        $this->loadModel('User');
        $ae_except_id = "";

        if ($ae_except_ids && count($ae_except_ids) > 1) {
            $ae_except_id = implode(',', $ae_except_ids);
        } else if ($ae_except_ids) {
            $ae_except_id = $ae_except_ids[0];
        }
        if ($office_parent_id == 0) {
            if ($ae_except_id) {
                $qry = "select u.id,u.user_group_id,u.sales_person_id,u.username,sp.name,sp.office_id from users u
                left join sales_people sp on sp.id=u.sales_person_id where u.user_group_id=1029 and u.id not in ($ae_except_id)";
            } else {
                $qry = "select u.id,u.user_group_id,u.sales_person_id,u.username,sp.name,sp.office_id from users u
                left join sales_people sp on sp.id=u.sales_person_id where u.user_group_id=1029";
            }
        } else {
            $office_id = $this->UserAuth->getOfficeId();
            if ($ae_except_id) {
                $qry = "select u.id,u.user_group_id,u.sales_person_id,u.username,sp.name,sp.office_id from users u
                left join sales_people sp on sp.id=u.sales_person_id where u.user_group_id=1029 and sp.office_id = $office_id and u.id not in ($ae_except_id)";
            } else {
                $qry = "select u.id,u.user_group_id,u.sales_person_id,u.username,sp.name,sp.office_id from users u
                left join sales_people sp on sp.id=u.sales_person_id where u.user_group_id=1029 and sp.office_id = $office_id ";
            }
        }



        $existing = $this->User->query($qry);

        return $existing;
    }

    public function admin_history()
    {
        $this->set('page_title', 'TSO History');

        //pr($this->request->data);exit;
        $this->loadModel('Office');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoHistory');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistAreaExecutive');
        $office_id = 0;
        $dist_tso_id = 0;
        $tso_id = 0;
        $dist_distributor_id = 0;
        $distTsos = array();
        $distDistributors = array();
        $tso = array();
        $dist = array();
        $page_conditions = array();

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $tso_conditions = array();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            if ($user_group_id == 1029 || $user_group_id == 1028) {
                if ($user_group_id == 1028) {
                    $dist_ae_info = $this->DistAreaExecutive->find('first', array(
                        'conditions' => array('DistAreaExecutive.user_id' => $user_id),
                        'recursive' => -1,
                    ));
                    $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                    $dist_tso_info = $this->DistTso->find('list', array(
                        'conditions' => array('dist_area_executive_id' => $dist_ae_id),
                        'fields' => array('DistTso.id', 'DistTso.dist_area_executive_id'),
                    ));

                    $tso_id = array_keys($dist_tso_info);
                } else {
                    $dist_tso_info = $this->DistTso->find('first', array(
                        'conditions' => array('DistTso.user_id' => $user_id),
                        'recursive' => -1,
                    ));
                    $tso_id = $dist_tso_info['DistTso']['id'];
                }
                $tso_conditions = array('DistTsoHistory.tso_id' => $tso_id);
                $page_conditions = array('DistTsoHistory.tso_id' => $tso_id);
            }
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));

        /***** TSO Start  ******/
        $tso_conditions['DistTsoHistory.office_id'] = array_keys($offices); // this condition added by Naser because showing all Routes if area office login.
        $tso = $this->DistTsoHistory->find('all', array('conditions' => $tso_conditions, 'fields' => array('DISTINCT DistTsoHistory.tso_id'), 'recursive' => 0));

        if (!empty($tso)) {
            $dist_tso_ids = array();
            foreach ($tso as $key => $value) {
                $dist_tso_ids[] = $value['DistTsoHistory']['tso_id'];
            }

            $distTso = $this->DistTso->find('all', array(
                'conditions' => array(
                    'DistTso.id' => $dist_tso_ids
                ),
                'order' => 'DistTso.name asc',
                'recursive' => 0
            ));

            foreach ($distTso as $key => $value) {
                $id = $value['DistTso']['id'];
                $distTsos[$id] = $value['DistTso']['name'];
            }
        }

        /***** TSO End  ******/


        /***** Distributor Start  ******/
        /*
        $dist=$this->DistTsoMappingHistory->find('all',array('fields' => array('DISTINCT DistTsoMappingHistory.dist_distributor_id'),'recursive' =>0));
       
        if(!empty($dist))
        {
              $dist_ids=array();
              foreach ($dist as $key => $value) {
                  $dist_ids[]=$value['DistTsoMappingHistory']['dist_distributor_id'];
              }
              
              $dist_data = $this->DistDistributor->find('all', array('conditions' => array(
                 'DistDistributor.id' => $dist_ids
             ),
             'order' => 'DistDistributor.name asc',
             'recursive' =>0));
              
         foreach ($dist_data as $key => $value) {
             $id=$value['DistDistributor']['id'];
             $distDistributors[$id]=$value['DistDistributor']['name'];
         }
              
        }
        */
        /***** Distributor End  ******/


        if ($this->request->is('post')) {
            $office_id = (isset($this->request->data['DistTso']['office_id'])) ? $this->request->data['DistTso']['office_id'] : 0;
            $dist_tso_id = (isset($this->request->data['DistTso']['dist_tso_id'])) ? $this->request->data['DistTso']['dist_tso_id'] : 0;
            // $dist_distributor_id=(isset($this->request->data['DistTsoMapping']['dist_distributor_id']))?$this->request->data['DistTsoMapping']['dist_distributor_id']:0;
            $dist_date_from = date("Y-m-d H:i:s", strtotime($this->request->data['DistTso']['date_from']));
            $dist_date_to = date("Y-m-d H:i:s", strtotime($this->request->data['DistTso']['date_to']));

            if ($office_id) {
                if ($user_group_id == 1029 || $user_group_id == 1028) {
                    $con = array('DistTso.id' => $tso_id);
                } else {
                    $con = array('DistTso.office_id' => $office_id);
                }
                $distTsos = $this->DistTso->find('list', array(
                    'conditions' => $con,
                    'order' => 'DistTso.name ASC',
                    'recursive' => -1
                ));
            }




            if ($dist_tso_id) {
                $page_conditions['DistTsoHistory.tso_id'] = $dist_tso_id;
            }

            if ($dist_date_from) {
                $page_conditions['DistTsoHistory.effective_date >='] = $dist_date_from;
            }

            if ($dist_date_to) {

                $page_conditions['case 
                    when DistTsoHistory.effective_end_date is not null then 
                     DistTsoHistory.effective_end_date
                    else 
                    convert(varchar(10),getDate(), 120)
                    end <='] = $dist_date_to;
            }

            if ($office_id) {
                $page_conditions['OR'] = array(array('DistTsoHistory.office_id' => $office_id), array('DistTsoHistory.pre_office_id' => $office_id));
            } else {
                $page_conditions['OR'] = array(array('DistTsoHistory.office_id' => array_keys($offices)), array('DistTsoHistory.pre_office_id' => array_keys($offices)));
            }


            //$page_conditions['OR']=array(array('DistTsoHistory.is_added'=>1),array('DistTsoHistory.is_transfer'=>1));




        }

        if (!$page_conditions) {
            $page_conditions['OR'] = array(array('DistTsoHistory.office_id' => array_keys($offices)), array('DistTsoHistory.pre_office_id' => array_keys($offices)));
        }
        $this->paginate = array(
            'conditions' => $page_conditions,
            'recursive' => 0
        );

        $data = $this->paginate('DistTsoHistory');
        // echo $this->DistTsoHistory->getLastQuery();
        // pr($data);exit;

        $this->loadModel('User');

        $users_data = $this->User->find('all', array(
            'fields' => array('User.id', 'User.user_group_id', 'User.username', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id'),
            'conditions' => array('User.active' => 1, 'User.user_group_id' => array(1, 2, 3, 5, 7, 1017, 1018, 1020)),
            'joins' => array(
                array(
                    'table' => 'sales_people',
                    'alias' => 'SalesPerson',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'User.sales_person_id = SalesPerson.id',
                    )
                )
            ),
            'order' => array('SalesPerson.name' => 'asc'),
            'recursive' => -1
        ));

        $users = array();
        foreach ($users_data as $key => $value) {
            $u_id = $value['User']['id'];
            $u_name = $value['SalesPerson']['name'];
            $users[$u_id] = $u_name;
        }



        $this->set(compact('data', 'distTsos', 'distDistributors', 'office_id', 'dist_tso_id', 'dist_distributor_id', 'offices', 'users'));
    }
    public function get_territory_list()
    {
        $this->loadModel('Territory');
        $office_id = $this->request->data['office_id'];
        $territory_list = $this->Territory->find('list', array(
            'conditions' => array('Territory.office_id' => $office_id, 'Territory.is_active' => 1),
            'order' => array('name' => 'asc'),
            'recursive' => -1
        ));
        $output = "<option value=''>--- Select ---</option>";
        foreach ($territory_list as $id => $name) {
            $output .= "<option value='$id'>$name</option>";
        }
        echo $output;
        $this->autoRender = false;
    }

    public function download_xl()
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes
        $params = $this->request->query['data'];
        $dist_conditions = array();
        $mapped_tso_conditions = array();

        $this->loadModel('Office');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_parent_id = $this->UserAuth->getOfficeParentId();

        if ($office_parent_id == 0) {
            $tso_conditions = array('Office.office_type_id' => 2);
        } else {
            if ($user_group_id == 1029 || $user_group_id == 1028) {
                if ($user_group_id == 1028) {
                    $dist_ae_info = $this->DistAreaExecutive->find('first', array(
                        'conditions' => array('DistAreaExecutive.user_id' => $user_id, 'DistAreaExecutive.is_active' => 1),
                        'recursive' => -1,
                    ));
                    $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];

                    $tso_conditions = array('DistTso.dist_area_executive_id' => $dist_ae_id);
                } else {
                    $tso_conditions = array('DistTso.user_id' => $user_id, 'DistTso.is_active' => 1);
                }
            } else {
                $tso_conditions = $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
            }
        }

        //adding db_code or name searching condition----------
        if (!empty($params['DistTso']['office_id'])) {
            $tso_conditions['DistTso.office_id'] = $params['DistTso']['office_id'];
        }
        if (!empty($params['DistTso']['name'])) {
            $tso_conditions['DistTso.name LIKE'] = '%' . $params['DistTso']['name'] . '%';
        }
        if (!empty($params['DistTso']['status'])) {
            $tso_conditions['DistTso.is_active'] = ($params['DistTso']['status'] == 1 ? 1 : 0);
        }
        $this->DistTso->unbindModel(
            array('belongsTo' => array('DistAreaExecutive', 'User'))
        );
        $this->DistTso->virtualFields = array(
			
			'office_order' => 'Office.order',
			
		);
        $disttso = $this->DistTso->find('all', array(
            'conditions' => $tso_conditions,
            // 'order' => array('DistTso.name'),
            'order' => array( 'office_order' => 'ASC','DistTso.name' => 'ASC'),
            'recursive' => 0
        ));

        $table = '<table border="1"><tbody>
        <tr>
            <td>Id</td>
            <td>Name</td>
            <td>Office</td>
            <td>Address</td>
            <td style="mso-number-format:\'\@\';">Mobile Number</td>
            <td>Status</td>
        </tr>
        ';
        /* pr($disttso);
        exit; */
        foreach ($disttso as $dis_data) {


            if ($dis_data['DistTso']['is_active'] == 1) {
                $status = 'Active';
            } else {
                $status = 'In-Active';
            }

            $table .= '<tr>
                    <td>' . $dis_data['DistTso']['id'] . '</td>
                    <td>' . $dis_data['DistTso']['name'] . '</td>
                    <td>' . $dis_data['Office']['office_name'] . '</td>
                    <td>' . $dis_data['DistTso']['address'] . '</td>
                    <td style="mso-number-format:\'\@\';">' . $dis_data['DistTso']['mobile_number'] . '</td>
                    <td>' . $status . '</td>
                </tr>
                ';
        }
        $table .= '</tbody></table>';

        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment; filename="DistTsos.xls"');
        header("Cache-Control: ");
        header("Pragma: ");
        header("Expires: 0");
        echo $table;
        $this->autoRender = false;
    }
}
