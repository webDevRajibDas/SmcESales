<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistSalesRepresentativesController extends AppController
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
        $this->set('page_title', 'Sales Representative List');
        $this->loadModel('Office');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $sr_conditions = array();
        if ($office_parent_id == 0) {
            $sr_conditions = $conditions = array('Office.office_type_id' => 2);
        } else {
            $this->loadModel('DistAreaExecutive');
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
                $sr_conditions = array('DistSalesRepresentative.dist_distributor_id' => array_keys($tso_dist_list));
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
                $sr_conditions = array('DistSalesRepresentative.dist_distributor_id' => $distributor_id);
            } else {
                $sr_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
            }
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
        $this->DistSalesRepresentative->virtualFields = array(
			
			'office_order' => 'Office.order',
			
		);
        $this->paginate = array(
            'conditions' => $sr_conditions,
            'recursive' => 0,
            // 'order' => array('DistSalesRepresentative.id' => 'DESC')
            'order' => array('office_order' => 'ASC','DistSalesRepresentative.id' => 'DESC'),
        );

        // echo $this->DistSalesRepresentative->getLastQuery();
        // exit;

        //pr($this->paginate());die();
        $this->set('territories', $this->paginate());
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
        $this->set('page_title', 'Sales Representative Details');
        if (!$this->DistSalesRepresentative->exists($id)) {
            throw new NotFoundException(__('Invalid Sales Representative'));
        }
        $options = array('conditions' => array('DistSalesRepresentative.' . $this->DistSalesRepresentative->primaryKey => $id));
        $this->set('territory', $this->DistSalesRepresentative->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add()
    {
        $this->set('page_title', 'Add New Sales Representative');

        if ($this->request->is('post')) {
            //pr($this->request->data);exit;
            $off_id = $this->request->data['DistSalesRepresentative']['office_id'];
            $code = $this->request->data['DistSalesRepresentative']['code'];
            $this->request->data['DistSalesRepresentative']['created_at'] = $this->current_datetime();
            $this->request->data['DistSalesRepresentative']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistSalesRepresentative']['updated_at'] = $this->current_datetime();
            $this->request->data['DistSalesRepresentative']['updated_by'] = $this->UserAuth->getUserId();

            $this->DistSalesRepresentative->create();
            if ($this->DistSalesRepresentative->save($this->request->data)) {
                $this->Session->setFlash(__('The Sales Representative has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Sales Representative could not be saved. Please, try again.'), 'flash/error');
                $this->request->data['DistSalesRepresentative']['code'] = $code;
            }
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
        $this->loadModel('Office');
        $offices_raw = $this->Office->find('all', array('fields' => array('Office.id', 'Office.office_name', 'Office.office_code'), 'conditions' => $conditions, 'recursive' => -1, 'order' => array('office_name' => 'asc')));
        $offices = array();
        $office_code = array();

        foreach ($offices_raw as $key => $value) {
            $id = $value['Office']['id'];
            $offices[$id] = $value['Office']['office_name'];
            $office_code[$id] = $value['Office']['office_code'];
        }

        $this->set(compact('offices', 'office_code', 'adding_type', 'is_new'));
    }

    public function admin_add_replacement()
    {
        $this->set('page_title', 'Replace Sales Representative');


        if ($this->request->is('post')) {

            $off_id = $this->request->data['DistSalesRepresentative']['office_id'];
            $dist_distributor_id = $this->request->data['DistSalesRepresentative']['dist_distributor_id'];
            $code_replacement = $this->request->data['DistSalesRepresentative']['code_replacement'];

            $previous_sr_info = $this->DistSalesRepresentative->find('first', array(
                'conditions' => array(
                    'DistSalesRepresentative.office_id' => $off_id,
                    'DistSalesRepresentative.dist_distributor_id' => $dist_distributor_id,
                    'DistSalesRepresentative.code' => $code_replacement,
                ),
                'recursive' => -1,
            ));
            //pr($previous_sr_info);die();
            // Deactive User First
            $prev_sr['DistSalesRepresentative']['id'] = $previous_sr_info['DistSalesRepresentative']['id'];
            $prev_sr['DistSalesRepresentative']['is_active'] = 0;
            $this->DistSalesRepresentative->save($prev_sr);

            $this->request->data['DistSalesRepresentative']['code'] = $this->request->data['DistSalesRepresentative']['code_replacement'];

            $code = $this->request->data['DistSalesRepresentative']['code'];

            //$code=$off_id.$code;

            /*if($this->DistSalesRepresentative->check_available_code($code,0,$off_id))
            {
                $this->Session->setFlash(__('This SR Code is already used in another SR.'), 'flash/error');
                $this->redirect(array('action' => 'add'));
            }*/

            $this->request->data['DistSalesRepresentative']['created_at'] = $this->current_datetime();
            $this->request->data['DistSalesRepresentative']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistSalesRepresentative']['updated_at'] = $this->current_datetime();
            $this->request->data['DistSalesRepresentative']['updated_by'] = $this->UserAuth->getUserId();


            unset($this->request->data['DistSalesRepresentative']['adding_type']);
            unset($this->request->data['DistSalesRepresentative']['code_replacement']);

            $this->DistSalesRepresentative->create();

            if ($this->DistSalesRepresentative->save($this->request->data)) {
                $this->Session->setFlash(__('The Sales Representative has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Sales Representative could not be saved. Please, try again.'), 'flash/error');
                $this->request->data['DistSalesRepresentative']['code_replacement'] = $code;
            }
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
        $this->loadModel('Office');
        $offices_raw = $this->Office->find('all', array('fields' => array('Office.id', 'Office.office_name', 'Office.office_code'), 'conditions' => $conditions, 'recursive' => -1, 'order' => array('office_name' => 'asc')));
        $offices = array();
        $office_code = array();

        foreach ($offices_raw as $key => $value) {
            $id = $value['Office']['id'];
            $offices[$id] = $value['Office']['office_name'];
            $office_code[$id] = $value['Office']['office_code'];
        }

        $this->set(compact('offices', 'office_code', 'adding_type', 'is_new'));
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
        $this->set('page_title', 'Edit Sales Representative');
        $this->DistSalesRepresentative->id = $id;
        if (!$this->DistSalesRepresentative->exists($id)) {
            throw new NotFoundException(__('Invalid Sales Representative'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            //pr($this->request->data);die();
            $this->request->data['DistSalesRepresentative']['updated_by'] = $this->UserAuth->getUserId();
            $code = $this->request->data['DistSalesRepresentative']['code'];
            $off_id = $this->request->data['DistSalesRepresentative']['office_id'];
            //$code=$off_id.$code;
            if ($this->DistSalesRepresentative->check_available_code($code, $id, $off_id)) {
                $this->Session->setFlash(__('This SR Code is already used in another SR.'), 'flash/error');
                $this->redirect(array('action' => "edit/$id"));
            }


            if ($this->DistSalesRepresentative->save($this->request->data)) {
                $this->loadModel('DistSalesRepTransferHistory');

                $this->request->data['DistSalesRepTransferHistory'] = $this->request->data['DistSalesRepresentative'];
                $this->request->data['DistSalesRepTransferHistory']['created_at'] = $this->current_datetime();
                $this->request->data['DistSalesRepTransferHistory']['created_by'] = $this->current_datetime();
                $this->request->data['DistSalesRepTransferHistory']['created_by'] = $this->UserAuth->getUserId();
                unset($this->request->data['DistSalesRepTransferHistory']['id']);
                //  pr($this->request->data);exit;
                $this->DistSalesRepTransferHistory->create();
                $this->DistSalesRepTransferHistory->save($this->request->data['DistSalesRepTransferHistory']);

                $this->Session->setFlash(__('The Sales Representative has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $options = array('conditions' => array('DistSalesRepresentative.' . $this->DistSalesRepresentative->primaryKey => $id));
            $this->request->data = $this->DistSalesRepresentative->find('first', $options);
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
        $this->loadModel('Office');
        $this->loadModel('Territory');
        $this->loadModel('DistDistributor');
        //$offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));



        $has_node = 0;
        if ($this->check_sr_child($id)) {
            $has_node = 1;
        }
        $this->set(compact('has_node'));

        $offices_raw = $this->Office->find('all', array('fields' => array('Office.id', 'Office.office_name', 'Office.office_code'), 'conditions' => $conditions, 'recursive' => -1, 'order' => array('office_name' => 'asc')));
        $offices = array();
        $office_code = array();

        foreach ($offices_raw as $key => $value) {
            $id = $value['Office']['id'];
            $offices[$id] = $value['Office']['office_name'];
            $office_code[$id] = $value['Office']['office_code'];
        }

        $territories = $this->Territory->find('list', array('conditions' => array('office_id' => $this->request->data['DistSalesRepresentative']['office_id']), 'order' => array('name' => 'asc')));
        $distDistributors = $this->DistDistributor->find('list', array('conditions' => array('office_id' => $this->request->data['DistSalesRepresentative']['office_id']), 'order' => array('name' => 'asc')));
        $this->set(compact('offices', 'territories', 'distDistributors', 'office_code'));
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
        $this->LoadModel('DistDistributor');
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->DistSalesRepresentative->id = $id;
        if (!$this->DistSalesRepresentative->exists()) {
            throw new NotFoundException(__('Invalid SR'));
        }


        /*         * ***************************** Check foreign key *************************** */
        if ($this->DistDistributor->checkForeignKeys("dist_sales_representatives", $id)) {
            $this->Session->setFlash(__('This Sales Representative has transactions'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }

        if ($this->DistSalesRepresentative->delete()) {
            $this->Session->setFlash(__('Sales Representative deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Sales Representative was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }

    public function check_sr_child($id)
    {

        /***********************  check Route mapping start ****************************************/

        $this->loadModel('DistSrRouteMapping');
        $conditions2 = array('DistSrRouteMapping.dist_sr_id' => $id);
        $existingCount2 = $this->DistSrRouteMapping->find('count', array('conditions' => $conditions2, 'recursive' => -1));
        if ($existingCount2) {
            return 2;
        }

        /***********************  check Route mapping End ****************************************/

        return 0;
    }

    public function get_sr_code()
    {

        $existing = array();
        $office_id = $this->request->data['office_id'];
        $this->loadModel('DistSalesRepresentative');
        $conditions2 = array('DistSalesRepresentative.office_id' => $office_id);
        $existing = $this->DistSalesRepresentative->find('all', array(
            'conditions' => $conditions2,
            'fields' => array('MAX(CAST(DistSalesRepresentative.code AS INT)) AS code')
        ));


        if ($existing) {
            echo $existing[0][0]['code'] + 1;
        } else {
            echo 1;
        }

        $this->autoRender = false;
    }

    public function get_inactive_sr_code()
    {

        $existing = array();
        $office_id = $this->request->data['office_id'];
        $dist_distributor_id = $this->request->data['dist_distributor_id'];
        $this->loadModel('DistSalesRepresentative');
        $conditions2 = array('DistSalesRepresentative.office_id' => $office_id, 'DistSalesRepresentative.dist_distributor_id' => $dist_distributor_id, 'DistSalesRepresentative.is_active' => 1);
        $existing = $this->DistSalesRepresentative->find('all', array(
            'conditions' => $conditions2, 'recursive' => -1,
            'fields' => array('distinct DistSalesRepresentative.code AS code', 'DistSalesRepresentative.name')
        ));
        //pr($existing);die();
        $output = "<option value=''>--- Select Code ---</option>";
        foreach ($existing as $key => $value) {
            $code = $value[0]['code'];
            $name_with_code  = $code . " (" . $value['DistSalesRepresentative']['name'] . ")";
            $output .= "<option value='$code'>$name_with_code</option>";
        }

        echo $output;
        $this->autoRender = false;
    }

    function  get_sr_name_by_office_id_dist_id_sr_code()
    {
        /*pr($this->request->data);exit;*/
        $office_id = $this->request->data['office_id'];
        $dist_distributor_id = $this->request->data['dist_distributor_id'];
        $sr_code = $this->request->data['sr_code'];
        $conditions2 = array('DistSalesRepresentative.office_id' => $office_id, 'DistSalesRepresentative.dist_distributor_id' => $dist_distributor_id, 'DistSalesRepresentative.is_active' => 0, 'DistSalesRepresentative.code' => $sr_code);
        $sr_info = $this->DistSalesRepresentative->find('first', array(
            'conditions' => $conditions2, 'recursive' => -1,
        ));
        echo json_encode($sr_info);
        $this->autoRender = false;
    }
    public function download_xl()
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes
        $params = $this->request->query['data'];
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $sr_conditions = array();
        if ($office_parent_id == 0) {
            $sr_conditions = array('Office.office_type_id' => 2);
        } else {
            $this->loadModel('DistAreaExecutive');
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
                $sr_conditions = array('DistSalesRepresentative.dist_distributor_id' => array_keys($tso_dist_list));
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
                $sr_conditions = array('DistSalesRepresentative.dist_distributor_id' => $distributor_id);
            } else {
                $sr_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
            }
        }

        if (!empty($params['DistSalesRepresentative']['name'])) {
            $sr_conditions[] = array(
                'OR' => array(
                    'DistSalesRepresentative.name like' => "%" . $params['DistSalesRepresentative']['name'] . "%",
                    'DistSalesRepresentative.code like' => "%" . $params['DistSalesRepresentative']['name'] . "%"
                )
            );
        }

        if (!empty($params['DistSalesRepresentative']['dist_distributor_id'])) {
            $sr_conditions['DistSalesRepresentative.dist_distributor_id'] = $params['DistSalesRepresentative']['dist_distributor_id'];
        }
        if (!empty($params['DistSalesRepresentative']['office_id'])) {
            $sr_conditions['DistSalesRepresentative.office_id'] = $params['DistSalesRepresentative']['office_id'];
        }
        if (!empty($params['DistSalesRepresentative']['status'])) {
            $sr_conditions['DistSalesRepresentative.is_active'] = ($params['DistSalesRepresentative']['status'] == 1 ? 1 : 0);
        }


        $this->DistSalesRepresentative->unbindModel(
            array('belongsTo' => array('User'))
        );

        $this->DistSalesRepresentative->virtualFields = array(
			
			'office_order' => 'Office.order',
			
		);
        $distsr = $this->DistSalesRepresentative->find('all', array(
            'conditions' => $sr_conditions,
            // 'order' => array('DistSalesRepresentative.name'),
            'order' => array('office_order' => 'ASC','DistSalesRepresentative.id' => 'DESC'),
            'recursive' => 0
        ));
        /* pr($distsr);
        exit; */
        $table = '<table border="1"><tbody>
        <tr>
            <td>Id</td>
            <td>Name</td>
            <td>Code</td>
            <td>Mobile</td>
            <td>Office</td>
            <td>Distributor</td>
            <td>Status</td>
        </tr>
        ';
        /* pr($disttso);
        exit; */
        foreach ($distsr as $dis_data) {


            if ($dis_data['DistSalesRepresentative']['is_active'] == 1) {
                $status = 'Active';
            } else {
                $status = 'In-Active';
            }

            $table .= '<tr>
                    <td>' . $dis_data['DistSalesRepresentative']['id'] . '</td>
                    <td>' . $dis_data['DistSalesRepresentative']['name'] . '</td>
                    <td>' . $dis_data['DistSalesRepresentative']['code'] . '</td>
                    <td style="mso-number-format:\'\@\';">' . $dis_data['DistSalesRepresentative']['mobile_number'] . '</td>
                    <td>' . $dis_data['Office']['office_name'] . '</td>
                    <td>' . $dis_data['DistDistributor']['name'] . '</td>
                    <td>' . $status . '</td>
                </tr>
                ';
        }
        $table .= '</tbody></table>';

        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment; filename="SalesRepresentatives.xls"');
        header("Cache-Control: ");
        header("Pragma: ");
        header("Expires: 0");
        echo $table;
        $this->autoRender = false;
    }
}
