<?php

App::uses('AppController', 'Controller');

/**
 * Institutes Controller
 *
 * @property Institute $Institute
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class InstitutesController extends AppController {

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
        $this->set('page_title', 'Institute List');
        $institute_list = array(
            1 => 'NGO',
            2 => 'Institute'
        );
        $this->set('institute_list', $institute_list);
        $this->Institute->recursive = 0;
        $this->paginate = array(
            'order' => array('Institute.id' => 'DESC')
        );
        $this->set('institutes', $this->paginate());
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null) {
        $this->set('page_title', 'Institute Details');
        if (!$this->Institute->exists($id)) {
            throw new NotFoundException(__('Invalid institute'));
        }
        $options = array('conditions' => array('Institute.' . $this->Institute->primaryKey => $id));
        $this->set('institute', $this->Institute->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add() {
        $this->set('page_title', 'Add Institute');
        if ($this->request->is('post')) {
            $this->request->data['Institute']['created_at'] = $this->current_datetime();
            $this->request->data['Institute']['updated_at'] = $this->current_datetime();
            $this->request->data['Institute']['created_by'] = $this->UserAuth->getUserId();
            $this->Institute->create();
            if ($this->Institute->save($this->request->data)) {
                $this->Session->setFlash(__('The institute has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                //$this->Session->setFlash(__('The institute could not be saved. Please, try again.'), 'flash/error');
            }
        }
        $institute_list = array(
            1 => 'NGO',
            2 => 'Institute'
        );
        $this->set('institute_list', $institute_list);
    }

    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_edit($id = null) {
        $this->set('page_title', 'Edit Institute');
        $this->Institute->id = $id;
        if (!$this->Institute->exists($id)) {
            throw new NotFoundException(__('Invalid institute'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['Institute']['updated_by'] = $this->UserAuth->getUserId();
            $this->request->data['Institute']['updated_at'] = $this->current_datetime();
            if ($this->Institute->save($this->request->data)) {
                $this->Session->setFlash(__('The institute has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                //$this->Session->setFlash(__('The institute could not be saved. Please, try again.'), 'flash/error');
            }
        } else {
            $options = array('conditions' => array('Institute.' . $this->Institute->primaryKey => $id));
            $this->request->data = $this->Institute->find('first', $options);
        }
        $institute_list = array(
            1 => 'NGO',
            2 => 'Institute'
        );
        $this->set('institute_list', $institute_list);
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
        $this->Institute->id = $id;
        if (!$this->Institute->exists()) {
            throw new NotFoundException(__('Invalid institute'));
        }
        if ($this->Institute->delete()) {
            $this->Session->setFlash(__('Institute deleted'), 'flash/success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Institute was not deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
    }

    public function get_institute_list() {
        $institute_type_id = $this->request->data['institute_type_id'];

        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $list = $this->Institute->find('all', array(
            'conditions' => array('type' => $institute_type_id, 'is_active' => 1),
            'order' => array('Institute.name' => 'ASC'),
            'recursive' => -1
        ));
        $data_array = Set::extract($list, '{n}.Institute');
        if (!empty($list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_mapping_institute_list() {
        $office_id = $this->request->data['office_id'];

        $list = $this->Institute->find('all', array(
            'conditions' => array('is_active' => 1),
            'fields' => array('id', 'name'),
            'order' => array('Institute.name' => 'ASC'),
            'recursive' => -1
        ));

        $this->loadModel('MappingInstituteToArea');
        $mapping_list = $this->MappingInstituteToArea->find('all', array(
            'conditions' => array('area_id' => $office_id),
            'fields' => array('institute_id'),
            'order' => array('id' => 'ASC'),
            'recursive' => -1
        ));

        $mapping_list_final = array();
        foreach ($mapping_list as $each) {
            $mapping_list_final[] = $each['MappingInstituteToArea']['institute_id'];
        }

        $final_data = array();
        $data_array = Set::extract($list, '{n}.Institute');

        $i = 0;
        foreach ($data_array as $each_ins) {
            if (in_array($each_ins['id'], $mapping_list_final)) {
                $data_array[$i]['is_check'] = "checked";
            } else {
                $data_array[$i]['is_check'] = "";
            }
            $i++;
        }

        echo json_encode($data_array);
        $this->autoRender = false;
    }

    public function admin_mapping_to_area($id = null) {
        $this->set('page_title', 'Institute mapping with Area office');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array();
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }

        $this->loadModel('Office');
        $this->loadModel('MappingInstituteToArea');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));



        if ($this->request->is('post')) {
            $data = array();
            /* detele existing data */
                        
            $this->MappingInstituteToArea->deleteAll(array('MappingInstituteToArea.area_id'=>$this->request->data['Institute']['office_id']));
            foreach ($this->request->data['checked_ngo'] as $key => $val) {
                $data['MappingInstituteToArea']['created_at'] = $this->current_datetime();
                $data['MappingInstituteToArea']['created_by'] = $this->UserAuth->getUserId();
                $data['MappingInstituteToArea']['updated_at'] = $this->current_datetime();
                $data['MappingInstituteToArea']['updated_by'] = $this->UserAuth->getUserId();
                $data['MappingInstituteToArea']['area_id'] = $this->request->data['Institute']['office_id'];
                $data['MappingInstituteToArea']['institute_id'] = $val;
                $data_array[] = $data;
            }

            if ($this->MappingInstituteToArea->saveAll($data_array)) {
                $this->Session->setFlash(__('The institute has been saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                //$this->Session->setFlash(__('The institute could not be saved. Please, try again.'), 'flash/error');
            }
        }

        $this->set(compact('offices'));
    }

}
