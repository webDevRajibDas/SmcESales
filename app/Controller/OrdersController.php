<?php
App::uses('AppController', 'Controller');

/**
 * Orders Controller
 *
 * @property Order $Order
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OrdersController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $uses = array('Order', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'Combination', 'OrderDetail', 'MeasurementUnit');
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index()
    {
        /* $confirmation_type=$this->Session->read('Office.company_info.Company.confirmation_type');
        pr($confirmation_type);die();*/

        //Configure::write('debug',2);



        $this->loadModel('DistTso');
        $this->loadModel('DistOutletMap');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $product_name = $this->Product->find('all', array(
            'fields' => array('Product.name', 'Product.id', 'MU.name as mes_name', 'Product.product_category_id'),
            'joins' => array(
                array(
                    'table' => 'measurement_units',
                    'alias' => 'MU',
                    'type' => 'LEFT',
                    'conditions' => array('MU.id= Product.sales_measurement_unit_id')
                )
            ),
            'conditions' => array('NOT' => array('Product.product_category_id' => 32)),
            'order' => 'Product.product_category_id',
            'recursive' => -1
        ));

        //pr($product_name);
        $requested_data = $this->request->data;
        //pr($requested_data);die();
        $this->set('page_title', 'Requisitions List');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');



        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $designation_id = $this->Session->read('Office.designation_id');

        $this->set('office_parent_id', $office_parent_id);

        if ($office_parent_id == 0) {
            $conditions = array('Order.order_date >=' => $this->current_date() . ' 00:00:00', 'Order.order_date <=' => $this->current_date() . ' 23:59:59');
            $office_conditions = array(
                'office_type_id' => 2,
                "NOT" => array("id" => array(30, 31, 37))
            );
            $dist_tso_info = $this->DistTso->find('list', array(
                'fields' => array('DistTso.id', 'DistTso.name'),
                'recursive' => -1,
            ));
            $tsos = $dist_tso_info;
        } else {
            if ($user_group_id == 1029 || $user_group_id == 1028) {
                if ($user_group_id == 1028) {
                    $dist_ae_info = $this->DistAreaExecutive->find('first', array(
                        'conditions' => array('DistAreaExecutive.user_id' => $user_id, 'DistAreaExecutive.is_active' => 1),
                        'recursive' => -1,
                    ));
                    $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
                    $dist_tso_info = $this->DistTso->find('list', array(
                        'conditions' => array('dist_area_executive_id' => $dist_ae_id),
                        'fields' => array('DistTso.id', 'DistTso.name'),
                    ));
                    $tsos = $dist_tso_info;
                    $dist_tso_id = array_keys($dist_tso_info);
                } else {
                    $dist_tso_info = $this->DistTso->find('first', array(
                        'conditions' => array('DistTso.user_id' => $user_id, 'DistTso.is_active' => 1),
                        'fields' => array('DistTso.id', 'DistTso.name'),
                        'recursive' => -1,
                    ));
                    $dist_tso_id = $dist_tso_info['DistTso']['id'];

                    $tsos[$dist_tso_info['DistTso']['id']] = $dist_tso_info['DistTso']['name'];
                }
                $dist_tso_map_list = $this->DistTsoMapping->find('all', array(
                    'conditions' => array('DistTsoMapping.dist_tso_id' => $dist_tso_id)
                ));
                $dist_list = array();
                foreach ($dist_tso_map_list as $key => $value) {
                    $dist_list[$key] = $value['DistTsoMapping']['dist_distributor_id'];
                }
                $dist_outlet_map_list = $this->DistOutletMap->find('list', array(
                    'conditions' => array(
                        'dist_distributor_id' => $dist_list
                    ),
                    'fields' => array('DistOutletMap.outlet_id', 'DistOutletMap.dist_distributor_id'),
                ));
                $dist_outlet_list = array_keys($dist_outlet_map_list);

                $conditions = array('Order.outlet_id' => $dist_outlet_list, 'Order.order_date >=' => $this->current_date() . ' 00:00:00', 'Order.order_date <=' => $this->current_date() . ' 23:59:59');
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $this->loadModel('DistTsoMapping');


                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];

                $tsos_info = $this->DistTsoMapping->find('first', array(
                    'conditions' => array(
                        'DistTsoMapping.dist_distributor_id' => $distributor_id
                    )
                ));
                $tsos[$tsos_info['DistTso']['id']] = $tsos_info['DistTso']['name'];

                $dist_outlet_map_list = $this->DistOutletMap->find('list', array(
                    'conditions' => array(
                        'dist_distributor_id' => $distributor_id
                    ),
                    'fields' => array('DistOutletMap.outlet_id', 'DistOutletMap.dist_distributor_id'),
                ));
                $dist_outlet_list = array_keys($dist_outlet_map_list);

                $conditions = array('Order.outlet_id' => $dist_outlet_list, 'Order.order_date >=' => $this->current_date() . ' 00:00:00', 'Order.order_date <=' => $this->current_date() . ' 23:59:59');
            } else {
                $tsos = $this->DistTso->find('list', array(
                    'conditions' => array(
                        'office_id' => $this->UserAuth->getOfficeId(),
                        'is_active' => 1
                    ),
                ));
                $conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId(), 'Order.order_date >=' => $this->current_date() . ' 00:00:00', 'Order.order_date <=' => $this->current_date() . ' 23:59:59');
            }

            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }

        /*else
		{
			$conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId(), 'Order.order_date >=' => $this->current_date() . ' 00:00:00', 'Order.order_date <=' => $this->current_date() . ' 23:59:59');
            
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());	
		}*/

        $group = array('Order.id', 'Order.order_no', 'Order.confirmed', 'Order.from_app', 'Order.memo_reference_no', 'Order.gross_value', 'Order.order_date', 'Order.order_time', 'Order.credit_amount', 'Order.memo_editable', 'Order.status', 'Order.confirm_status', 'Order.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Office.office_name', 'Order.office_id', 'Order.outlet_id', 'Order.territory_id', 'Order.confirm_status', 'DistOutletMap.dist_distributor_id', 'Order.market_id');
        if (isset($requested_data['Order']['payment_status'])) {
            if ($requested_data['Order']['payment_status'] == 1) {
                $group = array(
                    'Order.id', 'Order.order_no', 'Order.from_app', 'Order.memo_reference_no', 'Order.confirmed', 'Order.gross_value', 'Order.order_time', 'Order.credit_amount', 'Order.memo_editable', 'Order.status', 'Order.order_date', 'Order.confirm_status', 'Order.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Office.office_name', 'Order.office_id', 'Order.outlet_id', 'Order.territory_id', 'Order.confirm_status', 'DistOutletMap.dist_distributor_id', 'Order.market_id '
                );
            } elseif ($requested_data['Order']['payment_status'] == 2) {
                $group = array('Order.id', 'Order.order_no', 'Order.from_app', 'Order.memo_reference_no', 'Order.confirmed', 'Order.gross_value', 'Order.order_time', 'Order.order_date', 'Order.credit_amount', 'Order.memo_editable', 'Order.status', 'Order.action', 'Outlet.name', 'Market.name', 'Office.office_name', 'Order.office_id', 'Territory.name', 'Order.outlet_id', 'Order.territory_id', 'Order.confirm_status', 'DistOutletMap.dist_distributor_id', 'Order.market_id ');
            }
        }

        $this->Order->recursive = 0;

        $conditions1 = array(
            "NOT" => array("Order.id" => strtotime("now"))
        );

        $conditions2 = array_merge($conditions, $conditions1);

        $this->paginate = array(
            'fields' => array(
                'Order.id', 'Order.order_no', 'Order.from_app', 'Order.confirmed', 'Order.memo_reference_no', 'Order.gross_value', 'Order.order_date', 'Order.order_time', 'Order.credit_amount', 'Order.status', 'Order.memo_editable', 'Order.action', 'Outlet.name', 'Market.name', 'Territory.name', 'Office.office_name', 'Order.office_id', 'Order.outlet_id', 'Order.territory_id', 'Order.market_id', 'Order.confirm_status', 'DistOutletMap.dist_distributor_id'
            ),
            'conditions' => $conditions2,
            'joins' => array(

                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'conditions' => 'Order.office_id=Office.id',
                    'type' => 'Left'
                ),
                array(
                    'table' => 'markets',
                    'alias' => 'Market',
                    'conditions' => 'Order.market_id=Market.id',
                    'type' => 'Left'
                ),
                array(
                    'table' => 'outlets',
                    'alias' => 'Outlet',
                    'conditions' => 'Order.outlet_id=Outlet.id',
                    'type' => 'Left'
                ),
                array(
                    'table' => 'dist_outlet_maps',
                    'alias' => 'DistOutletMap',
                    'conditions' => 'Order.outlet_id=DistOutletMap.outlet_id',
                    'type' => 'Left'
                ),
                array(
                    'table' => 'territories',
                    'alias' => 'Territory',
                    'conditions' => 'Territory.id=Order.territory_id',
                    'type' => 'Left'
                ),

            ),
            'group' => $group,
            'order' => array('Order.id' => 'desc'),
            'limit' => 100
        );

        //pr($this->paginate());exit;

        /*array(
                    'table'=>'dist_tsos',
                    'alias'=>'Tso',
                    'conditions'=>'Tso.office_id=Order.office_id',
                    'type'=>'Left'
                    )*/


        $this->set('orders', $this->paginate());

        //$order = array();
        //$this->set('orders', $order);

        //echo 'hellosadfsad';exit;
        //pr($this->paginate());die();
        $this->set('office_id', $this->UserAuth->getOfficeId());
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        $office_id = isset($this->request->data['Order']['office_id']) != '' ? $this->request->data['Order']['office_id'] : 0;
        $territory_id = isset($this->request->data['Order']['territory_id']) != '' ? $this->request->data['Order']['territory_id'] : 0;
        $market_id = isset($this->request->data['Order']['market_id']) != '' ? $this->request->data['Order']['market_id'] : 0;
        $outlet_id = isset($this->request->data['Order']['outlet_id']) != '' ? $this->request->data['Order']['outlet_id'] : 0;
        $tso_id = isset($this->request->data['Order']['tso_id']) != '' ? $this->request->data['Order']['tso_id'] : 0;

        $distributors = array();
        $full_dis_conditions = array();
        if ($office_id) {
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
                $full_dis_conditions = array(
                    'conditions' => array('DistDistributor.id' => array_keys($tso_dist_list), 'DistDistributor.is_active' => 1), 'order' => array('DistDistributor.name' => 'asc'),
                );
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];

                $full_dis_conditions = array(
                    'conditions' => array('DistDistributor.id' => $distributor_id, 'DistDistributor.is_active' => 1), 'order' => array('DistDistributor.name' => 'asc'),
                );
            } else {
                $full_dis_conditions = array(
                    'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.is_active' => 1), 'order' => array('DistDistributor.name' => 'asc'),
                );
            }
        }

        $this->loadModel('DistDistributor');

        $distributors_info = $this->DistDistributor->find('all', $full_dis_conditions);
        if (!empty($distributors_info)) {
            foreach ($distributors_info as $key => $value) {
                $distributors[$value['DistOutletMap']['outlet_id']] = $value['DistDistributor']['name'];
            }
        }
        $this->loadModel('Territory');
        $territory = $this->Territory->find('all', array(
            'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
            'conditions' => array('Territory.office_id' => $office_id),
            'order' => array('Territory.name' => 'asc'),
            'recursive' => 0
        ));

        $data_array = array();

        foreach ($territory as $key => $value) {
            $t_id = $value['Territory']['id'];
            $t_val = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
            $data_array[$t_id] = $t_val;
        }

        $territories = $data_array;

        /*
        $territories = $this->Order->Territory->find('list', array(
            'conditions' => array('Territory.office_id' => $office_id),
            'order' => array('Territory.name' => 'asc')
            ));
        */

        if ($territory_id) {
            $markets = $this->Order->Market->find('list', array(
                'conditions' => array('Market.territory_id' => $territory_id),
                'order' => array('Market.name' => 'asc')
            ));
        } else {
            $markets = array();
        }

        $outlets = $this->Order->Outlet->find('list', array(
            'conditions' => array('Outlet.market_id' => $market_id),
            'order' => array('Outlet.name' => 'asc')
        ));
        // print_r($outlets);die();
        //print_r($Distributors);die();
        $current_date = date('d-m-Y', strtotime($this->current_date()));

        /*
         * Report generation query start ;
         */
        if (!empty($requested_data)) {
            if (!empty($requested_data['Order']['office_id'])) {


                $office_id = $requested_data['Order']['office_id'];
                $this->Order->recursive = -1;
                $sales_people = $this->Order->find('all', array(
                    'fields' => array('DISTINCT(sales_person_id) as sales_person_id', 'SalesPerson.name'),
                    'joins' => array(
                        array(
                            'table' => 'sales_people',
                            'alias' => 'SalesPerson',
                            'type' => 'INNER',
                            'conditions' => array(
                                ' SalesPerson.id=Order.sales_person_id',
                                'SalesPerson.office_id' => $office_id
                            )
                        )
                    ),
                    'conditions' => array(
                        'Order.order_date BETWEEN ? and ?' => array(date('Y-m-d', strtotime($requested_data['Order']['date_from'])), date('Y-m-d', strtotime($requested_data['Order']['date_to'])))
                    ),
                ));

                $sales_person = array();
                foreach ($sales_people as  $data) {
                    $sales_person[] = $data['0']['sales_person_id'];
                }
                $sales_person = implode(',', $sales_person);

                //pr($sales_person);

                if (!empty($sales_person)) {
                    $product_quantity = $this->Order->query(" SELECT m.sales_person_id,md.product_id,SUM(md.sales_qty) as pro_quantity
                       FROM orders m RIGHT JOIN order_details md on md.order_id=m.id
                       WHERE (m.order_date BETWEEN  '" . date('Y-m-d', strtotime($requested_data['Order']['date_from'])) . "' AND '" . date('Y-m-d', strtotime($requested_data['Order']['date_to'])) . "') AND sales_person_id IN (" . $sales_person . ")  GROUP BY m.sales_person_id,md.product_id");
                    $this->set(compact('product_quantity', 'sales_people'));
                }
            }
        }

        $this->set(compact('offices', 'territories', 'markets', 'outlets', 'current_date', 'requested_data', 'product_name', 'Distributors', 'outlet_id', 'distributors', 'tsos', 'tso_id'));
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

        //$this->check_data_by_company('Order',$id);
        $this->set('page_title', 'Requisition Order Confirmation Details');
        $dealer_is_limit_check = 1;
        $this->Order->unbindModel(array('hasMany' => array('OrderDetail')));
        $order = $this->Order->find('first', array(
            'conditions' => array('Order.id' => $id)
        ));
        $this->loadModel('DistOutletMap');
        $outletInfo = $this->DistOutletMap->find('first', array('conditions' => array('DistOutletMap.outlet_id' => $order['Order']['outlet_id'])));

        //pr($order);die();
        $this->loadModel('DistDistributor');
        $distributor = $this->DistDistributor->find('first', array('conditions' => array('DistDistributor.id' => $outletInfo['DistDistributor']['id'])));

        //$orderLimits=$distributor['DistDistributorBalanceHistory'];
        $orderDate = $order['Order']['order_date'];
        //pr($orderLimits);die();

        $this->loadModel('DistDistributorBalanceHistory');
        $limts = $this->DistDistributorBalanceHistory->find('first', array(
            'fields' => array('balance'),
            'conditions' => array(
                //'DistDistributorBalanceHistory.effective_date >=' => $orderDate,
                'DistDistributorBalanceHistory.dist_distributor_id' => $outletInfo['DistDistributor']['id'],
            ),
            //'order' => 'DistDistributorBalanceHistory.effective_date DESC',
            'limit' => 1,
            'recursive' => -1

        ));
        //pr($limts);die();
        $orderLimits = $limts['DistDistributorBalanceHistory']['balance'];

        $this->loadModel('DistDistributorBalanceHistory');
        $dealer_balance_info = $this->DistDistributorBalanceHistory->find('first', array(
            'conditions' => array(
                'DistDistributorBalanceHistory.dist_distributor_id' => $outletInfo['DistDistributor']['id'],
                //'DistDistributorBalanceHistory.is_execute' => 1,
            ),
            'order' => 'DistDistributorBalanceHistory.id DESC',
            'recursive' => -1
        ));
        $balance = $dealer_balance_info['DistDistributorBalanceHistory']['balance'];


        //pr($dealer_balance_info);die();
        $this->loadModel('OrderDetail');
        if (!$this->Order->exists($id)) {
            throw new NotFoundException(__('Invalid district'));
        }
        $this->OrderDetail->recursive = 0;
        $order_details = $this->OrderDetail->find(
            'all',
            array(
                'conditions' => array('OrderDetail.order_id' => $id),
                'joins' => array(
                    array(
                        'table' => 'products',
                        'alias' => 'Product',
                        'type' => 'Left',
                        'conditions' => 'Product.id=OrderDetail.product_id'
                    )
                ),
                'order' => array('Product.order' => 'asc')
            )
        );

        $this->set(compact('order', 'order_details', 'distributor', 'orderLimits', 'balance'));
    }


    /**
     * admin_delete method
     *
     * @return void
     */
    public function admin_delete($id = null, $redirect = 1)
    {

        $this->loadModel('Product');
        $this->loadModel('Order');
        $this->loadModel('OrderDetail');
        $this->loadModel('Deposit');
        $this->loadModel('Collection');
        //$this->check_data_by_company('Order',$id);
        if ($this->request->is('post')) {

            /*
             * This condition added for data synchronization 
             * Cteated by imrul in 09, April 2017
             * Duplicate order check
             */

            $order_id_arr = $this->Order->find('first', array(
                'conditions' => array(
                    'Order.id' => $id
                ),
                'recursive' => -1
            ));
        }

        $order_id = $order_id_arr['Order']['id'];
        $this->Order->id = $order_id;
        $this->OrderDetail->deleteAll(array('OrderDetail.order_id' => $order_id));
        $this->Order->delete();

        if ($redirect == 1) {
            $this->flash(__('Order was not deleted'), array('action' => 'index'));
            $this->redirect(array('action' => 'index'));
        } else {
        }
    }

    public function admin_confirm($id = null)
    {
        $this->set('page_title', 'Requisition Orders Confirmation Detail');


        $office_type_id = $this->Session->read('Office.office_type_id');
        $this->Order->unbindModel(array('hasMany' => array('OrderDetail')));
        $order = $this->Order->find('first', array(
            'conditions' => array('Order.id' => $id)
        ));
        $this->loadModel('Store');
        $this->loadModel('Outlet');
        $this->loadModel('DistOutletMap');

        $outletInfo = $this->DistOutletMap->find('first', array('conditions' => array('DistOutletMap.outlet_id' => $order['Order']['outlet_id'])));

        $distributor_id = $outletInfo['DistDistributor']['id'];
        $this->loadModel('DistDistributor');
        $distributor = $this->DistDistributor->find('first', array('conditions' => array('DistDistributor.id' => $distributor_id)));

        //for distibuter limit amount

        //$orderLimits=$distributor['DistDistributorBalanceHistory'];
        $orderDate = $order['Order']['order_date'];
        //pr($orderLimits);die();
        $this->loadModel('DistDistributorBalanceHistory');
        $limts = $this->DistDistributorBalanceHistory->find('first', array(
            'fields' => array('balance'),
            'conditions' => array(
                //'DistDistributorBalanceHistory.effective_date >=' => $orderDate,
                'DistDistributorBalanceHistory.dist_distributor_id' => $distributor_id
            ),
            'order' => array('DistDistributorBalanceHistory.id DESC'),
            'limit' => 1,
            'recursive' => -1

        ));
        $orderLimits = $limts['DistDistributorBalanceHistory']['balance'];

        $this->loadModel('DistDistributorBalanceHistory');
        $dealer_balance_info = $this->DistDistributorBalanceHistory->find('first', array(

            'conditions' => array(
                'DistDistributorBalanceHistory.dist_distributor_id' => $distributor_id,

            ),
            'order' => 'DistDistributorBalanceHistory.id DESC',
            'recursive' => -1
        ));
        $balance = $dealer_balance_info['DistDistributorBalanceHistory']['balance'];


        //$this->set(compact('dealer_is_limit_check'));   
        // pr($dealer_balance_info);die();
        $this->loadModel('OrderDetail');
        if (!$this->Order->exists($id)) {
            throw new NotFoundException(__('Invalid district'));
        }
        $this->OrderDetail->recursive = 0;
        $order_details = $this->OrderDetail->find(
            'all',
            array(
                'conditions' => array('OrderDetail.order_id' => $id),
                'joins' => array(
                    array(
                        'table' => 'products',
                        'alias' => 'Product',
                        'type' => 'Left',
                        'conditions' => 'Product.id=OrderDetail.product_id'
                    )
                ),
                'order' => array('Product.order' => 'asc')
            )
        );
        $this->loadModel('Product');
        $bns_product = array();
        foreach ($order_details as $key => $value) {
            if ($value['OrderDetail']['bonus_qty'] != 0) {
                $bns_product_list = $this->Product->find('first', array(
                    'conditions' => array('Product.id' => $value['OrderDetail']['bonus_product_id']),
                    'recursive' => -1,
                ));
                $bns_product[$bns_product_list['Product']['id']] = $bns_product_list['Product']['name'];
            }
        }
        if ($order['Order']['confirmed'] == 1) {
            $this->Session->setFlash(__('Already confirmed!'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }
        if ($this->request->is('post')) {
            if ($order['Order']['confirmed'] == 1) {
                $this->Session->setFlash(__('Already confirmed!'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            }
            //pr($this->request->data);die();
            $this->loadModel('Order');
            $data = array();
            if ($this->request->data['confirm'] == 'Confirm') {
                $data['Order']['id'] = $id;
                $data['Order']['confirmed'] = 1;
                $data['Order']['confirm_status'] = 0;
                $data['Order']['w_store_id'] = $this->request->data['Order']['w_store_id'];
                //pr($data);
                //exit;
                //pr($data);die();
                if ($this->Order->save($data)) {
                    $this->Session->setFlash(__('Confirmation Successful!'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                }
            }
        }
        $this->set(compact('order', 'order_details', 'distributor', 'orderLimits', 'balance', 'id', 'bns_product'));
    }


    //for bonus and bouns schema
    public function bouns_and_scheme_id_set($b_product_id = 0, $order_date = '')
    {
        $this->loadModel('Bonus');
        //$this->loadModel('OpenCombination');
        //$this->loadModel('OpenCombinationProduct');

        $bonus_result = array();

        $b_product_qty = 0;
        $bonus_id = 0;
        $bonus_scheme_id = 0;

        $bonus_info = $this->Bonus->find(
            'first',
            array(
                'conditions' => array(
                    'Bonus.effective_date <= ' => $order_date,
                    'Bonus.end_date >= ' => $order_date,
                    'Bonus.bonus_product_id' => $b_product_id
                ),
                'recursive' => -1,
            )
        );

        //pr($bonus_info);

        if ($bonus_info) {
            $bonus_table_id = $bonus_info['Bonus']['id'];
            $mother_product_id = $bonus_info['Bonus']['mother_product_id'];
            $mother_product_quantity = $bonus_info['Bonus']['mother_product_quantity'];

            $bonus_id = $bonus_table_id;

            //echo $bonus_id;
            //break;
        }


        /*echo 'Bonus = '.$bonus_id;
		echo '<br>';
		echo 'Bonus Scheme = '. $bonus_scheme_id;
		echo '<br>';
		echo '<br>';
		echo '<br>';*/

        $bonus_result['bonus_id'] = $bonus_id;
        $bonus_result['bonus_scheme_id'] = $bonus_scheme_id;

        return $bonus_result;
    }


    /* ----- ajax methods ----- */

    public function market_list()
    {
        $rs = array(array('id' => '', 'name' => '---- Select Market -----'));
        $thana_id = $this->request->data['thana_id'];
        $territory_id = $this->request->data['territory_id'];
        //$thana_id = 2;
        $market_list = $this->Market->find('all', array(
            'conditions' => array('Market.thana_id' => $thana_id, 'Market.territory_id' => $territory_id),
            'order' => array('Market.name' => 'asc'),
        ));
        $data_array = Set::extract($market_list, '{n}.Market');
        if (!empty($data_array)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_sales_officer_list()
    {
        $user_office_id = $this->UserAuth->getOfficeId();
        //$user_office_id = 2;
        $rs = array(array('id' => '', 'name' => '---- Select Market -----'));
        $sale_type_id = $this->request->data['sale_type_id'];
        //$sale_type_id = 1;
        if ($sale_type_id == 1 || $sale_type_id == 2 || $sale_type_id == 3 || $sale_type_id == 4) {
            $so_list = $this->SalesPerson->find('all', array(
                'conditions' => array('SalesPerson.office_id' => $user_office_id, 'User.user_group_id' => 4),
                'fields' => array('SalesPerson.id', 'SalesPerson.name'),
                'order' => array('SalesPerson.name' => 'asc'),
            ));
            $person_list = array();
            foreach ($so_list as $key => $val) {
                $list['id'] = $val['SalesPerson']['id'];
                $list['name'] = $val['SalesPerson']['name'];
                $person_list[] = $list;
            }
        }
        if (!empty($person_list)) {
            echo json_encode(array_merge($rs, $person_list));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    /* public function get_outlet_list() {
        $rs = array(array('id' => '', 'name' => '---- Select outlet -----'));
        $market_id = $this->request->data['market_id'];
        $outlet_list = $this->Outlet->find('all', array(
            'conditions' => array('Outlet.market_id' => $market_id)
            ));
        $data_array = Set::extract($outlet_list, '{n}.Outlet');

        if (!empty($data_array)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }
*/
    public function get_outlet_list()
    {
        $rs = array(array('id' => '', 'name' => '---- Select outlet -----'));
        $office_id = $this->request->data['office_id'];
        $this->loadModel('Outlet');
        $office_id = $this->Outlet->find('all', array(
            'conditions' => array('Outlet.office_id' => $office_id),
            'order' => array('Outlet.name' => 'asc'),
        ));
        $data_array = Set::extract($outlet_list, '{n}.Outlet');

        if (!empty($data_array)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }
    public function get_outlet_list_by_tso()
    {
        $rs = array(array('id' => '', 'name' => '---- Select outlet -----'));
        $tso_id = $this->request->data['tso_id'];
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistOutletMap');
        $this->loadModel('Outlet');
        if ($user_group_id == 1034) {
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');

            $distributor = $this->DistUserMapping->find('first', array(
                'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];

            $dist_tso_conditions = array('DistTsoMapping.dist_tso_id' => $tso_id, 'DistTsoMapping.dist_distributor_id' => $distributor_id, 'DistDistributor.is_active' => 1);
            $dist_tso_map_list = $this->DistTsoMapping->find('all', array(
                'conditions' => $dist_tso_conditions
            ));
            $dist_list = array();
            foreach ($dist_tso_map_list as $key => $value) {
                $dist_list[$key] = $value['DistTsoMapping']['dist_distributor_id'];
            }
            $dist_outlet_map_list = $this->DistOutletMap->find('all', array(
                'conditions' => array(
                    'dist_distributor_id' => $dist_list
                ),
                'joins' => array(
                    array(
                        'table' => 'outlets',
                        'alias' => 'Outlet',
                        'conditions' => 'Outlet.id=DistOutletMap.outlet_id'
                    )
                ),
                'fields' => array('Outlet.id', 'DistDistributor.name',),
            ));
        } else {
            $dist_tso_conditions = array('DistTsoMapping.dist_tso_id' => $tso_id, 'DistDistributor.is_active' => 1);
            $dist_tso_map_list = $this->DistTsoMapping->find('all', array(
                'conditions' => $dist_tso_conditions
            ));
            $dist_list = array();
            foreach ($dist_tso_map_list as $key => $value) {
                $dist_list[$key] = $value['DistTsoMapping']['dist_distributor_id'];
            }
            $dist_outlet_map_list = $this->DistOutletMap->find('all', array(
                'conditions' => array(
                    'dist_distributor_id' => $dist_list
                ),
                'joins' => array(
                    array(
                        'table' => 'outlets',
                        'alias' => 'Outlet',
                        'conditions' => 'Outlet.id=DistOutletMap.outlet_id'
                    )
                ),
                'fields' => array('Outlet.id', 'DistDistributor.name',),
            ));
        }
        foreach ($dist_outlet_map_list as $key => $value) {
            $data_array[] = array(
                'id' => $value['Outlet']['id'],
                'name' => $value['DistDistributor']['name'],
            );
        }
        // $data_array = Set::extract($dist_outlet_map_list, '{n}.Outlet');

        if (!empty($data_array)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }
    public function company_confirmation_type()
    {
        $company_id = $this->request->data['company_id'];
        $this->loadModel('Company');
        $comfirmation_type = $this->Company->find('first', array(
            'conditions' => array(
                'Company.id' => $company_id,
            )
        ));
        $data_array['confirmation_type'] = $comfirmation_type['Company']['confirmation_type'];

        if (!empty($data_array)) {
            echo json_encode($data_array);
        }

        $this->autoRender = false;
    }
    public function get_territory_id_info()
    {
        $office_id = $this->request->data['office_id'];
        $this->loadModel('Territory');
        $territory_info = $this->Territory->find('first', array(
            'conditions' => array(
                'Territory.office_id' => $office_id,
                'Territory.name LIKE' => '%Corporate Territory%',
            ),
            // 'order'=>array('Territory.name'=>'asc'),
        ));
        $territory_id = $territory_info['Territory']['id'];
        //pr($territory_id);die();
        if (!empty($territory_id)) {
            echo json_encode($territory_id);
        }

        $this->autoRender = false;
    }
    public function get_product_unit()
    {
        $current_date = $this->current_date();
        $territory_id = $this->request->data['territory_id'];
        $outlet_id = $this->request->data['outlet_id'];
        $this->loadModel('Territory');
        $office_info = $this->Territory->find('first', array(
            'fields' => array('Territory.office_id'),
            'conditions' => array('Territory.id' => $territory_id),
            'recursive' => -1
        ));

        $this->loadModel('Store');
        $this->Store->recursive = -1;
        $store_info = $this->Store->find('first', array(
            'conditions' => array(
                'office_id' => $office_info['Territory']['office_id'],
                'store_type_id' => 2
            )
        ));
        $store_id = $store_info['Store']['id'];
        $product_id = $this->request->data['product_id'];

        $this->loadModel('DistOutletMap');
        $outlet_info = $this->DistOutletMap->find('first', array(
            'conditions' => array('DistOutletMap.outlet_id' => $outlet_id),
        ));
        $distributor_id = $outlet_info['DistDistributor']['id'];


        $this->loadModel('DistStore');
        $dist_store_info = $this->DistStore->find('first', array(
            'conditions' => array('DistStore.dist_distributor_id' => $distributor_id, 'DistStore.office_id' => $office_info['Territory']['office_id']),
        ));
        $dist_store_id = $dist_store_info['DistStore']['id'];

        //----------------product expire last date-----------\\
        $this->loadModel('ProductMonth');

        $product_expire_month_info = $this->ProductMonth->find('first', array(
            'conditions' => array(
                'ProductMonth.product_id' => $product_id
            ),
            'fields' => array('ProductMonth.day_month'),
            'recursive' => -1
        ));
        if (empty($product_expire_month_info)) {
            $productExpireLimit = 0;
        } else {
            $productExpireLimit = $product_expire_month_info['ProductMonth']['day_month'];
        }

        $p_expire_date = date('Y-m-t', strtotime("+" . $productExpireLimit . " months"));

        //--------------end-------------\\


        $this->loadModel('CurrentInventory');
        $this->CurrentInventory->recursive = -1;
        $total_qty_arr = $this->CurrentInventory->find('all', array(
            'conditions' => array(
                'store_id' => $store_id,
                'product_id' => $product_id,
                //"(expire_date is null OR expire_date > '$p_expire_date' )",
            ),
            'fields' => array('sum(qty) as total')
        ));
        $total_qty = $total_qty_arr[0][0]['total'];

        $this->loadModel('DistCurrentInventory');
        $dist_inventory_info = $this->DistCurrentInventory->find('all', array(
            'conditions' => array('DistCurrentInventory.store_id' => $dist_store_id, 'DistCurrentInventory.product_id' => $product_id),
            'fields' => array('sum(qty) as total_dist_qty')
        ));
        $this->loadModel('Product');
        $this->Product->recursive = -1;
        $product_array = $this->Product->find('first', array(
            'conditions' => array('Product.id' => $product_id),
            'joins' => array(
                array(
                    'table' => 'measurement_units',
                    'alias' => 'MU',
                    'conditions' => 'MU.id=Product.sales_measurement_unit_id'
                )
            ),
            'fields' => array('sales_measurement_unit_id', 'MU.name')
        ));
        $measurement_unit_id = $product_array['Product']['sales_measurement_unit_id'];
        $measurement_unit_name = $product_array['MU']['name'];

        $total_dist_qty = $dist_inventory_info[0][0]['total_dist_qty'];
        $sales_total_qty = $this->unit_convertfrombase($this->request->data['product_id'], $measurement_unit_id, $total_qty);
        $sales_total_dist_qty = $this->unit_convertfrombase($this->request->data['product_id'], $measurement_unit_id, $total_dist_qty);

        $data_array['product_unit']['name'] = $measurement_unit_name;
        $data_array['product_unit']['id'] = $measurement_unit_id;

        if (!empty($sales_total_dist_qty)) {
            $data_array['total_dist_qty'] = $sales_total_dist_qty;
        } else {
            $data_array['total_dist_qty'] = '';
        }
        if (!empty($sales_total_qty)) {
            $data_array['total_qty'] = $sales_total_qty;
        } else {
            $data_array['total_qty'] = '';
        }
        echo json_encode($data_array);
        $this->autoRender = false;
    }

    /* ------- set_combind_or_individual_price --------- */

    public function get_product_price()
    {
        $this->LoadModel('ProductCombinationsV2');
        $this->LoadModel('CombinationsV2');
        $this->LoadModel('CombinationDetailsV2');
        $order_date = $this->request->data['order_date'];
        $product_id = $this->request->data['product_id'];
        $min_qty = $this->request->data['min_qty'];
        $product_wise_cart_qty = $this->request->data['cart_product'];
        $prev_combine_product = explode(',', $this->request->data['combined_product']);

        /*------------- min price slab finding ----------------------*/
        $slab_conditions = array();
        $slab_conditions['ProductCombinationsV2.effective_date <='] = date('Y-m-d', strtotime($order_date));
        $slab_conditions['ProductCombinationsV2.product_id'] = $product_id;
        $slab_conditions['ProductCombinationsV2.min_qty <='] = $min_qty;
        $price_slab = $this->ProductCombinationsV2->find('first', array(
            'conditions' => $slab_conditions,
            'joins' => array(
                array(
                    'table' => 'product_price_section_v2',
                    'alias' => 'PriceSection',
                    'conditions' => 'PriceSection.id=ProductCombinationsV2.section_id and PriceSection.is_db=1'
                ),
                array(
                    'table' => 'product_price_db_slabs',
                    'alias' => 'DBSlab',
                    'conditions' => 'DBSlab.product_combination_id=ProductCombinationsV2.id'
                )
            ),
            'fields' => array(
                'ProductCombinationsV2.id',
                'ProductCombinationsV2.effective_date',
                'ProductCombinationsV2.min_qty',
                'ProductCombinationsV2.price',
                'DBSlab.price',
                'DBSlab.discount_amount',
            ),
            'order' => array(
                'ProductCombinationsV2.effective_date desc',
                'ProductCombinationsV2.min_qty desc'
            ),
            'recursive' => -1
        ));
        $product_price_array = array();
        if ($price_slab) {
            $product_price_array['price'] = $price_slab['DBSlab']['price'];
            $product_price_array['price_id'] = $price_slab['ProductCombinationsV2']['id'];
            $product_price_array['total_value'] = sprintf('%.2f', $price_slab['DBSlab']['price'] * $product_wise_cart_qty[$product_id]);
            $product_price_array['combine_product'] = '';
            $combine_product = array();
            $combine_product[] = $product_id;
            $product_price_array['recall_product_for_price'] = array_udiff($prev_combine_product, $combine_product, function ($a, $b) {
                if ($a != $b) return 1;
            });
            $combination_conditions = array();


            $combinatios_data_check = $this->CombinationsV2->query(
                "
                                select
                                    t.id,
                                    t.effective_date,
                                    t.combined_qty
                                from
                                (
                                    select 
                                        pcl.id,
                                        pcl.effective_date,
                                        max(pcl.effective_date) over (partition by pcl.combined_qty) as max_effective_date,
                                        pcl.combined_qty
                                    from 
                                    combinations_v2 pcl
                                    inner join combination_details_v2 pcld on pcl.id=pcld.combination_id
                                    inner join product_combinations_v2 pc on pc.min_qty=pcl.combined_qty 
                                                                            and pc.product_id=pcld.product_id 
                                                                            and pc.effective_date='" . $price_slab['ProductCombinationsV2']['effective_date'] . "'
                                    inner join product_price_section_v2 pcs on pcs.id=pc.section_id
                                    where
                                    pcl.effective_date <='" . date('Y-m-d', strtotime($order_date)) . "'
                                    and pcld.product_id=$product_id
                                    and pcs.is_db=1
                                    group by 
                                        pcl.id,
                                        pcl.effective_date,
                                        pcl.combined_qty
                                )t
                                where t.max_effective_date=t.effective_date
                                 
                        "
            );
            $combination_product_array = array();
            if ($combinatios_data_check) {
                foreach ($combinatios_data_check as $com_data) {
                    $combination_details_conditions = array();
                    $combination_details_conditions['CombinationDetailsV2.combination_id'] = $com_data['0']['id'];
                    $combination_details = $this->CombinationDetailsV2->find('all', array(
                        'conditions' => $combination_details_conditions,
                        'joins' => array(
                            array(
                                'table' => 'product_combinations_v2',
                                'alias' => 'PC',
                                'type' => 'INNER',
                                'conditions' => 'PC.product_id=CombinationDetailsV2.product_id 
                                                and pc.min_qty=' . intval($com_data['0']['combined_qty']) .
                                    'and pc.effective_date=\'' . $price_slab['ProductCombinationsV2']['effective_date'] . '\''
                            ),
                            array(
                                'table' => 'product_price_db_slabs',
                                'alias' => 'DBSlab',
                                'conditions' => 'DBSlab.product_combination_id=PC.id'
                            )
                        ),
                        'group' => array(
                            'CombinationDetailsV2.product_id',
                            'PC.id',
                            'DBSlab.price',
                        ),
                        'fields' => array(
                            'CombinationDetailsV2.product_id',
                            'PC.id',
                            'DBSlab.price',
                        ),
                        'recursive' => -1
                    ));
                    $combined_cart_qty = 0;
                    $price_id = 0;
                    $price = 0;
                    foreach ($combination_details as $details_data) {
                        $combined_cart_qty += isset($product_wise_cart_qty[$details_data['CombinationDetailsV2']['product_id']])
                            ? $product_wise_cart_qty[$details_data['CombinationDetailsV2']['product_id']] : 0;
                        if ($product_id == $details_data['CombinationDetailsV2']['product_id']) {
                            $price_id = $details_data['PC']['id'];
                            $price = $details_data['DBSlab']['price'];
                        } else {
                            if (isset($product_wise_cart_qty[$details_data['CombinationDetailsV2']['product_id']])) {
                                $combine_product[] = $details_data['CombinationDetailsV2']['product_id'];
                                $combination_product_array[] = array(
                                    'product_id' => $details_data['CombinationDetailsV2']['product_id'],
                                    'price' => $details_data['DBSlab']['price'],
                                    'total_value' => sprintf('%.2f', $details_data['DBSlab']['price'] * $product_wise_cart_qty[$details_data['CombinationDetailsV2']['product_id']]),
                                    'price_id' => $details_data['PC']['id'],
                                    'combination_id' => $com_data['0']['id']
                                );
                            }
                        }
                    }
                    if ($combined_cart_qty >= $com_data['0']['combined_qty']) {
                        $product_price_array['price'] = $price;
                        $product_price_array['price_id'] = $price_id;
                        $product_price_array['total_value'] = sprintf('%.2f', $price * $product_wise_cart_qty[$product_id]);
                        $product_price_array['combination'] = $combination_product_array;
                        $product_price_array['combine_product'] = implode(",", $combine_product);
                        $product_price_array['combination_id'] = $com_data['0']['id'];
                        $product_price_array['recall_product_for_price'] = array_udiff($prev_combine_product, $combine_product, function ($a, $b) {
                            if ($a != $b) return 1;
                        });
                        break;
                    }
                }
            }
        } else {
            $product_price_array['price'] = 0;
            $product_price_array['price_id'] = 0;
            $product_price_array['total_value'] = 0;
            $product_price_array['combination'] = array();
            $product_price_array['combine_product'] = '';
            $product_price_array['recall_product_for_price'] = array();
        }
        /*---------Bonus-----------*/
        $this->loadModel('Bonus');
        $this->loadModel('Product');

        $this->Bonus->recursive = -1;
        $bonus_info = $this->Bonus->find('all', array(
            'conditions' => array(
                'mother_product_id' => $product_id,
                'effective_date <=' => date('Y-m-d', strtotime($order_date)),
                'end_date >=' => date('Y-m-d', strtotime($order_date))
            ),
            'fields' => array('mother_product_quantity', 'bonus_product_id', 'bonus_product_quantity')
        ));

        if (!empty($bonus_info[0]['Bonus']['mother_product_quantity'])) {
            $mother_product_quantity_bonus = $bonus_info[0]['Bonus']['mother_product_quantity'];
            $result_data['mother_product_quantity_bonus'] = $mother_product_quantity_bonus;
        }

        $no_of_bonus_slap = count($bonus_info);

        if ($no_of_bonus_slap != 0) {
            for ($slap_count = 0; $slap_count < $no_of_bonus_slap; $slap_count++) {
                $bonus_slap['bonus_product_id'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_id'];

                $this->Product->recursive = -1;
                $bonus_product_name = $this->Product->find('first', array(
                    'conditions' => array('id' => $bonus_slap['bonus_product_id'][$slap_count]),
                    'fields' => array('name', 'sales_measurement_unit_id')
                ));
                $quantity_slap['mother_product_quantity'][$slap_count] = $bonus_info[$slap_count]['Bonus']['mother_product_quantity'];

                $result_data['bonus_product_id'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_id'];
                $result_data['bonus_product_name'][$slap_count] = $bonus_product_name['Product']['name'];
                $result_data['sales_measurement_unit_id'][$slap_count] = $bonus_product_name['Product']['sales_measurement_unit_id'];
                $result_data['bonus_product_quantity'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_quantity'];
            }
            for ($i = 0; $i < count($quantity_slap['mother_product_quantity']); $i++) {
                $result_data['mother_product_quantity'][] = array(
                    'min' => $i == 0 ? 0 : $quantity_slap['mother_product_quantity'][$i - 1],
                    'max' => $quantity_slap['mother_product_quantity'][$i] - 1
                );
            }
        }
        if (isset($result_data)) {
            $product_price_array = array_merge($product_price_array, $result_data);
        }
        echo json_encode($product_price_array);
        $this->autoRender = false;
    }
    public function get_product_policy()
    {
        $this->LoadModel('Store');
        $this->LoadModel('DiscountBonusPolicy');
        $this->LoadModel('DiscountBonusPolicyProduct');
        $this->LoadModel('DiscountBonusPolicyOption');
        $this->LoadModel('DiscountBonusPolicyOptionExclusionInclusionProduct');
        $order_date = date('Y-m-d', strtotime($this->request->data['order_date']));
        $office_id = $this->request->data['office_id'];

        $store_info = $this->Store->find('first', array(
            'conditions' => array(
                'Store.territory_id' => null,
                'Store.office_id' => $office_id,
                'Store.store_type_id' => 2,
            ),
            'recursive' => -1
        ));
        $store_id = $store_info['Store']['id'];

        $product_id = $this->request->data['product_id'];
        $memo_total = $this->request->data['memo_total'];
        $min_qty = $this->request->data['min_qty'];
        $product_wise_cart_qty = $this->request->data['cart_product'];
        $old_selected_bonus = json_decode($this->request->data['selected_bonus'], 1);
        $old_selected_set = json_decode($this->request->data['selected_set'], 1);
        $old_selected_policy_type = json_decode($this->request->data['selected_policy_type'], 1);
        $old_other_policy_info = json_decode($this->request->data['other_policy_info'], 1);

        $conditions = array();
        $conditions['DiscountBonusPolicy.start_date <='] = $order_date;
        $conditions['DiscountBonusPolicy.end_date >='] = $order_date;
        $conditions['DiscountBonusPolicy.is_db'] = 1;
        $conditions['DiscountBonusPolicyOption.is_db'] = 1;
        $conditions['DiscountBonusPolicyProduct.product_id'] = array_keys($product_wise_cart_qty);
        $policy_data = $this->DiscountBonusPolicy->find('all', array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'discount_bonus_policy_products',
                    'alias' => 'DiscountBonusPolicyProduct',
                    'conditions' => 'DiscountBonusPolicyProduct.discount_bonus_policy_id=DiscountBonusPolicy.id'
                ),
                array(
                    'table' => 'discount_bonus_policy_options',
                    'alias' => 'DiscountBonusPolicyOption',
                    'conditions' => 'DiscountBonusPolicyOption.discount_bonus_policy_id=DiscountBonusPolicy.id'
                ),
            ),
            'group' => array(
                'DiscountBonusPolicy.id',
                'DiscountBonusPolicy.name',
                'DiscountBonusPolicy.start_date',
                'DiscountBonusPolicy.end_date',
            ),
            'fields' => array(
                'DiscountBonusPolicy.id',
                'DiscountBonusPolicy.name',
                'DiscountBonusPolicy.start_date',
                'DiscountBonusPolicy.end_date',
            ),
            'recursive' => -1,
        ));
        $policy_id = array_map(function ($data) {
            return $data['DiscountBonusPolicy']['id'];
        }, $policy_data);
        $policy_product = $this->DiscountBonusPolicyProduct->find('all', array(
            'conditions' => array('DiscountBonusPolicyProduct.discount_bonus_policy_id' => $policy_id),
            'fields' => array('product_id', 'discount_bonus_policy_id'),
            'recursive' => -1
        ));
        $other_policy_info = array();
        $policy_wise_product_data = array();
        foreach ($policy_product as $data) {
            $policy_wise_product_data[$data['DiscountBonusPolicyProduct']['discount_bonus_policy_id']][] = $data['DiscountBonusPolicyProduct']['product_id'];
            if (isset($product_wise_cart_qty[$data['DiscountBonusPolicyProduct']['product_id']]))
                $other_policy_info['policy_product'][$data['DiscountBonusPolicyProduct']['product_id']] =  $data['DiscountBonusPolicyProduct']['product_id'];
        }
        $discount_array = array();
        $total_discount = 0;
        $bonus_html = '';
        $selected_bonus = array();
        $selected_set = array();
        $selected_policy_type = array();
        foreach ($policy_data as $p_data) {
            if (!$policy_wise_product_data[$p_data['DiscountBonusPolicy']['id']]) {
                continue;
            }
            $cart_combined_qty = 0;
            foreach ($policy_wise_product_data[$p_data['DiscountBonusPolicy']['id']] as $p_id) {
                $cart_combined_qty += isset($product_wise_cart_qty[$p_id]) ? $product_wise_cart_qty[$p_id] : 0;
            }
            $policy_option = $this->DiscountBonusPolicyOption->find('all', array(
                'conditions' => array(
                    'DiscountBonusPolicyOption.discount_bonus_policy_id' => $p_data['DiscountBonusPolicy']['id'],
                    'DiscountBonusPolicyOption.is_db' => 1,
                    'DiscountBonusPolicyOption.min_qty_sale_unit <=' => $cart_combined_qty,
                ),
                'order' => array('DiscountBonusPolicyOption.min_qty_sale_unit desc'),
                'recursive' => -1
            ));
            $effective_slab_index = null;

            foreach ($policy_option as $key => $slab_data) {
                $min_memo_value = $slab_data['DiscountBonusPolicyOption']['min_memo_value'];
                if ($min_memo_value && $min_memo_value > $memo_total) {
                    continue;
                }
                $exclusion_product = $this->DiscountBonusPolicyOptionExclusionInclusionProduct->find('all', array(
                    'conditions' => array(
                        'DiscountBonusPolicyOptionExclusionInclusionProduct.discount_bonus_policy_option_id' => $slab_data['DiscountBonusPolicyOption']['id'],
                        'DiscountBonusPolicyOptionExclusionInclusionProduct.create_for' => 1
                    ),
                    'recursive' => -1
                ));
                $is_exclusion = 0;
                foreach ($exclusion_product as $ex_data) {
                    $ex_product_id = $ex_data['DiscountBonusPolicyOptionExclusionInclusionProduct']['product_id'];
                    $ex_min_qty = $ex_data['DiscountBonusPolicyOptionExclusionInclusionProduct']['min_qty'];
                    if ($ex_min_qty) {

                        if (isset($product_wise_cart_qty[$ex_product_id]) && $product_wise_cart_qty[$ex_product_id] >= $ex_min_qty) {
                            $is_exclusion = 1;
                            break;
                        }
                    } else {
                        if (isset($product_wise_cart_qty[$ex_product_id])) {
                            $is_exclusion = 1;
                            break;
                        }
                    }
                }
                if ($is_exclusion == 1) {
                    continue;
                }
                $inclusion_product = $this->DiscountBonusPolicyOptionExclusionInclusionProduct->find('all', array(
                    'conditions' => array(
                        'DiscountBonusPolicyOptionExclusionInclusionProduct.discount_bonus_policy_option_id' => $slab_data['DiscountBonusPolicyOption']['id'],
                        'DiscountBonusPolicyOptionExclusionInclusionProduct.create_for' => 2
                    ),
                    'recursive' => -1
                ));
                $is_inclusion = 1;
                foreach ($inclusion_product as $ex_data) {
                    $in_product_id = $ex_data['DiscountBonusPolicyOptionExclusionInclusionProduct']['product_id'];
                    $in_min_qty = $ex_data['DiscountBonusPolicyOptionExclusionInclusionProduct']['min_qty'];
                    if ($in_min_qty) {

                        if (isset($product_wise_cart_qty[$in_product_id]) && $product_wise_cart_qty[$in_product_id] < $in_min_qty) {
                            $is_inclusion = 0;
                            break;
                        }
                    } else {
                        if (!isset($product_wise_cart_qty[$in_product_id])) {
                            $is_inclusion = 0;
                            break;
                        }
                    }
                }
                if ($is_inclusion == 0) {
                    continue;
                }
                $effective_slab_index = $key;
                break;
            }
            if ($effective_slab_index === null) {
                continue;
            }
            $policy_type = $policy_option[$effective_slab_index]['DiscountBonusPolicyOption']['policy_type'];
            $policy_id = $p_data['DiscountBonusPolicy']['id'];

            if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['selected_option_id'] != $policy_option[$effective_slab_index]['DiscountBonusPolicyOption']['id']) {
                unset($old_selected_bonus[$policy_id]);
                unset($old_selected_set[$policy_id]);
                unset($old_selected_policy_type[$policy_id]);
            }
            $other_policy_info[$policy_id]['selected_option_id'] = $policy_option[$effective_slab_index]['DiscountBonusPolicyOption']['id'];


            if ($policy_type == 0 || $policy_type == 2) {
                $discount_array[] = $this->create_discount_array($policy_option[$effective_slab_index], $product_wise_cart_qty, $total_discount);
            }
            if ($policy_type == 1 || $policy_type == 2) {
                $bonus_html .= '<tr class="n_bonus_row"><th colspan="4">' . $p_data['DiscountBonusPolicy']['name'] . '</th><tr>';
                $bonus_html .= $this->create_bonus_html($store_id, $policy_option[$effective_slab_index], $cart_combined_qty, $selected_bonus, $old_selected_bonus, $selected_set, $old_selected_set, $other_policy_info, $old_other_policy_info);
            } else if ($policy_type == 3) {
                $policy_id = $p_data['DiscountBonusPolicy']['id'];
                $selected_policy_type_var = 1;
                if (isset($old_selected_policy_type[$policy_id])) {
                    $selected_policy_type_var = $old_selected_policy_type[$policy_id];
                }
                $selected_policy_type[$policy_id] = $selected_policy_type_var;
                if ($selected_policy_type_var == 1) {
                    $btn_type_1 = 'btn-primary';
                    $btn_type_2 = 'btn-basic';
                } else {
                    $btn_type_1 = 'btn-basic';
                    $btn_type_2 = 'btn-primary';
                }
                $bonus_html .= '<tr class="n_bonus_row"><th colspan="4">' . $p_data['DiscountBonusPolicy']['name'] . '</th><tr>';
                $bonus_html .=
                    '<tr class="n_bonus_row">
                    <th colspan="4">
                        <button class="btn ' . $btn_type_1 . ' btn_type" data-type="1" data-policy_id="' . $policy_id . '">Discount</button>
                        <button class="btn ' . $btn_type_2 . ' btn_type" data-type="2"  data-policy_id="' . $policy_id . '">Bonus</button>
                    </th>
                <tr>';
                if ($selected_policy_type_var == 1)
                    $discount_array[] = $this->create_discount_array($policy_option[$effective_slab_index], $product_wise_cart_qty, $total_discount);

                else if ($selected_policy_type_var == 2)
                    $bonus_html .= $this->create_bonus_html($store_id, $policy_option[$effective_slab_index], $cart_combined_qty, $selected_bonus, $old_selected_bonus, $selected_set, $old_selected_set, $other_policy_info, $old_other_policy_info);
            }
        }
        $result = array();
        $result['discount'] = $discount_array;
        $result['total_discount'] = $total_discount;
        $result['bonus_html'] = $bonus_html;
        $result['selected_bonus'] = $selected_bonus;
        $result['selected_set'] = $selected_set;
        $result['selected_policy_type'] = $selected_policy_type;
        $result['other_policy_info'] = $other_policy_info;
        echo json_encode($result);
        exit;
        $this->autoRender = false;
    }
    private function create_discount_array($policy_option, $product_wise_cart_qty, &$total_discount)
    {
        $this->loadModel('DiscountBonusPolicyOptionPriceSlab');
        $conditions = array();
        $conditions['DiscountBonusPolicyOptionPriceSlab.discount_bonus_policy_option_id'] = $policy_option['DiscountBonusPolicyOption']['id'];
        $price_slabs = $this->DiscountBonusPolicyOptionPriceSlab->find('all', array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'product_combinations_v2',
                    'alias' => 'PC',
                    'type' => 'inner',
                    'conditions' => 'PC.id=DiscountBonusPolicyOptionPriceSlab.db_slab_id'
                ),
                array(
                    'table' => 'product_price_db_slabs',
                    'alias' => 'PCD',
                    'type' => 'inner',
                    'conditions' => 'PC.id=PCD.product_combination_id'
                ),
            ),
            'fields' => array('PCD.price', 'DiscountBonusPolicyOptionPriceSlab.discount_product_id', 'PC.id'),
            'recursive' => -1
        ));
        $discount_amount = $policy_option['DiscountBonusPolicyOption']['discount_amount'];
        $discount_in_hand = $policy_option['DiscountBonusPolicyOption']['in_hand_discount_amount'];
        if (!$discount_in_hand) {
            $discount_in_hand = 0;
        }
        $discount_amount = $discount_amount - $discount_in_hand;
        $policy_id = $policy_option['DiscountBonusPolicyOption']['discount_bonus_policy_id'];
        $discount_type = $policy_option['DiscountBonusPolicyOption']['disccount_type'];
        $policy_type = $policy_option['DiscountBonusPolicyOption']['policy_type'];
        $discount_array = array();
        foreach ($price_slabs as $pr_slab_data) {
            if (isset($product_wise_cart_qty[$pr_slab_data['DiscountBonusPolicyOptionPriceSlab']['discount_product_id']])) {
                if ($discount_type == 0)
                    $discount_amount_price = ($pr_slab_data['PCD']['price'] * $discount_amount / 100);
                else
                    $discount_amount_price = $discount_amount;
                $discount_value = $discount_amount_price * $product_wise_cart_qty[$pr_slab_data['DiscountBonusPolicyOptionPriceSlab']['discount_product_id']];
                $total_discount += $discount_value;
                $discount_array[] = array(
                    'product_id' => $pr_slab_data['DiscountBonusPolicyOptionPriceSlab']['discount_product_id'],
                    'policy_id' => $policy_id,
                    'policy_type' => $policy_type,
                    'discount_type' => $discount_type,
                    'discount_amount' => $discount_amount_price,
                    'price' => $pr_slab_data['PCD']['price'],
                    'total_value' => sprintf("%0.2f", $pr_slab_data['PCD']['price'] * $product_wise_cart_qty[$pr_slab_data['DiscountBonusPolicyOptionPriceSlab']['discount_product_id']]),
                    'price_id' => $pr_slab_data['PC']['id'],
                    'total_discount_value' => sprintf("%0.2f", $discount_value),
                );
            }
        }
        return $discount_array;
    }
    private function create_bonus_html($store_id, $policy_option, $combined_qty, &$selected_bonus, $old_selected_bonus, &$selected_set, $old_selected_set, &$other_policy_info, $old_other_policy_info)
    {

        $this->loadModel('DiscountBonusPolicyOptionBonusProduct');
        $conditions['DiscountBonusPolicyOptionBonusProduct.discount_bonus_policy_option_id'] = $policy_option['DiscountBonusPolicyOption']['id'];
        $bonus_product = $this->DiscountBonusPolicyOptionBonusProduct->find('all', array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'products',
                    'alias' => 'Product',
                    'conditions' => 'Product.id=DiscountBonusPolicyOptionBonusProduct.bonus_product_id'
                ),
                array(
                    'table' => 'measurement_units',
                    'alias' => 'MU',
                    'conditions' => 'MU.id=DiscountBonusPolicyOptionBonusProduct.measurement_unit_id'
                ),
            ),
            'fields' => array(
                'DiscountBonusPolicyOptionBonusProduct.*',
                'Product.name',
                'MU.name',
            ),
            'recursive' => -1
        ));
        $formula = $policy_option['DiscountBonusPolicyOption']['bonus_formula_text_with_product_id'];
        $min_qty = $policy_option['DiscountBonusPolicyOption']['min_qty_sale_unit'];
        $policy_id = $policy_option['DiscountBonusPolicyOption']['discount_bonus_policy_id'];
        $policy_type = $policy_option['DiscountBonusPolicyOption']['policy_type'];
        $b_html = '';
        if (!$formula) {
            $s_disabled = 'readonly';
            $i = 0;
            foreach ($bonus_product as $data) {
                $bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
                $in_hand_bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['in_hand_bonus_qty'];
                if (!$in_hand_bonus_qty)
                    $in_hand_bonus_qty = 0;
                $bonus_qty = $bonus_qty - $in_hand_bonus_qty;
                $provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
                // echo $old_other_policy_info[$policy_id]['provided_bonus_qty'] . '----' . $provide_bonus_qty;
                if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['provided_bonus_qty'] != $provide_bonus_qty) {
                    unset($old_selected_bonus[$policy_id]);
                    unset($old_selected_set[$policy_id]);
                }
                /* echo $old_other_policy_info[$policy_id]['provided_bonus_qty'] . '----' . $provide_bonus_qty;
                exit; */
                $other_policy_info[$policy_id]['provided_bonus_qty'] = $provide_bonus_qty;
                if (isset($old_selected_bonus[$policy_id])) {
                    if (isset($old_selected_bonus[$policy_id][1][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']]) && $old_selected_bonus[$policy_id][1][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] > 0) {
                        $value = $old_selected_bonus[$policy_id][1][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']];
                        $min_qty_disabled = '';
                        $min_qty_checked = 'checked';
                       
                        $min_qty_required = 'required';
                    } else {
                        $value = 0;
                        $min_qty_disabled = 'readonly';
                        
                        $min_qty_checked = '';
                        $min_qty_required = '';
                    }
                } else {
                    if ($i == 0) {
                        $value = $provide_bonus_qty;
                        $min_qty_disabled = '';
                        $min_qty_checked = 'checked';
                        
                        $min_qty_required = 'required';
                    } else {
                        $value = 0;
                        $min_qty_disabled = 'readonly';
                        
                        $min_qty_checked = '';
                        $min_qty_required = '';
                    }
                }


                $prod_info = $this->Product->find('first', array(
                    'conditions' => array('id' => $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']),
                    'recursive' => -1
                ));



                if ($prod_info['Product']['is_virtual'] == 1) {

                    $pd_name_replace = $this->get_product_inventroy_check($store_id, $prod_info['Product']['id'], $prod_info['Product']['parent_id']);

                    if ($pd_name_replace == 1) {
                        $pdname = $this->get_parent_virtual_pd_info($data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']);

                        $data['Product']['name'] = $pdname['VirtualProduct']['name'];
                    }
                }

                $selected_bonus[$policy_id]['1'][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
               
                $i++;
                $b_html .= '
                    <tr class="bonus_row n_bonus_row bonus_policy_id_' . $policy_id . '">
                        <th class="text-center" id="bonus_product_list">
                            <div class="input select">
                                <select ' . $s_disabled . ' name="data[OrderDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
                                <option value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">' . $data['Product']['name'] . '</option>
                                </select>
                            </div>
                        </th>
                        <th class="text-center" width="12%">
                            <input ' . $s_disabled . ' type="text" name="" class="form-control width_100_this open_bonus_product_unit_name" value="' . $data['MU']['name'] . '" readonly disabled="">
                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][measurement_unit_id][]"  class="open_bonus_product_unit_id" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] . '">
                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" value="" />
                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type" value=""/>
                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type" value="' . $policy_type . '">
                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id" value="' . $policy_id . '">
                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus" value="3">
                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100_this open_bonus_product_rate" value="0.0">
                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100_this open_bonus_product_price_id" value="">
                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value="">
                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="1">
                        </th>
                        <th class="text-center" width="12%">
                            <input style="width: 50px;" ' . $min_qty_disabled . ' ' . $min_qty_required . ' data-set="1" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[OrderDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_1" value="' . $value . '">
                            
                        </th>
                        <th class="text-center" width="10%">
                            <input type="checkbox" ' . $min_qty_checked . ' class="is_bonus_checked" name="data[OrderDetail][bonus_check][]" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">
                        </th>
                    </tr>';
            }
        } else {
            $parsed_fourmula = $this->parse_formla($formula);
            $product_wise_bonus = array();
            foreach ($bonus_product as $data) {
                $product_wise_bonus[$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $data;
            }
            if ($parsed_fourmula['set_relation'] == 'AND' || $parsed_fourmula['set_relation'] == '') {
                foreach ($parsed_fourmula['formula_product'] as $set => $formula_product) {
                    $b_html .= '<tr class="n_bonus_row set"' . $set . '><th colspan="4">Set - ' . $set . '</th><tr>';
                    $element_relation = $parsed_fourmula['element_relation'][$set];
                    if ($element_relation == 'OR') {
                        $i = 0;
                        $s_disabled = 'readonly';
                        foreach ($formula_product as $product_id) {
                            $data = $product_wise_bonus[$product_id];
                            $bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
                            $in_hand_bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['in_hand_bonus_qty'];
                            if (!$in_hand_bonus_qty)
                                $in_hand_bonus_qty = 0;
                            $bonus_qty = $bonus_qty - $in_hand_bonus_qty;
                            $provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
                            if ($old_other_policy_info[$policy_id]['provided_bonus_qty'] != $provide_bonus_qty) {
                                unset($old_selected_bonus[$policy_id]);
                                unset($old_selected_set[$policy_id]);
                            }
                            $other_policy_info[$policy_id]['provided_bonus_qty'] = $provide_bonus_qty;
                            if (isset($old_selected_bonus[$policy_id])) {
                                if (isset($old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']]) && $old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] > 0) {
                                    $value = $old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']];
                                    $min_qty_disabled = '';
                                    $min_qty_checked = 'checked';
                                    $min_qty_required = 'required';
                                } else {
                                    $value = 0;
                                    $min_qty_disabled = 'readonly';
                                    $min_qty_checked = '';
                                    $min_qty_required = '';
                                }
                            } else {
                                if ($i == 0) {
                                    $value = $provide_bonus_qty;
                                    $min_qty_disabled = '';
                                    $min_qty_checked = 'checked';
                                    $min_qty_required = 'required';
                                } else {
                                    $value = 0;
                                    $min_qty_disabled = 'readonly';
                                    $min_qty_checked = '';
                                    $min_qty_required = '';
                                }
                            }

                            $prod_info = $this->Product->find('first', array(
                                'conditions' => array('id' => $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']),
                                'recursive' => -1
                            ));

                            if ($prod_info['Product']['is_virtual'] == 1) {

                                $pd_name_replace = $this->get_product_inventroy_check($store_id, $prod_info['Product']['id'], $prod_info['Product']['parent_id']);

                                if ($pd_name_replace == 1) {
                                    $pdname = $this->get_parent_virtual_pd_info($data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']);

                                    $data['Product']['name'] = $pdname['VirtualProduct']['name'];
                                }
                            }

                            $selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
                            $i++;
                            $b_html .= '
                                <tr class="bonus_row n_bonus_row bonus_policy_id_' . $policy_id . '">
                                    <th class="text-center" id="bonus_product_list">
                                        <div class="input select">
                                            <select ' . $s_disabled . ' name="data[OrderDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
                                            <option value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">' . $data['Product']['name'] . '</option>
                                            </select>
                                        </div>
                                    </th>
                                    <th class="text-center" width="12%">
                                        <input ' . $s_disabled . ' type="text" name="" class="form-control width_100_this open_bonus_product_unit_name" value="' . $data['MU']['name'] . '" readonly disabled="">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="open_bonus_product_unit_id" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] . '">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" value="" />
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type" value=""/>
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type" value="' . $policy_type . '">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id" value="' . $policy_id . '">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus" value="3">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100_this open_bonus_product_rate" value="0.0">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100_this open_bonus_product_price_id" value="">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value="">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="' . $set . '">
                                    </th>
                                    <th class="text-center" width="12%">
                                        <input ' . $min_qty_disabled . ' ' . $min_qty_required . ' data-set="' . $set . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[OrderDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_' . $set . '" value="' . $value . '">
                                    </th>
                                    <th class="text-center" width="10%">
                                        <input type="checkbox" ' . $min_qty_checked . ' class="is_bonus_checked" name="data[OrderDetail][bonus_check][]" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">
                                    </th>
                                </tr>';
                        }
                    } else if ($element_relation == 'AND') {
                        $i = 0;
                        $s_disabled = 'readonly';
                        foreach ($formula_product as $product_id) {
                            $data = $product_wise_bonus[$product_id];
                            $bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
                            $in_hand_bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['in_hand_bonus_qty'];
                            if (!$in_hand_bonus_qty)
                                $in_hand_bonus_qty = 0;
                            $bonus_qty = $bonus_qty - $in_hand_bonus_qty;
                            $provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
                            if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['provided_bonus_qty'] != $provide_bonus_qty) {
                                unset($old_selected_bonus[$policy_id]);
                                unset($old_selected_set[$policy_id]);
                            }
                            $other_policy_info[$policy_id]['provided_bonus_qty'] = $provide_bonus_qty;
                            $value = $provide_bonus_qty;
                            $min_qty_disabled = 'readonly';
                            $min_qty_checked = 'checked';
                            $min_qty_required = 'required';

                            $prod_info = $this->Product->find('first', array(
                                'conditions' => array('id' => $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']),
                                'recursive' => -1
                            ));

                            if ($prod_info['Product']['is_virtual'] == 1) {

                                $pd_name_replace = $this->get_product_inventroy_check($store_id, $prod_info['Product']['id'], $prod_info['Product']['parent_id']);

                                if ($pd_name_replace == 1) {
                                    $pdname = $this->get_parent_virtual_pd_info($data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']);

                                    $data['Product']['name'] = $pdname['VirtualProduct']['name'];
                                }
                            }

                            $selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
                            $i++;
                            $b_html .= '
                                <tr class="bonus_row n_bonus_row bonus_policy_id_' . $policy_id . '">
                                    <th class="text-center" id="bonus_product_list">
                                        <div class="input select">
                                            <select ' . $s_disabled . ' name="data[OrderDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
                                            <option value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">' . $data['Product']['name'] . '</option>
                                            </select>
                                        </div>
                                    </th>
                                    <th class="text-center" width="12%">
                                        <input ' . $s_disabled . ' type="text" name="" class="form-control width_100_this open_bonus_product_unit_name" value="' . $data['MU']['name'] . '" readonly disabled="">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="open_bonus_product_unit_id" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] . '">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" value="" />
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type" value=""/>
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type" value="' . $policy_type . '">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id" value="' . $policy_id . '">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus" value="3">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100_this open_bonus_product_rate" value="0.0">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100_this open_bonus_product_price_id" value="">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value="">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="' . $set . '">
                                    </th>
                                    <th class="text-center" width="12%">
                                        <input ' . $min_qty_disabled . ' ' . $min_qty_required . ' data-set="' . $set . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[OrderDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_' . $set . '" value="' . $value . '">
                                    </th>
                                    <th class="text-center" width="10%">
                                        <input type="checkbox" disabled ' . $min_qty_checked . ' class="is_bonus_checked" name="data[OrderDetail][bonus_check][]" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">
                                    </th>
                                </tr>';
                        }
                    }
                }
            } else if ($parsed_fourmula['set_relation'] == 'OR') {
                $selected_set_var = 1;
                if (isset($old_selected_set[$policy_id])) {
                    $selected_set_var = $old_selected_set[$policy_id];
                }
                $selected_set[$policy_id] = $selected_set_var;
                if ($selected_set_var == 1) {
                    $btn_set_1 = 'btn-success';
                    $btn_set_2 = 'btn-default';
                } else {
                    $btn_set_1 = 'btn-default';
                    $btn_set_2 = 'btn-success';
                }
                $b_html .=
                    '<tr class="n_bonus_row set">
                    <th colspan="4" class="text-center">
                        <button class="btn ' . $btn_set_1 . ' btn_set" data-set="1" data-policy_id="' . $policy_id . '">Set-1</button>
                        <button class="btn ' . $btn_set_2 . ' btn_set" data-set="2" data-policy_id="' . $policy_id . '">Set-2</button>
                    </th>
                <tr>';
                foreach ($parsed_fourmula['formula_product'] as $set => $formula_product) {
                    $element_relation = $parsed_fourmula['element_relation'][$set];
                    $display_none = 'display_none';
                    $disabled = 'disabled';
                    if ($selected_set_var == $set) {
                        $display_none = '';
                        $disabled = '';
                    }
                    if ($element_relation == 'OR') {
                        $i = 0;
                        $s_disabled = 'readonly';
                        foreach ($formula_product as $product_id) {
                            $data = $product_wise_bonus[$product_id];
                            $bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
                            $in_hand_bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['in_hand_bonus_qty'];
                            if (!$in_hand_bonus_qty)
                                $in_hand_bonus_qty = 0;
                            $bonus_qty = $bonus_qty - $in_hand_bonus_qty;
                            $provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
                            if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['provided_bonus_qty'] != $provide_bonus_qty) {
                                unset($old_selected_bonus[$policy_id]);
                                unset($old_selected_set[$policy_id]);
                            }
                            $other_policy_info[$policy_id]['provided_bonus_qty'] = $provide_bonus_qty;
                            if (isset($old_selected_bonus[$policy_id])) {
                                if (isset($old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']]) && $old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] > 0) {
                                    $value = $old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']];
                                    $min_qty_disabled = '';
                                    $min_qty_checked = 'checked';
                                    $min_qty_required = 'required';
                                } else {
                                    $value = 0;
                                    $min_qty_disabled = 'readonly';
                                    $min_qty_checked = '';
                                    $min_qty_required = '';
                                }
                            } else {
                                if ($i == 0) {
                                    $value = $provide_bonus_qty;
                                    $min_qty_disabled = '';
                                    $min_qty_checked = 'checked';
                                    $min_qty_required = 'required';
                                } else {
                                    $value = 0;
                                    $min_qty_disabled = 'readonly';
                                    $min_qty_checked = '';
                                    $min_qty_required = '';
                                }
                            }

                            $prod_info = $this->Product->find('first', array(
                                'conditions' => array('id' => $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']),
                                'recursive' => -1
                            ));

                            if ($prod_info['Product']['is_virtual'] == 1) {

                                $pd_name_replace = $this->get_product_inventroy_check($store_id, $prod_info['Product']['id'], $prod_info['Product']['parent_id']);

                                if ($pd_name_replace == 1) {
                                    $pdname = $this->get_parent_virtual_pd_info($data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']);

                                    $data['Product']['name'] = $pdname['VirtualProduct']['name'];
                                }
                            }

                            $selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
                            $i++;
                            $b_html .= '
                                <tr class="bonus_row n_bonus_row ' . $display_none . ' bonus_policy_id_' . $policy_id . ' set_' . $set . '">
                                    <th class="text-center" id="bonus_product_list">
                                        <div class="input select">
                                            <select ' . $s_disabled . ' ' . $disabled . ' name="data[OrderDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
                                            <option value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">' . $data['Product']['name'] . '</option>
                                            </select>
                                        </div>
                                    </th>
                                    <th class="text-center" width="12%">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="text" name="" class="form-control width_100_this open_bonus_product_unit_name" value="' . $data['MU']['name'] . '" readonly disabled="">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="open_bonus_product_unit_id" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] . '">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" value="" />
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type" value=""/>
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type" value="' . $policy_type . '">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id" value="' . $policy_id . '">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus" value="3">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100_this open_bonus_product_rate" value="0.0">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100_this open_bonus_product_price_id" value="">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value="">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="' . $set . '">
                                    </th>
                                    <th class="text-center" width="12%">
                                        <input ' . $min_qty_disabled . ' ' . $disabled . ' ' . $min_qty_required . ' data-set="' . $set . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[OrderDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_' . $set . '" value="' . $value . '">
                                    </th>
                                    <th class="text-center" width="10%">
                                        <input type="checkbox" ' . $min_qty_checked . '  class="is_bonus_checked" name="data[OrderDetail][bonus_check][]" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">
                                    </th>
                                </tr>';
                        }
                    } else if ($element_relation == 'AND') {
                        $i = 0;
                        $s_disabled = 'readonly';
                        foreach ($formula_product as $product_id) {
                            $data = $product_wise_bonus[$product_id];
                            $bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
                            $in_hand_bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['in_hand_bonus_qty'];
                            if (!$in_hand_bonus_qty)
                                $in_hand_bonus_qty = 0;
                            $bonus_qty = $bonus_qty - $in_hand_bonus_qty;
                            $provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
                            if (isset($old_other_policy_info[$policy_id]) && $old_other_policy_info[$policy_id]['provided_bonus_qty'] != $provide_bonus_qty) {
                                unset($old_selected_bonus[$policy_id]);
                                unset($old_selected_set[$policy_id]);
                            }
                            $other_policy_info[$policy_id]['provided_bonus_qty'] = $provide_bonus_qty;
                            $value = $provide_bonus_qty;
                            $min_qty_disabled = 'readonly';
                            $min_qty_checked = 'checked';
                            $min_qty_required = 'required';

                            $prod_info = $this->Product->find('first', array(
                                'conditions' => array('id' => $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']),
                                'recursive' => -1
                            ));

                            if ($prod_info['Product']['is_virtual'] == 1) {

                                $pd_name_replace = $this->get_product_inventroy_check($store_id, $prod_info['Product']['id'], $prod_info['Product']['parent_id']);

                                if ($pd_name_replace == 1) {
                                    $pdname = $this->get_parent_virtual_pd_info($data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']);

                                    $data['Product']['name'] = $pdname['VirtualProduct']['name'];
                                }
                            }

                            $selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
                            $i++;
                            $b_html .= '
                                <tr class="bonus_row n_bonus_row ' . $display_none . ' bonus_policy_id_' . $policy_id . ' set_' . $set . '">
                                    <th class="text-center" id="bonus_product_list">
                                        <div class="input select">
                                            <select ' . $s_disabled . ' ' . $disabled . ' name="data[OrderDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
                                            <option value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">' . $data['Product']['name'] . '</option>
                                            </select>
                                        </div>
                                    </th>
                                    <th class="text-center" width="12%">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="text" name="" class="form-control width_100_this open_bonus_product_unit_name" value="' . $data['MU']['name'] . '" disabled="">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="open_bonus_product_unit_id" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'] . '">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][discount_amount][]" class="form-control width_100 discount_amount" value="" />
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][disccount_type][]" class="form-control width_100 disccount_type" value=""/>
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type" value="' . $policy_type . '">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id" value="' . $policy_id . '">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus" value="3">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100_this open_bonus_product_rate" value="0.0">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][product_price_id][]" class="form-control width_100_this open_bonus_product_price_id" value="">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][combination_id][]" step="any" class="combination_id" value="">
                                        <input ' . $s_disabled . ' ' . $disabled . ' type="hidden" name="data[OrderDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="' . $set . '">
                                    </th>
                                    <th class="text-center" width="12%">
                                        <input ' . $min_qty_disabled . ' ' . $min_qty_required . ' data-set="' . $set . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[OrderDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_' . $set . '" value="' . $value . '">
                                    </th>
                                    <th class="text-center" width="10%">
                                        <input type="checkbox" disabled ' . $min_qty_checked . ' class="is_bonus_checked" name="data[OrderDetail][bonus_check][]" value="' . $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'] . '">
                                    </th>
                                </tr>';
                        }
                    }
                }
            }
        }
        return $b_html;
    }

    public function get_parent_virtual_pd_info($pid)
    {

        $this->loadModel('CurrentInventory');
        $this->loadModel('Product');

        $productinfo = $this->Product->find('first', array(
            'conditions' => array(
                'Product.id' => $pid
            ),
            'joins' => array(
                array(
                    'alias' => 'VirtualProduct',
                    'table' => 'products',
                    'type' => 'LEFT',
                    'conditions' => 'Product.parent_id = VirtualProduct.id'
                )
            ),
            'fields' => array('Product.id', 'Product.name', 'VirtualProduct.id', 'VirtualProduct.name'),
            'recursive' => -1
        ));

        return $productinfo;
    }

    public function get_product_inventroy_check($soter_id, $vpid, $parent_product_id)
    {

        $this->loadModel('CurrentInventory');
        $this->loadModel('Product');

        $parentproductcount = $this->CurrentInventory->find('count', array(
            'fields' => array('CurrentInventory.product_id'),
            'conditions' => array(
                'CurrentInventory.store_id' => $soter_id,
                'CurrentInventory.product_id' => $parent_product_id
            ),
            'group' => 'CurrentInventory.product_id HAVING sum(CurrentInventory.qty)>0',
            'recursive' => -1
        ));

        $chilhproductCount = $this->CurrentInventory->find('count', array(
            'fields' => array('CurrentInventory.product_id'),
            'conditions' => array(
                'CurrentInventory.store_id' => $soter_id,
                'Product.parent_id' => $parent_product_id
            ),
            'joins' => array(
                array(
                    'alias' => 'Product',
                    'table' => 'products',
                    'type' => 'INNER',
                    'conditions' => 'Product.id = CurrentInventory.product_id'
                )
            ),
            'group' => 'CurrentInventory.product_id HAVING sum(CurrentInventory.qty)>0',
            'recursive' => -1
        ));

        if (empty($parentproductcount)) {
            $parentproductcount = 0;
        }

        if (empty($chilhproductCount)) {
            $chilhproductCount = 0;
        }

        //echo $soter_id . '---' . $parent_product_id . '---' . $parentproductcount . '--' . $chilhproductCount;exit;

        if ($parentproductcount == 0 and $chilhproductCount == 1) {
            $show = 1;
        } else {
            $show = 0;
        }

        return $show;
    }

    private function parse_formla($formula)
    {
        $set_relation = '';
        $formula = explode(' ', $formula);
        $formula_product = array();
        $formula_element_relation = array();
        $set = 1;
        for ($i = 0; $i < count($formula); $i++) {
            if ($formula[$i] == '(') {
                continue;
            }
            if ($formula[$i] == ')') {
                if (($i + 1) < count($formula))
                    $set_relation = $formula[$i + 1];
                $set += 1;
                $i = $i + 1;
            } else {
                if ($formula[$i] == 'AND' || $formula[$i] == 'OR') {
                    $formula_element_relation[$set] = $formula[$i];
                } else {
                    $formula_product[$set][] = $formula[$i];
                }
            }
        }
        $parse_formula = array(
            'set_relation' => $set_relation,
            'formula_product' => $formula_product,
            'element_relation' => $formula_element_relation
        );
        return $parse_formula;
    }
    public function get_combine_or_individual_price()
    {

        $product_items_id = explode(',', rtrim($this->request->data['product_id_list'], ","));

        /* ---- read session data ----- */
        $cart_data = $this->Session->read('cart_session_data');
        $matched_data = $this->Session->read('matched_session_data');

        /*    echo "<pre>";
          echo "Cart data ----------------";
          print_r($cart_data);
          echo "Cart data ----------------";
          print_r($matched_data); exit;*/


        /* ---- read session data ----- */
        $combined_product = $this->request->data['combined_product'];
        $min_qty = $this->request->data['min_qty'];
        $product_id = $this->request->data['product_id'];

        /*---------Bonus-----------*/
        /*$this->loadModel('Bonus');
        $this->Bonus->recursive = -1;
        $bonus_info = $this->Bonus->find('first',array(
            'conditions'=>array('mother_product_id'=>$product_id, 'mother_product_quantity'=>$min_qty),
            'fields'=>array('mother_product_quantity', 'bonus_product_id', 'bonus_product_quantity')
        ));

        if (count($bonus_info) != 0) {
            $bonus_product_id = $bonus_info['Bonus']['bonus_product_id'];
            $this->loadModel('Product');
            $this->Product->recursive = -1;
            $bonus_product_name = $this->Product->find('first',array(
                'conditions'=>array('id'=>$bonus_product_id),
                'fields'=>array('name', 'sales_measurement_unit_id')
            ));
            $result_data['mother_product_quantity'] = $bonus_info['Bonus']['mother_product_quantity'];
            $result_data['bonus_product_id'] = $bonus_product_id;
            $result_data['bonus_product_name'] = $bonus_product_name['Product']['name'];
            $result_data['bonus_measurement_unit_id'] = $bonus_product_name['Product']['sales_measurement_unit_id'];
            $result_data['bonus_product_qty'] = $bonus_info['Bonus']['bonus_product_quantity'];
        }*/

        /*---------Bonus-----------*/
        $this->loadModel('Bonus');
        $this->loadModel('Product');

        $this->Bonus->recursive = -1;
        $bonus_info = $this->Bonus->find('all', array(
            'conditions' => array(
                'mother_product_id' => $product_id,
                'effective_date <=' => date('Y-m-d'),
                'end_date >=' => date('Y-m-d'),
            ),
            'fields' => array('mother_product_quantity', 'bonus_product_id', 'bonus_product_quantity')
        ));

        if (!empty($bonus_info[0]['Bonus']['mother_product_quantity'])) {
            $mother_product_quantity_bonus = $bonus_info[0]['Bonus']['mother_product_quantity'];
            $result_data['mother_product_quantity_bonus'] = $mother_product_quantity_bonus;
        }

        $no_of_bonus_slap = count($bonus_info);

        if ($no_of_bonus_slap != 0) {
            for ($slap_count = 0; $slap_count < $no_of_bonus_slap; $slap_count++) {
                $bonus_slap['bonus_product_id'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_id'];

                $this->Product->recursive = -1;
                $bonus_product_name = $this->Product->find('first', array(
                    'conditions' => array('id' => $bonus_slap['bonus_product_id'][$slap_count]),
                    'fields' => array('name', 'sales_measurement_unit_id')
                ));
                $quantity_slap['mother_product_quantity'][$slap_count] = $bonus_info[$slap_count]['Bonus']['mother_product_quantity'];

                $result_data['bonus_product_id'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_id'];
                $result_data['bonus_product_name'][$slap_count] = $bonus_product_name['Product']['name'];
                $result_data['sales_measurement_unit_id'][$slap_count] = $bonus_product_name['Product']['sales_measurement_unit_id'];
                $result_data['bonus_product_quantity'][$slap_count] = $bonus_info[$slap_count]['Bonus']['bonus_product_quantity'];
            }
            for ($i = 0; $i < count($quantity_slap['mother_product_quantity']); $i++) {
                $result_data['mother_product_quantity'][] = array(
                    'min' => $i == 0 ? 0 : $quantity_slap['mother_product_quantity'][$i - 1],
                    'max' => $quantity_slap['mother_product_quantity'][$i] - 1
                );
            }
        }

        global $qty_data;
        $qty_data = array();

        function build_inputed_qty_data($product_id = null, $min_qty = null, &$qty_data = array())
        {
            if (!array_key_exists($product_id, $qty_data)) {
                $qty_data[$product_id] = $min_qty;
            } else {
                $qty_data[$product_id] = $min_qty;
            }
        }

        if (!empty($combined_product)) {
            if ($this->Session->read('combintaion_qty_data') == NULL) {
                $qty_data = array();
            } else {
                $qty_data = $this->Session->read('combintaion_qty_data');
                /*echo '<pre>';
                pr($qty_data);exit;*/
            }
            build_inputed_qty_data($product_id, $min_qty, $qty_data);
            $this->Session->write('combintaion_qty_data', $qty_data);
        }
        /* echo "<br/>";
          echo "Cart data ----------------";
          print_r($matched_data);
          exit; */
        $current_qty = $this->Session->read('combintaion_qty_data');
        /* echo "<br/>";
          echo "Cart data ----------------";
          print_r($current_qty); */
        if (!empty($current_qty)) {
            $prev_data = array();
            foreach ($current_qty as $q_key => $q_val) {
                $prev_data[] = $q_key;
            }
            $diff_product_id = array_diff($prev_data, $product_items_id);
            if (!empty($diff_product_id)) {
                foreach ($diff_product_id as $key => $val) {
                    unset($current_qty[$val]);
                }
                $this->Session->write('combintaion_qty_data', $current_qty);
            }
        }
        //pr($combined_product);pr($matched_data);die();
        if ($combined_product) {
            foreach ($matched_data as $combined_product_key => $combined_product_val) {
                if ($combined_product_key == $combined_product) {
                    /*if ($combined_product_val['is_matched_yet'] == 'NO') {
                        foreach ($cart_data as $no_com_key => $no_com_val) {
                            if ($no_com_key == $product_id) {
                                $less_qty_array = array();
                                foreach ($no_com_val['individual_slab'] as $in_slab_qty => $in_slab_val) {
                                    if ($min_qty >= $in_slab_qty) {
                                        $less_qty_array[$in_slab_qty] = $in_slab_val;
                                    }
                                }
                                ksort($less_qty_array);
                                $unit_rate = array_pop($less_qty_array);

                                if (empty($unit_rate)) {
                                    $unit_rate = $no_com_val['product_price']['general_price'];
                                }
                                if ($unit_rate) {
                                    $result_data ['unit_rate'] = sprintf("%1\$.6f", $unit_rate);
                                    $result_data ['total_value'] = sprintf("%1\$.6f", $unit_rate) * $min_qty;
                                } else {
                                    $result_data ['unit_rate'] = '';
                                    $result_data ['total_value'] = '';
                                }
                            }
                        }
                    }*/
                    if ($combined_product_val['is_matched_yet'] == 'NO' || $combined_product_val['is_matched_yet'] == 'YES') {
                        $current_qty = $this->Session->read('combintaion_qty_data');
                        /* echo "<pre>";
                          print_r($current_qty);
                          print_r($cart_data);
                          exit; */
                        foreach ($cart_data as $no_com_key => $no_com_val) {
                            $combined_product = explode(',', $no_com_val['combined_product']);
                            $combined_inputed_val = 0;
                            foreach ($combined_product as $qty_key => $qty_val) {
                                /* if($qty_val == $no_com_key){
                                  $combined_inputed_val = $combined_inputed_val + $current_qty[$qty_val];
                              } */
                                if (array_key_exists($qty_val, $current_qty)) {
                                    $combined_inputed_val = $combined_inputed_val + $current_qty[$qty_val];
                                }
                            }
                            if ($no_com_key == $product_id) {
                                //echo $no_com_key." == ".$product_id."<br/>";
                                $less_qty_data = array();
                                foreach ($no_com_val['combined_slab'] as $in_slab_qty => $in_slab_val) {
                                    if ($combined_inputed_val >= $in_slab_qty) {

                                        $less_qty_data[$in_slab_qty] = $in_slab_val;
                                    }
                                }
                                ksort($less_qty_data);
                                $actual_data = array_pop($less_qty_data);
                                $combined_less_qty = array();
                                if (is_array($actual_data) && is_array($current_qty)) {
                                    $combined_common_product = array_intersect_key($actual_data, $current_qty);
                                }
                                if (!empty($actual_data[$product_id]) && count($combined_common_product) > 1) {
                                    foreach ($actual_data as $ac_key => $ac_val) {
                                        $combined_less_qty[$ac_key]['unit_rate'] = sprintf("%1\$.6f", $ac_val);
                                        if (!empty($current_qty[$ac_key])) {
                                            $combined_less_qty[$ac_key]['total_value'] = $current_qty[$ac_key] * sprintf("%1\$.6f", $ac_val);
                                        }
                                    }
                                } else {
                                    /* --------------------------------- */
                                    /* =============================================== */
                                    $individual_less_qty = array();
                                    foreach ($combined_product as $combined_key => $combined_val) {
                                        if (array_key_exists($combined_val, $cart_data)) {
                                            $individual_less_qty_unique = array();
                                            foreach ($cart_data[$combined_val]['individual_slab'] as $in_slab_qty => $in_slab_val) {
                                                if ($current_qty[$combined_val] >= $in_slab_qty) {
                                                    $individual_less_qty_unique[$in_slab_qty] = $in_slab_val;
                                                }
                                            }
                                            ksort($individual_less_qty_unique);
                                            $individual_actual_data = array_pop($individual_less_qty_unique);
                                            if (empty($individual_actual_data)) {
                                                $individual_actual_data = $cart_data[$combined_val]['product_price']['general_price'];
                                                /* 	echo "<pre>";
                                                  echo "dsfsdf";
                                                  print_r($individual_actual_data);
                                                  print_r($cart_data[$combined_val]); */
                                            }
                                            $individual_less_qty[$combined_val]['unit_rate'] = sprintf("%1\$.6f", $individual_actual_data);
                                            $individual_less_qty[$combined_val]['total_value'] = sprintf("%1\$.6f", $individual_actual_data) * $current_qty[$combined_val];
                                        }
                                    }
                                    //exit;
                                    /* --------------------------------- */
                                }
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($cart_data as $no_com_key => $no_com_val) {
                if ($product_id == $no_com_key) {
                    $less_qty_val = array();
                    foreach ($no_com_val['individual_slab'] as $in_slab_key => $in_slab_val) {
                        if ($min_qty >= $in_slab_key) {
                            $less_qty_val[$in_slab_key] = $in_slab_val;
                        }
                    }
                    ksort($less_qty_val);
                    $unit_rate = array_pop($less_qty_val);
                    if (empty($unit_rate)) {
                        $unit_rate = $no_com_val['product_price']['general_price'];
                    }
                    if ($unit_rate) {
                        $result_data['unit_rate'] = sprintf("%1\$.6f", $unit_rate);
                        $result_data['total_value'] = $unit_rate * sprintf("%1\$.6f", $min_qty);
                    } else {
                        $result_data['unit_rate'] = '';
                        $result_data['total_value'] = '';
                    }
                }
            }
        }
        if (!empty($result_data)) {
            echo json_encode($result_data);
        } elseif (!empty($individual_less_qty)) {
            echo json_encode($individual_less_qty);
        } elseif (!empty($combined_less_qty)) {
            echo json_encode($combined_less_qty);
        }
        $this->autoRender = false;
    }


    public function delete_order()
    {
        /* -------- Start Session data --------- */
        $cart_data = $this->Session->read('cart_session_data');
        $matched_data = $this->Session->read('matched_session_data');
        $current_qty = $this->Session->read('combintaion_qty_data');
        /* -------- End Session data --------- */
        $product_id = $this->request->data['product_id'];
        $combined_product = $this->request->data['combined_product'];
        if (!empty($product_id)) {
            unset($cart_data[$product_id]);
            $this->Session->write('cart_session_data', $cart_data);

            if (!empty($combined_product)) {
                if ($matched_data[$combined_product]['matched_count_so_far'] == 1) {
                    unset($matched_data[$combined_product]);
                    $this->Session->write('matched_session_data', $matched_data);
                } else {
                    $matched_id_so_far = rtrim($matched_data[$combined_product]['matched_id_so_far'], ',' . $product_id);
                    $matched_count_so_far = $matched_data[$combined_product]['matched_count_so_far'] - 1;
                    $matched_data[$combined_product]['is_matched_yet'] = 'NO';
                    $matched_data[$combined_product]['matched_count_so_far'] = $matched_count_so_far;
                    $matched_data[$combined_product]['matched_id_so_far'] = $matched_id_so_far;
                    $this->Session->write('matched_session_data', $matched_data);
                }
            }
            if (!empty($current_qty)) {
                unset($current_qty[$product_id]);
                $this->Session->write('combintaion_qty_data', $current_qty);
            }
            echo 'yes';
        }

        $this->autoRender = false;
    }


    public function get_territory_id()
    {
        $sales_person_id = $this->request->data['sales_person_id'];
        //$sales_person_id = 2;
        $this->SalesPerson->recursive = 0;
        $territory_id = $this->SalesPerson->find('all', array(
            'conditions' => array('SalesPerson.id' => $sales_person_id),
            'fields' => array('SalesPerson.territory_id')
        ));

        if ($territory_id) {
            $response['territory_id'] = $territory_id[0]['SalesPerson']['territory_id'];
        } else {
            $response['territory_id'] = '';
        }

        if ($response) {
            echo json_encode($response);
        }
        $this->autoRender = false;
    }

    public function admin_order_map()
    {

        $this->set('page_title', 'Requisition Orders List on Map');
        $message = '';
        $map_data = array();
        $conditions = array();
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $conditions[] = array();
            $office_conditions = array();
        } else {
            $conditions[] = array('Territory.office_id' => $this->UserAuth->getOfficeId());
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }

        // Custome Search
        if (($this->request->is('post') || $this->request->is('put'))) {

            if ($this->request->data['Order']['office_id'] != '') {
                $conditions[] = array('Territory.office_id' => $this->request->data['Order']['office_id']);
            }
            if ($this->request->data['Order']['territory_id'] != '') {
                $conditions[] = array('Order.territory_id' => $this->request->data['Order']['territory_id']);
            }
            if ($this->request->data['Order']['date_from'] != '') {
                $conditions[] = array('Order.order_date >=' => Date('Y-m-d', strtotime($this->request->data['Order']['date_from'])));
            }
            if ($this->request->data['Order']['date_to'] != '') {
                $conditions[] = array('Order.order_date <=' => Date('Y-m-d', strtotime($this->request->data['Order']['date_to'])));
            }

            $this->Order->recursive = 0;
            $order_list = $this->Order->find('all', array(
                'conditions' => $conditions,
                'order' => array('Order.id' => 'desc'),
                'recursive' => 0
            ));

            if (!empty($order_list)) {
                foreach ($order_list as $val) {
                    if ($val['Order']['latitude'] > 0 and $val['Order']['longitude'] > 0) {
                        $data['title'] = $val['Outlet']['name'];
                        $data['lng'] = $val['Order']['longitude'];
                        $data['lat'] = $val['Order']['latitude'];
                        $data['description'] = '<p><b>Outlet : ' . $val['Outlet']['name'] . '</b></br>' .
                            'Market : </b>' . $val['Market']['name'] . '</br>' .
                            'Territory : </b>' . $val['Territory']['name'] . '</p>' .
                            '<p>Order No. : ' . $val['Order']['order_no'] . '</br>' .
                            'Order Date : ' . date('d-M-Y', strtotime($val['Order']['order_date'])) . '</br>' .
                            'Order Amount : ' . sprintf('%.2f', $val['Order']['gross_value']) . '</p>' .
                            '<a class="btn btn-primary btn-xs" href="' . BASE_URL . 'admin/orders/view/' . $val['Order']['id'] . '" target="_blank">Order Details</a>';
                        $map_data[] = $data;
                    }
                }
            }
            if (!empty($map_data))
                $message = '';
            else
                $message = '<div class="alert alert-danger">No order found.</div>';
        }

        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        $office_id = (isset($this->request->data['Order']['office_id']) ? $this->request->data['Order']['office_id'] : 0);
        $territories = $this->Order->Territory->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));
        $this->set(compact('offices', 'territories', 'map_data', 'message'));
    }

    public function update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '')
    {

        $this->loadModel('CurrentInventory');

        $find_type = 'all';
        if ($update_type == 'add')
            $find_type = 'first';

        pr($quantity);
        pr($product_id);
        pr($store_id);
        pr($update_type);
        pr($transaction_type_id);
        pr($transaction_date);

        $inventory_info = $this->CurrentInventory->find($find_type, array(
            'conditions' => array(
                //'CurrentInventory.qty >=' => 0,
                'CurrentInventory.store_id' => $store_id,
                'CurrentInventory.inventory_status_id' => 1,
                'CurrentInventory.product_id' => $product_id
            ),
            'order' => array('CurrentInventory.expire_date' => 'asc'),
            'recursive' => -1
        ));


        pr($inventory_info); //die();

        if ($update_type == 'deduct') {
            foreach ($inventory_info as $val) {
                if ($quantity <= $val['CurrentInventory']['qty']) {
                    $this->CurrentInventory->id = $val['CurrentInventory']['id'];
                    $this->CurrentInventory->updateAll(
                        array(
                            'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $quantity,
                            'CurrentInventory.transaction_type_id' => $transaction_type_id,
                            'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                            'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                        ),
                        array('CurrentInventory.id' => $val['CurrentInventory']['id'])
                    );
                    break;
                } else {
                    $quantity = $quantity - $val['CurrentInventory']['qty'];

                    if ($val['CurrentInventory']['qty'] > 0) {

                        $this->CurrentInventory->id = $val['CurrentInventory']['id'];
                        $this->CurrentInventory->updateAll(
                            array(
                                'CurrentInventory.qty' => 'CurrentInventory.qty - ' . $val['CurrentInventory']['qty'],
                                'CurrentInventory.transaction_type_id' => $transaction_type_id,
                                'CurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                'CurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                            ),
                            array('CurrentInventory.id' => $val['CurrentInventory']['id'])
                        );
                    }
                }
            }
        } else {
            /* $this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty + '.$inventory_info['CurrentInventory']['qty']),array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])); */
            if (!empty($inventory_info)) {

                $this->CurrentInventory->updateAll(
                    array('CurrentInventory.qty' => 'CurrentInventory.qty + ' . $quantity, 'CurrentInventory.transaction_type_id' => $transaction_type_id, 'CurrentInventory.store_id' => $store_id, 'CurrentInventory.transaction_date' => "'" . $transaction_date . "'"),
                    array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])
                );
            }
        }

        return true;
    }


    // it will be called from order not from order_details 
    // cal_type=1 means increment and 2 means deduction 

    public function ec_calculation($gross_value, $outlet_id, $terrority_id, $order_date, $cal_type)
    {
        // from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
        // check gross_value >0

        if ($gross_value > 0) {
            $this->loadModel('Outlet');
            // from outlet_id, retrieve pharma or non-pharma
            $outlet_info = $this->Outlet->find('first', array(
                'conditions' => array(
                    'Outlet.id' => $outlet_id
                ),
                'recursive' => -1
            ));

            if (!empty($outlet_info)) {
                $is_pharma_type = $outlet_info['Outlet']['is_pharma_type'];
                // from order_date , split month and get month name and compare month table with order year
                $orderDate = strtotime($order_date);
                $month = date("n", $orderDate);
                $year = date("Y", $orderDate);
                $this->loadModel('Month');

                // from outlet_id, retrieve pharma or non-pharma
                $fasical_info = $this->Month->find('first', array(
                    'conditions' => array(
                        'Month.month' => $month,
                        'Month.year' => $year
                    ),
                    'recursive' => -1
                ));


                if (!empty($fasical_info)) {
                    $this->loadModel('SaleTargetMonth');
                    if ($cal_type == 1) {
                        if ($is_pharma_type == 1) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_pharma_achievement' => "SaleTargetMonth.effective_call_pharma_achievement+1");
                        } else if ($is_pharma_type == 0) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_non_pharma_achievement' => "SaleTargetMonth.effective_call_non_pharma_achievement+1");
                        }
                    } else {
                        if ($is_pharma_type == 1) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_pharma_achievement' => "SaleTargetMonth.effective_call_pharma_achievement-1");
                        } else if ($is_pharma_type == 0) {
                            $update_fields_arr = array('SaleTargetMonth.effective_call_non_pharma_achievement' => "SaleTargetMonth.effective_call_non_pharma_achievement-1");
                        }
                    }

                    $conditions_arr = array('SaleTargetMonth.product_id' => 0, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'], 'SaleTargetMonth.month_id' => $fasical_info['Month']['id']);

                    $this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
                }
            }
        }
    }

    // cal_type=1 means increment and 2 means deduction 
    // it will be called from  order_details 
    public function sales_calculation($product_id, $terrority_id, $quantity, $gross_value, $order_date, $cal_type)
    {
        // from so_id , retrieve aso_id,terrority_id : no need as we have territory_id
        // from order_date , split month and get month name and compare month table with order year
        $orderDate = strtotime($order_date);
        $month = date("n", $orderDate);
        $year = date("Y", $orderDate);
        $this->loadModel('Month');
        // from outlet_id, retrieve pharma or non-pharma
        $fasical_info = $this->Month->find('first', array(
            'conditions' => array(
                'Month.month' => $month,
                'Month.year' => $year
            ),
            'recursive' => -1
        ));

        if (!empty($fasical_info)) {
            $this->loadModel('SaleTargetMonth');
            if ($cal_type == 1) {
                $update_fields_arr = array('SaleTargetMonth.target_quantity_achievement' => "SaleTargetMonth.target_quantity_achievement+$quantity", 'SaleTargetMonth.target_amount_achievement' => "SaleTargetMonth.target_amount_achievement+$gross_value");
            } else {
                $update_fields_arr = array('SaleTargetMonth.target_quantity_achievement' => "SaleTargetMonth.target_quantity_achievement-$quantity", 'SaleTargetMonth.target_amount_achievement' => "SaleTargetMonth.target_amount_achievement-$gross_value");
            }

            $conditions_arr = array('SaleTargetMonth.product_id' => $product_id, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'], 'SaleTargetMonth.month_id' => $fasical_info['Month']['id']);
            $this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
        }
    }

    // cal_type=1 means increment and 2 means deduction 
    // it will be called from order not from order_details 
    public function oc_calculation($terrority_id, $gross_value, $outlet_id, $order_date, $order_time, $cal_type)
    {

        // from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
        // check gross_value >0
        if ($gross_value > 0) {
            $this->loadModel('Order');
            // this will be updated monthly , if done then increment else no action
            $month_first_date = date('Y-m-01', strtotime($order_date));
            $count = $this->Order->find('count', array(
                'conditions' => array(
                    'Order.outlet_id' => $outlet_id,
                    'Order.order_date >= ' => $month_first_date,
                    'Order.order_time < ' => $order_time
                )
            ));

            if ($count == 0) {

                $this->loadModel('Outlet');
                // from outlet_id, retrieve pharma or non-pharma
                $outlet_info = $this->Outlet->find('first', array(
                    'conditions' => array(
                        'Outlet.id' => $outlet_id
                    ),
                    'recursive' => -1
                ));

                if (!empty($outlet_info)) {
                    $is_pharma_type = $outlet_info['Outlet']['is_pharma_type'];
                    // from order_date , split month and get month name and compare month table with order year
                    $orderDate = strtotime($order_date);
                    $month = date("n", $orderDate);
                    $year = date("Y", $orderDate);
                    $this->loadModel('Month');
                    // from outlet_id, retrieve pharma or non-pharma
                    $fasical_info = $this->Month->find('first', array(
                        'conditions' => array(
                            'Month.month' => $month,
                            'Month.year' => $year
                        ),
                        'recursive' => -1
                    ));

                    if (!empty($fasical_info)) {
                        $this->loadModel('SaleTargetMonth');
                        if ($cal_type == 1) {
                            if ($is_pharma_type == 1) {
                                $update_fields_arr = array('SaleTargetMonth.outlet_coverage_pharma_achievement' => "SaleTargetMonth.outlet_coverage_pharma_achievement+1");
                            } else if ($is_pharma_type == 0) {
                                $update_fields_arr = array('SaleTargetMonth.outlet_coverage_non_pharma_achievement' => "SaleTargetMonth.outlet_coverage_non_pharma_achievement+1");
                            }
                        } else {
                            if ($is_pharma_type == 1) {
                                $update_fields_arr = array('SaleTargetMonth.outlet_coverage_pharma_achievement' => "SaleTargetMonth.outlet_coverage_pharma_achievement-1");
                            } else if ($is_pharma_type == 0) {
                                $update_fields_arr = array('SaleTargetMonth.outlet_coverage_non_pharma_achievement' => "SaleTargetMonth.outlet_coverage_non_pharma_achievement-1");
                            }
                        }

                        $conditions_arr = array('SaleTargetMonth.product_id' => 0, 'SaleTargetMonth.territory_id' => $terrority_id, 'SaleTargetMonth.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'], 'SaleTargetMonth.month_id' => $fasical_info['Month']['id']);
                        $this->SaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
                        //pr($conditions_arr);
                        //pr($update_fields_arr);
                        //exit;
                    }
                }
            }
        }
    }

    // it will be called from order_details 
    public function stamp_calculation($order_no, $terrority_id, $product_id, $outlet_id, $quantity, $order_date, $cal_type, $gross_amount, $market_id)
    {
        // from outlet_id, get bonus_type_id and check if null then no action else action

        $this->loadModel('Outlet');
        // from outlet_id, retrieve pharma or non-pharma
        $outlet_info = $this->Outlet->find('first', array(
            'conditions' => array(
                'Outlet.id' => $outlet_id
            ),
            'recursive' => -1
        ));

        if (!empty($outlet_info) && $gross_amount > 0) {
            $bonus_type_id = $outlet_info['Outlet']['bonus_type_id'];
            if (($bonus_type_id === NULL) || (empty($bonus_type_id))) {
                // no action 
            } else {
                // from order_date , split month and get month name and compare month table with order year (get fascal year id)
                $orderDate = strtotime($order_date);
                $month = date("n", $orderDate);
                $year = date("Y", $orderDate);
                $this->loadModel('Month');
                $fasical_info = $this->Month->find('first', array(
                    'conditions' => array(
                        'Month.month' => $month,
                        'Month.year' => $year
                    ),
                    'recursive' => -1
                ));

                if (!empty($fasical_info)) {
                    // check bonus card table , where is_active,and others  and get min qty per order
                    $this->loadModel('BonusCard');
                    $bonus_card_info = $this->BonusCard->find('first', array(
                        'conditions' => array(
                            'BonusCard.fiscal_year_id' => $fasical_info['Month']['fiscal_year_id'],
                            'BonusCard.is_active' => 1,
                            'BonusCard.product_id' => $product_id,
                            'BonusCard.bonus_card_type_id' => $bonus_type_id
                        ),
                        'recursive' => -1
                    ));

                    // if exist min qty per order , then stamp_no=mod(quantity/min qty per order)
                    if (!empty($bonus_card_info)) {
                        $min_qty_per_order = $bonus_card_info['BonusCard']['min_qty_per_order'];
                        if ($min_qty_per_order && $min_qty_per_order <= $quantity) {
                            $stamp_no = floor($quantity / $min_qty_per_order);
                            if ($cal_type != 1) {
                                $stamp_no = $stamp_no * (-1);
                                $quantity = $quantity * (-1);
                            }


                            $this->loadModel('StoreBonusCard');
                            $log_data = array();
                            $log_data['StoreBonusCard']['created_at'] = $this->current_datetime();
                            $log_data['StoreBonusCard']['territory_id'] = $terrority_id;
                            $log_data['StoreBonusCard']['outlet_id'] = $outlet_id;
                            $log_data['StoreBonusCard']['market_id'] = $market_id;
                            $log_data['StoreBonusCard']['product_id'] = $product_id;
                            $log_data['StoreBonusCard']['quantity'] = $quantity;
                            $log_data['StoreBonusCard']['no_of_stamp'] = $stamp_no;
                            $log_data['StoreBonusCard']['bonus_card_id'] = $bonus_card_info['BonusCard']['id'];
                            $log_data['StoreBonusCard']['bonus_card_type_id'] = $bonus_type_id;
                            $log_data['StoreBonusCard']['fiscal_year_id'] = $bonus_card_info['BonusCard']['fiscal_year_id'];
                            $log_data['StoreBonusCard']['order_no'] = $order_no;

                            $this->StoreBonusCard->create();
                            $this->StoreBonusCard->save($log_data);
                        }
                    }
                }
            }
        }
    }

    public function admin_order_no_validation()
    {
        $this->loadModel('CsaOrder');
        //pr($this->request->data);die();
        if ($this->request->is('post')) {
            $order_no = $this->request->data['order_no'];
            $sale_type_id = $this->request->data['sale_type'];

            if ($sale_type_id == 1 || $sale_type_id == 3 || $sale_type_id == 4) {
                $order_list = $this->Order->find('list', array(
                    'conditions' => array('Order.order_no' => $order_no),
                    'fields' => array('order_no'),
                    'recursive' => -1
                ));
            } else {
                $order_list = $this->CsaOrder->find('list', array(
                    'conditions' => array('CsaOrder.csa_order_no' => $order_no),
                    'fields' => array('csa_order_no'),
                    'recursive' => -1
                ));
            }
            $order_exist = count($order_list);

            echo json_encode($order_exist);
        }

        $this->autoRender = false;
    }

    public function admin_get_product()
    {
        //pr($this->request->data);die();        
        $this->loadModel('Store');
        $this->loadModel('Product');
        $this->loadModel('CurrentInventory');
        $outlet_id = $this->request->data['outlet_id'];
        $office_id = $this->request->data['office_id'];
        $rs = array(array('id' => '', 'name' => '---- Select Product -----'));
        $store_info = $this->Store->find('first', array(
            'conditions' => array(
                'Store.territory_id' => null,
                'Store.office_id' => $office_id,
                'Store.store_type_id' => 2,
            ),
            'recursive' => -1
        ));

        if (isset($store_info['Store']['id']) && $store_info['Store']['id']) {
            $store_id = $store_info['Store']['id'];

            /*if(isset($this->request->data['csa_id']) && $this->request->data['csa_id']!=0)
			{*/
            //$conditions=array('CurrentInventory.store_id' => $store_id,'inventory_status_id'=>1);
            //}
            //else
            //{
            // $conditions=array('CurrentInventory.store_id' => $store_id,'CurrentInventory.qty > ' => 0,'inventory_status_id'=>1);
            $conditions = array('CurrentInventory.store_id' => $store_id, 'inventory_status_id' => 1, 'CurrentInventory.qty > ' => 0);
            //}

            $products_from_ci = $this->CurrentInventory->find('all', array(
                'fields' => array('DISTINCT CurrentInventory.product_id'),
                'conditions' => $conditions,
            ));

            $product_ci = array();
            foreach ($products_from_ci as $each_ci) {
                $product_ci[] = $each_ci['CurrentInventory']['product_id'];
            }
            //echo '<pre>';print_r($product_ci);exit;
            $this->loadModel('ProductPricesV2');
            $product_list_for_distributor = $this->ProductPricesV2->find('all', array(
                'conditions' => array('ProductPricesV2.product_id' => $product_ci),
            ));
            $product_lists = array();
            foreach ($product_list_for_distributor as $val) {
                $product_lists[] = $val['ProductPricesV2']['product_id'];
            }
            //pr($product_lists);
            $product_ci_in = implode(",", $product_ci);

            $products = $this->Product->find('all', array(
                'conditions' => array(
                    // 'Product.id' => $product_lists,
                    'Product.is_distributor_product' => 1,
                ),
                'order' => array('Product.order' => 'asc'),
                'joins' => array(
                    array(
                        'table' => 'products',
                        'alias' => 'ParentProduct',
                        'type' => 'left',
                        'conditions' => 'ParentProduct.id=Product.parent_id'
                    )
                ),
                'fields' => array('Product.id as id', 'Product.name as name', 'ParentProduct.id as p_id', 'ParentProduct.name as p_name'),
                'recursive' => -1
            ));

            $product_grops = $this->Product->find('all', array(
                'conditions' => array(
                    'Product.id' => $product_lists,
                    'Product.is_distributor_product' => 1,
                ),
                'order' => array('Product.order' => 'asc'),
                'joins' => array(
                    array(
                        'table' => 'products',
                        'alias' => 'ParentProduct',
                        'type' => 'left',
                        'conditions' => 'ParentProduct.id=Product.parent_id'
                    )
                ),
                'fields' => array('Product.id as id', 'Product.name as name', 'ParentProduct.id as p_id', 'ParentProduct.name as p_name'),
                'recursive' => -1
            ));
            //echo $this->Product->getLastQuery();


            $group_product = array();
            foreach ($product_grops as $data) {
                if ($data[0]['p_id']) {
                    $group_product[$data[0]['p_id']][] = $data[0]['id'];
                } else {
                    $group_product[$data[0]['id']][] = $data[0]['id'];
                }
            }


            $product_array = array();
            foreach ($products as $data) {
                if ($data['0']['p_id'] && count($group_product[$data[0]['p_id']]) <= 1) {
                    $name = $data[0]['p_name'];
                } else {
                    $name = $data[0]['name'];
                }
                $product_array[] = array(
                    'id' => $data[0]['id'],
                    'name' => $name
                );
            }

            if (!empty($products)) {
                echo json_encode(array_merge($rs, $product_array));
            } else {
                echo json_encode($rs);
            }
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }


    public function search_array($value, $key, $array)
    {
        foreach ($array as $k => $val) {
            if ($val[$key] == $value) {
                return $array[$k];
            }
        }
        return null;
    }


    public function admin_memo_editable($id = null)
    {
        if ($id) {
            $this->Order->id = $id;
            if ($this->Order->id) {
                if ($this->Order->saveField('memo_editable', 1)) {
                    $this->Session->setFlash(__('The setting has been saved!'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('Order editable failed!'), 'flash/error');
                    $this->redirect(array('action' => 'index'));
                }
            }
        }
        $this->autoRender = false;
    }

    public function get_bonus_product_details()
    {
        $this->LoadModel('Product');

        $product_id = $this->request->data['product_id'];
        $territory_id = $this->request->data['territory_id'];
        $office_id = $this->request->data['office_id'];

        $product_details = $this->Product->find('first', array(
            'fields' => array('MIN(Product.product_category_id) as category_id', 'MIN(Product.sales_measurement_unit_id) as measurement_unit_id', 'MIN(MeasurementUnit.name) as measurement_unit_name', 'SUM(CurrentInventory.qty) as total_qty'),
            'conditions' => array('Product.id' => $product_id, 'Store.office_id' => $office_id),
            'joins' => array(
                array(
                    'table' => 'measurement_units',
                    'alias' => 'MeasurementUnit',
                    'conditions' => 'MeasurementUnit.id=Product.sales_measurement_unit_id'
                ),
                array(
                    'table' => 'current_inventories',
                    'alias' => 'CurrentInventory',
                    'type' => 'Inner',
                    'conditions' => 'CurrentInventory.product_id=Product.id'
                ),
                array(
                    'table' => 'stores',
                    'alias' => 'Store',
                    'type' => 'Inner',
                    'conditions' => 'CurrentInventory.store_id=Store.id'
                )
            ),
            'group' => array('Product.id', 'Store.id', 'Store.territory_id'),
            'recursive' => -1
        ));
        //pr($product_details);exit;
        $data['category_id'] = $product_details[0]['category_id'];
        $data['measurement_unit_id'] = $product_details[0]['measurement_unit_id'];
        $data['measurement_unit_name'] = $product_details[0]['measurement_unit_name'];
        $data['total_qty'] = $this->unit_convertfrombase($product_id, $data['measurement_unit_id'], $product_details[0]['total_qty']);
        echo json_encode($data);
        $this->autoRender = false;
    }

    public function get_bonus_product()
    {
        $product_list = array();
        echo json_encode($product_list);
        $this->autoRender = false;
        exit;
        $this->LoadModel('Product');
        $this->LoadModel('Store');

        $office_id = $this->request->data['office_id'];
        $store_id = $this->Store->find('first', array(
            'fields' => array('Store.id'),
            'conditions' => array('Store.office_id' => $office_id, 'Store.store_type_id' => 2),
            'recursive' => -1
        ));
        $product_list = $this->Product->find('all', array(
            'fields' => array('Product.id', 'Product.name'),
            'joins' => array(
                array(
                    'table' => 'current_inventories',
                    'alias' => 'CurrentInventory',
                    'type' => 'Inner',
                    'conditions' => 'CurrentInventory.product_id=Product.id'
                ),
                array(
                    'table' => 'open_combination_products',
                    'alias' => 'OpenCombinationProduct',
                    'type' => 'Inner',
                    'conditions' => 'OpenCombinationProduct.product_id=Product.id'
                ),
                array(
                    'table' => 'open_combinations',
                    'alias' => 'OpenCombination',
                    'type' => 'Inner',
                    'conditions' => 'OpenCombinationProduct.combination_id=OpenCombination.id'
                ),

            ),
            'conditions' => array(
                'CurrentInventory.qty >' => 0,
                'CurrentInventory.store_id' => $store_id['Store']['id'],
                'OpenCombination.start_date <=' => date('Y-m-d'),
                'OpenCombination.end_date >=' => date('Y-m-d'),
                'OpenCombination.is_bonus' => 1
            ),
            'group' => array('Product.id', 'Product.name'),
            'recursive' => -1
        ));
        echo json_encode($product_list);
        $this->autoRender = false;
    }

    public function get_product_price_id($product_id, $product_prices, $all_product_id)
    {
        // echo $product_id.'--'.$product_prices.'<br>';
        $this->LoadModel('ProductCombination');
        $this->LoadModel('Combination');
        $data = array();
        $product_price = $this->ProductCombination->find('first', array(
            'conditions' => array(
                'ProductCombination.product_id' => $product_id,
                'ProductCombination.price' => $product_prices,
                'ProductCombination.effective_date <=' => $this->current_date(),
            ),
            'order' => array('ProductCombination.id' => 'DESC'),
            'recursive' => -1
        ));

        // pr($product_price);exit;
        // echo $this->ProductCombination->getLastquery().'<br>';
        if ($product_price) {
            $is_combine = 0;
            if ($product_price['ProductCombination']['combination_id'] != 0) {
                $combination = $this->Combination->find('first', array(
                    'conditions' => array('Combination.id' => $product_price['ProductCombination']['combination_id']),
                    'recursive' => -1
                ));
                $combination_product = explode(',', $combination['Combination']['all_products_in_combination']);
                foreach ($combination_product as $combination_prod) {
                    if ($product_id != $combination_prod && in_array($combination_prod, $all_product_id)) {
                        $data['combination_id'] = $product_price['ProductCombination']['combination_id'];
                        $data['product_price_id'] = $product_price['ProductCombination']['id'];
                        $is_combine = 1;
                        break;
                    }
                }
            }
            if ($is_combine == 0) {
                $product_price = $this->ProductCombination->find('first', array(
                    'conditions' => array(
                        'ProductCombination.product_id' => $product_id,
                        'ProductCombination.price' => $product_prices,
                        'ProductCombination.effective_date <=' => $this->current_date(),
                        'ProductCombination.parent_slab_id' => 0
                    ),
                    'order' => array('ProductCombination.id DESC'),
                    'recursive' => -1
                ));
                $data['combination_id'] = '';
                $data['product_price_id'] = $product_price['ProductCombination']['id'];
            }
            return $data;
        } else {
            $data['combination_id'] = '';
            $data['product_price_id'] = '';
            return $data;
        }
    }
    function get_csa_list_by_office_id()
    {
        /*pr($this->request->data);*/
        $office_id = $this->request->data['office_id'];
        $output = "<option value=''>--- Select Csa ---</option>";
        if ($office_id) {
            $csa_outlet = $this->Outlet->find('list', array(
                'conditions' => array('Territory.office_id' => $office_id, 'Outlet.is_csa' => 1),
                'joins' => array(
                    array(
                        'table' => 'markets',
                        'alias' => 'Market',
                        'conditions' => 'Market.id=Outlet.market_id'
                    ),
                    array(
                        'table' => 'territories',
                        'alias' => 'Territory',
                        'conditions' => 'Territory.id=Market.territory_id'
                    ),
                )
            ));
            if ($csa_outlet) {
                foreach ($csa_outlet as $key => $data) {
                    $output .= "<option value='$key'>$data</option>";
                }
            }
        }
        echo $output;
        $this->autoRender = false;
    }
    function get_territory_list_by_csa_id()
    {
        $this->LoadModel('Territory');
        $csa_id = $this->request->data['csa_id'];
        $output = "<option value=''>--- Select Territory ---</option>";
        if ($csa_id) {
            $territory = $this->Territory->find('list', array(
                'conditions' => array('Outlet.id' => $csa_id),
                'joins' => array(
                    array(
                        'alias' => 'Market',
                        'table' => 'markets',
                        'type' => 'INNER',
                        'conditions' => 'Market.territory_id = Territory.id'
                    ),
                    array(
                        'alias' => 'Outlet',
                        'table' => 'outlets',
                        'type' => 'INNER',
                        'conditions' => 'Outlet.market_id = Market.id'
                    ),
                )
            ));

            if ($territory) {
                foreach ($territory as $key => $data) {
                    $output .= "<option value='$key'>$data</option>";
                }
            }
        }

        echo $output;
        $this->autoRender = false;
    }
    function get_thana_by_territory_id()
    {
        $territory_id = $this->request->data['territory_id'];
        $output = "<option value=''>--- Select Thana ---</option>";
        if ($territory_id) {
            $thana = $this->Thana->find('list', array(
                'conditions' => array('ThanaTerritory.territory_id' => $territory_id),
                'joins' => array(
                    array(
                        'table' => 'thana_territories',
                        'alias' => 'ThanaTerritory',
                        'conditions' => 'ThanaTerritory.thana_id=Thana.id'
                    )
                )
            ));

            if ($thana) {
                foreach ($thana as $key => $data) {
                    $output .= "<option value='$key'>$data</option>";
                }
            }
        }

        echo $output;
        $this->autoRender = false;
    }
    function get_market_by_thana_id()
    {
        $thana_id = $this->request->data['thana_id'];
        $output = "<option value=''>--- Select Market ---</option>";
        if ($thana_id) {
            $market = $this->Market->find('list', array(
                'conditions' => array('Market.thana_id' => $thana_id)
            ));
            if ($market) {
                foreach ($market as $key => $data) {
                    $output .= "<option value='$key'>$data</option>";
                }
            }
        }
        echo $output;
        $this->autoRender = false;
    }


    private function outletGroupCheck($outlet_id = 0)
    {
        if ($outlet_id) {
            $this->loadModel('Outlet');
            $result = $this->Outlet->find('first', array(
                'fields' => array('is_within_group'),
                'conditions' => array('Outlet.id' => $outlet_id),
                'recursive' => -1
            ));
            if ($result) {
                return $result['Outlet']['is_within_group'];
            } else {
                return 0;
            }
        }
    }

    private function productInjectableCheck($products_ids = array())
    {
        if ($products_ids) {
            $this->loadModel('Product');

            $result = $this->Product->find('first', array(
                'fields' => array('is_injectable'),
                'conditions' => array(
                    'Product.id' => $products_ids,
                    'Product.is_injectable' => 1
                ),
                'recursive' => -1
            ));
            if ($result) {
                return $result['Product']['is_injectable'];
            } else {
                return 0;
            }
        }
    }



    private function getoutletlist($company_id)
    {

        $this->loadModel('Outlet');
        $this->loadModel('DistDistributor');
        //$rs = array(array('id' => '', 'name' => '---- Select Distributor -----'));
        $rs = array();
        //$company_id = $this->request->data['company_id'];
        $outlet_list = $this->Outlet->find('all', array(
            'fields' => array('Outlet.id', 'Outlet.name', 'Outlet.distributor_id'),
            'conditions' => array(
                'Outlet.company_id' => $company_id
            ),
            'order' => array('Outlet.id' => 'asc'),
            'recursive' => -1
        ));

        //pr($outlet_list);die();

        //$data_array = Set::extract($outlet_list, '{n}.0');

        $data_array = array();

        foreach ($outlet_list as $key => $value) {
            if ($value['Outlet']['distributor_id'] != null) {

                $name = $this->DistDistributor->find('all', array(
                    'fields' => array('DistDistributor.name'),
                    'conditions' => array(
                        'DistDistributor.id' => $value['Outlet']['distributor_id']
                    ),

                    'recursive' => -1
                ));

                $data_array[] = array(
                    'id' => $value['Outlet']['id'],
                    'name' => $name[0]['DistDistributor']['name'],
                );
            }
        }
        //pr($data_array);die();
        /* if(!empty($outlet_list)){
        echo json_encode(array_merge($rs,$data_array));
    }else{
        echo json_encode($rs);
    } 
    $this->autoRender = false;*/
        return $data_array;
    }

    private function getDistributorListWithNamebyoffice($office_id)
    {

        $this->loadModel('Outlet');
        $this->loadModel('DistDistributor');

        $rs = array();
    }

    private function getDistributorListWithName($company_id)
    {

        $this->loadModel('Outlet');
        $this->loadModel('DistDistributor');
        //$rs = array(array('id' => '', 'name' => '---- Select Distributor -----'));
        $rs = array();
        // $company_id = $this->request->data['company_id'];
        $outlet_list = $this->Outlet->find('all', array(
            'fields' => array('Outlet.id', 'Outlet.name', 'Outlet.distributor_id'),
            'conditions' => array(
                'Outlet.company_id' => $company_id
            ),
            'order' => array('Outlet.id' => 'asc'),
            'recursive' => -1
        ));
        // pr($outlet_list);die();
        $data_array = array();

        foreach ($outlet_list as $key => $value) {
            if ($value['Outlet']['distributor_id'] != null) {

                $name = $this->DistDistributor->find('all', array(
                    'fields' => array('DistDistributor.name'),
                    'conditions' => array(
                        'DistDistributor.id' => $value['Outlet']['distributor_id']
                    ),

                    'recursive' => -1
                ));

                /* $data_array[] = array(
                'id' => $value['Outlet']['id'],
                 'name'=>$name[0]['DistDistributor']['name'],
                );*/

                $data_array[] = array(
                    'id' => $value['Outlet']['id'],
                    'name' => $name[0]['DistDistributor']['name'],
                );
            }
        }

        return $data_array;
    }

    public function get_outlet_list_with_distributor_name($company_id)
    {

        $this->loadModel('Outlet');
        $this->loadModel('DistDistributor');
        $rs = array(array('id' => '', 'name' => '---- Select Distributor -----'));
        //$company_id = $this->request->data['company_id'];
        $outlet_list = $this->Outlet->find('all', array(
            'fields' => array('Outlet.id', 'Outlet.name', 'Outlet.distributor_id'),
            'conditions' => array(
                'Outlet.company_id' => $company_id
            ),
            'order' => array('Outlet.id' => 'asc'),
            'recursive' => -1
        ));

        //pr($outlet_list);die();

        //$data_array = Set::extract($outlet_list, '{n}.0');

        $data_array = array();

        foreach ($outlet_list as $key => $value) {
            if ($value['Outlet']['distributor_id'] != null) {

                $name = $this->DistDistributor->find('all', array(
                    'fields' => array('DistDistributor.name'),
                    'conditions' => array(
                        'DistDistributor.id' => $value['Outlet']['distributor_id']
                    ),

                    'recursive' => -1
                ));

                $data_array[] = array(
                    $value['Outlet']['id'] => $name[0]['DistDistributor']['name'],
                );
            }
        }
        return $data_array;
    }

    function getTSOName($order_date, $distributor_id, $office_id)
    {
        $qry = "select distinct dist_tso_id from dist_tso_mapping_histories
			  where office_id=$office_id and is_change=1 and dist_distributor_id=" . $distributor_id . " and 
				'" . $order_date . "' between effective_date and 
				case 
				when end_date is not null then 
				 end_date
				else 
				getdate()
				end";
        $this->loadModel('DistTsoMappingHistory');
        $dist_data = $this->DistTsoMappingHistory->query($qry);
        //pr($dist_data);exit;
        $dist_ids = array();

        foreach ($dist_data as $k => $v) {
            $dist_ids[] = $v[0]['dist_tso_id'];
        }
        $tso_id = "";
        if ($dist_ids) {
            $tso_id = $dist_ids[0];
        }

        $select = "select name from dist_tsos where id='$tso_id'";
        $tso_name = $this->DistTsoMappingHistory->query($select);
        return $tso_name[0][0]['name'];
    }

    function get_tso_list()
    {
        $office_id = $this->request->data['office_id'];

        $rs = array(array('id' => '', 'name' => '---- Select TSO -----'));

        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        if ($user_group_id == 1029) {
            $dist_tso_conditions = array('DistTso.user_id' => $user_id);
        } elseif ($user_group_id == 1028) {
            $dist_ae_info = $this->DistAreaExecutive->find('first', array(
                'conditions' => array('DistAreaExecutive.user_id' => $user_id),
                'recursive' => -1,
            ));
            $dist_ae_id = $dist_ae_info['DistAreaExecutive']['id'];
            $dist_tso_conditions = array('DistTso.dist_area_executive_id' => $dist_ae_id);
        } elseif ($user_group_id == 1034) {
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');
            $distributor = $this->DistUserMapping->find('first', array(
                'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];

            $tsos_info = $this->DistTsoMapping->find('first', array(
                'conditions' => array(
                    'DistTsoMapping.dist_distributor_id' => $distributor_id
                )
            ));
            $dist_tso_conditions = array('DistTso.id' => $tsos_info['DistTso']['id']);
        } else {
            $dist_tso_conditions = array('DistTso.office_id' => $office_id);
        }

        $dist_tso_info = $this->DistTso->find('all', array(
            'conditions' => $dist_tso_conditions,
        ));
        if ($dist_tso_info) {
            foreach ($dist_tso_info as $key => $data) {
                $data_array[] = array(
                    'id' => $data['DistTso']['id'],
                    'name' => $data['DistTso']['name'],
                );
            }
        }
        if (!empty($data_array)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }


    function get_db_balance()
    {
        $outlet_id = $this->request->data['distributor_id'];
        $this->loadModel('DistOutletMap');
        $this->loadModel('DistDistributorBalance');
        $dist_outlet_info = $this->DistOutletMap->find('first', array(
            'conditions' => array('DistOutletMap.outlet_id' => $outlet_id),
            'recursive' => -1
        ));
        $dist_distributor_id = $dist_outlet_info['DistOutletMap']['dist_distributor_id'];

        $dist_distributor_balances = $this->DistDistributorBalance->find('first', array(
            'conditions' => array('DistDistributorBalance.dist_distributor_id' => $dist_distributor_id),
            'recursive' => -1
        ));
        if (!empty($dist_distributor_balances)) {
            $balance = $dist_distributor_balances['DistDistributorBalance']['balance'];
        } else {
            $balance = 0;
        }
        echo $balance;
        $this->autoRender = false;
    }

    public function get_remarks()
    {
        $min_qty = $this->request->data['min_qty'];
        $product_id = $this->request->data['product_id'];
        $this->loadModel('Product');
        $products_info = $this->Product->find('first', array('conditions' => array('Product.id' => $product_id), 'recursive' => -1));
        $measurement_unit_id = $products_info['Product']['sales_measurement_unit_id'];

        $base_qty = $this->unit_convert($product_id, $measurement_unit_id, $min_qty);
        $cartoon_qty = $this->cartoon_convertfrombase($product_id, 16, $base_qty);

        $cartoon = explode('.', $cartoon_qty);
        $cartoon_qty = $cartoon[0];
        if ($cartoon[1] != '00' && $cartoon[1]) {
            $this->loadModel('MeasurementUnit');
            $meauserment_unit = $this->MeasurementUnit->find('first', array(
                'conditions' => array(
                    'MeasurementUnit.id' => $measurement_unit_id
                ),
                'recursive' => -1
            ));
            $measurement_unit_name = $meauserment_unit['MeasurementUnit']['name'];
            if (strlen($measurement_unit_name) > 4) {
                $measurement_unit_name = substr($measurement_unit_name, 0, 4) . '.';
            }
            $base_qty = $this->unit_convert($product_id, 16, '.' . $cartoon[1]);
            $dispenser = $this->unit_convertfrombase($product_id, $measurement_unit_id, $base_qty);
        }
        $result_data = array();
        $result_data['remarks'] = '';
        if ($cartoon_qty)
            $result_data['remarks'] .= $cartoon_qty . " S/c";
        if (isset($dispenser)) {
            $result_data['remarks'] .= ($result_data['remarks'] ? ', ' : '') . $dispenser . " $measurement_unit_name";
        }
        echo json_encode($result_data);
        $this->autoRender = false;
    }

    public function cartoon_convertfrombase($product_id = '', $measurement_unit_id = '', $qty = '')
    {
        $this->loadModel('ProductMeasurement');
        $unit_info = $this->ProductMeasurement->find('first', array(
            'conditions' => array(
                'ProductMeasurement.product_id' => $product_id,
                'ProductMeasurement.measurement_unit_id' => $measurement_unit_id
            )
        ));
        $number = $qty;
        if (!empty($unit_info)) {
            $number = $qty / $unit_info['ProductMeasurement']['qty_in_base'];
            return $number;
        } else {
            return $number;
        }
    }



    public function get_remarks_product_id_munit_id($product_id, $unit_id, $min_qty)
    {
        $this->loadModel('Product');
        $products_info = $this->Product->find('first', array('conditions' => array('Product.id' => $product_id), 'recursive' => -1));
        $measurement_unit_id = $products_info['Product']['sales_measurement_unit_id'];
        $base_measurement_unit_id = $products_info['Product']['base_measurement_unit_id'];

        $base_qty = $this->convert_unit_to_unit($product_id, $unit_id, $base_measurement_unit_id, $min_qty);
        $cartoon_qty = $this->unit_convertfrombase($product_id, 16, $base_qty);

        $cartoon = explode('.', $cartoon_qty);
        $cartoon_qty = $cartoon[0];
        if ($cartoon_qty <= 0) {
            $this->loadModel('MeasurementUnit');
            $meauserment_unit = $this->MeasurementUnit->find('first', array(
                'conditions' => array(
                    'MeasurementUnit.id' => $measurement_unit_id
                ),
                'recursive' => -1
            ));
            $measurement_unit_name = $meauserment_unit['MeasurementUnit']['name'];
            if (strlen($measurement_unit_name) > 4) {
                $measurement_unit_name = substr($measurement_unit_name, 0, 4) . '.';
            }
            $dispenser = $this->unit_convertfrombase($product_id, $measurement_unit_id, $base_qty);
        } else {
            if ($cartoon[1] != '00' && $cartoon[1]) {
                $this->loadModel('MeasurementUnit');
                $meauserment_unit = $this->MeasurementUnit->find('first', array(
                    'conditions' => array(
                        'MeasurementUnit.id' => $measurement_unit_id
                    ),
                    'recursive' => -1
                ));
                $measurement_unit_name = $meauserment_unit['MeasurementUnit']['name'];
                if (strlen($measurement_unit_name) > 4) {
                    $measurement_unit_name = substr($measurement_unit_name, 0, 4) . '.';
                }
                $base_qty = $this->unit_convert($product_id, 16, '.' . $cartoon[1]);
                $dispenser = $this->unit_convertfrombase($product_id, $measurement_unit_id, $base_qty);
            }
        }

        $result_data = array();
        $result_data['remarks'] = '';
        if ($cartoon_qty)
            $result_data['remarks'] .= $cartoon_qty . " S/c";
        if (isset($dispenser)) {
            $result_data['remarks'] .= ($result_data['remarks'] ? ', ' : '') . $dispenser . " $measurement_unit_name";
        }
        $this->autoRender = false;
        return $result_data['remarks'];
    }
}
