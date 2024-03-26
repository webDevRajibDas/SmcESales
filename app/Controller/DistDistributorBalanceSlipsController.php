<?php

App::uses('AppController', 'Controller');

/**
 * Territories Controller
 *
 * @property Territory $Territory
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistDistributorBalanceSlipsController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Session', 'Filter.Filter');
    public $uses = array('DistDistributorBalanceHistory');
    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index()
    {
        $this->set('page_title', 'Distributor Balance List');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $designation_id = $this->Session->read('Office.designation_id');
        $this->loadModel('Office');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $this->loadModel('DistDistributorBalance');
        $this->loadModel('DistDistributorBalanceHistory');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $conditions = array();
        $b_conditions = array();
        $dist_con = array();
        //==================25-6-19====//
        //echo $office_parent_id;
        $current_date = date('d-m-Y', strtotime($this->current_date()));
        if ($office_parent_id == 0) //for super user admin
        {
            $conditions = array('office_type_id' => 2);
            $b_conditions = array();
            $dist_con = array('DistDistributor.is_active' => 1);
            $h_conditions = array('DistDistributorBalanceHistory.transaction_date >=' => $this->current_date() . ' 00:00:00', 'DistDistributorBalanceHistory.transaction_date <=' => $this->current_date() . ' 23:59:59', 'DistDistributorBalanceHistory.deposit_id !=' => NULL);
        } else //for company office user admin
        {
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
                $b_conditions = array('DistDistributorBalance.dist_distributor_id' => array_keys($tso_dist_list));
                $dist_con = array('DistDistributor.id' => array_keys($tso_dist_list), 'DistDistributor.is_active' => 1);
                $h_conditions = array('DistDistributorBalanceHistory.dist_tso_id' => $dist_tso_id, 'DistDistributorBalanceHistory.transaction_date >=' => $this->current_date() . ' 00:00:00', 'DistDistributorBalanceHistory.transaction_date <=' => $this->current_date() . ' 23:59:59', 'DistDistributorBalanceHistory.deposit_id !=' => NULL);
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];

                $b_conditions = array('DistDistributorBalance.dist_distributor_id' => $distributor_id);
                $dist_con = array('DistDistributor.id' => $distributor_id, 'DistDistributor.is_active' => 1);
                $h_conditions = array('DistDistributorBalanceHistory.dist_distributor_id' => $dist_distributor_id, 'DistDistributorBalanceHistory.transaction_date >=' => $this->current_date() . ' 00:00:00', 'DistDistributorBalanceHistory.transaction_date <=' => $this->current_date() . ' 23:59:59', 'DistDistributorBalanceHistory.deposit_id !=' => NULL);
            } else {
                $b_conditions = array('DistDistributorBalance.office_id' => $this->UserAuth->getOfficeId());
                $dist_con = array('DistDistributor.office_id' => $this->UserAuth->getOfficeId(), 'DistDistributor.is_active' => 1);
                $h_conditions = array('DistDistributorBalanceHistory.office_id' => $this->UserAuth->getOfficeId(), 'DistDistributorBalanceHistory.transaction_date >=' => $this->current_date() . ' 00:00:00', 'DistDistributorBalanceHistory.transaction_date <=' => $this->current_date() . ' 23:59:59', 'DistDistributorBalanceHistory.deposit_id !=' => NULL);
            }
        }

        /*pr($conditions);
        exit;*/
        $group = array(
            'DistDistributorBalanceHistory.id',
            'DistDistributorBalanceHistory.dist_distributor_balance_id',
            'DistDistributorBalanceHistory.dist_tso_id',
            'DistDistributorBalanceHistory.dist_distributor_id',
            'DistDistributorBalanceHistory.balance',
            'DistDistributorBalanceHistory.balance_type',
            'DistDistributorBalanceHistory.transaction_amount',
            'DistDistributorBalanceHistory.balance_transaction_type_id',
            'DistDistributorBalanceHistory.transaction_date',
            'DistDistributorBalanceHistory.office_id',
            'DistDistributorBalanceHistory.created_at',
            'DistDistributorBalanceHistory.created_by',
            'DistDistributorBalanceHistory.updated_at',
            'DistDistributorBalanceHistory.updated_by',
            'DistDistributorBalanceHistory.deposit_id',
            'DistDistributor.id',
            'DistDistributor.name',
            'Office.id',
            'Office.office_name',
            'DistTso.name'
        );
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $conditions, 'order' => array('office_name' => 'asc')));

        if ($this->request->query) {
            // pr($this->request->query);die();
            // $h_conditions = array();
            $from_date = date('Y-m-d', strtotime($this->request->query['date_from']));
            $to_date = date('Y-m-d', strtotime($this->request->query['date_to']));
            $office_id = @$this->request->query['office_id'];
            $dist_distributor_id = @$this->request->query['dist_distributor_id'];
            if (!empty($from_date)) {
                unset($h_conditions['DistDistributorBalanceHistory.transaction_date >=']);
                $h_conditions[] = array('DistDistributorBalanceHistory.transaction_date >=' => $from_date);
            }
            if (!empty($to_date)) {
                unset($h_conditions['DistDistributorBalanceHistory.transaction_date <=']);
                $h_conditions[] = array('DistDistributorBalanceHistory.transaction_date <=' => $to_date);
            }
            if (!empty($office_id)) {
                unset($h_conditions['DistDistributorBalanceHistory.office_id']);
                $h_conditions[] = array('DistDistributorBalanceHistory.office_id' => $office_id);
            }
            if (!empty($dist_distributor_id)) {
                unset($h_conditions['DistDistributorBalanceHistory.dist_distributor_id']);
                $h_conditions[] = array('DistDistributorBalanceHistory.dist_distributor_id' => $dist_distributor_id);
            }
            $h_conditions[] = array('DistDistributorBalanceHistory.deposit_id !=' => NULL);
            //pr($h_conditions);die();
        }
        //================end==25-6-19====//
        $this->paginate = array(
            //'fields'=> array('DistDistributorBalanceHistory.*','DistDistributor.id','DistDistributor.name','Office.office_name'),
            'fields' => array('DistDistributorBalanceHistory.*', 'DistDistributor.id', 'DistDistributor.name', 'Office.office_name', 'DistTso.name'),
            // 'conditions' => $b_conditions,
            'conditions' => $h_conditions,
            'joins' => array(
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'LEFT',
                    'conditions' => array('Office.id= DistDistributor.office_id')
                ),
                array(
                    'table' => 'dist_tsos',
                    'alias' => 'DistTso',
                    'type' => 'LEFT',
                    'conditions' => array('DistTso.id= DistDistributorBalanceHistory.dist_tso_id')
                )
            ),
            'recursive' => 0,
            'group' => $group,
            'order' => array('DistDistributorBalanceHistory.transaction_date' => 'ASC')
        );
        //pr($h_conditions);die();echo $this->DistDistributorBalanceHistory->getLastquery();die();
        //pr($this->paginate());die();
        $this->set('dist_distributor_balances', $this->paginate());


        $this->loadModel('DistDistributor');

        $distDistributors = $this->DistDistributor->find('list', array(
            'conditions' => $dist_con
        ));
        //pr($distDistributors);die();
        $this->set(compact('offices', 'current_date'));
        $this->set(compact('distDistributors'));
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */


    public function admin_view_slip($id = null)
    {
        $this->set('page_title', 'DistributorBalance');
        $this->loadModel('DistDistributorBalance');
        $this->loadModel('DistDistributorBalanceHistory');
        if (!$this->DistDistributorBalance->exists($id)) {
            throw new NotFoundException(__('Invalid DistAreaExecutive'));
        }

        $options = array(
            'conditions' => array('DistDistributorBalanceHistory.dist_distributor_balance_id' => $id),
            'order' => array('DistDistributorBalanceHistory.id DESC'),
        );
        $this->set('distributor_balance', $this->DistDistributorBalanceHistory->find('all', $options));
    }
    /**
     * admin_add method
     *
     * @return void
     */


    public function admin_add_slip()
    {
        $this->set('page_title', 'Add Distributor Balance');


        //for company and office list

        $this->loadModel('DistDistributor');
        $this->loadModel('Office');
        $this->loadModel('DistBalanceTransactionType');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $this->loadModel('InstrumentType');
        $this->loadModel('Bank');
        $this->loadModel('Deposit');
        $this->loadModel('Territory');
        $this->loadModel('FiscalYear');
        $this->loadModel('Week');
        $this->loadModel('DistDistributorBalance');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $transaction_type = $this->DistBalanceTransactionType->find('list', array(
            'conditions' => array(
                // 'id IN'=> array(1,6),
                'id' => 1,
            ),
        ));
        $deposite_type = array(1 => 'Cash', 2 => 'Instrument');
        $conditions = array(
            'Week.start_date >=' => date('Y-m-d', strtotime(date('Y-m-01') . ' -2 months')),
            'Week.start_date <=' => date('Y-m-t')
        );

        $weeks = $this->Week->find('list', array(
            //'fields' => array('Week.id', 'Week.week_name', 'Week.updated_at', 'Week.start_date', 'Week.end_date'),
            'conditions' => $conditions,
            'order' => array('Week.updated_at' => 'asc'),
            'recursive' => -1
        ));
        //pr($weeks);die();

        $instrument_types = $this->InstrumentType->find('list', array('conditions' => array('NOT' => array('id' => array(1, 2)))));
        $banks = $this->Bank->find('all', array('fields' => array('Bank.id', 'Bank.name')));
        $bank_list = array();
        foreach ($banks as $key => $value) {
            $bank_list[$value['Bank']['id']] = $value['Bank']['name'];
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $designation_id = $this->Session->read('Office.designation_id');
        $this->set(compact('office_parent_id'));
        $tso_conditions = array();
        if ($office_parent_id == 0) {
            $office_conditions = array(
                'office_type_id' => 2,
                "NOT" => array("id" => array(30, 31, 37))
            );
            $office_conditions = array('Office.office_type_id' => 2);
            $officelists = $this->Office->find('all', array('conditions' => $office_conditions, 'order' => array('Office.office_name' => 'asc')));

            // pr($officelists);die();
            foreach ($officelists as $key => $value) {
                $office_ids[$key] = $value['Office']['id'];
                $offices[$value['Office']['id']] = $value['Office']['office_name'];
            }
            $distDistributors = $this->DistDistributor->find('list', array('conditions' => array(
                'DistDistributor.office_id' => $office_ids, 'DistDistributor.is_active' => 1,
                //'DistDistributor.dealer_is_limit_check'=> 1
            )));
            //pr($distDistributors);die();
            $tso_conditions = array('DistTso.is_active' => 1);
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
                        'fields' => array('DistTso.id', 'DistTso.name'),
                    ));
                    //pr($dist_tso_info);die();
                    $dist_tso_id = array_keys($dist_tso_info);
                    $tso_conditions = array('dist_area_executive_id' => $dist_ae_id);
                } else {
                    $dist_tso_info = $this->DistTso->find('first', array(
                        'conditions' => array('DistTso.user_id' => $user_id, 'DistTso.is_active' => 1),
                        'recursive' => -1,
                    ));
                    $dist_tso_id = $dist_tso_info['DistTso']['id'];
                    $tso_conditions = array('id' => $dist_tso_id);
                }

                $tso_dist_list = $this->DistTsoMapping->find('list', array(
                    'conditions' => array(
                        'dist_tso_id' => $dist_tso_id,
                    ),
                    'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
                ));
                $b_conditions = array('DistDistributor.id' => array_keys($tso_dist_list));
            } else {
                $b_conditions = array('DistDistributor.office_id' => $this->UserAuth->getOfficeId(), 'DistDistributor.is_active' => 1);
                $tso_conditions = array('office_id' => $this->UserAuth->getOfficeId());
            }
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
            $office_id = $office_conditions['Office.id'];
            $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
            $distDistributors = $this->DistDistributor->find('list', array('conditions' => $b_conditions));
        }


        $dist_tso_list = $this->DistTso->find('list', array(
            'conditions' => $tso_conditions,
            'fields' => array('DistTso.id', 'DistTso.name'),
        ));

        $this->set(compact('distDistributors', 'transaction_type', 'deposite_type', 'instrument_types', 'bank_list', 'dist_tso_list', 'weeks'));

        $this->set(compact('offices'));
        //for company and office list


        if ($this->request->is('post')) {

            //pr($this->request->data);die();
            $office_id = $this->request->data['DistDistributorBalance']['office_id'];
            $dist_distributor_id = $this->request->data['DistDistributorBalance']['dist_distributor_id'];
            $transaction_type_id = 1;
            $balance_inout = 0;
            $week_id = $this->request->data['DistDistributorBalance']['sales_week'];
            $distributor_balance = $this->DistDistributorBalance->find('first', array(
                'conditions' => array(
                    'DistDistributorBalance.office_id' => $office_id,
                    'DistDistributorBalance.dist_distributor_id' => $dist_distributor_id,
                )
            ));

            $ammount = $this->request->data['DistDistributorBalance']['balance'];
            $teritrry_info = $this->Territory->find('first', array(
                'fields' => array('Territory.id', 'SalesPerson.id'),
                'conditions' => array(
                    'Territory.name like' => '%Corporate Territory%',
                    'Territory.office_id' => $office_id
                ),
            ));

            $teritrry_id = $teritrry_info['Territory']['id'];
            $sales_people_id = $teritrry_info['SalesPerson']['id'];

            $transaction_date = $this->request->data['DistDistributorBalance']['Deposite_date'];
            $date = date('Y-m-d', strtotime($transaction_date));
            /*$fiscal_year = $this->FiscalYear->find('first',array(
                'fields'=> array('FiscalYear.id'),
                'conditions' => array(
                    'FiscalYear.start_date <= '=> $date,
                    'FiscalYear.end_date >= ' => $date
                ),
            ));

            $fiscal_year_id = NULL;
            if(!empty($fiscal_year)){
                $fiscal_year_id = fiscal_year['FiscalYear']['id'];
            }*/

            /*$week_info = $this->Week->find('first',array(
                'fields'=> array('Week.id','Week.month_id'),
                'conditions' => array(
                    'Week.start_date <= '=> date('Y-m-d'),
                    'Week.end_date >= ' => date('Y-m-d')
                ),
            ));
            $week_id = NULL;
            $month_id = NULL;
            if(!empty($week_info)){
                $week_id = $week_info['Week']['id'];
                $month_id = $week_info['Week']['month_id'];
            }*/
            $balance_type = 1;
            if ($balance_inout == 1) {
                $ammount = $ammount * (-1);
                $balance_type = 2;
            }

            if (!empty($distributor_balance)) {

                $this->request->data['DistDistributorBalance']['balance'] = $ammount + $distributor_balance['DistDistributorBalance']['balance'];


                $this->request->data['DistDistributorBalance']['id'] = $distributor_balance['DistDistributorBalance']['id'];
            } else {
                $this->request->data['DistDistributorBalance']['balance'] = $ammount;
                $this->DistDistributorBalance->create();
            }


            $dist_distributor_id = $this->request->data['DistDistributorBalance']['dist_distributor_id'];

            $this->request->data['DistDistributorBalance']['is_active'] = 1;
            $this->request->data['DistDistributorBalance']['has_slip'] = 1;
            $this->request->data['DistDistributorBalance']['created_at'] = $this->current_datetime();
            $this->request->data['DistDistributorBalance']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistDistributorBalance']['updated_at'] = $this->current_datetime();
            $this->request->data['DistDistributorBalance']['updated_by'] = $this->UserAuth->getUserId();
            //pr($this->request->data);die();
            unset($this->request->data['DistDistributorBalance']['balance_transaction_type_id']);


            if ($this->DistDistributorBalance->save($this->request->data['DistDistributorBalance'])) {
                if (!empty($distributor_balance)) {
                    $id = $this->request->data['DistDistributorBalance']['id'];
                } else {
                    $id = $this->DistDistributorBalance->getLastInsertID();
                }
                $this->loadModel('DistDistributorBalanceHistory');
                $distributor_balance_info = $this->DistDistributorBalanceHistory->find('first', array(
                    'conditions' => array(
                        'DistDistributorBalanceHistory.office_id' => $office_id,
                        'DistDistributorBalanceHistory.dist_distributor_id' => $dist_distributor_id,
                    ),
                    'order' => 'DistDistributorBalanceHistory.id DESC',
                ));
                // pr($distributor_balance_info);
                if (!empty($distributor_balance_info)) {
                    $balance = $ammount + $distributor_balance_info['DistDistributorBalanceHistory']['balance'];
                } else {
                    $balance = $ammount;
                }
                if ($balance_inout == 1) {
                    $ammount = $ammount * (-1);
                }
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory'] = $this->request->data['DistDistributorBalance'];
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['transaction_amount'] = $ammount;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance'] = $balance;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['dist_distributor_balance_id'] = $id;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance_type'] = $balance_type;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance_transaction_type_id'] = $transaction_type_id;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['transaction_date'] = date('Y-m-d', strtotime($this->request->data['DistDistributorBalance']['Deposite_date']));


                //unset($DistDistributorBalanceHistory['DistDistributorBalanceHistory']['id']);
                //unset($DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance']);
                // pr($DistDistributorBalanceHistory);die();
                unset($DistDistributorBalanceHistory['DistDistributorBalanceHistory']['id']);
                $this->DistDistributorBalanceHistory->create();
                if ($this->DistDistributorBalanceHistory->save($DistDistributorBalanceHistory['DistDistributorBalanceHistory'])) {
                    if ($balance_inout == 0) {
                        $dist_distributor_balance_history_id = $this->DistDistributorBalanceHistory->getLastInsertID();

                        $user_id = $this->UserAuth->getUserId();
                        $generate_deposite_no = 'D' . $user_id . date('d') . date('m') . date('h') . date('i') . date('s');

                        $deposit = array();

                        $deposit['temp_id'] = $generate_deposite_no;
                        $deposit['memo_no'] = 0;
                        $deposit['memo_id'] = 0;
                        $deposit['sales_person_id'] = $sales_people_id;
                        $deposit['payment_id'] = 0;
                        $deposit['deposit_amount'] = $ammount;
                        $deposit['deposit_date'] = date('Y-m-d', strtotime($this->request->data['DistDistributorBalance']['Deposite_date']));
                        $deposit['type'] = $this->request->data['DistDistributorBalance']['deposite_type_id'];
                        $deposit['instrument_type'] = $this->request->data['DistDistributorBalance']['instrument_type_id'];
                        $deposit['instrument_ref_no'] = $this->request->data['DistDistributorBalance']['instrument_ref_no'];
                        $deposit['bank_branch_id'] = $this->request->data['DistDistributorBalance']['bank_branch_id'];
                        $deposit['slip_no'] = $this->request->data['DistDistributorBalance']['reference_number'];
                        $deposit['instrument_clearing_status'] = '';
                        $deposit['week_id'] = $week_id;
                        //$deposit['fiscal_year_id'] = $fiscal_year_id;
                        //$deposit['month_id'] = $month_id;
                        $deposit['remarks'] = '';
                        $deposit['created_at'] = $this->request->data['DistDistributorBalance']['created_at'];
                        $deposit['action'] = 1;
                        $deposit['territory_id'] = $teritrry_id;
                        $deposit['is_distributor'] = 1;
                        $deposit['dist_distributor_balance_history_id'] = $dist_distributor_balance_history_id;

                        $this->Deposit->create();
                        if ($this->Deposit->save($deposit)) {
                            $deposit_id = $this->Deposit->getLastInsertID();
                            $distributor_balance_info = $this->DistDistributorBalanceHistory->find('first', array(
                                'conditions' => array(
                                    'DistDistributorBalanceHistory.id' => $dist_distributor_balance_history_id
                                ),
                            ));
                            $data['DistDistributorBalanceHistory']['id'] = $dist_distributor_balance_history_id;
                            $data['DistDistributorBalanceHistory']['deposit_id'] = $deposit_id;
                            $this->DistDistributorBalanceHistory->save($data['DistDistributorBalanceHistory']);
                        }
                    }
                }

                $this->Session->setFlash(__('Balance added successfully!'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Balance could not be saved. Please, try again.'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            }
        }
    }

    public function admin_adjustment()
    {
        $this->set('page_title', 'Adjustment Distributor Adjustment');


        //for company and office list

        $this->loadModel('DistDistributor');
        $this->loadModel('Office');
        $this->loadModel('DistBalanceTransactionType');
        $this->loadModel('DistDistributorBalance');
        $this->loadModel('DistDistributorBalanceHistory');
        $transaction_type = $this->DistBalanceTransactionType->find('list', array(
            'conditions' => array(
                'id' => 6,
            ),
        ));

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $designation_id = $this->Session->read('Office.designation_id');
        $this->set(compact('office_parent_id'));
        if ($office_parent_id == 0) {
            $office_conditions = array(
                'office_type_id' => 2,
                "NOT" => array("id" => array(30, 31, 37))
            );
            $office_conditions = array('Office.office_type_id' => 2);
            $officelists = $this->Office->find('all', array('conditions' => $office_conditions, 'order' => array('Office.office_name' => 'asc')));

            // pr($officelists);die();
            foreach ($officelists as $key => $value) {
                $office_ids[$key] = $value['Office']['id'];
                $offices[$value['Office']['id']] = $value['Office']['office_name'];
            }
            $distDistributors = $this->DistDistributor->find('list', array('conditions' => array(
                'DistDistributor.office_id' => $office_ids, 'DistDistributor.is_active' => 1,
                //'DistDistributor.dealer_is_limit_check'=> 1
            )));
            //pr($distDistributors);die();
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
            $office_id = $office_conditions['Office.id'];
            $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
            $distDistributors = $this->DistDistributor->find('list', array('conditions' => array(
                'DistDistributor.office_id' => $this->UserAuth->getOfficeId(), 'DistDistributor.is_active' => 1,
                //'DistDistributor.dealer_is_limit_check'=> 1
            )));
        }

        $this->set(compact('distDistributors', 'transaction_type'));

        $this->set(compact('offices'));
        //for company and office list


        if ($this->request->is('post')) {


            $office_id = $this->request->data['DistDistributorBalance']['office_id'];
            $dist_distributor_id = $this->request->data['DistDistributorBalance']['dist_distributor_id'];
            $transaction_type_id = $this->request->data['DistDistributorBalance']['balance_transaction_type_id'];

            $distributor_balance = $this->DistDistributorBalance->find('first', array(
                'conditions' => array(
                    'DistDistributorBalance.office_id' => $office_id,
                    'DistDistributorBalance.dist_distributor_id' => $dist_distributor_id,
                )
            ));

            $ammount = $this->request->data['DistDistributorBalance']['balance'];

            if (!empty($distributor_balance)) {

                if ($distributor_balance['DistDistributorBalance']['balance'] >= $ammount) {
                    $this->request->data['DistDistributorBalance']['balance'] =  $distributor_balance['DistDistributorBalance']['balance'] - $ammount;

                    $this->request->data['DistDistributorBalance']['id'] = $distributor_balance['DistDistributorBalance']['id'];
                } else {
                    $this->Session->setFlash(__('Given Amount Is Bigger Then Balance. The Balance could not be saved. Please, try again.'), 'flash/error');
                    $this->redirect(array('action' => 'adjustment'));
                }
            } else {
                $this->Session->setFlash(__('Please Add Balance First. Please, try again.'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            }


            $dist_distributor_id = $this->request->data['DistDistributorBalance']['dist_distributor_id'];

            $this->request->data['DistDistributorBalance']['is_active'] = 1;
            $this->request->data['DistDistributorBalance']['created_at'] = $this->current_datetime();
            $this->request->data['DistDistributorBalance']['created_by'] = $this->UserAuth->getUserId();
            $this->request->data['DistDistributorBalance']['updated_at'] = $this->current_datetime();
            $this->request->data['DistDistributorBalance']['updated_by'] = $this->UserAuth->getUserId();

            unset($this->request->data['DistDistributorBalance']['balance_transaction_type_id']);


            if ($this->DistDistributorBalance->save($this->request->data['DistDistributorBalance'])) {
                if (!empty($distributor_balance)) {
                    $id = $this->request->data['DistDistributorBalance']['id'];
                } else {
                    $id = $this->DistDistributorBalance->getLastInsertID();
                }
                $this->loadModel('DistDistributorBalanceHistory');
                $distributor_balance_info = $this->DistDistributorBalanceHistory->find('first', array(
                    'conditions' => array(
                        'DistDistributorBalanceHistory.office_id' => $office_id,
                        'DistDistributorBalanceHistory.dist_distributor_id' => $dist_distributor_id,
                    ),
                    'order' => 'DistDistributorBalanceHistory.id DESC',
                ));
                // pr($distributor_balance_info);
                if (!empty($distributor_balance_info)) {
                    $balance =  $distributor_balance_info['DistDistributorBalanceHistory']['balance'] - $ammount;
                } else {
                    $balance = $ammount;
                }

                $DistDistributorBalanceHistory['DistDistributorBalanceHistory'] = $this->request->data['DistDistributorBalance'];
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['transaction_amount'] = $ammount;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance'] = $balance;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['dist_distributor_balance_id'] = $id;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance_type'] = 2;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance_transaction_type_id'] = $transaction_type_id;
                $DistDistributorBalanceHistory['DistDistributorBalanceHistory']['transaction_date'] = $this->request->data['DistDistributorBalance']['updated_at'];

                //unset($DistDistributorBalanceHistory['DistDistributorBalanceHistory']['id']);
                //unset($DistDistributorBalanceHistory['DistDistributorBalanceHistory']['balance']);
                //pr($DistDistributorBalanceHistory);die();
                unset($DistDistributorBalanceHistory['DistDistributorBalanceHistory']['id']);
                $this->DistDistributorBalanceHistory->create();
                $this->DistDistributorBalanceHistory->save($DistDistributorBalanceHistory['DistDistributorBalanceHistory']);

                $this->Session->setFlash(__('Limit added successfully!'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The Dealer Wise Limit could not be saved. Please, try again.'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            }
        }
    }

    public function admin_edit_slip($id = null)
    {
        $this->loadModel('DistDistributorBalanceHistory');
        $this->set('page_title', 'Edit Distributor Balance');
        $this->DistDistributorBalanceHistory->id = $id;
        if (!$this->DistDistributorBalanceHistory->exists($id)) {
            throw new NotFoundException(__('Invalid Distributor Balance'));
        }
        $this->loadModel('DistDistributor');
        $this->loadModel('Office');
        $this->loadModel('DistDistributorBalance');
        $this->loadModel('DistDistributorBalanceHistory');
        $this->loadModel('DistBalanceTransactionType');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $this->loadModel('InstrumentType');
        $this->loadModel('Bank');
        $this->loadModel('Deposit');
        $this->loadModel('Territory');
        $this->loadModel('FiscalYear');
        $this->loadModel('Week');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $transaction_type = $this->DistBalanceTransactionType->find('list', array(
            'conditions' => array(
                // 'id IN'=> array(1,6),
                'id' => 1,
            ),
        ));
        $deposite_type = array(1 => 'Cash', 2 => 'Instrument');


        $instrument_types = $this->InstrumentType->find('list', array('conditions' => array('NOT' => array('id' => array(1, 2)))));
        $banks = $this->Bank->find('all', array('fields' => array('Bank.id', 'Bank.name')));
        $bank_list = array();
        foreach ($banks as $key => $value) {
            $bank_list[$value['Bank']['id']] = $value['Bank']['name'];
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $designation_id = $this->Session->read('Office.designation_id');
        $this->set(compact('office_parent_id'));
        $tso_conditions = array();
        if ($office_parent_id == 0) {
            $office_conditions = array(
                'office_type_id' => 2,
                "NOT" => array("id" => array(30, 31, 37))
            );
            $office_conditions = array('Office.office_type_id' => 2);
            $officelists = $this->Office->find('all', array('conditions' => $office_conditions, 'order' => array('Office.office_name' => 'asc')));

            // pr($officelists);die();
            foreach ($officelists as $key => $value) {
                $office_ids[$key] = $value['Office']['id'];
                $offices[$value['Office']['id']] = $value['Office']['office_name'];
            }
            $distDistributors = $this->DistDistributor->find('list', array('conditions' => array(
                'DistDistributor.office_id' => $office_ids, 'DistDistributor.is_active' => 1,
                //'DistDistributor.dealer_is_limit_check'=> 1
            )));
            //pr($distDistributors);die();
            $tso_conditions = array('DistTso.is_active' => 1);
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
                        'fields' => array('DistTso.id', 'DistTso.name'),
                    ));
                    //pr($dist_tso_info);die();
                    $dist_tso_id = array_keys($dist_tso_info);
                    $tso_conditions = array('dist_area_executive_id' => $dist_ae_id);
                } else {
                    $dist_tso_info = $this->DistTso->find('first', array(
                        'conditions' => array('DistTso.user_id' => $user_id),
                        'recursive' => -1,
                    ));
                    $dist_tso_id = $dist_tso_info['DistTso']['id'];
                    $tso_conditions = array('id' => $dist_tso_id);
                }

                $tso_dist_list = $this->DistTsoMapping->find('list', array(
                    'conditions' => array(
                        'dist_tso_id' => $dist_tso_id,
                    ),
                    'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
                ));
                $b_conditions = array('DistDistributor.id' => array_keys($tso_dist_list));
            } else {
                $b_conditions = array('DistDistributor.office_id' => $this->UserAuth->getOfficeId(), 'DistDistributor.is_active' => 1);
                $tso_conditions = array('office_id' => $this->UserAuth->getOfficeId());
            }
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
            $office_id = $office_conditions['Office.id'];
            $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
            $distDistributors = $this->DistDistributor->find('list', array('conditions' => $b_conditions));
        }
        $dist_tso_list = $this->DistTso->find('list', array(
            'conditions' => $tso_conditions,
            'fields' => array('DistTso.id', 'DistTso.name'),
        ));




        $this->set(compact('transaction_type', 'deposite_type', 'instrument_types', 'bank_list', 'dist_tso_list'));

        $this->set(compact('offices'));
        //for company and office list

        $prv_balance = 0;
        $prv_transaction_amount = 0;


        if ($this->request->is('post')) {
            //pr($this->request->data);die();
            $options = array('conditions' => array('DistDistributorBalanceHistory.' . $this->DistDistributorBalanceHistory->primaryKey => $id));
            $history_info = $this->DistDistributorBalanceHistory->find('first', $options);

            $prv_balance = $history_info['DistDistributorBalanceHistory']['balance'];
            $prv_transaction_amount = $history_info['DistDistributorBalanceHistory']['transaction_amount'];
            $new_balance = 0;
            $new_transaction_amount = 0;

            $office_id = $this->request->data['DistDistributorBalance']['office_id'];
            $dist_distributor_id = $this->request->data['DistDistributorBalance']['dist_distributor_id'];
            $transaction_type_id = 1;
            $dist_distributor_balance_id = $this->request->data['DistDistributorBalance']['dist_distributor_balance_id'];
            $balance_inout = 0;
            $week_id = $this->request->data['DistDistributorBalance']['sales_week'];

            $distributor_balance = $this->DistDistributorBalance->find('first', array(
                'conditions' => array(
                    'DistDistributorBalance.id' => $dist_distributor_balance_id
                )
            ));

            $ammount = $this->request->data['DistDistributorBalance']['balance'];
            if ($prv_transaction_amount > $ammount) {
                $diff_transaction_amount = $prv_transaction_amount - $ammount;
                $new_balance = $prv_balance - $diff_transaction_amount;
            }
            if ($prv_transaction_amount < $ammount) {
                $diff_transaction_amount = $ammount - $prv_transaction_amount;
                $new_balance = $diff_transaction_amount + $prv_balance;
            }
            if ($prv_transaction_amount == $ammount) {
                $new_balance = $prv_balance;
            }



            $teritrry_info = $this->Territory->find('first', array(
                'fields' => array('Territory.id', 'SalesPerson.id'),
                'conditions' => array(
                    'Territory.name like' => '%Corporate Territory%',
                    'Territory.office_id' => $office_id
                ),
            ));

            $teritrry_id = $teritrry_info['Territory']['id'];
            $sales_people_id = $teritrry_info['SalesPerson']['id'];

            /*$fiscal_year = $this->FiscalYear->find('first',array(
                'fields'=> array('FiscalYear.id'),
                'conditions' => array(
                    'FiscalYear.start_date <= '=> date('Y-m-d'),
                    'FiscalYear.end_date >= ' => date('Y-m-d')
                ),
            ));

            $fiscal_year_id = NULL;
            if(!empty($fiscal_year)){
                $fiscal_year_id = fiscal_year['FiscalYear']['id'];
            }
            
            $week_info = $this->Week->find('first',array(
                'fields'=> array('Week.id','Week.month_id'),
                'conditions' => array(
                    'Week.start_date <= '=> date('Y-m-d'),
                    'Week.end_date >= ' => date('Y-m-d')
                ),
            ));
            $week_id = NULL;
            $month_id = NULL;
            if(!empty($week_info)){
                $week_id = $week_info['Week']['id'];
                $month_id = $week_info['Week']['month_id'];
            }*/


            $balance_history = array();
            $balance_history['id'] = $id;
            $balance_history['dist_distributor_balance_id'] = $this->request->data['DistDistributorBalance']['dist_distributor_balance_id'];
            $balance_history['dist_tso_id'] = $this->request->data['DistDistributorBalance']['dist_tso_id'];
            $db_id = $balance_history['dist_distributor_id'] = $this->request->data['DistDistributorBalance']['dist_distributor_id'];
            $balance_history['balance_transaction_type_id'] = $this->request->data['DistDistributorBalance']['balance_transaction_type_id'];

            //$balance_history['balance'] = $this->request->data['DistDistributorBalance']['balance'];
            $balance_history['balance'] = $new_balance;
            $ammount = $balance_history['transaction_amount'] = $this->request->data['DistDistributorBalance']['balance'];

            $balance_history['balance_type'] = $this->request->data['DistDistributorBalance']['balance_inout'];

            $balance_history['transaction_date'] = date('Y-m-d', strtotime($this->request->data['DistDistributorBalance']['Deposite_date']));

            if ($this->DistDistributorBalanceHistory->save($balance_history)) {

                $deposit_id = $this->request->data['DistDistributorBalance']['deposit_id'];

                $this->Deposit->id = $deposit_id;
                $this->Deposit->delete();

                $dist_distributor_balance_history_id = $this->DistDistributorBalanceHistory->getLastInsertID();

                $user_id = $this->UserAuth->getUserId();
                $generate_deposite_no = 'D' . $user_id . date('d') . date('m') . date('h') . date('i') . date('s');

                $deposit = array();

                $deposit['id'] = $deposit_id;
                $deposit['temp_id'] = $generate_deposite_no;
                $deposit['memo_no'] = 0;
                $deposit['memo_id'] = 0;
                $deposit['sales_person_id'] = $sales_people_id;
                $deposit['payment_id'] = 0;
                $deposit['deposit_amount'] = $ammount;
                $deposit['deposit_date'] = date('Y-m-d', strtotime($this->request->data['DistDistributorBalance']['Deposite_date']));
                $deposit['type'] = $this->request->data['DistDistributorBalance']['deposite_type_id'];
                $deposit['instrument_type'] = $this->request->data['DistDistributorBalance']['instrument_type_id'];
                $deposit['instrument_ref_no'] = $this->request->data['DistDistributorBalance']['instrument_ref_no'];
                $deposit['bank_branch_id'] = $this->request->data['DistDistributorBalance']['bank_branch_id'];
                $deposit['slip_no'] = $this->request->data['DistDistributorBalance']['reference_number'];
                $deposit['instrument_clearing_status'] = '';
                $deposit['week_id'] = $week_id;
                //$deposit['fiscal_year_id'] = $fiscal_year_id;
                //$deposit['month_id'] = $month_id;
                $deposit['remarks'] = '';
                $deposit['created_at'] = date('Y-m-d', strtotime($this->request->data['DistDistributorBalance']['Deposite_date']));
                $deposit['action'] = 1;
                $deposit['territory_id'] = $teritrry_id;
                $deposit['is_distributor'] = 1;
                $deposit['dist_distributor_balance_history_id'] = $id;

                $this->Deposit->create();
                $this->Deposit->save($deposit);

                $sql = "DECLARE @db_id  INT, 
                          @from_tran_id INT;
						SET @db_id=$db_id;
                        SET @from_tran_id=$id;
                          DECLARE @history_id    INT, 
                          @history_tran_amount DECIMAL(18,2), 
                          @history_balance     DECIMAL(18,2), 
                          @history_inout_type  INT, 
                          @inout               INT;
						  DECLARE @prev_balance  DECIMAL(10,2);
						SELECT @prev_balance=balance 
                        FROM   dist_distributor_balance_histories 
                        WHERE  dist_distributor_id=@db_id 
                        AND    id = @from_tran_id;DECLARE history CURSOR FOR 
                        SELECT   id, 
                                 balance, 
                                 transaction_amount, 
                                 balance_type 
                        FROM     dist_distributor_balance_histories 
                        WHERE    dist_distributor_id=@db_id 
                        AND      id >@from_tran_id 
                        ORDER BY id;OPEN history;FETCH next 
                        FROM  history 
                        INTO  @history_id, 
                              @history_balance, 
                              @history_tran_amount, 
                              @history_inout_type WHILE @@FETCH_STATUS = 0 
                        BEGIN 
                          IF @prev_balance IS NULL 
                          BEGIN 
                            UPDATE dist_distributor_balance_histories 
                            SET    balance=@history_tran_amount 
                            WHERE  id=@history_id; 
                             
                            SET @prev_balance=@history_tran_amount; 
                          END 
                          ELSE 
                          BEGIN 
                            IF @history_inout_type=1 
                            BEGIN 
                              UPDATE dist_distributor_balance_histories 
                              SET    balance=@prev_balance+@history_tran_amount 
                              WHERE  id=@history_id; 
                               
                              SET @prev_balance=@prev_balance+@history_tran_amount; 
                            END 
                            ELSE 
                            BEGIN 
                              UPDATE dist_distributor_balance_histories 
                              SET    balance=@prev_balance-@history_tran_amount 
                              WHERE  id=@history_id; 
                               
                              SET @prev_balance=@prev_balance-@history_tran_amount; 
                            END 
                          END 
                          FETCH next 
                          FROM  history 
                          INTO  @history_id, 
                                @history_balance, 
                                @history_tran_amount, 
                                @history_inout_type 
                        END UPDATE dist_distributor_balances 
                        SET    balance=@prev_balance 
                        WHERE  dist_distributor_id=$db_id;CLOSE history;
                        DEALLOCATE history";
                //pr($sql);die();
                //if($prv_transaction_amount != $ammount){
                $this->DistDistributorBalanceHistory->query($sql);
                //}
                $this->Session->setFlash(__('Balance added successfully!'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('Balance could not be saved. Please, try again.'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $options = array('conditions' => array('DistDistributorBalanceHistory.' . $this->DistDistributorBalanceHistory->primaryKey => $id));
            $this->request->data = $this->DistDistributorBalanceHistory->find('first', $options);
            //pr($this->request->data);die();
            $office_id = $this->request->data['DistDistributorBalanceHistory']['office_id'];
            $dist_tso_id = $this->request->data['DistDistributorBalanceHistory']['dist_tso_id'];
            $dist_distributor_id = $this->request->data['DistDistributorBalanceHistory']['dist_distributor_id'];
            $deposite_type_id = $this->request->data['Deposit']['type'];
            $week_id = $this->request->data['Deposit']['week_id'];
            $deposit_date = $this->request->data['Deposit']['deposit_date'];
            $tso_dist_list = $this->DistTsoMapping->find('list', array(
                'conditions' => array(
                    'dist_tso_id' => $dist_tso_id,
                ),
                'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
            ));
            $b_conditions = array('DistDistributor.id' => array_keys($tso_dist_list));

            $distDistributors = $this->DistDistributor->find('list', array('conditions' => $b_conditions));

            $this->loadModel('BankBranch');

            $bank_branch = $this->BankBranch->find('first', array(
                'conditions' => array(
                    'BankBranch.id' => $this->request->data['Deposit']['bank_branch_id']
                ),
            ));
            $bank_id = $bank_branch['BankBranch']['bank_id'];
            $bank_branches = $this->BankBranch->find('list', array(
                'conditions' => array(
                    'BankBranch.bank_id' => $bank_id
                ),
            ));

            $conditions = array(
                'Week.start_date >=' => date('Y-m-01', strtotime($deposit_date . '-2 months')),
                'Week.start_date <=' => date('Y-m-t', strtotime($deposit_date))
            );

            $weeks = $this->Week->find('list', array(
                //'fields' => array('Week.id', 'Week.week_name', 'Week.updated_at', 'Week.start_date', 'Week.end_date'),
                'conditions' => $conditions,
                'order' => array('Week.updated_at' => 'asc'),
                'recursive' => -1
            ));
            $this->set(compact('distDistributors', 'bank_id', 'bank_branches', 'week_id', 'weeks', 'deposite_type_id'));
        }
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
        $this->loadModel('DistDistributorBalance');
        $this->loadModel('DistDistributorBalanceHistory');
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->DistAreaExecutive->id = $id;
        if (!$this->DistAreaExecutive->exists()) {
            throw new NotFoundException(__('Invalid Area Executive'));
        }
        $this->check_data_by_company('DistDistributorBalance', $id);
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
    public function admin_get_tso()
    {
        $office_id = $this->request->data['office_id'];
        $rs = array(array('id' => '', 'name' => '---- All -----'));
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $this->loadModel('DistDistributor');
        if ($user_group_id == 1029 || $user_group_id == 1028) {
            if ($user_group_id == 1028) {
                $dist_ae_info = $this->DistAreaExecutive->find('first', array(
                    'conditions' => array('DistAreaExecutive.user_id' => $user_id),
                    'recursive' => -1,
                ));
                $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];

                $tso_conditions = array('dist_area_executive_id' => $dist_ae_id);
            } else {
                $tso_conditions = array('user_id' => $user_id);
            }
        } else {
            $tso_conditions = array('office_id' => $office_id);
        }

        $dist_tso_info = $this->DistTso->find('all', array(
            'conditions' => $tso_conditions,
            'fields' => array('DistTso.id', 'DistTso.name'),
            'recursive' => -1,
        ));

        $data_array = array();
        foreach ($dist_tso_info as $key => $value) {
            $data_array[] = array(
                'id' => $value['DistTso']['id'],
                'name' => $value['DistTso']['name'],
            );
        }

        if (!empty($dist_tso_info)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }
    public function admin_get_distribute()
    {
        $tso_id = $this->request->data['dist_tso_id'];
        $rs = array(array('id' => '', 'name' => '---- All -----'));
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $this->loadModel('DistDistributor');
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
            $dist_conditions = array('DistDistributor.id' => array_keys($tso_dist_list), 'DistDistributor.is_active' => 1);
        } elseif ($user_group_id == 1034) {
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');
            $distributor = $this->DistUserMapping->find('first', array(
                'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];

            $dist_conditions = array('DistDistributor.id' => $distributor_id, 'DistDistributor.is_active' => 1);
        } else {

            $tso_dist_list = $this->DistTsoMapping->find('list', array(
                'conditions' => array(
                    'dist_tso_id' => $tso_id,
                ),
                'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
            ));
            $dist_conditions = array('DistDistributor.id' => array_keys($tso_dist_list), 'DistDistributor.is_active' => 1);
        }

        $distributor_list = $this->DistDistributor->find('all', array(
            'conditions' => $dist_conditions,
            'order' => array('DistDistributor.name' => 'asc'),
            'recursive' => -1,
        ));

        $data_array = array();
        foreach ($distributor_list as $key => $value) {
            $data_array[] = array(
                'id' => $value['DistDistributor']['id'],
                'name' => $value['DistDistributor']['name'],
            );
        }

        if (!empty($distributor_list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function admin_get_distribute_without_tso()
    {
        $office_id = $this->request->data['office_id'];
        $rs = array(array('id' => '', 'name' => '---- All -----'));
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $this->loadModel('DistDistributor');
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

            $dist_conditions = array('DistDistributor.id' => $distributor_id, 'DistDistributor.is_active' => 1);
        } else {

            $dist_conditions = array('DistDistributor.office_id' => $office_id, 'DistDistributor.is_active' => 1);
        }

        $distributor_list = $this->DistDistributor->find('all', array(
            'conditions' => $dist_conditions,
            'order' => array('DistDistributor.name' => 'asc'),
            'recursive' => -1,
        ));

        $data_array = array();
        foreach ($distributor_list as $key => $value) {
            $data_array[] = array(
                'id' => $value['DistDistributor']['id'],
                'name' => $value['DistDistributor']['name'],
            );
        }

        if (!empty($distributor_list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }
    public function admin_get_balance_transaction_type()
    {

        $dist_distributor_id = $this->request->data['dist_distributor_id'];
        $slip = $this->request->data['slip'];
        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $this->loadModel('DistDistributorBalance');
        $this->loadModel('DistDistributorBalanceHistory');
        $this->loadModel('DistBalanceTransactionType');

        $distributor_balance_info = $this->DistDistributorBalance->find('first', array(
            'conditions' => array(
                'DistDistributorBalance.dist_distributor_id' => $dist_distributor_id
            ),
        ));
        if ($slip == 0) {
            $conditions = array(
                'OR' => array(
                    array('DistBalanceTransactionType.id IN' => array(1, 6)),
                    array('DistBalanceTransactionType.status' => 1)
                )
            );
        } else {
            $conditions = array('DistBalanceTransactionType.id' => 1);
        }
        $data_array = "<option value=''>--- Select ---</option>";
        if ($distributor_balance_info) {
            $transaction_type = $this->DistBalanceTransactionType->find('all', array(
                'conditions' => $conditions,
            ));
            //$dist_balance = $distributor_balance_info['DistDistributorBalance']['balance'];
        } else {
            $transaction_type = $this->DistBalanceTransactionType->find('all', array(
                'conditions' => array(
                    'id' => 7,
                ),
            ));
            //$dist_balance = 0;
        }
        foreach ($transaction_type as $key => $data) {
            $k = $data['DistBalanceTransactionType']['id'];
            $v = $data['DistBalanceTransactionType']['name'];
            $data_array .= "<option value='$k'>$v</option>";
        }
        echo $data_array;
        $this->autoRender = false;
    }
    public function admin_get_last_balance_ammount()
    {
        $dist_distributor_id = $this->request->data['dist_distributor_id'];
        $transaction_type_id = $this->request->data['transaction_type_id'];
        //pr($dist_distributor_id);
        $this->loadModel('DistDistributorBalance');
        $this->loadModel('DistBalanceTransactionType');
        $balance_inout_info = $this->DistBalanceTransactionType->find('first', array(
            'conditions' => array(
                'DistBalanceTransactionType.id' => $transaction_type_id
            ),
        ));

        $distributor_balance_info = $this->DistDistributorBalance->find('first', array(
            'conditions' => array(
                'DistDistributorBalance.dist_distributor_id' => $dist_distributor_id
            ),
        ));
        /*pr($distributor_balance_info);
        die();*/
        $dist_balance = $distributor_balance_info['DistDistributorBalance']['balance'];
        if ($balance_inout_info) {
            $balance_inout = $balance_inout_info['DistBalanceTransactionType']['inout'];
        }
        if ($distributor_balance_info) {
            if ($dist_balance > 0) {
                $data_array = array(
                    'balance' => $dist_balance,
                    'balance_inout' => $balance_inout
                );
            }
        }
        //pr($data_array);die();
        echo json_encode($data_array);
        $this->autoRender = false;
    }

    public function admin_get_bank_branch()
    {
        $bank_id = $this->request->data['bank_id'];
        $rs = array(array('id' => '', 'name' => '---- Select -----'));
        $this->loadModel('BankBranch');

        $bank_branches = $this->BankBranch->find('all', array(
            'conditions' => array(
                'BankBranch.bank_id' => $bank_id
            ),
        ));

        $data_array = array();
        foreach ($bank_branches as $key => $value) {
            $data_array[] = array(
                'id' => $value['BankBranch']['id'],
                'name' => $value['BankBranch']['name'],
            );
        }

        if (!empty($bank_branches)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_week()
    {
        $this->loadModel('Week');
        $deposit_date = $this->request->data['deposit_date'];
        $weeks = $this->Week->find(
            'list',
            array(
                'conditions' => array(
                    'start_date >=' => date('Y-m-01', strtotime($deposit_date . '-2 months')),
                    'end_date <=' => date('Y-m-t', strtotime($deposit_date))
                )
            )
        );
        $view = new View($this);
        $form = $view->loadHelper('Form');
        if ($weeks) {
            $form->create('DistDistributorBalance', array('role' => 'form', 'action' => 'index'));

            echo $form->input(
                'sales_week',
                array(
                    'id' => 'sales_week', 'class' => 'form-control sales_week', 'empty' => '----Select Week----',
                    'options' => $weeks,
                    'required' => true
                )
            );
            $form->end();
        } else {
            echo '';
        }
        $this->autoRender = false;
    }
}
