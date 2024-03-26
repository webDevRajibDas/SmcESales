<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistAreaExecutivesController extends AppController
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
        $this->set('page_title', 'Distributors Area Executives List');
        $this->loadModel('Office');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $this->DistAreaExecutive->virtualFields = array(
			
			'office_order' => 'Office.order',
			
		);

        $this->paginate = array(
            'conditions' => $conditions,
            'recursive' => 0,
            'order' => array( 'office_order' => 'ASC','DistAreaExecutive.name' => 'ASC')
            // 'order' => array('DistAreaExecutive.name' => 'ASC')
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
        $this->set('page_title', 'Area Executives Details');
        if (!$this->DistAreaExecutive->exists($id)) {
            throw new NotFoundException(__('Invalid DistAreaExecutive'));
        }
        $options = array('conditions' => array('DistAreaExecutive.' . $this->DistAreaExecutive->primaryKey => $id));
        $this->set('tso', $this->DistAreaExecutive->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add()
    {
        $this->set('page_title', 'Add Area Executive');

        $users = array();
        $aes = array();
        $existing_ae_data = $this->get_available_ae_list();

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
            $user_id = $this->request->data['DistAreaExecutive']['user_id'];

            if ($user_id && array_key_exists($user_id, $aes)) {
                $ae_name = $users[$user_id]['name'];
                $this->request->data['DistAreaExecutive']['name'] = $ae_name;
            } else {
                $this->Session->setFlash(__('Please select Valid Area Executive.'), 'flash/error');
                $this->redirect(array('action' => 'add'));
            }


            $this->request->data['DistAreaExecutive']['is_added'] = 1;
            $this->request->data['DistAreaExecutive']['created_at'] = $this->current_datetime();
            $this->request->data['DistAreaExecutive']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistAreaExecutive']['updated_at'] = $this->current_datetime();
            $this->request->data['DistAreaExecutive']['updated_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistAreaExecutive']['pre_office_id'] = $this->request->data['DistAreaExecutive']['office_id'];
            $this->request->data['DistAreaExecutive']['effective_date'] = date("Y-m-d H:i:s", strtotime($this->request->data['DistAreaExecutive']['effective_date']));
            $this->DistAreaExecutive->create();
            if ($this->DistAreaExecutive->save($this->request->data)) {
                $this->loadModel('DistAreaExecutiveHistory');
                $this->request->data['DistAreaExecutive']['ae_id'] = $this->DistAreaExecutive->getLastInsertID();
                $DistAreaExecutiveHistory['DistAreaExecutiveHistory'] = $this->request->data['DistAreaExecutive'];
                $this->DistAreaExecutiveHistory->save($DistAreaExecutiveHistory);
                $this->Session->setFlash(__('The Area Executive has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Area Executive could not be saved. Please, try again.'), 'flash/error');
                $this->redirect(array('action' => 'add'));
            }
        }

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
        $this->set('page_title', 'Edit Area Executive');
        $this->DistAreaExecutive->id = $id;
        if (!$this->DistAreaExecutive->exists($id)) {
            throw new NotFoundException(__('Invalid Area Executive'));
        }
        $this->loadModel('DistAreaExecutiveHistory');
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['DistAreaExecutive']['updated_by'] = $this->UserAuth->getUserId();
            /*             * ************ Check valid date ******************* */
            $this->request->data['DistAreaExecutive']['effective_date'] = date("Y-m-d H:i:s", strtotime($this->request->data['DistAreaExecutive']['effective_date']));
            $this->request->data['DistAreaExecutive']['pre_effective_date'] = date("Y-m-d H:i:s", strtotime($this->request->data['DistAreaExecutive']['pre_effective_date']));

            if (new DateTime($this->request->data['DistAreaExecutive']['pre_effective_date']) > new DateTime($this->request->data['DistAreaExecutive']['effective_date'])) {
                $this->Session->setFlash(__('New Effective should not be less than the existing effective date'), 'flash/error');
                $this->redirect(array('action' => "edit/$id"));
            }

            $curr_effective_date = date("Y-m-d");

            if (new DateTime($curr_effective_date) > new DateTime($this->request->data['DistAreaExecutive']['effective_date'])) {
                $this->Session->setFlash(__('Effective date can not be any past date'), 'flash/error');
                $this->redirect(array('action' => "edit/$id"));
            }

            $this->loadModel('DistAreaExecutiveHistory');
            $DistAreaExecutiveHistory['DistAreaExecutiveHistory'] = $this->request->data['DistAreaExecutive'];
            if (($this->request->data['DistAreaExecutive']['office_id'] != $this->request->data['DistAreaExecutive']['pre_office_id']) || ($this->request->data['DistAreaExecutive']['effective_date'] != $this->request->data['DistAreaExecutive']['pre_effective_date'])) {
                $DistAreaExecutiveHistory['DistAreaExecutiveHistory']['is_transfer'] = 1;

                /*                 * ***************  Set start and end effective date  *********************** */

                if (new DateTime($this->request->data['DistAreaExecutive']['pre_effective_date']) >= new DateTime($this->request->data['DistAreaExecutive']['effective_date'])) {
                    $this->Session->setFlash(__('New Effective should not be less than the existing effective date'), 'flash/error');
                    $this->redirect(array('action' => "edit/$id"));
                }

                $effective_end_date = date('Y-m-d', strtotime($this->request->data['DistAreaExecutive']['effective_date'] . ' -1 day'));
                $this->DistAreaExecutiveHistory->query("update dist_area_executive_histories set active=0,effective_end_date='$effective_end_date' where ae_id=$id and active=1 and effective_end_date is NULL");
            } else {
                $DistAreaExecutiveHistory['DistAreaExecutiveHistory']['active'] = 0;
            }

            if ($this->DistAreaExecutive->save($this->request->data)) {
                $DistAreaExecutiveHistory['DistAreaExecutiveHistory']['created_at'] = $this->current_datetime();
                $DistAreaExecutiveHistory['DistAreaExecutiveHistory']['ae_id'] = $id;
                $DistAreaExecutiveHistory['DistAreaExecutiveHistory']['created_by'] = $this->UserAuth->getUserId();
                $DistAreaExecutiveHistory['DistAreaExecutiveHistory']['updated_at'] = $this->current_datetime();
                unset($DistAreaExecutiveHistory['DistAreaExecutiveHistory']['id']);
                $this->DistAreaExecutiveHistory->save($DistAreaExecutiveHistory);

                $this->Session->setFlash(__('The Area Executive has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $options = array('conditions' => array('DistAreaExecutive.' . $this->DistAreaExecutive->primaryKey => $id));
            $this->request->data = $this->DistAreaExecutive->find('first', $options);
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions = array('Office.office_type_id' => 2);
        } else {
            $conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        $this->set(compact('offices'));

        $has_node = 0;
        if ($this->check_tso_exist($id)) {
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
        $this->DistAreaExecutive->id = $id;
        if (!$this->DistAreaExecutive->exists()) {
            throw new NotFoundException(__('Invalid Area Executive'));
        }

        /*         * ********** Check foreign key ********** */
        if ($this->DistAreaExecutive->checkForeignKeys("dist_area_executives", $id)) {
            $this->Session->setFlash(__('This Area Executive has transactions'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }


        if ($this->DistAreaExecutive->delete()) {
            $this->Session->setFlash(__('Area Executive deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Area Executive was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }

    public function get_area_executive_list()
    {
        // $rs = array('---- Plsease Select -----');
        $rs = array();
        $office_id = $this->request->data['office_id'];
        $distAreaExecutives = $this->DistAreaExecutive->find('list', array(
            'conditions' => array(
                'DistAreaExecutive.office_id' => $office_id, 'DistAreaExecutive.is_active' => 1
            )
        ));
        if (!empty($distAreaExecutives)) {
            echo json_encode($distAreaExecutives);
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function check_tso_exist($ae_id)
    {

        $this->loadModel('DistTso');
        $conditions = array('DistTso.dist_area_executive_id' => $ae_id);
        $existingCount = $this->DistTso->find('all', array('conditions' => $conditions, 'recursive' => -1));
        //pr($existingCount); exit;
        return $existingCount;
    }

    public function get_available_ae_list()
    {

        $this->loadModel('DistAreaExecutive');

        $dist_ae_except = $this->DistAreaExecutive->find('all', array(
            'conditions' => array('DistAreaExecutive.is_active' => 1),
            'recursive' => -1
        ));


        $ae_except_ids = array();
        foreach ($dist_ae_except as $k => $v) {
            if ($v['DistAreaExecutive']['user_id'])
                $ae_except_ids[] = $v['DistAreaExecutive']['user_id'];
        }

        $this->loadModel('User');
        $ae_except_id = "";

        if ($ae_except_ids && count($ae_except_ids) > 1) {
            $ae_except_id = implode(',', $ae_except_ids);
        } else if ($ae_except_ids) {
            $ae_except_id = $ae_except_ids[0];
        }

        if ($ae_except_id) {
            $qry = "select u.id,u.user_group_id,u.sales_person_id,u.username,sp.name,sp.office_id from users u
            left join sales_people sp on sp.id=u.sales_person_id where u.user_group_id=1028 and u.id not in ($ae_except_id)";
        } else {
            $qry = "select u.id,u.user_group_id,u.sales_person_id,u.username,sp.name,sp.office_id from users u
            left join sales_people sp on sp.id=u.sales_person_id where u.user_group_id=1028";
        }


        $existing = $this->User->query($qry);

        return $existing;
    }
    public function download_xl()
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes
        $params = $this->request->query['data'];

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $dist_conditions = array();
        if ($office_parent_id == 0) {
            $dist_conditions = array('Office.office_type_id' => 2);
        } else {
            $dist_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        //adding db_code or name searching condition----------

        if (!empty($params['DistAreaExecutive']['name'])) {
            $dist_conditions['DistAreaExecutive.name LIKE'] = '%' . $params['DistAreaExecutive']['name'] . '%';
        }
        if (!empty($params['DistAreaExecutive']['status'])) {
            $dist_conditions['DistAreaExecutive.is_active'] = ($params['DistAreaExecutive']['status'] == 1 ? 1 : 0);
        }
        if (!empty($params['DistAreaExecutive']['office_id'])) {
            $dist_conditions['DistAreaExecutive.office_id'] = $params['DistAreaExecutive']['office_id'];
        }
        $this->DistAreaExecutive->unbindModel(
            array('belongsTo' => array('User'))
        );
        $this->DistAreaExecutive->virtualFields = array(
			
			'office_order' => 'Office.order',
			
		);
        $distae = $this->DistAreaExecutive->find('all', array(
            'conditions' => $dist_conditions,
            // 'order' => array('DistAreaExecutive.name'),
            'order' => array( 'office_order' => 'ASC','DistAreaExecutive.name' => 'ASC'),
            'recursive' => 0
        ));
        // pr($distae);
        // exit;
        $table = '<table border="1"><tbody>
        <tr>
            <td>Id</td>
            <td>Name</td>
            <td>Office</td>
            <td>Status</td>
        </tr>
        ';
        /* pr($disttso);
        exit; */
        foreach ($distae as $dis_data) {


            if ($dis_data['DistAreaExecutive']['is_active'] == 1) {
                $status = 'Active';
            } else {
                $status = 'In-Active';
            }

            $table .= '<tr>
                    <td>' . $dis_data['DistAreaExecutive']['id'] . '</td>
                    <td>' . $dis_data['DistAreaExecutive']['name'] . '</td>
                    <td>' . $dis_data['Office']['office_name'] . '</td>
                    <td>' . $status . '</td>
                </tr>
                ';
        }
        $table .= '</tbody></table>';

        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment; filename="AreaExecutive.xls"');
        header("Cache-Control: ");
        header("Pragma: ");
        header("Expires: 0");
        echo $table;
        $this->autoRender = false;
    }
}
