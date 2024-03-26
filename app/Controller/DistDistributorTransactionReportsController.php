<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistDistributorTransactionReportsController extends AppController
{

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
    public function admin_index()
    {
        $this->set('page_title', 'Distributor Transaction Ledger Reports');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $designation_id = $this->Session->read('Office.designation_id');
        $this->loadModel('Office');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistStore');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $conditions = array();
        $b_conditions = array();
        $dist_con = array();
        //==================25-6-19====//
        //echo $office_parent_id;

        if ($office_parent_id == 0) //for super user admin
        {
            $conditions = array('office_type_id' => 2);
            $b_conditions = array();
            $dist_con = array('DistDistributor.is_active' => 1);
        } else //for company office user admin
        {
            $conditions = array('office_type_id' => 2, 'Office.id' => $this->UserAuth->getOfficeId());
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
                $b_conditions = array('DistDistributorBalance.dist_distributor_id' => array_keys($tso_dist_list));
                $dist_con = array('DistDistributor.id' => array_keys($tso_dist_list), 'DistDistributor.is_active' => 1);
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];

                $b_conditions = array('DistDistributorBalance.dist_distributor_id' => $distributor_id);
                $dist_con = array('DistDistributor.id' => $distributor_id, 'DistDistributor.is_active' => 1);
            } else {
                $b_conditions = array('DistDistributorBalance.office_id' => $this->UserAuth->getOfficeId());
                $dist_con = array('DistDistributor.office_id' => $this->UserAuth->getOfficeId(), 'DistDistributor.is_active' => 1);
            }
        }

        /*pr($conditions);
        exit;*/

        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));
        //================end==25-6-19====//
        $this->paginate = array(
            'fields' => array('DistDistributorBalance.*', 'DistDistributor.id', 'DistDistributor.name', 'Office.office_name'),
            'conditions' => $b_conditions,
            'joins' => array(
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'LEFT',
                    'conditions' => array('Office.id= DistDistributor.office_id')
                )
            ),
            'recursive' => 0,
            //'group' => array('Office.id'),
            //'order' => array('DistDistributorBalance.effective_date' => 'DESC')
        );
        $this->set('dist_distributor_balances', $this->paginate());


        $this->loadModel('DistDistributor');

        $distDistributors = $this->DistDistributor->find('list', array(
            'conditions' => $dist_con
        ));
        //pr($distDistributors);die();
        $this->set(compact('offices'));
        $this->set(compact('distDistributors'));

        if ($this->request->is('post')) {
            //pr($this->request->data);die();
            $dist_distributor_id = $this->request->data['DistDistributorBalance']['dist_distributor_id'];
            $office_id = $this->request->data['DistDistributorBalance']['office_id'];
            $from_date = $this->request->data['DistDistributorBalance']['date_from'];
            $to_date = $this->request->data['DistDistributorBalance']['date_to'];
            $date_from = date('Y-m-d', strtotime($from_date));
            $date_to = date('Y-m-d', strtotime($to_date));

            $date_from_for_excel = date('Y-m-d', strtotime($from_date));
            $date_to_for_excel = date('Y-m-d', strtotime($to_date));

            $dist_info = $this->DistDistributor->find('first', array(
                'conditions' => array(
                    'DistDistributor.id' => $dist_distributor_id

                ),
                'joins' => array(
                    array(
                        'table' => 'dist_stores',
                        'alias' => 'DistStore',
                        'type' => 'LEFT',
                        'conditions' => array('DistStore.dist_distributor_id= DistDistributor.id')
                    )
                ),
                'fields' => array(
                    'DistDistributor.name',
                    'DistStore.name', 'DistDistributor.db_code', 'DistDistributor.address'
                ),
                'recursive' => -1
            ));

            // pr($dist_info);exit;

            /* $opening_balance = $this->DistDistributorBalanceHistory->find('first',array(
				'conditions'=>array(
                    'DistDistributorBalanceHistory.dist_distributor_id' => $dist_distributor_id,
                    'DistDistributorBalanceHistory.office_id' => $office_id,
                    'DistDistributorBalanceHistory.transaction_date <=' => $date_from
                ),
				'order'=> array('DistDistributorBalanceHistory.id ASC'),
				'recursive' => -1
			)); */

            $balance_debit_credit_opening_balance = $this->DistDistributorBalanceHistory->find('all', array(
                'conditions' => array(
                    'DistDistributorBalanceHistory.dist_distributor_id' => $dist_distributor_id,
                    'DistDistributorBalanceHistory.office_id' => $office_id,
                    'DistDistributorBalanceHistory.transaction_date <' => $date_from,
                    /* 'DistDistributorBalanceHistory.id !=' => $opening_balance['DistDistributorBalanceHistory']['id'], */
                ),
                'fields' => array(
                    'SUM(case when DistDistributorBalanceHistory.balance_type = 1 then transaction_amount end) as debit',
                    'SUM(case when DistDistributorBalanceHistory.balance_type = 2 then transaction_amount end) as credit'
                )
            ));
            $opening_balance['DistDistributorBalanceHistory']['balance'] = /* $opening_balance['DistDistributorBalanceHistory']['balance'] + */ $balance_debit_credit_opening_balance[0][0]['debit'] - $balance_debit_credit_opening_balance[0][0]['credit'];
            $balance_history = $this->DistDistributorBalanceHistory->find('all', array(
                'conditions' => array(
                    'DistDistributorBalanceHistory.dist_distributor_id' => $dist_distributor_id,
                    'DistDistributorBalanceHistory.office_id' => $office_id,
                    'DistDistributorBalanceHistory.transaction_date >=' => $date_from,
                    'DistDistributorBalanceHistory.transaction_date <=' => $date_to,
                    //'DistDistributorBalanceHistory.balance_transaction_type_id !=' => 7
                ),
                'fields' => array(
                    'DistDistributorBalanceHistory.transaction_date as date',
                    'SUM(case when DistDistributorBalanceHistory.balance_type = 1 then transaction_amount end) as dabit',
                    'SUM(case when DistDistributorBalanceHistory.balance_type = 2 then transaction_amount end) as credit'
                ),
                'group' => array(
                    'DistDistributorBalanceHistory.transaction_date'
                ),
                'order' => array('DistDistributorBalanceHistory.transaction_date')
            ));
            //echo $this->DistDistributorBalanceHistory->getLastquery();die();
            //pr($balance_history);die();
            $this->set(compact('balance_history', 'date_from', 'date_to', 'opening_balance', 'date_from_for_excel', 'date_to_for_excel', 'dist_info'));

            if ($office_parent_id == 0) //for super user admin
            {
                $conditions = array('office_type_id' => 2);
                $b_conditions = array();
                $dist_con = array('DistDistributor.is_active' => 1, 'DistDistributor.office_id' => $office_id);
                $distDistributors = $this->DistDistributor->find('list', array(
                    'conditions' => $dist_con
                ));
                $this->set(compact('distDistributors'));
            }
        }
    }
}
