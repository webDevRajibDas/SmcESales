<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'php-excel-reader/excel_reader2');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistDistributorsController extends AppController
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

        $this->set('page_title', 'Distributors List');

        $this->loadModel('Office');
        $this->loadModel('Outlet');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistAreaExecutive');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        if ($office_parent_id == 0) {
            $conditions = array('office_type_id' => 2);
            $dist_conditions = array('office_type_id' => 2, 'DistDistributor.is_active' => 1);
        } else {
            $conditions = array('office_type_id' => 2, 'Office.id' => $this->UserAuth->getOfficeId());
            if ($user_group_id == 1029 || $user_group_id == 1028) {
                if ($user_group_id == 1028) {
                    $dist_ae_info = $this->DistAreaExecutive->find('first', array(
                        'conditions' => array('DistAreaExecutive.user_id' => $user_id, 'DistAreaExecutive.is_active' => 1),
                        'recursive' => -1,
                    ));
                    $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                    $dist_tso_info = $this->DistTso->find('list', array(
                        'conditions' => array('dist_area_executive_id' => $dist_ae_id),
                        'fields' => array('DistTso.id', 'DistTso.dist_area_executive_id'),
                    ));

                    $dist_tso_id = array_keys($dist_tso_info);
                } else {
                    $dist_tso_info = $this->DistTso->find('first', array(
                        'conditions' => array('DistTso.user_id' => $user_id, 'DistTso.is_active' => 1),
                        'recursive' => -1,
                    ));
                    $dist_tso_id = $dist_tso_info['DistTso']['id'];
                }

                $tso_dist_list = $this->DistTsoMapping->find('list', array(
                    'conditions' => array(
                        'dist_tso_id' => $dist_tso_id,
                    ),
                    'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
                ));

                $dist_conditions = array('DistDistributor.is_active' => 1, 'DistDistributor.id' => array_keys($tso_dist_list));
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
                $dist_conditions = array('DistDistributor.is_active' => 1, 'DistDistributor.id' => $distributor_id);
            } else {
                $dist_conditions = array('office_type_id' => 2, 'DistDistributor.is_active' => 1, 'Office.id' => $this->UserAuth->getOfficeId());
            }
        }
        //pr($conditions);die();
        $this->DistDistributor->virtualFields = array(

            'office_order' => 'Office.order',

        );
        $this->paginate = array(
            'conditions' => $dist_conditions,
            'fields' => array('DistDistributor.*', 'Office.*', 'DistOutletMap.*', 'DistTso.name', 'DistAE.name'),
            'joins' => array(

                array(
                    'table' => 'dist_tso_mappings',
                    'alias' => 'DistTsoMapping',
                    'type' => 'LEFT',
                    'conditions' => 'DistTsoMapping.dist_distributor_id = DistDistributor.id'

                ),
                array(
                    'table' => 'dist_tsos',
                    'alias' => 'DistTso',
                    'type' => 'LEFT',
                    'conditions' => 'DistTso.id = DistTsoMapping.dist_tso_id'

                ),
                array(
                    'table' => 'dist_area_executives',
                    'alias' => 'DistAE',
                    'type' => 'LEFT',
                    'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'

                ),




            ),
            'recursive' => 1,
            'order' => array('office_order' => 'ASC')
            // 'order' => array('DistDistributor.name' => 'ASC')
        );

        // pr($this->paginate()); exit;
        $this->set('territories', $this->paginate());


        $outlets = $this->Outlet->find('list', array(
            'conditions' => array('Outlet.category_id' => 17),
            'recursive' => -1
        ));

        $this->set('outlets', $outlets);


        $this->loadModel('DistTsoMapping');
        $mapped_tso = $this->DistTsoMapping->find('all', array(
            'recursive' => 0
        ));

        $mapped_tsos = array();

        foreach ($mapped_tso as $key => $value) {
            $idd = $value['DistTsoMapping']['dist_distributor_id'];
            $mapped_tsos[$idd] = $value['DistTso']['name'];
        }

        $this->set('mapped_tsos', $mapped_tsos);

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
        $this->set('page_title', 'Distributors Details');
        if (!$this->DistDistributor->exists($id)) {
            throw new NotFoundException(__('Invalid territory'));
        }
        $options = array('conditions' => array('DistDistributor.' . $this->DistDistributor->primaryKey => $id));
        $this->set('territory', $this->DistDistributor->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add()
    {
        $this->set('page_title', 'Add Distributor');
        if ($this->request->is('post')) {
            $this->request->data['DistDistributor']['created_at'] = $this->current_datetime();
            $this->request->data['DistDistributor']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistDistributor']['updated_at'] = $this->current_datetime();
            $this->DistDistributor->create();
            if ($this->DistDistributor->save($this->request->data)) {
                if (!empty($this->request->data['thana_id'])) {
                    $thana_array = array();
                    foreach ($this->request->data['thana_id'] as $key => $val) {
                        $data['thana_id'] = $val;
                        $data['dist_distributor_id'] = $this->DistDistributor->id;
                        $data['updated_at'] = $this->current_datetime();
                        $thana_array[] = $data;
                    }
                    $this->loadModel('ThanaTerritory');
                    $this->ThanaTerritory->saveAll($thana_array);
                }


                //create a store automatically in store table
                $this->request->data['DistStore']['name'] = $this->request->data['DistDistributor']['name'];
                $this->request->data['DistStore']['store_type_id'] = 4;
                $this->request->data['DistStore']['office_id'] = $this->request->data['DistDistributor']['office_id'];
                $this->request->data['DistStore']['dist_distributor_id'] = $this->DistDistributor->getLastInsertID();

                $this->request->data['DistStore']['created_at'] = $this->current_datetime();
                $this->request->data['DistStore']['updated_at'] = $this->current_datetime();
                $this->request->data['DistStore']['created_by'] = $this->UserAuth->getUserId();
                $this->request->data['DistStore']['updated_by'] = $this->UserAuth->getUserId();


                $this->loadModel('DistStore');
                $this->DistStore->create();
                $this->DistStore->save($this->request->data);

                $this->Session->setFlash(__('The Distributor has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                //$this->Session->setFlash(__('The territory could not be saved. Please, try again.'), 'flash/error');
            }
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('office_type_id' => 2);
            //$OfficeConditions = array();
        } else {
            $conditions = array('office_type_id' => 2, 'Office.id' => $this->UserAuth->getOfficeId());
            //$OfficeConditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));


        $this->loadModel('District');
        $districts = $this->District->find('list', array('order' => array('District.name' => 'asc')));
        $this->set(compact('offices', 'districts'));
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
        $this->set('page_title', 'Edit Distributor');
        $this->DistDistributor->id = $id;
        if (!$this->DistDistributor->exists($id)) {
            throw new NotFoundException(__('Invalid Distributor'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['DistDistributor']['updated_by'] = $this->UserAuth->getUserId();
            if ($this->DistDistributor->save($this->request->data)) {
                if (!empty($this->request->data['thana_id'])) {
                    $thana_array = array();
                    foreach ($this->request->data['thana_id'] as $key => $val) {
                        $data['thana_id'] = $val;
                        $data['territory_id'] = $id;
                        $data['updated_at'] = $this->current_datetime();
                        $thana_array[] = $data;
                    }
                    $this->loadModel('ThanaTerritory');
                    $this->ThanaTerritory->saveAll($thana_array);
                }
                $this->Session->setFlash(__('The Distributor has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $options = array('conditions' => array('DistDistributor.' . $this->DistDistributor->primaryKey => $id));
            $this->request->data = $this->DistDistributor->find('first', $options);
        }
        //$offices = $this->Territory->Office->find('list');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('office_type_id' => 2);
            //$OfficeConditions = array();
        } else {
            $conditions = array('office_type_id' => 2, 'Office.id' => $this->UserAuth->getOfficeId());
            //$OfficeConditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));

        $this->loadModel('District');
        $this->loadModel('ThanaTerritory');
        $districts = $this->District->find('list', array('order' => array('District.name' => 'asc')));
        $thanas = $this->ThanaTerritory->find('all', array('conditions' => array('ThanaTerritory.territory_id' => $id)));
        $this->set(compact('offices', 'districts', 'thanas'));

        $has_node = 0;
        if ($this->check_distributor_child($id)) {
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
        $this->DistDistributor->id = $id;
        if (!$this->DistDistributor->exists()) {
            throw new NotFoundException(__('Invalid Distributor'));
        }

        /*         * ***************************** Check foreign key *************************** */
        if ($this->DistDistributor->checkForeignKeys("dist_distributors", $id)) {
            $this->Session->setFlash(__('This Distributor has transactions'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }


        if ($this->DistDistributor->delete()) {
            $this->Session->setFlash(__('Distributor deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }


        $this->Session->setFlash(__('Distributor was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }

    public function admin_delete_thana($id = null, $territory_id = null)
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->loadModel('ThanaTerritory');
        $this->ThanaTerritory->id = $id;
        if (!$this->ThanaTerritory->exists()) {
            throw new NotFoundException(__('Invalid Distributor'));
        }
        if ($this->ThanaTerritory->delete()) {
            $this->Session->setFlash(__('Thana deleted'), 'flash/success');
            $this->redirect(array('action' => 'edit/' . $territory_id));
        }
    }

    public function admin_get_thana()
    {
        $this->loadModel('Thana');
        $district_id = $this->request->data['district_id'];
        $rs = array(array('id' => '', 'name' => '---- Select Thana -----'));
        $thana_list = $this->Thana->find('all', array(
            'fields' => array('Thana.id', 'Thana.name'),
            'conditions' => array(
                'Thana.district_id' => $district_id
            ),
            'order' => array('Thana.name' => 'asc'),
            'recursive' => -1
        ));
        $data_array = Set::extract($thana_list, '{n}.Thana');
        if (!empty($thana_list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_dist_distributor_list()
    {
        $rs = array(array('id' => '', 'name' => '---- All -----'));
        $office_id = $this->request->data['office_id'];
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
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

                $dist_tso_id = array_keys($dist_tso_info);
            } else {
                $dist_tso_info = $this->DistTso->find('first', array(
                    'conditions' => array('DistTso.user_id' => $user_id),
                    'recursive' => -1,
                ));
                $dist_tso_id = $dist_tso_info['DistTso']['id'];
            }

            $tso_dist_list = $this->DistTsoMapping->find('list', array(
                'conditions' => array(
                    'dist_tso_id' => $dist_tso_id,
                ),
                'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
            ));
            $dist_conditions = array('DistDistributor.id' => array_keys($tso_dist_list), 'DistDistributor.is_active' => 1);
        } elseif ($user_group_id == 1034) {
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');
            $distributor = $this->DistUserMapping->find('first', array(
                'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
            $dist_conditions = array('DistDistributor.is_active' => 1, 'DistDistributor.id' => $distributor_id);
        } else {
            $dist_conditions = array('DistDistributor.office_id' => $office_id, 'DistDistributor.is_active' => 1);
        }

        $distDistributors = $this->DistDistributor->find('all', array(
            'fields' => array('DistDistributor.id', 'DistDistributor.name'),
            'conditions' => $dist_conditions,
            'order' => array('DistDistributor.name' => 'asc'),
            'recursive' => 0
        ));

        $data_array = array();

        foreach ($distDistributors as $key => $value) {
            $data_array[] = array(
                'id' => $value['DistDistributor']['id'],
                'name' => $value['DistDistributor']['name'],
            );
        }

        if (!empty($distDistributors)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_sr_list()
    {
        $rs = array(array('id' => '', 'name' => '---- All -----'));
        $office_id = $this->request->data['office_id'];
        $this->loadModel('DistSalesRepresentative');
        $distDistributors = $this->DistSalesRepresentative->find('all', array(
            'fields' => array('DistSalesRepresentative.id', 'DistSalesRepresentative.name'),
            'conditions' => array('DistSalesRepresentative.office_id' => $office_id),
            'order' => array('DistSalesRepresentative.name' => 'asc'),
            'recursive' => 0
        ));

        $data_array = array();

        foreach ($distDistributors as $key => $value) {
            $data_array[] = array(
                'id' => $value['DistSalesRepresentative']['id'],
                'name' => $value['DistSalesRepresentative']['name'],
            );
        }

        if (!empty($distDistributors)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function download_xl()
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes
        $params = $this->request->query['data'];

        $this->loadModel('Office');
        $this->loadModel('DistDistributor');

        $dist_conditions = array();
        $mapped_tso_conditions = array();
        $this->loadModel('Office');
        $this->loadModel('Outlet');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistAreaExecutive');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        if ($office_parent_id == 0) {
            $dist_conditions = array('office_type_id' => 2, 'DistDistributor.is_active' => 1);
            $mapped_tso_conditions = array('DistTso.office_id' => $this->UserAuth->getOfficeId(), 'DistTso.is_active' => 1);
        } else {
            $mapped_tso_conditions = array('DistTso.is_active' => 1);
            if ($user_group_id == 1029 || $user_group_id == 1028) {
                if ($user_group_id == 1028) {
                    $dist_ae_info = $this->DistAreaExecutive->find('first', array(
                        'conditions' => array('DistAreaExecutive.user_id' => $user_id, 'DistAreaExecutive.is_active' => 1),
                        'recursive' => -1,
                    ));
                    $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                    $dist_tso_info = $this->DistTso->find('list', array(
                        'conditions' => array('dist_area_executive_id' => $dist_ae_id),
                        'fields' => array('DistTso.id', 'DistTso.dist_area_executive_id'),
                    ));

                    $dist_tso_id = array_keys($dist_tso_info);
                } else {
                    $dist_tso_info = $this->DistTso->find('first', array(
                        'conditions' => array('DistTso.user_id' => $user_id, 'DistTso.is_active' => 1),
                        'recursive' => -1,
                    ));
                    $dist_tso_id = $dist_tso_info['DistTso']['id'];
                }

                $tso_dist_list = $this->DistTsoMapping->find('list', array(
                    'conditions' => array(
                        'dist_tso_id' => $dist_tso_id,
                    ),
                    'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
                ));

                $dist_conditions = array('DistDistributor.is_active' => 1, 'DistDistributor.id' => array_keys($tso_dist_list));
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
                $dist_conditions = array('DistDistributor.is_active' => 1, 'DistDistributor.id' => $distributor_id);
            } else {
                $dist_conditions = array('office_type_id' => 2, 'DistDistributor.is_active' => 1, 'Office.id' => $this->UserAuth->getOfficeId());
            }
        }
        //adding db_code or name searching condition----------

        if (!empty($params['DistDistributor']['name'])) {
            $dist_conditions[] = array(
                'OR' => array(
                    'DistDistributor.name like' => "%" . $params['DistDistributor']['name'] . "%",
                    'DistDistributor.db_code like' => "%" . $params['DistDistributor']['name'] . "%"
                )
            );
            unset($dist_conditions['DistDistributor.is_active']);
        }
        if (!empty($params['DistDistributor']['office_id'])) {
            $dist_conditions['DistDistributor.office_id'] = $params['DistDistributor']['office_id'];
            unset($dist_conditions['DistDistributor.is_active']);
        }
        if (!empty($params['DistDistributor']['status'])) {
            $dist_conditions['DistDistributor.is_active'] = ($params['DistDistributor']['status'] == 1 ? 1 : 0);
        }

        $this->DistDistributor->virtualFields = array(

            'office_order' => 'Office.order',

        );
        $distributors = $this->DistDistributor->find('all', array(
            'conditions' => $dist_conditions,
            'fields' => array('DistDistributor.*', 'Office.*', 'DistOutletMap.*', 'DistTso.name', 'DistAE.name'),
            'joins' => array(

                array(
                    'table' => 'dist_tso_mappings',
                    'alias' => 'DistTsoMapping',
                    'type' => 'LEFT',
                    'conditions' => 'DistTsoMapping.dist_distributor_id = DistDistributor.id'

                ),
                array(
                    'table' => 'dist_tsos',
                    'alias' => 'DistTso',
                    'type' => 'LEFT',
                    'conditions' => 'DistTso.id = DistTsoMapping.dist_tso_id'

                ),
                array(
                    'table' => 'dist_area_executives',
                    'alias' => 'DistAE',
                    'type' => 'LEFT',
                    'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'

                ),




            ),
            // 'order' => array('DistDistributor.name'),
            'order' => array('office_order' => 'ASC'),
            'recursive' => 1
        ));

        $this->DistTsoMapping->unbindModel(
            array('belongsTo' => array('Office', 'DistDistributor'))
        );
        $mapped_tso = $this->DistTsoMapping->find('all', array(
            'recursive' => 0,
            'fields' => array('DistTso.*', 'DistTsoMapping.dist_distributor_id'),
            'conditions' => $mapped_tso_conditions
        ));
        $mapped_tsos = array();

        foreach ($mapped_tso as $key => $value) {
            $idd = $value['DistTsoMapping']['dist_distributor_id'];
            $mapped_tsos[$idd] = $value['DistTso']['name'];
        }

        $table = '<table border="1"><tbody>
        <tr>
            <td>Id</td>
            <td>Name</td>
            <td>DB Code</td>
            <td>Office</td>
            <td>Area Executive</td>
            <td>Mapped TSO</td>
            <td>Address</td>
            <td>Mobile Number</td>
            <td>Status</td>
        </tr>
        ';

        foreach ($distributors as $dis_data) {

            $distributor_id = $dis_data['DistDistributor']['id'];
            $distributor_name = $dis_data['DistDistributor']['name'];
            $distributor_code = $dis_data['DistDistributor']['db_code'];
            $office_name = $dis_data['Office']['office_name'];
            $distributor_address = $dis_data['DistDistributor']['address'];
            $distributor_mobile = $dis_data['DistDistributor']['mobile_number'];
            if ($dis_data['DistDistributor']['is_active'] == 1) {
                $status = 'Active';
            } else {
                $status = 'In-Active';
            }

            $tso = $dis_data['DistTso']['name'];
            $area_executive = $dis_data['DistAE']['name'];

            $table .= '<tr>
                    <td>' . $distributor_id . '</td>
                    <td>' . $distributor_name . '</td>
                    <td>' . $distributor_code . '</td>
                    <td>' . $office_name . '</td>
                    <td>' . $area_executive . '</td>
                    <td>' . $tso . '</td>
                    <td>' . $distributor_address . '</td>
                    <td style="mso-number-format:\'\@\';">' . $distributor_mobile . '</td>
                    <td>' . $status . '</td>
                </tr>
                ';
        }

        $table .= '</tbody></table>';
        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment; filename="Distributor.xls"');
        header("Cache-Control: ");
        header("Pragma: ");
        header("Expires: 0");
        echo $table;
        $this->autoRender = false;
    }


    public function check_distributor_child($id)
    {



        /***********************  check TSO Mapping start ****************************************/

        $this->loadModel('DistTsoMapping');
        $conditions = array('DistTsoMapping.dist_distributor_id' => $id);
        $existingCount = $this->DistTsoMapping->find('count', array('conditions' => $conditions, 'recursive' => -1));
        if ($existingCount) {
            return 1;
        }
        /***********************  check TSO Mapping End ****************************************/

        /***********************  check Route mapping start ****************************************/

        $this->loadModel('DistRouteMapping');
        $conditions2 = array('DistRouteMapping.dist_distributor_id' => $id);
        $existingCount2 = $this->DistRouteMapping->find('count', array('conditions' => $conditions2, 'recursive' => -1));
        if ($existingCount2) {
            return 2;
        }

        /***********************  check Route mapping End ****************************************/

        /***********************  check SR start ****************************************/

        $this->loadModel('DistSalesRepresentative');
        $conditions3 = array('DistSalesRepresentative.dist_distributor_id' => $id);
        $existingCount3 = $this->DistSalesRepresentative->find('count', array('conditions' => $conditions3, 'recursive' => -1));
        if ($existingCount3) {
            return 3;
        }

        /***********************  check SR End ****************************************/
        return 0;
    }


    public function admin_distributor_transfer()
    {

        $this->set('page_title', 'Distributor Trnasfer');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('office_type_id' => 2);
            $distributor_conditions = array('is_active' => 1);
        } else {
            $Office_id = $this->UserAuth->getOfficeId();
            $conditions = array('office_type_id' => 2, 'Office.id' => $Office_id);
            $distributor_conditions = array('office_id' => $Office_id, 'is_active' => 1);
        }
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $this->set(compact('offices'));

        $distributor_list = $this->DistDistributor->find('list', array('conditions' => $distributor_conditions,));

        $this->set(compact('distributor_list'));

        if ($this->request->is('post')) {
            //pr($this->request->data);die();
            $office_id = $this->request->data['DistDistributor']['office_id'];
            $distributor_id_to = $this->request->data['DistDistributor']['distributor_id_to'];
            $distributor_id_from = $this->request->data['DistDistributor']['distributor_id_from'];


            $new_sr = array();
            $new_dm = array();
            /****************************************Start Dist Tso Mapping History*************************************************/
            $this->loadModel('DistTsoMappingHistory');
            $dist_tso_mapping_histories_info = $this->DistTsoMappingHistory->find('all', array(
                'conditions' => array('DistTsoMappingHistory.dist_distributor_id' => $distributor_id_from, 'DistTsoMappingHistory.active' => 1),
                'recursive' => -1,
            ));

            $dist_tso_mapping_histories_array = $dist_tso_mapping_histories_info;
            if (!empty($dist_tso_mapping_histories_info)) {
                /* foreach ($dist_tso_mapping_histories_info as $key => $value) {
                    $dist_tso_mapping_histories_info[$key]['DistTsoMappingHistory']['dist_distributor_id'] = $distributor_id_to;
                    $dist_tso_mapping_histories_info[$key]['DistTsoMappingHistory']['updated_at'] = $this->current_datetime();
                    $dist_tso_mapping_histories_info[$key]['DistTsoMappingHistory']['updated_by'] = $this->UserAuth->getUserId();

                    unset($dist_tso_mapping_histories_info[$key]['DistTsoMappingHistory']['id']);
                }

                $this->DistTsoMappingHistory->create();
                if($this->DistTsoMappingHistory->saveAll($dist_tso_mapping_histories_info)){ */
                foreach ($dist_tso_mapping_histories_array as $key => $value) {
                    $dist_tso_mapping_histories_array[$key]['DistTsoMappingHistory']['active'] = 0;
                    $dist_tso_mapping_histories_array[$key]['DistTsoMappingHistory']['end_date'] = $this->current_date();
                    $dist_tso_mapping_histories_array[$key]['DistTsoMappingHistory']['updated_at'] = $this->current_datetime();
                    $dist_tso_mapping_histories_array[$key]['DistTsoMappingHistory']['updated_by'] = $this->UserAuth->getUserId();
                }
                $this->DistTsoMappingHistory->saveAll($dist_tso_mapping_histories_array);
                // }
            }

            /****************************************end Dist Tso Mapping History*********************************************/
            /****************************************Start Dist Sales Representative************************************************************/

            $this->loadModel('DistSalesRepresentative');
            $dist_sale_representative_info = $this->DistSalesRepresentative->find('all', array(
                'conditions' => array('DistSalesRepresentative.dist_distributor_id' => $distributor_id_from),
                'recursive' => -1,
            ));
            $dist_sale_representative_array = $dist_sale_representative_info;

            if (!empty($dist_sale_representative_info)) {
                $all_sr_save = 1;
                foreach ($dist_sale_representative_info as $key => $value) {
                    if (!$value['DistSalesRepresentative']['mobile_number']) {

                        $dist_sale_representative_info[$key]['DistSalesRepresentative']['mobile_number'] = '00001';
                    }
                    $dist_sale_representative_info[$key]['DistSalesRepresentative']['dist_distributor_id'] = $distributor_id_to;
                    $dist_sale_representative_info[$key]['DistSalesRepresentative']['updated_at'] = $this->current_datetime();
                    $dist_sale_representative_info[$key]['DistSalesRepresentative']['updated_by'] = $this->UserAuth->getUserId();

                    unset($dist_sale_representative_info[$key]['DistSalesRepresentative']['id']);
                    $this->DistSalesRepresentative->create();
                    if (!$this->DistSalesRepresentative->save($dist_sale_representative_info[$key])) {
                        $all_sr_save = 0;
                    } else {
                        $new_sr[$value['DistSalesRepresentative']['id']] = $this->DistSalesRepresentative->getInsertID();
                    }
                }
                if ($all_sr_save) {
                    foreach ($dist_sale_representative_array as $key => $value) {
                        $dist_sale_representative_array[$key]['DistSalesRepresentative']['is_active'] = 0;
                    }
                    $this->DistSalesRepresentative->saveAll($dist_sale_representative_array);
                }
            }

            /****************************************end Dist Sales Representative************************************************************/
            /****************************************Start DistDelivery Man************************************************************/

            $this->loadModel('DistDeliveryMan');
            $dist_delivery_man_info = $this->DistDeliveryMan->find('all', array(
                'conditions' => array('DistDeliveryMan.dist_distributor_id' => $distributor_id_from),
                'recursive' => -1,
            ));
            $dist_delivery_man_array = $dist_delivery_man_info;
            if (!empty($dist_delivery_man_info)) {
                foreach ($dist_delivery_man_info as $key => $value) {
                    if (!$value['DistDeliveryMan']['mobile_number']) {

                        $dist_delivery_man_info[$key]['DistDeliveryMan']['mobile_number'] = '00001';
                    }
                    $dist_delivery_man_info[$key]['DistDeliveryMan']['dist_distributor_id'] = $distributor_id_to;
                    $dist_delivery_man_info[$key]['DistDeliveryMan']['updated_at'] = $this->current_datetime();
                    $dist_delivery_man_info[$key]['DistDeliveryMan']['updated_by'] = $this->UserAuth->getUserId();

                    unset($dist_delivery_man_info[$key]['DistDeliveryMan']['id']);
                }
                $this->DistDeliveryMan->create();
                if ($this->DistDeliveryMan->saveAll($dist_delivery_man_info)) {
                    foreach ($dist_sale_representative_array as $key => $value) {
                        $dist_delivery_man_array[$key]['DistDeliveryMan']['is_active'] = 0;
                    }
                    $this->DistDeliveryMan->saveAll($dist_delivery_man_array);
                }
            }
            /****************************************end DistDelivery Man************************************************************/

            /****************************************Start Dist Sr Route Mapping************************************************************/

            $this->loadModel('DistSrRouteMapping');
            $dist_sr_route_mapping_info = $this->DistSrRouteMapping->find('all', array(
                'conditions' => array('DistSrRouteMapping.dist_distributor_id' => $distributor_id_from),
                'recursive' => -1,
            ));
            if (!empty($dist_sr_route_mapping_info)) {
                foreach ($dist_sr_route_mapping_info as $key => $value) {
                    $dist_sr_route_mapping_info[$key]['DistSrRouteMapping']['dist_distributor_id'] = $distributor_id_to;
                    $dist_sr_route_mapping_info[$key]['DistSrRouteMapping']['dist_sr_id'] = $new_sr[$value['DistSrRouteMapping']['dist_sr_id']];
                    $dist_sr_route_mapping_info[$key]['DistSrRouteMapping']['updated_at'] = $this->current_datetime();
                    $dist_sr_route_mapping_info[$key]['DistSrRouteMapping']['updated_by'] =  $this->UserAuth->getUserId();

                    unset($dist_sr_route_mapping_info[$key]['DistSrRouteMapping']['id']);
                }
                $this->DistSrRouteMapping->create();
                if ($this->DistSrRouteMapping->saveAll($dist_sr_route_mapping_info)) {
                    $this->DistSrRouteMapping->deleteAll(array('DistSrRouteMapping.dist_distributor_id' => $distributor_id_from));
                }
            }


            /****************************************end Dist Sr Route Mapping************************************************************/
            /****************************************Start Dist Sale Target************************************************************/

            $this->loadModel('DistSaleTarget');
            $dist_sale_target_info = $this->DistSaleTarget->find('all', array(
                'conditions' => array('DistSaleTarget.dist_distributor_id' => $distributor_id_from),
                'recursive' => -1,
            ));
            if (!empty($dist_sale_target_info)) {
                foreach ($dist_sale_target_info as $key => $value) {
                    $dist_sale_target_info[$key]['DistSaleTarget']['dist_distributor_id'] = $distributor_id_to;
                    $dist_sale_target_info[$key]['DistSaleTarget']['updated_at'] =  $this->current_datetime();
                    $dist_sale_target_info[$key]['DistSaleTarget']['updated_by'] = $this->UserAuth->getUserId();

                    unset($dist_sale_target_info[$key]['DistSaleTarget']['id']);
                }
                $this->DistSaleTarget->create();
                $this->DistSaleTarget->saveAll($dist_sale_target_info);
            }

            /****************************************end Dist Sale Target************************************************************/
            /****************************************Start Dist Sale Target Month************************************************************/

            $this->loadModel('DistSaleTargetMonth');
            $dist_sale_target_month_info = $this->DistSaleTargetMonth->find('all', array(
                'conditions' => array('DistSaleTargetMonth.dist_distributor_id' => $distributor_id_from),
                'recursive' => -1,
            ));
            if (!empty($dist_sale_target_month_info)) {
                foreach ($dist_sale_target_month_info as $key => $value) {
                    $dist_sale_target_month_info[$key]['DistSaleTargetMonth']['dist_distributor_id'] = $distributor_id_to;
                    $dist_sale_target_month_info[$key]['DistSaleTargetMonth']['updated_at'] = $this->current_datetime();
                    $dist_sale_target_month_info[$key]['DistSaleTargetMonth']['updated_by'] = $this->UserAuth->getUserId();

                    unset($dist_sale_target_month_info[$key]['DistSaleTargetMonth']['id']);
                }
                $this->DistSaleTargetMonth->create();
                $this->DistSaleTargetMonth->saveAll($dist_sale_target_month_info);
            }
            /****************************************end Dist Sale Target Month************************************************************/
            /****************************************Start Dealer Wise Limit************************************************************/

            /*$this->loadModel('DealerWiseLimit');
            $dealer_wise_limit_info = $this->DealerWiseLimit->find('all',array(
                'conditions'=>array('DealerWiseLimit.dist_distributor_id'=>$distributor_id_from),
                'recursive'=> -1,
                ));
            if(!empty($dealer_wise_limit_info)){
                foreach ($dealer_wise_limit_info as $key => $value) {
                    $dealer_wise_limit_info[$key]['DealerWiseLimit']['dist_distributor_id'] = $distributor_id_to;
                    $dealer_wise_limit_info[$key]['DealerWiseLimit']['updated_at'] = $this->current_datetime(); 
                    $dealer_wise_limit_info[$key]['DealerWiseLimit']['updated_by'] = $this->UserAuth->getUserId();
                    
                    unset($dealer_wise_limit_info[$key]['DealerWiseLimit']['id']);
                }
                $this->DealerWiseLimit->create();
                $this->DealerWiseLimit->saveAll($dealer_wise_limit_info);
            }*/
            /****************************************end Dealer Wise Limit************************************************************/
            /*************************************Start Dist Sr Route Mapping History************************************************/

            $this->loadModel('DistSrRouteMappingHistory');
            $dist_sr_route_mapping_history_info = $this->DistSrRouteMappingHistory->find('all', array(
                'conditions' => array(
                    'DistSrRouteMappingHistory.dist_distributor_id' => $distributor_id_from,
                    'DistSrRouteMappingHistory.end_date' => NULL,
                    'DistSrRouteMappingHistory.is_change' => 1,
                    'DistSrRouteMappingHistory.active' => 1
                ),
                'recursive' => -1,
            ));
            $dist_sr_route_mapping_history_array = $dist_sr_route_mapping_history_info;


            if (!empty($dist_sr_route_mapping_history_info)) {
                foreach ($dist_sr_route_mapping_history_info as $key => $value) {
                    $dist_sr_route_mapping_history_info[$key]['DistSrRouteMappingHistory']['dist_distributor_id'] = $distributor_id_to;
                    $dist_sr_route_mapping_history_info[$key]['DistSrRouteMappingHistory']['dist_sr_id'] = $new_sr[$value['DistSrRouteMappingHistory']['dist_sr_id']];
                    $dist_sr_route_mapping_history_info[$key]['DistSrRouteMappingHistory']['effective_date'] = $this->current_date();
                    $dist_sr_route_mapping_history_info[$key]['DistSrRouteMappingHistory']['updated_at'] = $this->current_datetime();
                    $dist_sr_route_mapping_history_info[$key]['DistSrRouteMappingHistory']['updated_by'] = $this->UserAuth->getUserId();

                    unset($dist_sr_route_mapping_history_info[$key]['DistSrRouteMappingHistory']['id']);
                }
                $this->DistSrRouteMappingHistory->create();
                if ($this->DistSrRouteMappingHistory->saveAll($dist_sr_route_mapping_history_info)) {

                    foreach ($dist_sr_route_mapping_history_array as $key => $value) {
                        $dist_sr_route_mapping_history_array[$key]['DistSrRouteMappingHistory']['active'] = 0;
                        $dist_sr_route_mapping_history_array[$key]['DistSrRouteMappingHistory']['end_date'] = $this->current_date();
                    }
                    $this->DistSrRouteMappingHistory->saveAll($dist_sr_route_mapping_history_array);
                }
                // pr($dist_sr_route_mapping_history_info);
            }

            /*************************************end Dist Sr Route Mapping History************************************************/
            /****************************************Start Dist Sr Visit Plan************************************************************/

            $this->loadModel('DistSrVisitPlan');
            $dist_sr_visit_plan_info = $this->DistSrVisitPlan->find('all', array(
                'conditions' => array('DistSrVisitPlan.distributor_id' => $distributor_id_from),
                'recursive' => -1,
            ));
            if (!empty($dist_sr_visit_plan_info)) {
                foreach ($dist_sr_visit_plan_info as $key => $value) {
                    $dist_sr_visit_plan_info[$key]['DistSrVisitPlan']['distributor_id'] = $distributor_id_to;
                    $dist_sr_visit_plan_info[$key]['DistSrVisitPlan']['updated_at'] = $this->current_datetime();
                    $dist_sr_visit_plan_info[$key]['DistSrVisitPlan']['updated_by'] = $this->UserAuth->getUserId();

                    unset($dist_sr_visit_plan_info[$key]['DistSrVisitPlan']['id']);
                }
                $this->DistSrVisitPlan->create();
                $this->DistSrVisitPlan->saveAll($dist_sr_visit_plan_info);
            }
            /****************************************end Dist Sr Visit Plan************************************************************/
            /****************************************Start Dist Outlet Map************************************************************/

            /*$this->loadModel('DistOutletMap');
            $dist_outlet_mapping_info = $this->DistOutletMap->find('all',array(
                'conditions'=>array('DistOutletMap.dist_distributor_id'=>$distributor_id_from),
                'recursive'=> -1,
                ));
            if(!empty($dist_outlet_mapping_info)){
                foreach ($dist_outlet_mapping_info as $key => $value) {
                    $dist_outlet_mapping_info[$key]['DistOutletMap']['dist_distributor_id'] = $distributor_id_to;
                    $dist_outlet_mapping_info[$key]['DistOutletMap']['updated_at'] = $this->current_datetime();
                    $dist_outlet_mapping_info[$key]['DistOutletMap']['updated_by'] = $this->UserAuth->getUserId();
                    
                    unset($dist_outlet_mapping_info[$key]['DistOutletMap']['id']);
                }
                $this->DistOutletMap->create();
                $this->DistOutletMap->saveAll($dist_outlet_mapping_info);
            }*/
            /****************************************end Dist Outlet Map************************************************************/
            /****************************************Start SR Order ************************************************************/
            $this->loadModel('DistOrder');
            $sr_order_list = $this->DistOrder->find('all', array(
                'conditions' => array(
                    'DistOrder.distributor_id' => $distributor_id_from,
                    'DistOrder.status >' => 0,
                    'DistOrder.processing_status <' => 2,
                ),
            ));
            $sr_order_list_info = $sr_order_list;
            if (!empty($sr_order_list)) {
                foreach ($sr_order_list as $key => $value) {
                    $sr_order_list[$key]['DistOrder']['distributor_id'] = $distributor_id_to;
                    $sr_order_list[$key]['DistOrder']['updated_at'] = $this->current_datetime();
                    $sr_order_list[$key]['DistOrder']['updated_by'] = $this->UserAuth->getUserId();

                    unset($sr_order_list[$key]['DistOrder']['id']);
                }
                $this->DistOrder->create();
                if ($this->DistOrder->saveAll($sr_order_list)) {
                    foreach ($sr_order_list_info as $key => $value) {
                        $sr_order_list_info[$key]['DistOrder']['is_active'] = 0;
                        $sr_order_list_info[$key]['DistOrder']['status'] = 3;
                    }
                    $this->DistOrder->saveAll($sr_order_list_info);
                }
            }
            /****************************************end SR Order ************************************************************/

            /****************************************Start Dist Route Mapping************************************************************/

            $this->loadModel('DistRouteMapping');
            $dist_route_mapping_info = $this->DistRouteMapping->find('all', array(
                'conditions' => array('DistRouteMapping.dist_distributor_id' => $distributor_id_from),
                'recursive' => -1,
            ));
            if (!empty($dist_route_mapping_info)) {
                foreach ($dist_route_mapping_info as $key => $value) {
                    $dist_route_mapping_info[$key]['DistRouteMapping']['dist_distributor_id'] = $distributor_id_to;
                    $dist_route_mapping_info[$key]['DistRouteMapping']['updated_at'] = $this->current_datetime();
                    $dist_route_mapping_info[$key]['DistRouteMapping']['updated_by'] = $this->UserAuth->getUserId();

                    unset($dist_route_mapping_info[$key]['DistRouteMapping']['id']);
                }
                $this->DistRouteMapping->create();
                if ($this->DistRouteMapping->saveAll($dist_route_mapping_info)) {

                    $this->DistRouteMapping->deleteAll(array('DistRouteMapping.dist_distributor_id' => $distributor_id_from));
                }
            }
            /****************************************end Dist Route Mapping************************************************************/
            /****************************************Start Dist Route Mapping History*********************************************************/

            $this->loadModel('DistRouteMappingHistory');
            $dist_route_mapping_histories_info = $this->DistRouteMappingHistory->find('all', array(
                'conditions' => array('DistRouteMappingHistory.dist_distributor_id' => $distributor_id_from),
                'recursive' => -1,
            ));
            $dist_route_mapping_histories_array = $dist_route_mapping_histories_info;
            if (!empty($dist_route_mapping_histories_info)) {
                foreach ($dist_route_mapping_histories_info as $key => $value) {
                    $dist_route_mapping_histories_info[$key]['DistRouteMappingHistory']['dist_distributor_id'] = $distributor_id_to;
                    $dist_route_mapping_histories_info[$key]['DistRouteMappingHistory']['active'] = 1;
                    $dist_route_mapping_histories_info[$key]['DistRouteMappingHistory']['effective_date'] = $this->current_date();
                    $dist_route_mapping_histories_info[$key]['DistRouteMappingHistory']['updated_at'] = $this->current_datetime();
                    $dist_route_mapping_histories_info[$key]['DistRouteMappingHistory']['updated_by'] =  $this->UserAuth->getUserId();

                    unset($dist_route_mapping_histories_info[$key]['DistRouteMappingHistory']['id']);
                }
                $this->DistRouteMappingHistory->create();
                if ($this->DistRouteMappingHistory->saveAll($dist_route_mapping_histories_info)) {
                    foreach ($dist_route_mapping_histories_array as $key => $value) {
                        $dist_route_mapping_histories_array[$key]['DistRouteMappingHistory']['active'] = 0;
                        $dist_route_mapping_histories_array[$key]['DistRouteMappingHistory']['end_date'] = $this->current_date();
                    }
                    $this->DistRouteMappingHistory->saveAll($dist_route_mapping_histories_array);
                }
            }
            /****************************************end Dist Route Mapping History*********************************************************/
            /*-------------------------------------- Stock Transfer Process : Start --------------------------------------------------------*/
            if ($this->request->data['DistDistributor']['stock_transfer']) {
                $this->LoadModel('DistCurrentInventory');
                $this->LoadModel('DistStore');
                $this->loadModel('DistInventoryAdjustment');
                $this->loadModel('DistInventoryAdjustmentDetail');

                $store_info_from = $this->DistStore->find('first', array(
                    'conditions' => array('DistStore.dist_distributor_id' => $distributor_id_from),
                    'joins' => array(
                        array(
                            'table' => 'dist_distributors',
                            'alias' => 'DistDistributor',
                            'conditions' => 'DistDistributor.id=DistStore.dist_distributor_id'
                        )
                    ),
                    'fields' => array(
                        'DistStore.id',
                        'DistDistributor.name'
                    ),
                    'recursive' => -1
                ));
                $store_id_from = $store_info_from['DistStore']['id'];
                $db_name_from = $store_info_from['DistDistributor']['name'];


                $store_info_to = $this->DistStore->find('first', array(
                    'conditions' => array('DistStore.dist_distributor_id' => $distributor_id_to),
                    'joins' => array(
                        array(
                            'table' => 'dist_distributors',
                            'alias' => 'DistDistributor',
                            'conditions' => 'DistDistributor.id=DistStore.dist_distributor_id'
                        )
                    ),
                    'fields' => array(
                        'DistStore.id',
                        'DistDistributor.name'
                    ),
                    'recursive' => -1
                ));
                $store_id_to = $store_info_to['DistStore']['id'];
                $db_name_to = $store_info_to['DistDistributor']['name'];

                $prev_db_stock = $this->DistCurrentInventory->find('all', array(
                    'conditions' => array(
                        'DistCurrentInventory.store_id' => $store_id_from,
                        'DistCurrentInventory.qty >' => 0
                    ),
                    'recursive' => -1
                ));

                $adjustment_data_from_db['DistInventoryAdjustment']['office_id'] = $office_id;
                $adjustment_data_from_db['DistInventoryAdjustment']['dist_store_id'] = $store_id_from;
                $adjustment_data_from_db['DistInventoryAdjustment']['distributor_id'] = $distributor_id_from;
                $adjustment_data_from_db['DistInventoryAdjustment']['transaction_type_id'] = 18; // Stock Transfer by DB replace(Out)
                $adjustment_data_from_db['DistInventoryAdjustment']['status'] = 1;
                $adjustment_data_from_db['DistInventoryAdjustment']['remarks'] = "Stock transfer to ($db_name_to) for DB replacing";
                $adjustment_data_from_db['DistInventoryAdjustment']['created_at'] = $this->current_datetime();
                $adjustment_data_from_db['DistInventoryAdjustment']['created_by'] = $this->UserAuth->getUserId();
                $adjustment_data_from_db['DistInventoryAdjustment']['updated_at'] = $this->current_datetime();
                $adjustment_data_from_db['DistInventoryAdjustment']['updated_by'] = $this->UserAuth->getUserId();
                $adjustment_data_from_db['DistInventoryAdjustment']['approval_status'] = 1;

                $this->DistInventoryAdjustment->create();
                $this->DistInventoryAdjustment->save($adjustment_data_from_db);
                $adjustment_from_id = $this->DistInventoryAdjustment->id;


                $adjustment_data_to_db['DistInventoryAdjustment']['office_id'] = $office_id;
                $adjustment_data_to_db['DistInventoryAdjustment']['dist_store_id'] = $store_id_to;
                $adjustment_data_to_db['DistInventoryAdjustment']['distributor_id'] = $distributor_id_to;
                $adjustment_data_to_db['DistInventoryAdjustment']['transaction_type_id'] = 15; // Stock Received by DB replace(In)
                $adjustment_data_to_db['DistInventoryAdjustment']['status'] = 2;
                $adjustment_data_to_db['DistInventoryAdjustment']['remarks'] = "Stock received from ($db_name_from) for DB replacing";
                $adjustment_data_to_db['DistInventoryAdjustment']['created_at'] = $this->current_datetime();
                $adjustment_data_to_db['DistInventoryAdjustment']['created_by'] = $this->UserAuth->getUserId();
                $adjustment_data_to_db['DistInventoryAdjustment']['updated_at'] = $this->current_datetime();
                $adjustment_data_to_db['DistInventoryAdjustment']['updated_by'] = $this->UserAuth->getUserId();
                $adjustment_data_to_db['DistInventoryAdjustment']['approval_status'] = 1;

                $this->DistInventoryAdjustment->create();
                $this->DistInventoryAdjustment->save($adjustment_data_to_db);
                $adjustment_to_id = $this->DistInventoryAdjustment->id;
                $stock_update_array = array();
                $data_array = array();
                foreach ($prev_db_stock as $prev_data) {
                    // Inventory Adjustment details data
                    $data_from['DistInventoryAdjustmentDetail']['dist_inventory_adjustment_id'] = $adjustment_from_id;
                    $data_from['DistInventoryAdjustmentDetail']['dist_current_inventory_id'] = $prev_data['DistCurrentInventory']['id'];
                    $data_from['DistInventoryAdjustmentDetail']['quantity'] = $prev_data['DistCurrentInventory']['qty'];
                    $data_array[] = $data_from;

                    /*----------------- Stock update : Start ---------------------------*/

                    $current_qty = $prev_data['DistCurrentInventory']['qty'];
                    $current_inventory_id = $prev_data['DistCurrentInventory']['id'];

                    $inventory_data['DistCurrentInventory']['id'] = $current_inventory_id;
                    $inventory_data['DistCurrentInventory']['qty'] = 0;
                    $inventory_data['DistCurrentInventory']['transaction_type_id'] = 14;
                    $inventory_data['DistCurrentInventory']['transaction_date'] = $this->current_date();
                    $stock_update_array[] = $inventory_data;
                    unset($inventory_data);
                    /*----------------- Stock update : END ---------------------------*/

                    $CurrentInventory = $this->DistCurrentInventory->find('first', array(
                        'fields' => array('DistCurrentInventory.id', 'DistCurrentInventory.qty'),
                        'conditions' => array(
                            'DistCurrentInventory.store_id' => $store_id_to,
                            'DistCurrentInventory.product_id' => $prev_data['DistCurrentInventory']['product_id']
                        )
                    ));
                    if (empty($CurrentInventory)) {
                        $t_id = 15;
                        $cd = $this->current_date();
                        $this->DistCurrentInventory->query("insert into dist_current_inventories (store_id,inventory_status_id,product_id,qty,updated_at,transaction_date,transaction_type_id)
                        values ($store_id_to,1," . $prev_data['DistCurrentInventory']['product_id'] . ",0,getdate(),'$cd',$t_id)");

                        $CurrentInventory = $this->DistCurrentInventory->find('first', array(
                            'fields' => array('DistCurrentInventory.id', 'DistCurrentInventory.qty'),
                            'conditions' => array(
                                'DistCurrentInventory.store_id' => $store_id_to,
                                'DistCurrentInventory.product_id' => $prev_data['DistCurrentInventory']['product_id']
                            )
                        ));
                    }
                    //adjustment
                    // Inventory Adjustment details data
                    $data_to['DistInventoryAdjustmentDetail']['dist_inventory_adjustment_id'] = $adjustment_to_id;
                    $data_to['DistInventoryAdjustmentDetail']['dist_current_inventory_id'] = $CurrentInventory['DistCurrentInventory']['id'];
                    $data_to['DistInventoryAdjustmentDetail']['quantity'] = $prev_data['DistCurrentInventory']['qty'];
                    $data_array[] = $data_to;

                    /*----------------- Stock update : Start ---------------------------*/

                    $current_qty = $CurrentInventory['DistCurrentInventory']['qty'];
                    $current_inventory_id = $CurrentInventory['DistCurrentInventory']['id'];

                    $inventory_data['DistCurrentInventory']['id'] = $current_inventory_id;
                    $inventory_data['DistCurrentInventory']['qty'] = $current_qty + $prev_data['DistCurrentInventory']['qty'];
                    $inventory_data['DistCurrentInventory']['transaction_type_id'] = 15;
                    $inventory_data['DistCurrentInventory']['transaction_date'] = $this->current_date();
                    $stock_update_array[] = $inventory_data;
                    unset($inventory_data);
                    /*----------------- Stock update : END ---------------------------*/
                }
                $this->DistInventoryAdjustmentDetail->saveAll($data_array);
                $this->DistCurrentInventory->saveAll($stock_update_array);
            }
            /*-------------------------------------- Stock Transfer Process : END --------------------------------------------------------*/


            $this->loadModel('DistDistributor');

            $distributor_info = $this->DistDistributor->find('first', array('conditions' => array(
                'DistDistributor.id' => $distributor_id_from,
            )));

            $distributor_info['DistDistributor']['is_active'] = 0;
            if ($this->DistDistributor->save($distributor_info)) {
                $this->Session->setFlash(__('The Distributor has been Trnasferd'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
        }
    }

    public function admin_get_dsitributor_list()
    {



        $office_id = $this->request->data['office_id'];
        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $this->loadModel('DistDistributor');

        $distributor_info = $this->DistDistributor->find('all', array(
            'conditions' => array(
                'DistDistributor.office_id' => $office_id, 'DistDistributor.is_active' => 1
            ),
            'order' => array('DistDistributor.name ASC'),
        ));

        $data_array = "<option value=''>--- Select ---</option>";
        foreach ($distributor_info as $key => $data) {
            $k = $data['DistDistributor']['id'];
            $v = $data['DistDistributor']['name'];
            $data_array .= "<option value='$k'>$v</option>";
        }

        echo $data_array;
        $this->autoRender = false;
    }

    public function admin_get_dsitributor_list_for_transfer()
    {

        $office_id = $this->request->data['office_id'];

        $this->loadModel('DistStore');

        $dist_store_list = $this->DistStore->find('all', array(
            'conditions' => array(
                'DistStore.office_id' => $office_id,
                'DCI.id !=' => null,
            ),
            'joins' => array(
                array(
                    'table' => 'dist_current_inventories',
                    'alias' => 'DCI',
                    'type' => 'LEFT',
                    'conditions' => array('DCI.store_id = DistStore.id')
                ),
                array(
                    'table' => 'dist_distributors',
                    'alias' => 'DDB',
                    'type' => 'INNER',
                    'conditions' => array('DDB.id = DistStore.dist_distributor_id')
                ),
            ),
            'fields' => array('DDB.id', 'DDB.name'),
            'group' => array('DDB.id', 'DDB.name', 'DistStore.id'),
            'order' => array('DDB.name ASC'),
        ));
        //echo $this->DistStore->getLastQuery();exit;

        $data_array = "<option value=''>--- Select ---</option>";
        foreach ($dist_store_list as $key => $data) {
            $k = $data['DDB']['id'];
            $v = $data['DDB']['name'];
            $data_array .= "<option value='$k'>$v</option>";
        }

        echo $data_array;
        $this->autoRender = false;
    }
    public function admin_get_except_dsitributor_list()
    {

        $office_id = $this->request->data['office_id'];
        $distributor_id = $this->request->data['distributor_id'];
        $this->loadModel('DistStore');

        $dist_store_list = $this->DistStore->find('all', array(
            'conditions' => array(
                'DistStore.office_id' => $office_id,
                'DCI.id' => null,
                'NOT' => array('DDB.id' => $distributor_id),
            ),
            'joins' => array(
                array(
                    'table' => 'dist_current_inventories',
                    'alias' => 'DCI',
                    'type' => 'LEFT',
                    'conditions' => array('DCI.store_id = DistStore.id')
                ),
                array(
                    'table' => 'dist_distributors',
                    'alias' => 'DDB',
                    'type' => 'INNER',
                    'conditions' => array('DDB.id = DistStore.dist_distributor_id')
                ),
            ),
            'fields' => array('DDB.id', 'DDB.name'),
            'group' => array('DDB.id', 'DDB.name', 'DistStore.id'),
            'order' => array('DDB.name ASC'),
        ));

        $data_array = "<option value=''>--- Select ---</option>";
        foreach ($dist_store_list as $key => $data) {
            $k = $data['DDB']['id'];
            $v = $data['DDB']['name'];
            $data_array .= "<option value='$k'>$v</option>";
        }

        echo $data_array;
        $this->autoRender = false;
    }
    public function admin_get_dsitributor_balance()
    {
        $office_id = $this->request->data['office_id'];
        $distributor_id = $this->request->data['distributor_id'];

        $this->loadModel('DistDistributorBalance');
        //$this->loadModel('DistDistributorLimit');
        $this->loadModel('Order');
        $this->loadModel('DistOutletMap');
        $this->loadModel('DistCurrentInventory');
        $this->loadModel('DistStore');

        $distributor_balance = $this->DistDistributorBalance->find('first', array('conditions' => array(
            'DistDistributorBalance.dist_distributor_id' => $distributor_id,
            'DistDistributorBalance.office_id' => $office_id,
        )));
        /*$distributor_limit = $this->DistDistributorLimit->find('first',array('conditions'=>array(
            'DistDistributorLimit.dist_distributor_id' => $distributor_id,
            'DistDistributorLimit.office_id' => $office_id,
        )));*/
        $store_info = $this->DistStore->find('first', array('conditions' => array(
            'DistStore.office_id' => $office_id,
            'DistStore.dist_distributor_id' => $distributor_id,
        )));
        $store_id = $store_info['DistStore']['id'];
        $inventory_info = $this->DistCurrentInventory->find('all', array('conditions' => array(
            'DistCurrentInventory.store_id' => $store_id
        )));
        $stock_balance = 0;
        foreach ($inventory_info as $key => $value) {
            if ($value['DistCurrentInventory']['qty'] > 0) {
                $stock_balance = 1;
                break;
            }
        }


        $dist_outlet_map_info = $this->DistOutletMap->find('first', array(
            'conditions' => array(
                'DistOutletMap.office_id' => $office_id,
                'DistOutletMap.dist_distributor_id' => $distributor_id,
            ),
        ));
        $outlet_id = $dist_outlet_map_info['DistOutletMap']['outlet_id'];
        $market_id = $dist_outlet_map_info['DistOutletMap']['market_id'];
        $territory_id = $dist_outlet_map_info['DistOutletMap']['territory_id'];

        $all_order_info = $this->Order->find('all', array(
            'conditions' => array(
                'Order.office_id' => $office_id,
                'Order.outlet_id' => $outlet_id,
                'Order.status >' => 0,
                'Order.confirm_status !=' => 2,
            ),
            'order' => array('Order.id DESC')
        ));
        $pending_order = 0;
        if ($all_order_info) {
            $pending_order = 1;
        }
        $balance = 0;
        $data_array = array();
        if (!empty($distributor_balance)) {
            $balance = $distributor_balance['DistDistributorBalance']['balance'];
            $data_array = array(
                'balance' => $balance,
                'pending_order' => $pending_order,
                'stock_balance' => $stock_balance,
            );
        } else {
            $balance = 0;
            $data_array = array(
                'balance' => $balance,
                'pending_order' => $pending_order,
                'stock_balance' => $stock_balance,
            );
        }
        echo json_encode($data_array);

        $this->autoRender = false;
    }
}
