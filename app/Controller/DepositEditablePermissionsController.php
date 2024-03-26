<?php
App::uses('AppController', 'Controller');

/**
 * Deposits Controller
 *
 * @property Deposit $Deposit
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DepositEditablePermissionsController extends AppController
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
        ini_set('max_execution_time', 1000);
        $this->set('page_title', 'Deposit Edit Permissions');
        $this->loadModel('Office');
        $this->loadModel('Territory');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $territory_conditions = array();
            $office_conditions = array('office_type_id' => 2);
            $deposit_permited_conditions = array();
        } else {
            $territory_conditions = array('office_id' => $this->UserAuth->getOfficeId());
            $office_conditions = array('id' => $this->UserAuth->getOfficeId());
            $deposit_permited_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }
        $office_list = $this->Office->find('list', array(
            'conditions' => $office_conditions,
            'order' => array('Office.office_name' => 'asc')
        ));
        $territory_list = $this->Territory->find('list', array(
            'conditions' => $territory_conditions,
            'order' => array('Territory.name' => 'asc')
        ));


        $permited = $this->DepositEditablePermission->find('all', array(
            'fields' => array(
                'Deposit.id',
                'Territory.office_id',
                'Deposit.territory_id',
                'Territory.name',
                'Office.office_name',
                'Type.name',
                'InstrumentType.name',
                'Deposit.deposit_date',
                'Deposit.deposit_amount',
                'Deposit.slip_no',
                'DepositEditablePermission.remarks',
                'DepositEditablePermission.recommender_note',
                'DepositEditablePermission.approval_note',
                'DepositEditablePermission.status'
            ),
            'conditions' => $deposit_permited_conditions,
            'joins' => array(
                array(
                    'table' => 'territories',
                    'alias' => 'Territory',
                    'type' => 'LEFT',
                    'conditions' => array('Territory.id= Deposit.territory_id')
                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'LEFT',
                    'conditions' => array('Office.id= Territory.office_id')
                ),
                array(
                    'table' => 'instrument_type',
                    'alias' => 'Type',
                    'type' => 'LEFT',
                    'conditions' => array('Type.id= Deposit.type')
                ),
                array(
                    'table' => 'instrument_type',
                    'alias' => 'InstrumentType',
                    'type' => 'LEFT',
                    'conditions' => array('InstrumentType.id= Deposit.instrument_type')
                ),
            ),
            'order' => array('DepositEditablePermission.id DESC'),
            'limit' => '50'
        ));

        $instrument_types = array(
            '1' => 'Cash',
            '2' => 'Instrument'
        );
        $this->set(compact('instrument_types'));
        $this->set(compact('territory_list', 'office_list', 'permited'));

        // pr($permited);die();
    }

    public function admin_create_requisition()
    {
        ini_set('max_execution_time', 1000);
        $this->set('page_title', 'Create Requisitions');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');
        $this->loadModel('Office');
        $this->loadModel('Territory');
        $this->loadModel('Deposit');
        $this->loadModel('SystemNotification');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $territory_conditions = array();
            $office_conditions = array('office_type_id' => 2);
        } else {
            $territory_conditions = array('office_id' => $this->UserAuth->getOfficeId());
            $office_conditions = array('id' => $this->UserAuth->getOfficeId());
        }
        $office_list = $this->Office->find('list', array(
            'conditions' => $office_conditions,
            'order' => array('Office.office_name' => 'asc')
        ));
        $territory_list = $this->Territory->find('list', array(
            'conditions' => $territory_conditions,
            'order' => array('Territory.name' => 'asc')
        ));
        $this->set(compact('territory_list', 'office_list'));

        if ($this->request->is('post')) {
            $deposits = $this->request->data['DepositEditablePermission']['deposit_id'];
            $deposit_info = $this->Deposit->find('all', array(
                'conditions' => array(
                    'Deposit.id' => $deposits
                ),
                'joins' => array(
                    array(
                        'table' => 'territories',
                        'alias' => 'Territory',
                        'type' => 'LEFT',
                        'conditions' => array('Territory.id= Deposit.territory_id')
                    ),
                ),
                'fields' => array('Deposit.*', 'Territory.office_id'),
                'recursive' => -1
            ));
            $deposit_for_editable = array();
            $deposit_no_lists = array();
            foreach ($deposit_info as $key => $value) {
                $deposit_for_editable[$value['Deposit']['id']] = $value['Territory']['office_id'];
            }

            $data_array = array();
            foreach ($deposits as $key => $value) {
                if (!empty($value)) {
                    $data = array();
                    $deposit_id = $value;
                    $data['deposit_id'] = $deposit_id;
                    $data['remarks'] = $this->request->data['DepositEditablePermission']['remarks'][$key];
                    $data['office_id'] = $deposit_for_editable[$deposit_id];
                    $data['requisition_at'] = $this->current_datetime();
                    $data['requisition_by'] = $this->UserAuth->getUserId();
                    $data['status'] = 0;
                    $data['created_at'] = $this->current_datetime();
                    $data['created_by'] = $this->UserAuth->getUserId();
                    $data['updated_at'] = $this->current_datetime();
                    $data['updated_by'] = $this->UserAuth->getUserId();

                    $data_array[] = $data;
                }
            }
            $this->loadModel('DepositEditablePermission');
            $this->DepositEditablePermission->create();
            if ($this->DepositEditablePermission->saveAll($data_array)) {
                $message = "Requision Sbmited";
                $this->Session->setFlash(__($message), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $message = "Requision is not Sbmited";
                $this->Session->setFlash(__($message), 'flash/error');
            }
        }
        $instrument_types = array(
            '1' => 'Cash',
            '2' => 'Instrument'
        );
        $this->set(compact('instrument_types'));
    }
    public function admin_recommender_list()
    {
        ini_set('max_execution_time', 1000);
        $this->set('page_title', 'Deposit Recommendations');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');
        $this->loadModel('Office');
        $this->loadModel('Territory');

        $office_id = $this->UserAuth->getOfficeId();
        $this->update_notifications('DepositEditablePermissions', 'admin_recommender_list');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $territory_conditions = array();
            $office_conditions = array('office_type_id' => 2);
            $deposit_permited_conditions = array('DepositEditablePermission.status' => 0);
        } else {
            $territory_conditions = array('office_id' => $this->UserAuth->getOfficeId());
            $office_conditions = array('id' => $this->UserAuth->getOfficeId());
            $deposit_permited_conditions = array('DepositEditablePermission.status' => 0, 'DepositEditablePermission.office_id' => $this->UserAuth->getOfficeId());
        }
        $office_list = $this->Office->find('list', array(
            'conditions' => $office_conditions,
            'order' => array('Office.office_name' => 'asc')
        ));
        $territory_list = $this->Territory->find('list', array(
            'conditions' => $territory_conditions,
            'order' => array('Territory.name' => 'asc')
        ));


        $permited = $this->DepositEditablePermission->find('all', array(
            'fields' => array(
                'Deposit.id',
                'Territory.office_id',
                'Deposit.territory_id',
                'Territory.name',
                'Office.office_name',
                'Type.name',
                'InstrumentType.name',
                'Deposit.deposit_date',
                'Deposit.deposit_amount',
                'Deposit.slip_no',
                'DepositEditablePermission.id',
                'DepositEditablePermission.remarks',
            ),
            'conditions' => $deposit_permited_conditions,
            'joins' => array(
                array(
                    'table' => 'territories',
                    'alias' => 'Territory',
                    'type' => 'LEFT',
                    'conditions' => array('Territory.id= Deposit.territory_id')
                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'LEFT',
                    'conditions' => array('Office.id= Territory.office_id')
                ),
                array(
                    'table' => 'instrument_type',
                    'alias' => 'Type',
                    'type' => 'LEFT',
                    'conditions' => array('Type.id= Deposit.type')
                ),
                array(
                    'table' => 'instrument_type',
                    'alias' => 'InstrumentType',
                    'type' => 'LEFT',
                    'conditions' => array('InstrumentType.id= Deposit.instrument_type')
                ),
            ),
            'order' => array('DepositEditablePermission.id DESC')
        ));

        $instrument_types = array(
            '1' => 'Cash',
            '2' => 'Instrument'
        );
        $this->set(compact('instrument_types'));
        $this->set(compact('territory_list', 'office_list', 'permited'));
    }
    public function admin_approval_list()
    {
        ini_set('max_execution_time', 1000);
        $this->set('page_title', 'Deposit Approval');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');
        $this->loadModel('Office');
        $this->loadModel('Territory');

        $this->update_notifications('DepositEditablePermissions', 'admin_approval_list');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $territory_conditions = array();
            $office_conditions = array('office_type_id' => 2);
            $deposit_permited_conditions = array('DepositEditablePermission.status' => 1);
        } else {
            $territory_conditions = array('office_id' => $this->UserAuth->getOfficeId());
            $office_conditions = array('id' => $this->UserAuth->getOfficeId());
            $deposit_permited_conditions = array('DepositEditablePermission.status' => 1, 'DepositEditablePermission.office_id' => $this->UserAuth->getOfficeId());
        }
        $office_list = $this->Office->find('list', array(
            'conditions' => $office_conditions,
            'order' => array('Office.office_name' => 'asc')
        ));
        $territory_list = $this->Territory->find('list', array(
            'conditions' => $territory_conditions,
            'order' => array('Territory.name' => 'asc')
        ));


        $permited = $this->DepositEditablePermission->find('all', array(
            'fields' => array(
                'Deposit.id',
                'Territory.office_id',
                'Deposit.territory_id',
                'Territory.name',
                'Office.office_name',
                'Type.name',
                'InstrumentType.name',
                'Deposit.deposit_date',
                'Deposit.deposit_amount',
                'Deposit.slip_no',
                'DepositEditablePermission.remarks',
                'DepositEditablePermission.id',
                'DepositEditablePermission.recommender_note'
            ),
            'conditions' => $deposit_permited_conditions,
            'joins' => array(
                array(
                    'table' => 'territories',
                    'alias' => 'Territory',
                    'type' => 'LEFT',
                    'conditions' => array('Territory.id= Deposit.territory_id')
                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'LEFT',
                    'conditions' => array('Office.id= Territory.office_id')
                ),
                array(
                    'table' => 'instrument_type',
                    'alias' => 'Type',
                    'type' => 'LEFT',
                    'conditions' => array('Type.id= Deposit.type')
                ),
                array(
                    'table' => 'instrument_type',
                    'alias' => 'InstrumentType',
                    'type' => 'LEFT',
                    'conditions' => array('InstrumentType.id= Deposit.instrument_type')
                ),
            ),
            'order' => array('DepositEditablePermission.id DESC')
        ));

        $instrument_types = array(
            '1' => 'Cash',
            '2' => 'Instrument'
        );
        $this->set(compact('instrument_types'));
        $this->set(compact('territory_list', 'office_list', 'permited'));
    }





    public function admin_get_territory()
    {
        $this->loadModel('Territory');
        $office_id = $this->request->data['office_id'];
        $rs = array(array('id' => '', 'name' => '--- Select ----'));
        $territory_list = $this->Territory->find('all', array(
            'fields' => array('Territory.id as id', 'Territory.name as name'),
            'conditions' => array(
                'Territory.office_id' => $office_id
            ),
            'order' => array('Territory.name' => 'asc'),
            'recursive' => -1
        ));
        $data_array = Set::extract($territory_list, '{n}.0');
        if (!empty($territory_list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }



    public function get_deposit_list()
    {
        $date_from = $this->request->data['date_from'];
        $date_to = $this->request->data['date_to'];
        $office_id = $this->request->data['office_id'];
        $territory_id = $this->request->data['territory_id'];
        $type = $this->request->data['type'];

        ini_set('max_execution_time', 1000);

        $this->loadModel('DepositEditablePermission');
        $Deposit_conditions = array();
        $existed_deposit_conditions = array();
        if (!empty($date_from)) {
            $Deposit_date_from = date('Y-m-d', strtotime($date_from));
            $Deposit_conditions[] = array('Deposit.deposit_date >=' => $Deposit_date_from);
            $existed_deposit_conditions['Deposit.deposit_date >='] = $Deposit_date_from;
        }
        if (!empty($date_to)) {
            $Deposit_date_to = date('Y-m-d', strtotime($date_to));
            $Deposit_conditions[] = array('Deposit.deposit_date <=' => $Deposit_date_to);
            $existed_deposit_conditions['Deposit.deposit_date <='] = $Deposit_date_to;
        }
        if (!empty($office_id)) {
            $Deposit_conditions[] = array('Territory.office_id' => $office_id);
            $existed_deposit_conditions['DepositEditablePermission.office_id'] = $office_id;
        }
        if (!empty($territory_id)) {
            $Deposit_conditions[] = array('Deposit.territory_id' => $territory_id);
        }
        if (!empty($type)) {
            $Deposit_conditions[] = array('Deposit.type' => $type);
        }

        $Deposit_conditions[] = array(
            'OR' => array(
                array('Deposit.editable' => 0),
                array('Deposit.editable' => NULL)
            )
        );
        $Deposit_conditions[] = array(
            'OR' => array(
                array('Deposit.is_distributor' => 0),
                array('Deposit.is_distributor' => NULL)
            )
        );
        $this->loadModel('DepositEditablePermission');
        $existed_deposit_conditions['DepositEditablePermission.status'] = array(0, 1);
        $exist_permited_Deposit = $this->DepositEditablePermission->find('list', array(
            'conditions' => $existed_deposit_conditions,
            'fields' => array('DepositEditablePermission.deposit_id', 'DepositEditablePermission.id'),
            'joins' => array(
                array(
                    'table' => 'Deposits',
                    'alias' => 'Deposit',
                    'type' => 'inner',
                    'conditions' => 'Deposit.id=DepositEditablePermission.deposit_id'
                ),
            ),
            'recursive' => -1
        ));
        $exist_permited_Deposit_list = array_keys($exist_permited_Deposit);
        $Deposit_conditions[] = array('NOT' => array('Deposit.id' => $exist_permited_Deposit_list));
        $this->loadModel('Deposit');
        $Deposits = $this->Deposit->find('all', array(
            'fields' => array(
                'Deposit.id',
                'Territory.office_id',
                'Deposit.territory_id',
                'Territory.name',
                'Office.office_name',
                'Type.name',
                'InstrumentType.name',
                'Deposit.deposit_date',
                'Deposit.deposit_amount',
                'Deposit.slip_no',
            ),
            'conditions' => $Deposit_conditions,
            'joins' => array(
                array(
                    'table' => 'territories',
                    'alias' => 'Territory',
                    'type' => 'LEFT',
                    'conditions' => array('Territory.id= Deposit.territory_id')
                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'LEFT',
                    'conditions' => array('Office.id= Territory.office_id')
                ),
                array(
                    'table' => 'instrument_type',
                    'alias' => 'Type',
                    'type' => 'LEFT',
                    'conditions' => array('Type.id= Deposit.type')
                ),
                array(
                    'table' => 'instrument_type',
                    'alias' => 'InstrumentType',
                    'type' => 'LEFT',
                    'conditions' => array('InstrumentType.id= Deposit.instrument_type')
                ),
            ),
            'recursive' => -1
        ));

        /*echo $this->Deposit->getLastQuery();
        pr($Deposits);exit;*/

        echo json_encode($Deposits);
        $this->autoRender = false;
    }
    public function get_deposit_editable_permited_list()
    {

        $date_from = $this->request->data['date_from'];
        $date_to = $this->request->data['date_to'];
        $office_id = $this->request->data['office_id'];
        $territory_id = $this->request->data['territory_id'];
        $type = $this->request->data['type'];

        ini_set('max_execution_time', 1000);
        $deposit_conditions = array();

        if (!empty($date_from)) {
            $deposit_date_from = date('Y-m-d', strtotime($date_from));
            $deposit_conditions[] = array('Deposit.deposit_date >=' => $deposit_date_from);
        }
        if (!empty($date_to)) {
            $deposit_date_to = date('Y-m-d', strtotime($date_to));
            $deposit_conditions[] = array('Deposit.deposit_date <=' => $deposit_date_to);
        }
        if (!empty($office_id)) {
            $deposit_conditions[] = array('Territory.office_id' => $office_id);
        }

        if (!empty($territory_id)) {
            $deposit_conditions[] = array('Deposit.territory_id' => $territory_id);
        }
        if (!empty($type)) {
            $deposit_conditions[] = array('Deposit.type' => $type);
        }
        $this->loadModel('DepositEditablePermission');
        $permited = $this->DepositEditablePermission->find('all', array(
            'fields' => array(
                'Deposit.id',
                'Territory.office_id',
                'Deposit.territory_id',
                'Territory.name',
                'Office.office_name',
                'Type.name',
                'InstrumentType.name',
                'Deposit.deposit_date',
                'Deposit.deposit_amount',
                'Deposit.slip_no',
                'DepositEditablePermission.remarks',
                'DepositEditablePermission.recommender_note',
                'DepositEditablePermission.approval_note',
                'DepositEditablePermission.status'
            ),
            'conditions' => $deposit_conditions,
            'joins' => array(
                array(
                    'table' => 'territories',
                    'alias' => 'Territory',
                    'type' => 'LEFT',
                    'conditions' => array('Territory.id= Deposit.territory_id')
                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'LEFT',
                    'conditions' => array('Office.id= Territory.office_id')
                ),
                array(
                    'table' => 'instrument_type',
                    'alias' => 'Type',
                    'type' => 'LEFT',
                    'conditions' => array('Type.id= Deposit.type')
                ),
                array(
                    'table' => 'instrument_type',
                    'alias' => 'InstrumentType',
                    'type' => 'LEFT',
                    'conditions' => array('InstrumentType.id= Deposit.instrument_type')
                ),
            ),
            'order' => array('DepositEditablePermission.id DESC')
        ));
        $data_array = array();
        if (!empty($permited)) {
            foreach ($permited as $key => $value) {
                $data = array();
                $data['deposit_id'] = $value['Deposit']['id'];
                $data['office_name'] = $value['Office']['office_name'];
                $data['territory_name'] = $value['Territory']['name'];
                $data['type'] = $value['Type']['name'];
                $data['inst_type'] = $value['InstrumentType']['name'];
                $data['slip_no'] = $value['Deposit']['slip_no'];
                $data['deposit_date'] = $value['Deposit']['deposit_date'];
                $data['deposit_amount'] = $value['Deposit']['deposit_amount'];
                $data['remarks'] = $value['DepositEditablePermission']['remarks'];
                $data['recommender_note'] = $value['DepositEditablePermission']['recommender_note'];
                $data['approval_note'] = $value['DepositEditablePermission']['approval_note'];
                $data['status'] = $value['DepositEditablePermission']['status'];

                $data_array[] = $data;
            }
        }

        echo json_encode($data_array);
        $this->autoRender = false;
    }


    public function get_deposit_recommended_list()
    {

        $office_id = $this->request->data['office_id'];
        $territory_id = $this->request->data['territory_id'];
        $type = $this->request->data['type'];

        ini_set('max_execution_time', 1000);
        $deposit_conditions = array();


        if (!empty($office_id)) {
            $deposit_conditions[] = array('Territory.office_id' => $office_id);
        }

        if (!empty($territory_id)) {
            $deposit_conditions[] = array('Deposit.territory_id' => $territory_id);
        }
        if (!empty($type)) {
            $deposit_conditions[] = array('Deposit.type' => $type);
        }

        $deposit_conditions[] = array('DepositEditablePermission.status' => 0);
        $this->loadModel('DepositEditablePermission');
        $permited = $this->DepositEditablePermission->find('all', array(
            'fields' => array(
                'Deposit.id',
                'Territory.office_id',
                'Deposit.territory_id',
                'Territory.name',
                'Office.office_name',
                'Type.name',
                'InstrumentType.name',
                'Deposit.deposit_date',
                'Deposit.deposit_amount',
                'Deposit.slip_no',
                'DepositEditablePermission.id',
                'DepositEditablePermission.remarks',
            ),
            'conditions' => $deposit_conditions,
            'joins' => array(
                array(
                    'table' => 'territories',
                    'alias' => 'Territory',
                    'type' => 'LEFT',
                    'conditions' => array('Territory.id= Deposit.territory_id')
                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'LEFT',
                    'conditions' => array('Office.id= Territory.office_id')
                ),
                array(
                    'table' => 'instrument_type',
                    'alias' => 'Type',
                    'type' => 'LEFT',
                    'conditions' => array('Type.id= Deposit.type')
                ),
                array(
                    'table' => 'instrument_type',
                    'alias' => 'InstrumentType',
                    'type' => 'LEFT',
                    'conditions' => array('InstrumentType.id= Deposit.instrument_type')
                ),
            ),
            'order' => array('DepositEditablePermission.id DESC')
        ));
        $data_array = array();
        if (!empty($permited)) {
            foreach ($permited as $key => $value) {
                $data = array();

                $data['id'] = $value['DepositEditablePermission']['id'];
                $data['deposit_id'] = $value['Deposit']['id'];
                $data['office_name'] = $value['Office']['office_name'];
                $data['territory_name'] = $value['Territory']['name'];
                $data['type'] = $value['Type']['name'];
                $data['inst_type'] = $value['InstrumentType']['name'];
                $data['slip_no'] = $value['Deposit']['slip_no'];
                $data['deposit_date'] = $value['Deposit']['deposit_date'];
                $data['deposit_amount'] = $value['Deposit']['deposit_amount'];
                $data['remarks'] = $value['DepositEditablePermission']['remarks'];


                $data_array[] = $data;
            }
        }
        echo json_encode($data_array);
        $this->autoRender = false;
    }
    public function deposit_recommended()
    {
        $id = $this->request->data['id'];
        $recommender_note = $this->request->data['recommender_note'];

        $data_array = array();
        $data_array['id'] = $id;
        $data_array['recommender_note'] = $recommender_note;
        $data_array['recommended_at'] = $this->current_datetime();
        $data_array['recommended_by'] = $this->UserAuth->getUserId();
        $data_array['status'] = 1;

        if ($this->DepositEditablePermission->save($data_array)) {
            $msg[0] = '1';
        } else {
            $msg[0] = '0';
        }

        echo json_encode($msg);
        $this->autoRender = false;
    }


    public function get_deposit_approved_list()
    {
        $office_id = $this->request->data['office_id'];
        $territory_id = $this->request->data['territory_id'];
        $type = $this->request->data['type'];

        ini_set('max_execution_time', 1000);
        $deposit_conditions = array();


        if (!empty($office_id)) {
            $deposit_conditions[] = array('Territory.office_id' => $office_id);
        }

        if (!empty($territory_id)) {
            $deposit_conditions[] = array('Deposit.territory_id' => $territory_id);
        }
        if (!empty($type)) {
            $deposit_conditions[] = array('Deposit.type' => $type);
        }
        $deposit_conditions[] = array('DepositEditablePermission.status' => 1);
        $this->loadModel('DepositEditablePermission');
        $permited = $this->DepositEditablePermission->find('all', array(
            'fields' => array(
                'Deposit.id',
                'Territory.office_id',
                'Deposit.territory_id',
                'Territory.name',
                'Office.office_name',
                'Type.name',
                'InstrumentType.name',
                'Deposit.deposit_date',
                'Deposit.deposit_amount',
                'Deposit.slip_no',
                'DepositEditablePermission.id',
                'DepositEditablePermission.remarks',
                'DepositEditablePermission.recommender_note',
            ),
            'conditions' => $deposit_conditions,
            'joins' => array(
                array(
                    'table' => 'territories',
                    'alias' => 'Territory',
                    'type' => 'LEFT',
                    'conditions' => array('Territory.id= Deposit.territory_id')
                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'LEFT',
                    'conditions' => array('Office.id= Territory.office_id')
                ),
                array(
                    'table' => 'instrument_type',
                    'alias' => 'Type',
                    'type' => 'LEFT',
                    'conditions' => array('Type.id= Deposit.type')
                ),
                array(
                    'table' => 'instrument_type',
                    'alias' => 'InstrumentType',
                    'type' => 'LEFT',
                    'conditions' => array('InstrumentType.id= Deposit.instrument_type')
                ),
            ),
            'order' => array('DepositEditablePermission.id DESC')
        ));
        $data_array = array();
        if (!empty($permited)) {
            foreach ($permited as $key => $value) {
                $data = array();

                $data['id'] = $value['DepositEditablePermission']['id'];
                $data['deposit_id'] = $value['Deposit']['id'];
                $data['office_name'] = $value['Office']['office_name'];
                $data['territory_name'] = $value['Territory']['name'];
                $data['type'] = $value['Type']['name'];
                $data['inst_type'] = $value['InstrumentType']['name'];
                $data['slip_no'] = $value['Deposit']['slip_no'];
                $data['deposit_date'] = $value['Deposit']['deposit_date'];
                $data['deposit_amount'] = $value['Deposit']['deposit_amount'];
                $data['remarks'] = $value['DepositEditablePermission']['remarks'];
                $data['recommender_note'] = $value['DepositEditablePermission']['recommender_note'];

                $data_array[] = $data;
            }
        }

        echo json_encode($data_array);
        $this->autoRender = false;
    }
    public function deposit_approved()
    {
        $id = $this->request->data['id'];
        $approval_note = $this->request->data['approval_note'];

        $this->loadModel('Deposit');
        $this->loadModel('DepositEditablePermission');
        $this->loadModel('SystemNotification');
        $deposit_editable_info = $this->DepositEditablePermission->find('first', array(
            'conditions' => array(
                'DepositEditablePermission.id' => $id
            ),
            //'recursive'=> -1
        ));
        $deposit_id = $deposit_editable_info['DepositEditablePermission']['deposit_id'];
        $data_array = array();
        $data_array['id'] = $id;
        $data_array['approval_note'] = $approval_note;
        $data_array['approval_at'] = $this->current_datetime();
        $data_array['approval_by'] = $this->UserAuth->getUserId();
        $data_array['status'] = 2;

        if ($this->DepositEditablePermission->save($data_array)) {
            $msg[0] = '1';
            //Update Deposit
            $deposit_arr['id'] = $deposit_id;
            $deposit_arr['editable'] = 1;
            $this->Deposit->Query("ALTER TABLE deposits DISABLE TRIGGER deposit_logs_after_update");
            $this->Deposit->save($deposit_arr);
            $this->Deposit->Query("ALTER TABLE deposits ENABLE TRIGGER deposit_logs_after_update");
            //end Update

        } else {
            $msg[0] = '0';
        }

        echo json_encode($msg);
        $this->autoRender = false;
    }
    public function deposit_not_approved()
    {
        $id = $this->request->data['id'];
        $approval_note = $this->request->data['approval_note'];

        $this->loadModel('Deposit');
        $this->loadModel('DepositEditablePermission');
        $this->loadModel('SystemNotification');

        $data_array = array();
        $data_array['id'] = $id;
        $data_array['approval_note'] = $approval_note;
        $data_array['approval_at'] = $this->current_datetime();
        $data_array['approval_by'] = $this->UserAuth->getUserId();
        $data_array['status'] = 3;

        if ($this->DepositEditablePermission->save($data_array)) {
            $msg[0] = '1';
        } else {
            $msg[0] = '0';
        }

        echo json_encode($msg);
        $this->autoRender = false;
    }


    public function update_shown_notifications()
    {
        $this->loadModel('SystemNotification');
        $id = $this->request->data['id'];
        $data = array();
        $data['id'] = id;
        $data['notification_seen'] = 1;
        $data['status'] = 1;
        if ($this->SystemNotification->save($data)) {
            $msg = 1;
        } else {
            $msg = 0;
        }
        echo json_encode($msg);
        $this->autoRender = false;
    }
}
