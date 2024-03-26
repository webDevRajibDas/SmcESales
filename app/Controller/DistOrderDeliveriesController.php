<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'php-excel-reader/excel_reader2');

/**
 * DistOrders Controller
 *
 * @property Order $DistOrder
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistOrderDeliveriesController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $uses = array('DistOrder', 'DistDistributor', 'DistSalesRepresentative', 'Thana', 'SalesPerson', 'DistMarket', 'DistSalesRepresentative', 'DistOutlet', 'Product', 'MeasurementUnit', 'DistSrProductPrice', 'DistSrProductCombination', 'DistSrCombination', 'DistOrderDetail', 'MeasurementUnit', 'DistRoute', 'DistTsoMappingHistory');
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index()
    {

        //$this->Session->delete('from_outlet');
        //$this->Session->delete('from_market');

        $product_wise_sales_bonus_report = array();
        $product_list = array();
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        //$company_id = $this->Session->read('Office.company_id');
        //$user_group_id = $this->Session->read('Office.group_id');
        //pr($this->Session->read());die();
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
            //'conditions' => array('Product.company_id'=>$company_id),
            'order' => 'Product.product_category_id',
            'recursive' => -1
        ));
        //$status_list = array(0=>'Draft', 1 =>' Pending',2 =>'Invoice Created', 3 => 'Cancel',4=>'Delivered');
        $status_list = array(0 => 'Draft', 4 => 'Delivered');
        $requested_data = $this->request->data;
        //pr($requested_data);die();
        $this->set('page_title', 'SR Order List');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $this->set('office_parent_id', $office_parent_id);



        if ($office_parent_id == 0) {
            //if ($user_group_id == 1) {
            $conditions = array('DistOrder.order_date >=' => $this->current_date() . ' 00:00:00', 'DistOrder.order_date <=' => $this->current_date() . ' 23:59:59');
            $office_conditions = array(
                'office_type_id' => 2,
                "NOT" => array("id" => array(30, 31, 37))
            );
            $tsos = $this->DistTso->find('list', array(
                'conditions' => array('is_active' => 1),
                'fields' => array('DistTso.id', 'DistTso.name'),
            ));
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
                    $tsos = $dist_tso_info;
                    $dist_tso_id = array_keys($dist_tso_info);
                } else {
                    $dist_tso_info = $this->DistTso->find('first', array(
                        'conditions' => array('DistTso.user_id' => $user_id),
                        'fields' => array('DistTso.id', 'DistTso.name'),
                        'recursive' => -1,
                    ));
                    $dist_tso_id = $dist_tso_info['DistTso']['id'];

                    $tsos[$dist_tso_info['DistTso']['id']] = $dist_tso_info['DistTso']['name'];
                }


                $conditions = array('DistOrder.tso_id' => $dist_tso_id, 'DistOrder.order_date >=' => $this->current_date() . ' 00:00:00', 'DistOrder.order_date <=' => $this->current_date() . ' 23:59:59');
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
                $conditions = array('DistOrder.distributor_id' => $distributor_id, 'DistOrder.order_date >=' => $this->current_date() . ' 00:00:00', 'DistOrder.order_date <=' => $this->current_date() . ' 23:59:59');
            } else {
                $tsos = $this->DistTso->find('list', array(
                    'conditions' => array(
                        'office_id' => $this->UserAuth->getOfficeId(),
                        'is_active' => 1
                    ),
                ));
                $conditions = array('DistOrder.office_id' => $this->UserAuth->getOfficeId(), 'DistOrder.order_date >=' => $this->current_date() . ' 00:00:00', 'DistOrder.order_date <=' => $this->current_date() . ' 23:59:59');
            }

            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }
        $this->set(compact('tsos'));

        $conditions['DistOrder.order_type'] = 1;
        $conditions['DistOrder.from_app'] = 0;


        /*********************** Check Product Wise Sales Bonus Report Start ***********************/
        if ($this->request->is('post')) {

            if (isset($this->request->data)  && array_key_exists('sr_wise_sales_bonus_report', $this->request->data['DistOrder']) && ($this->request->data['DistOrder']['sr_wise_sales_bonus_report'] == 1)) {
                $params = $this->request->data;
                if (!empty($params['DistOrder']['office_id'])) {
                } else {

                    if ($office_parent_id == 0) {
                    } else {

                        $this->request->data['DistOrder']['office_id'] = $this->UserAuth->getOfficeId();
                    }
                }


                $sr_wise_sales_bonus_report = $this->DistOrder->find('all', array(
                    /*'conditions'=>$report_conditions,*/
                    'joins' => array(
                        array(
                            'table'         => 'dist_order_details',
                            'alias'         => 'DistOrderDetail',
                            'type'          => 'inner',
                            'conditions'    => 'DistOrder.id=DistOrderDetail.dist_order_id'
                        ),
                        array(
                            'table'         => 'products',
                            'alias'         => 'Product',
                            'type'          => 'inner',
                            'conditions'    => 'DistOrderDetail.product_id=Product.id'
                        )
                    ),
                    'fields' => array('DistOrder.distributor_id', 'DistOrder.sr_id', 'Product.name', 'COUNT(DISTINCT(DistOrder.outlet_id)) as no_outlet', 'COUNT(DISTINCT(DistOrder.id)) as no_Order', 'SUM(CASE WHEN price>0 THEN DistOrderDetail.sales_qty END) as sales_qty', 'SUM(CASE WHEN price=0 THEN DistOrderDetail.sales_qty END) as bonus_qty', 'SUM(DistOrderDetail.price * DistOrderDetail.sales_qty) as revenue'),
                    'group' => array('DistOrder.distributor_id', 'DistOrder.sr_id', 'DistOrderDetail.product_id', 'Product.name', 'Product.order'),
                    'ORDER' => array('DistOrder.distributor_id ASC', 'DistOrder.sr_id ASC', 'Product.order ASC'),
                    'recursive' => -1
                ));
            }



            /*********************** Check Product Wise Sales Bonus Report End ***********************/
            if (isset($this->request->data) && array_key_exists('p_wise_sales_bonus_report', $this->request->data['DistOrder']) && ($this->request->data['DistOrder']['p_wise_sales_bonus_report'] == 1)) {
                $params = $this->request->data;
                if (!empty($params['DistOrder']['office_id'])) {
                } else {

                    if ($office_parent_id == 0) {
                    } else {

                        $this->request->data['DistOrder']['office_id'] = $this->UserAuth->getOfficeId();
                    }
                }
                $p_wise_sales_bonus_report = $this->DistOrder->find('all', array(
                    /*'conditions'=>$report_conditions,*/
                    'joins' => array(
                        array(
                            'table'         => 'dist_order_details',
                            'alias'         => 'DistOrderDetail',
                            'type'          => 'inner',
                            'conditions'    => 'DistOrder.id=DistOrderDetail.dist_order_id'
                        ),
                        array(
                            'table'         => 'products',
                            'alias'         => 'Product',
                            'type'          => 'inner',
                            'conditions'    => 'DistOrderDetail.product_id=Product.id'
                        )
                    ),
                    'fields' => array('DistOrderDetail.product_id', 'Product.name', 'DistOrder.distributor_id', 'DistOrder.sr_id', 'DistOrder.market_id', 'DistOrder.outlet_id', 'DistOrder.thana_id', 'DistOrder.order_reference_no', 'DistOrder.order_date', 'SUM(CASE WHEN price>0 THEN DistOrderDetail.sales_qty END) as sales_qty', 'SUM(CASE WHEN price=0 THEN DistOrderDetail.sales_qty END) as bonus_qty', 'SUM(DistOrderDetail.price * DistOrderDetail.sales_qty) as revenue'),
                    'group' => array('DistOrderDetail.product_id', 'Product.name', 'Product.order', 'DistOrder.distributor_id', 'DistOrder.sr_id', 'DistOrder.market_id', 'DistOrder.outlet_id', 'DistOrder.thana_id', 'DistOrder.order_reference_no', 'DistOrder.order_date'),
                    'order' => array('Product.order ASC', 'DistOrder.distributor_id ASC', 'DistOrder.sr_id ASC', 'DistOrder.order_date ASC'),
                    'recursive' => -1
                ));
            }
        } else {

            // $this->DistCurrentInventory->virtualFields = array(
			
            //     'office_order' => 'Office.order',
                
            // );
            $this->DistOrder->recursive = 0;
            $this->paginate = array(
                'fields' => array('DistOrder.*', 'DistOutlet.name', 'DistMarket.name', 'DistDistributor.name', 'DistRoute.name','DistTso.name','Office.office_name','DistAE.name'),
                'conditions' => $conditions,
                'joins' => array(
                    array(
                        'alias' => 'DistOutlet',
                        'table' => 'dist_outlets',
                        'type' => 'INNER',
                        'conditions' => 'DistOutlet.id = DistOrder.outlet_id'
                    ),
                    array(
                        'alias' => 'DistMarket',
                        'table' => 'dist_markets',
                        'type' => 'INNER',
                        'conditions' => 'DistMarket.id = DistOrder.market_id'
                    ),
                    array(
                        'alias' => 'DistDistributor',
                        'table' => 'dist_distributors',
                        'type' => 'INNER',
                        'conditions' => 'DistDistributor.id = DistOrder.distributor_id'
                    ),
                    array(
                        'alias' => 'DistRoute',
                        'table' => 'dist_routes',
                        'type' => 'INNER',
                        'conditions' => 'DistRoute.id = DistOrder.dist_route_id'
                    ),
                    /*array(
                        'alias' => 'DistSalesRepresentative',
                        'table' => 'dist_sales_representatives',
                        'type' => 'INNER',
                        'conditions' => 'DistSalesRepresentative.id = DistOrder.sr_id'
                    ),*/
                    array(
                        'table'=>'dist_tso_mappings',
                        'alias'=>'DistTsoMapping',
                        'type' => 'LEFT',
                        'conditions' => 'DistTsoMapping.dist_distributor_id = DistOrder.distributor_id'
                        
                    ),
                    array(
                        'table'=>'dist_tsos',
                        'alias'=>'DistTso',
                        'type' => 'LEFT',
                        'conditions' => 'DistTso.id = DistTsoMapping.dist_tso_id'
                        
                    ),
                    array(
                        'table'=>'dist_area_executives',
                        'alias'=>'DistAE',
                        'type' => 'LEFT',
                        'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'
                        
                    ),

                ),

                'order' => array('DistOrder.id' => 'desc'),

                'limit' => 100
            );
            $this->set('dist_Orders', $this->paginate());
        }
        //pr($this->paginate());die();
        // pr($conditions);
        // echo $this->DistOrder->getLastquery();
        // pr($p_wise_sales_bonus_report);exit;
        $orders = $this->paginate();
        // echo $this->DistOrder->getLastquery().'<br>';
        // pr($orders);exit;
        $sr_lists = array();
        $sr_names = array();
        //pr($orders);die();
        $total_order_value_find = $this->DistOrder->find('all', array(
            'fields' => array('SUM(DistOrder.gross_value) as total_order'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'alias' => 'DistOutlet',
                    'table' => 'dist_outlets',
                    'type' => 'INNER',
                    'conditions' => 'DistOutlet.id = DistOrder.outlet_id'
                ),
                array(
                    'alias' => 'DistMarket',
                    'table' => 'dist_markets',
                    'type' => 'INNER',
                    'conditions' => 'DistMarket.id = DistOrder.market_id'
                ),
                array(
                    'alias' => 'DistDistributor',
                    'table' => 'dist_distributors',
                    'type' => 'INNER',
                    'conditions' => 'DistDistributor.id = DistOrder.distributor_id'
                )
            ),
            'reccursive' => -1
        ));
        // echo $this->DistOrder->getLastquery();exit;
        $total_value_of_order = $total_order_value_find[0][0]['total_order'];
        if ($orders) {

            foreach ($orders as $key => $value) {
                $sr_lists[$key] = $value['DistOrder']['sr_id'];
                // $total_value_of_order = $total_value_of_order + $value['DistOrder']['gross_value'];
            }
            $count_sr = count($sr_lists);
            $this->loadmodel('DistSalesRepresentative');
            if ($count_sr == 1) {
                $sr_info = $this->DistSalesRepresentative->find('all', array(
                    'conditions' => array('DistSalesRepresentative.id' => $sr_lists),
                    'recursive' => -1,
                ));
            } else {
                $sr_info = $this->DistSalesRepresentative->find('all', array(
                    'conditions' => array('DistSalesRepresentative.id IN' => $sr_lists),
                    'recursive' => -1,
                ));
            }
            foreach ($sr_info as $key => $value) {
                $sr_names[$value['DistSalesRepresentative']['id']] = $value['DistSalesRepresentative']['name'];
            }
        }
        $this->set('office_id', $this->UserAuth->getOfficeId());
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        $office_id = isset($this->request->data['DistOrder']['office_id']) != '' ? $this->request->data['DistOrder']['office_id'] : 0;
        $distributor_id = isset($this->request->data['DistOrder']['distributor_id']) != '' ? $this->request->data['DistOrder']['distributor_id'] : 0;

        $distributors = array();


        if ($office_id) {
            $order_date_from = $this->request->data['DistOrder']['date_from'];
            $order_date_to = $this->request->data['DistOrder']['date_to'];

            if ($order_date_from && $office_id && $order_date_to) {
                $order_date_from = date("Y-m-d", strtotime($order_date_from));
                $order_date_to = date("Y-m-d", strtotime($order_date_to));


                $distDistributors_raw = $this->DistDistributor->find('list', array(
                    'conditions' => array('DistOrder.office_id' => $office_id, 'DistOrder.order_date >=' => $order_date_from, 'DistOrder.order_date <=' => $order_date_to, 'DistDistributor.is_active' => 1),
                    'joins' => array(array('table' => 'dist_Orders', 'alias' => 'DistOrder', 'conditions' => 'DistOrder.distributor_id=DistDistributor.id')),
                    'order' => array('DistDistributor.name' => 'asc'),
                ));


                if ($distDistributors_raw) {
                    foreach ($distDistributors_raw as $key => $data) {
                        $distributors[$key] = $data;
                    }
                }
            }
        }

        $territory_id = isset($this->request->data['DistOrder']['territory_id']) != '' ? $this->request->data['DistOrder']['territory_id'] : 0;
        $market_id = isset($this->request->data['DistOrder']['market_id']) != '' ? $this->request->data['DistOrder']['market_id'] : 0;


        $sr_id = isset($this->request->data['DistOrder']['sr_id']) != '' ? $this->request->data['DistOrder']['sr_id'] : 0;
        $dist_route_id = isset($this->request->data['DistOrder']['dist_route_id']) != '' ? $this->request->data['DistOrder']['dist_route_id'] : 0;
        $outlet_id = isset($this->request->data['DistOrder']['outlet_id']) != '' ? $this->request->data['DistOrder']['outlet_id'] : 0;
        $order_reference_no = isset($this->request->data['DistOrder']['order_reference_no']) != '' ? $this->request->data['DistOrder']['order_reference_no'] : 0;
        $tso_id = isset($this->request->data['DistOrder']['tso_id']) != '' ? $this->request->data['DistOrder']['tso_id'] : 0;
        //pr($this->request->data);die();
        $srs = array();
        $routes = array();
        $this->loadModel('DistRoute');
        if ($sr_id || $distributor_id) {
            $order_date_from = $this->request->data['DistOrder']['date_from'];
            $order_date_to = $this->request->data['DistOrder']['date_to'];

            $order_date_from = date("Y-m-d", strtotime($order_date_from));
            $order_date_to = date("Y-m-d", strtotime($order_date_to));

            $sr_raw = $this->DistSalesRepresentative->find('list', array(
                'conditions' => array('DistSalesRepresentative.dist_distributor_id' => $distributor_id, 'DistSalesRepresentative.is_active' => 1), 'order' => array('DistSalesRepresentative.name' => 'asc')
            ));

            if ($sr_raw) {
                foreach ($sr_raw as $key => $data) {
                    $srs[$key] = $data;
                }
            }

            $dist_routes = $this->DistRouteMapping->find('list', array(
                'conditions' => array('DistRouteMapping.dist_distributor_id' => $distributor_id),
                'fields' => array('DistRouteMapping.dist_route_id', 'DistRouteMapping.dist_distributor_id')
            ));

            $routes = $this->DistRoute->find('list', array('conditions' => array('DistRoute.id' => array_keys($dist_routes))));
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



        if ($territory_id) {
            $markets = $this->DistOrder->Market->find('list', array(
                'conditions' => array('Market.territory_id' => $territory_id),
                'order' => array('Market.name' => 'asc')
            ));
        } else {
            $markets = array();
        }
        $this->loadModel('DistMarket');
        if ($dist_route_id) {
            $markets = $this->DistMarket->find('list', array(
                'conditions' => array('DistMarket.dist_route_id' => $dist_route_id),
                'order' => array('DistMarket.name' => 'asc')
            ));
        }
        /*$outlets = $this->DistOrder->Outlet->find('list', array(
            'conditions' => array('Outlet.dist_market_id' => $market_id),
            'order' => array('Outlet.name' => 'asc')
        ));*/
        $this->loadModel('DistOutlet');
        $outlets = $this->DistOutlet->find('list', array(
            'conditions' => array('DistOutlet.dist_market_id' => $market_id),
            'order' => array('DistOutlet.name' => 'asc')
        ));
        $current_date = date('d-m-Y', strtotime($this->current_date()));
        /*
         * Report generation query start ;
         */
        if (!empty($requested_data)) {
            if (!empty($requested_data['DistOutlet']['office_id'])) {
                $office_id = $requested_data['DistOutlet']['office_id'];
                $this->DistOutlet->recursive = -1;
                $sales_people = array();
            }
        }

        $this->set(compact('offices', 'distributors', 'distributor_id', 'srs', 'sr_id', 'territories', 'markets', 'outlets', 'current_date', 'requested_data', 'product_name', 'sr_wise_sales_bonus_report', 'product_list', 'p_wise_sales_bonus_report', 'sr_names', 'status_list', 'routes', 'tso_id', 'total_value_of_order'));
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
        $this->set('page_title', 'SR Order Details');

        $this->DistOrder->unbindModel(array('hasMany' => array('DistOrderDetail')));
        $Order = $this->DistOrder->find('first', array(
            'conditions' => array('DistOrder.id' => $id),
            'joins' => array(
                array(
                    'alias' => 'DistOutlet',
                    'table' => 'dist_outlets',
                    'type' => 'INNER',
                    'conditions' => 'DistOutlet.id = DistOrder.outlet_id'
                ),
                array(
                    'alias' => 'DistMarket',
                    'table' => 'dist_markets',
                    'type' => 'INNER',
                    'conditions' => 'DistMarket.id = DistOrder.market_id'
                ),
                array(
                    'alias' => 'DistDistributor',
                    'table' => 'dist_distributors',
                    'type' => 'INNER',
                    'conditions' => 'DistDistributor.id = DistOrder.distributor_id'
                ),

            ),
            'fields' => array('DistOrder.*', 'DistMarket.name', 'DistOutlet.name', 'DistDistributor.name',),
        ));

        $sr_id = $Order['DistOrder']['sr_id'];
        $this->loadmodel('DistSalesRepresentative');
        $sr_info = $this->DistSalesRepresentative->find('first', array(
            'conditions' => array('DistSalesRepresentative.id' => $sr_id),
            'fields' => array('DistSalesRepresentative.name'),
            'recursive' => -1,
        ));
        $Order['DistSalesRepresentative']['name'] = $sr_info['DistSalesRepresentative']['name'];

        $this->loadModel('DistOrderDetail');
        if (!$this->DistOrder->exists($id)) {
            throw new NotFoundException(__('Invalid district'));
        }
        $this->DistOrderDetail->recursive = -1;
        $order_details = $this->DistOrderDetail->find(
            'all',
            array(
                'conditions' => array('DistOrderDetail.dist_order_id' => $id),
                'joins' => array(
                    array(
                        'table'         => 'products',
                        'alias'         => 'Product',
                        'type'          => 'inner',
                        'conditions'    => 'DistOrderDetail.product_id=Product.id'
                    )
                ),
                'order' => array('Product.order' => 'asc'),
                'fields' => array('DistOrderDetail.*', 'Product.name'),
            )
        );

        $this->set(compact('Order', 'order_details'));
    }

    /**
     * admin_delete method
     *
     * @return void
     */
    public function admin_delete($id = null, $redirect = 1)
    {

        $this->loadModel('Product');
        $this->loadModel('DistOrder');
        $this->loadModel('DistOrderDetail');



        if ($this->request->is('post')) {


            /*
             * This condition added for data synchronization 
             * Cteated by imrul in 09, April 2017
             * Duplicate Order check
             */
            $count = $this->DistOrder->find('count', array(
                'conditions' => array(
                    'DistOrder.id' => $id
                )
            ));

            $order_id_arr = $this->DistOrder->find('first', array(
                'conditions' => array(
                    'DistOrder.id' => $id
                )
            ));

            $this->loadModel('DistStore');
            $store_id_arr = $this->DistStore->find('first', array(
                'conditions' => array(
                    'DistStore.dist_distributor_id' => $order_id_arr['DistOrder']['distributor_id']
                )
            ));

            $store_id = $store_id_arr['DistStore']['id'];



            $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
            $product_list = Set::extract($products, '{n}.Product');



            for ($order_detail_count = 0; $order_detail_count < count($order_id_arr['DistOrderDetail']); $order_detail_count++) {
                $product_id = $order_id_arr['DistOrderDetail'][$order_detail_count]['product_id'];
                $sales_qty = $order_id_arr['DistOrderDetail'][$order_detail_count]['sales_qty'];
                $sales_price = $order_id_arr['DistOrderDetail'][$order_detail_count]['price'];
                $order_territory_id = $order_id_arr['DistOrder']['territory_id'];
                $order_no = $order_id_arr['DistOrder']['dist_order_no'];
                $order_date = $order_id_arr['DistOrder']['order_date'];
                $outlet_id = $order_id_arr['DistOrder']['outlet_id'];
                $market_id = $order_id_arr['DistOrder']['market_id'];

                $punits_pre = $this->search_array($product_id, 'id', $product_list);
                if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {

                    if ($sales_price == 0) {
                        $base_quantity = 0;
                        $booking_bonus_qty = $sales_qty;
                    } else {
                        $base_quantity = $sales_qty;
                        $booking_bonus_qty = 0;
                    }
                } else {
                    if ($sales_price == 0) {
                        $base_quantity = 0;
                        $booking_bonus_qty = $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
                    } else {
                        $base_quantity =  $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
                        $booking_bonus_qty = 0;
                    }
                }

                if ($order_id_arr['DistOrder']['status'] == 1) {
                    $update_type = 'deduct';
                    $this->dist_booking_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $order_id_arr['DistOrder']['order_date'], $booking_bonus_qty);
                }


                // subract sales achievement and stamp achievemt 
                // sales calculation
                $t_price = $sales_qty * $sales_price;
            }

            $order_id = $order_id_arr['DistOrder']['id'];
            $order_no = $order_id_arr['DistOrder']['dist_order_no'];
            $this->DistOrder->id = $order_id;
            $this->DistOrderDetail->deleteAll(array('DistOrderDetail.dist_order_id' => $order_id));
            $this->DistOrder->delete();

            if ($redirect == 1) {
                $this->flash(__('Distributor Order was not deleted'), array('action' => 'index'));
                $this->redirect(array('action' => 'index'));
            } else {
            }
        }
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_create_order()
    {

        $distributors = array();
        $pre_srs = array();
        $pre_routes = array();
        $pre_markets = array();
        $pre_outlets = array();
        //pr($pre_data);die();
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');


        $office_parent_id = $this->UserAuth->getOfficeParentId();
        date_default_timezone_set('Asia/Dhaka');
        $this->set('page_title', 'Create SR Order');
        $this->loadModel('DistOrderDetail');

        /* ------ unset cart data ------- */
        $last_order = $this->Session->read('last_order');
        $pre_submit_data = array();

        if (!empty($last_order)) {
            $pre_submit_data = $last_order;
            $this->Session->delete('last_order');
        }
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');


        $user_id = $this->UserAuth->getUserId();

        $generate_order_no = 'O' . $user_id . date('d') . date('m') . date('h') . date('i') . date('s');
        $this->set(compact('generate_order_no'));

        /****** tso and ae list start  *****/

        $this->loadModel('DistTso');
        if ($office_parent_id == 0) {
            $tso_conditions = array();
            $ae_conditions = array();
        } else {
            $tso_conditions = array('DistTso.office_id' => $this->Session->read('Office.id'));
            $ae_conditions = array('DistAreaExecutive.office_id' => $this->Session->read('Office.id'));
        }

        $tso_list = $this->DistTso->find('list', array('conditions' => $tso_conditions, 'order' => array('DistTso.name' => 'asc'), 'recursive' => -1));

        $this->loadModel('DistAreaExecutive');
        $ae_list = $this->DistAreaExecutive->find('list', array('order' => array('DistAreaExecutive.name' => 'asc'), 'recursive' => -1));
        $this->set(compact('tso_list', 'ae_list'));
        /****** tso and ae list end  *****/
        /* pr($tso_list);
        pr($ae_list);die();*/

        $dist = 1;
        if ($dist == 1) {

            /* ------ start code of sale type list ------ */
            $sale_type_list = array(
                10 => 'Distributor Sales'
            );

            /* ------ end code of sale type list ------ */
        }

        $this->set('dist', $dist);

        $this->loadModel('Product');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        //if ($user_group_id == 1) {
        if ($office_parent_id == 0) {
            $office_conditions = array(
                'office_type_id' => 2,
                "NOT" => array("id" => array(30, 31, 37))
            );
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }


        $this->set('office_id', $this->UserAuth->getOfficeId());
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));


        $office_id = 0;
        $territory_id = 0;
        $thana_id = 0;
        $market_id = 0;
        $distributor_id = 0;
        $dist_route_id = 0;
        $outlets = array();
        //pr($this->request->data);die();
        if ($this->request->is('post')) {
            //pr($this->request->data);die();
            $office_id = isset($this->request->data['DistOrder']['office_id']) != '' ? $this->request->data['DistOrder']['office_id'] : 0;
            $territory_id = isset($this->request->data['DistOrder']['territory_id']) != '' ? $this->request->data['DistOrder']['territory_id'] : 0;
            $market_id = isset($this->request->data['DistOrder']['market_id']) != '' ? $this->request->data['DistOrder']['market_id'] : '';
            $distributor_id = isset($this->request->data['DistOrder']['distributor_id']) != '' ? $this->request->data['DistOrder']['distributor_id'] : 0;
            $dist_route_id = isset($this->request->data['DistOrder']['dist_route_id']) != '' ? $this->request->data['DistOrder']['dist_route_id'] : 0;

            $this->loadModel('DistStore');
            $this->DistStore->recursive = -1;
            $store_info = $this->DistStore->find('first', array(
                'conditions' => array('dist_distributor_id' => $distributor_id)
            ));
            $store_id = $store_info['DistStore']['id'];
        } else if ($office_parent_id == 0) {
            reset($offices);
            $office_id = key($offices);
            $product_id = '';
        }


        $this->loadModel('Territory');

        $distRoutes = $this->DistRoute->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));


        $territories_list = $this->Territory->find('all', array(
            'conditions' => array(
                'Territory.office_id' => $office_id, //'User.user_group_id !=' => 1008
            ),
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'SalesPerson.id = User.sales_person_id'
                )
            ),
            'order' => array('Territory.name' => 'asc'),
            'recursive' => 0
        ));


        // pr($distRoutes);pr($territories_list);die();
        $territories = array();
        foreach ($territories_list as $t_result) {
            $territories[$t_result['Territory']['id']] = $t_result['Territory']['name'] . ' (' . $t_result['SalesPerson']['name'] . ')';
        }

        $spo_territories = $this->Territory->find('list', array(
            'conditions' => array(
                'Territory.office_id' => $office_id, //'User.user_group_id' => 1008
            ),
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'SalesPerson.id = User.sales_person_id'
                )
            ),
            'order' => array('Territory.name' => 'asc'),
            'recursive' => 0
        ));

        $this->set(compact('spo_territories'));


        $markets = $this->DistMarket->find('list', array(
            'conditions' => array('territory_id' => $territory_id, 'dist_route_id' => $dist_route_id),
            'order' => array('name' => 'asc')
        ));

        if ($market_id) {
            $outlets = $this->DistOutlet->find('list', array(
                'conditions' => array('dist_market_id' => $market_id, 'dist_route_id' => $dist_route_id),
                'order' => array('name' => 'asc')
            ));
        }


        $current_date = date('d-m-Y', strtotime($this->current_date()));




        /* ----- start code of product list ----- */
        //$product_list = $this->Product->find('list', array('conditions'=>array('company_id'=>$company_id),'order' => array('order' => 'asc')));
        $product_list = $this->Product->find('list', array('order' => array('order' => 'asc')));
        // pr($product_list);die();
        /* ----- start code of product list ----- */

        if ($this->request->is('post')) {
            $sale_type_id = $this->request->data['DistOrder']['sale_type_id'];

            /*pr($this->request->data); 
            exit;*/

            $outlet_is_within_group = $this->outletGroupCheck($this->request->data['DistOrder']['outlet_id']);
            $product_is_injectable = $this->productInjectableCheck($this->request->data['OrderDetail']['product_id']);

            if ($product_is_injectable && !$outlet_is_within_group && $sale_type_id != 10) {
                $this->Session->setFlash(__("Special product can't sell to ordinary outlet!"), 'flash/error');
                $this->redirect(array('action' => 'create_Order'));
                exit;
            }


            $ae_id = $this->request->data['DistOrder']['ae_id'];
            $tso_id = $this->request->data['DistOrder']['tso_id'];

            if (!$ae_id && !$tso_id) {
                $this->Session->setFlash(__("Area Executive and TSO are not mapped properly. Please map first!"), 'flash/error');
                $this->redirect(array('action' => 'create_Order'));
                exit;
            } else if (!$ae_id) {
                $this->Session->setFlash(__("Area Executive is not mapped properly. Please map first!"), 'flash/error');
                $this->redirect(array('action' => 'create_Order'));
                exit;
            } else if (!$tso_id) {
                $this->Session->setFlash(__("TSO is not mapped properly. Please map first!"), 'flash/error');
                $this->redirect(array('action' => 'create_Order'));
                exit;
            }



            $outlet_info = $this->DistOutlet->find('first', array(
                'conditions' => array('DistOutlet.id' => $this->request->data['DistOrder']['outlet_id']),
                'recursive' => -1
            ));

            $is_dist_outlet = 1;

            $this->loadModel('DistOrder');
            $order_no = $this->request->data['DistOrder']['order_no'];


            if ($sale_type_id == 10) {
                $order_count = $this->DistOrder->find('count', array(
                    'conditions' => array('DistOrder.dist_order_no' => $order_no),
                    'fields' => array('dist_order_no'),
                    'recursive' => -1
                ));
            }

            $pre_submit_data = array();
            if ($order_count == 0) {
                if ($this->request->data['DistOrder']) {
                    // pr($this->request->data);exit;
                    /* START ADD NEW */
                    //get office id 
                    $office_id = $this->request->data['DistOrder']['office_id'];

                    //get thana id 
                    $this->loadModel('DistMarket');
                    $market_info = $this->DistMarket->find(
                        'first',
                        array(
                            'conditions' => array('DistMarket.id' => $this->request->data['DistOrder']['market_id']),
                            'fields' => 'DistMarket.thana_id',
                            'order' => array('DistMarket.id' => 'asc'),
                            'recursive' => -1,
                            //'limit' => 100
                        )
                    );
                    $thana_id = $market_info['DistMarket']['thana_id'];
                    /* END ADD NEW */

                    $sale_type_id = $this->request->data['DistOrder']['sale_type_id'];

                    if ($sale_type_id == 10) {
                        $this->loadModel('DistOrder');
                        $this->loadModel('DistOrderDetail');

                        $this->request->data['DistOrder']['is_active'] = 1;
                        //$this->request->data['Order']['status'] = ($this->request->data['Order']['credit_amount'] != 0) ? 1 : 2;
                        if (array_key_exists('draft', $this->request->data)) {
                            $this->request->data['DistOrder']['status'] = 0;
                            $message = "Distributor Order Has Been Saved as Draft";
                            //$message = "Distributor Order Has Been Saved";
                        } else {
                            $message = "Distributor Order Has Been Saved";
                            $this->request->data['DistOrder']['status'] = 1;
                        }

                        $sales_person = $this->SalesPerson->find('list', array(
                            'conditions' => array('territory_id' => $this->request->data['DistOrder']['territory_id']),
                            'order' => array('name' => 'asc')
                        ));

                        $sr_id = $this->request->data['DistOrder']['sales_person_id'] = key($sales_person);

                        $this->request->data['DistOrder']['entry_date'] = date('Y-m-d', strtotime($this->request->data['DistOrder']['entry_date']));
                        $this->request->data['DistOrder']['order_date'] = date('Y-m-d', strtotime($this->request->data['DistOrder']['order_date']));

                        $generate_order_no = 'O' . $sr_id . date('d') . date('m') . date('h') . date('i') . date('s');

                        $OrderData['office_id'] = $this->request->data['DistOrder']['office_id'];
                        $OrderData['distributor_id'] = $this->request->data['DistOrder']['distributor_id'];
                        $OrderData['sr_id'] = $this->request->data['DistOrder']['sr_id'];
                        $OrderData['dist_route_id'] = $this->request->data['DistOrder']['dist_route_id'];
                        $OrderData['sale_type_id'] = $this->request->data['DistOrder']['sale_type_id'];
                        $OrderData['territory_id'] = $this->request->data['DistOrder']['territory_id'];
                        $OrderData['market_id'] = $this->request->data['DistOrder']['market_id'];
                        $OrderData['outlet_id'] = $this->request->data['DistOrder']['outlet_id'];
                        $OrderData['entry_date'] = $this->request->data['DistOrder']['entry_date'];
                        $OrderData['order_date'] = $this->request->data['DistOrder']['order_date'];
                        $OrderData['dist_order_no'] = $order_no;
                        $OrderData['gross_value'] = $this->request->data['DistOrder']['gross_value'] - $this->request->data['DistOrder']['total_discount'];
                        $OrderData['cash_recieved'] = $this->request->data['DistOrder']['cash_recieved'];
                        $OrderData['credit_amount'] = $this->request->data['DistOrder']['credit_amount'];
                        $OrderData['is_active'] = $this->request->data['DistOrder']['is_active'];
                        $OrderData['status'] = $this->request->data['DistOrder']['status'];
                        //$OrderData['order_time'] = $this->current_datetime();   
                        $OrderData['order_time'] = $this->request->data['DistOrder']['entry_date'];
                        $OrderData['sales_person_id'] = $this->request->data['DistOrder']['sales_person_id'];
                        $OrderData['from_app'] = 0;
                        $OrderData['action'] = 1;
                        $OrderData['ae_id'] = $this->request->data['DistOrder']['ae_id'];
                        $OrderData['tso_id'] = $this->request->data['DistOrder']['tso_id'];
                        $OrderData['discount_percent'] = $this->request->data['DistOrder']['discount_percent'];
                        $OrderData['discount_value'] = $this->request->data['DistOrder']['discount_value'];
                        $OrderData['total_discount'] = $this->request->data['DistOrder']['total_discount'];
                        $OrderData['order_reference_no'] = $this->request->data['DistOrder']['order_reference_no'];

                        $OrderData['created_at'] = $this->current_datetime();
                        $OrderData['created_by'] = $this->UserAuth->getUserId();
                        $OrderData['updated_at'] = $this->current_datetime();
                        $OrderData['updated_by'] = $this->UserAuth->getUserId();

                        $OrderData['office_id'] = $office_id ? $office_id : 0;
                        $OrderData['thana_id'] = $thana_id ? $thana_id : 0;

                        $OrderData['order_type'] = 1;

                        // pr($this->request->data);//die();
                        if ($this->request->data['DistOrder']['order_date'] >= date('Y-m-d', strtotime('-5 month'))) {
                            $this->DistOrder->create();
                            if ($this->DistOrder->save($OrderData)) {


                                $dist_order_id = $this->DistOrder->getLastInsertId();


                                $order_info_arr = $this->DistOrder->find('first', array(
                                    'conditions' => array(
                                        'DistOrder.id' => $dist_order_id
                                    )
                                ));

                                $this->loadModel('DistStore');

                                $store_id_arr = $this->DistStore->find('first', array(
                                    'conditions' => array(
                                        'DistStore.dist_distributor_id' => $order_info_arr['DistOrder']['distributor_id']
                                    )
                                ));

                                $store_id = $store_id_arr['DistStore']['id'];

                                if ($dist_order_id) {
                                    $all_product_id = $this->request->data['OrderDetail']['product_id'];
                                    if (!empty($this->request->data['OrderDetail'])) {
                                        $total_product_data = array();
                                        $order_details = array();
                                        $order_details['DistOrderDetail']['dist_order_id'] = $dist_order_id;

                                        foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) {
                                            if ($val == NULL || !$this->request->data['OrderDetail']['sales_qty'][$key]) {
                                                continue;
                                            }
                                            $sales_price = $this->request->data['OrderDetail']['Price'][$key];
                                            if ($sales_price > 0.00) {
                                                $product_id = $order_details['DistOrderDetail']['product_id'] = $val;
                                                $order_details['DistOrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
                                                $order_details['DistOrderDetail']['price'] = $sales_price - $this->request->data['OrderDetail']['discount_amount'][$key];
                                                $order_details['DistOrderDetail']['actual_price'] = $sales_price;
                                                $sales_qty = $order_details['DistOrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];

                                                $order_details['DistOrderDetail']['product_price_id'] = $this->request->data['OrderDetail']['product_price_id'][$key];
                                                // $order_details['DistOrderDetail']['product_combination_id'] = $this->request->data['OrderDetail']['combination_id'][$key];

                                                //add new for policy
                                                $order_details['DistOrderDetail']['discount_type'] = $this->request->data['OrderDetail']['disccount_type'][$key];
                                                $order_details['DistOrderDetail']['discount_amount'] = $this->request->data['OrderDetail']['discount_amount'][$key];
                                                $order_details['DistOrderDetail']['policy_type'] = $this->request->data['OrderDetail']['policy_type'][$key];
                                                $order_details['DistOrderDetail']['policy_id'] = $this->request->data['OrderDetail']['policy_id'][$key];
                                                // $order_details['DistOrderDetail']['is_bonus'] = 0;
                                                if ($this->request->data['OrderDetail']['is_bonus'][$key] == 3) $order_details['DistOrderDetail']['is_bonus'] = 3;
                                                else $order_details['DistOrderDetail']['is_bonus'] = $this->request->data['OrderDetail']['is_bonus'][$key];

                                                //Start for bonus
                                                $order_date = date('Y-m-d', strtotime($this->request->data['DistOrder']['order_date']));

                                                $bonus_product_qty = $this->request->data['OrderDetail']['bonus_product_qty'];

                                                if ($this->request->data['OrderDetail']['bonus_product_qty'][$key] > 0) {
                                                    $order_details['DistOrderDetail']['bonus_product_id'] = $this->request->data['OrderDetail']['bonus_product_id'][$key];
                                                    $order_details['DistOrderDetail']['bonus_product_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
                                                    $order_details['DistOrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
                                                } else {
                                                    $order_details['DistOrderDetail']['bonus_product_id'] = '';
                                                    $order_details['DistOrderDetail']['bonus_product_qty'] = '';
                                                    $order_details['DistOrderDetail']['bonus_qty'] = '';
                                                }

                                                $total_product_data[] = $order_details;
                                                /*if ($this->request->data['OrderDetail']['bonus_product_qty'][$key] > 0) {
                                                    $bonus_order_details = array();
                                                    $bonus_order_details['DistOrderDetail']['dist_order_id'] = $dist_order_id;
                                                    $bonus_order_details['DistOrderDetail']['product_id'] = $this->request->data['OrderDetail']['bonus_product_id'][$key];
                                                    $bonus_order_details['DistOrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
                                                    $bonus_order_details['DistOrderDetail']['price'] = 0.0;
                                                    $bonus_order_details['DistOrderDetail']['is_bonus'] = 1;
                                                    $sales_qty = $bonus_order_details['DistOrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];

                                                    $total_product_data[] = $bonus_order_details;

                                                }*/
                                            } else {
                                                $bonus_order_details = array();
                                                $bonus_order_details['DistOrderDetail']['dist_order_id'] = $dist_order_id;
                                                $product_id = $bonus_order_details['DistOrderDetail']['product_id'] = $val;
                                                $bonus_order_details['DistOrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
                                                $bonus_order_details['DistOrderDetail']['price'] = 0.0;
                                                //add new for policy
                                                $bonus_order_details['DistOrderDetail']['discount_type'] = $this->request->data['OrderDetail']['disccount_type'][$key];
                                                $bonus_order_details['DistOrderDetail']['discount_amount'] = $this->request->data['OrderDetail']['discount_amount'][$key];
                                                $bonus_order_details['DistOrderDetail']['policy_type'] = $this->request->data['OrderDetail']['policy_type'][$key];
                                                $bonus_order_details['DistOrderDetail']['policy_id'] = $this->request->data['OrderDetail']['policy_id'][$key];
                                                // $order_details['DistOrderDetail']['is_bonus'] = 0;
                                                if ($this->request->data['OrderDetail']['is_bonus'][$key] == 3) $bonus_order_details['DistOrderDetail']['is_bonus'] = 3;
                                                else $bonus_order_details['DistOrderDetail']['is_bonus'] = $this->request->data['OrderDetail']['is_bonus'][$key];
                                                $selected_set = '';
                                                if (isset($this->request->data['OrderDetail']['selected_set'][$bonus_order_details['DistOrderDetail']['policy_id']])) {
                                                    $selected_set = $this->request->data['OrderDetail']['selected_set'][$bonus_order_details['DistOrderDetail']['policy_id']];
                                                }
                                                $other_info = array();
                                                if ($selected_set) {
                                                    $other_info = array(
                                                        'selected_set' => $selected_set
                                                    );
                                                }
                                                if ($other_info)
                                                    $bonus_order_details['DistOrderDetail']['other_info'] = json_encode($other_info);
                                                $sales_qty = $bonus_order_details['DistOrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];

                                                $total_product_data[] = $bonus_order_details;
                                            }
                                            //update inventory
                                            $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
                                            $product_list = Set::extract($products, '{n}.Product');

                                            $punits_pre = $this->search_array($product_id, 'id', $product_list);
                                            if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                                                //
                                                if ($sales_price == 0) {
                                                    $booking_bonus_qty = $sales_qty;
                                                    $base_quantity = 0;
                                                } else {
                                                    $base_quantity = $sales_qty;
                                                    $booking_bonus_qty = 0;
                                                }
                                            } else {
                                                if ($sales_price == 0) {
                                                    $booking_bonus_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
                                                    $base_quantity = 0;
                                                } else {
                                                    $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
                                                    $booking_bonus_qty = 0;
                                                }
                                            }

                                            $msg = $this->check_current_inventory($product_id, $store_id, $booking_bonus_qty);

                                            // sales calculation
                                            $tt_price = $sales_qty * $sales_price;
                                        }

                                        //exit; 

                                        $this->DistOrderDetail->saveAll($total_product_data);
                                    }
                                }
                            }
                            //pr($msg); die();
                            if ($msg != null) {
                                $this->Session->setFlash(__($msg), 'flash/error');
                            } else {
                                $this->Session->setFlash(__($message), 'flash/success');
                            }


                            $pre_submit_data = $this->request->data['DistOrder'];

                            //$this->redirect(array("controller" => "DistOrderDeliveries", 'action' => 'index'));
                            if ($dist_order_id) {
                                //$this->redirect(array('action' => 'edit', $this->Order->id));
                                $this->redirect(array("controller" => "DistOrderDeliveries", 'action' => 'edit', $dist_order_id));
                            }
                            // exit;
                        } else {
                            $this->Session->setFlash(__('Distributor Order Date Should Not Be less Then 3 Months'), 'flash/error');
                        }
                    }
                }
            } else {
                $this->Session->setFlash(__('Distributor Order number already exist'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            }
        }
        //pr($this->Session->read('from_outlet'));die();
        if (!empty($this->Session->read('from_outlet'))) {
            $from_outlet = $this->Session->read('from_outlet');
            $this->loadModel('DistDistributor');
            $distributors = $this->DistDistributor->find('list', array(
                'conditions' => array('DistDistributor.office_id' => $from_outlet['DistOutlet']['office_id'], 'DistDistributor.is_active' => 1),
            ));

            $this->loadModel('DistSalesRepresentative');
            $srs = $this->DistSalesRepresentative->find('list', array(
                'conditions' => array(
                    'DistSalesRepresentative.office_id' => $from_outlet['DistOutlet']['office_id'],
                    'DistSalesRepresentative.dist_distributor_id' => $from_outlet['DistOutlet']['dist_distributor_id'],
                ),
            ));

            $this->loadModel('DistSalesRepresentative');
            $srs = $this->DistSalesRepresentative->find('list', array(
                'conditions' => array(
                    'DistSalesRepresentative.office_id' => $from_outlet['DistOutlet']['office_id'],
                    'DistSalesRepresentative.dist_distributor_id' => $from_outlet['DistOutlet']['dist_distributor_id'],
                ),
            ));

            $pre_srs = $srs;

            //pr($from_outlet);die();
            $this->loadModel('DistRouteMapping');
            $this->loadModel('DistRoute');
            $office_id = $from_outlet['DistOutlet']['office_id'];
            $sr_id = $from_outlet['DistOutlet']['dist_sales_representative_id'];
            $distributor_id = $from_outlet['DistOutlet']['dist_distributor_id'];
            $dist_route_id = $from_outlet['DistOutlet']['dist_route_id'];
            $market_order_date = date("Y-m-d", strtotime($from_outlet['DistOutlet']['market_order_date']));

            if ($sr_id && $distributor_id && $market_order_date) {
                /*$qry_route="select distinct dist_route_id from dist_sr_route_mapping_histories
                    where dist_sr_id=$sr_id and dist_distributor_id=$distributor_id and is_change=1 and 
                    '".$market_order_date."' between effective_date and 
                    case 
                    when end_date is not null then 
                     end_date
                    else 
                    getdate()
                    end";*/
                $qry_route = "select dist_route_id from dist_sr_route_mappings
                    where dist_sr_id=$sr_id and dist_distributor_id=$distributor_id ";

                $route_data = $this->DistRouteMapping->query($qry_route);
                $route_ids = array();

                foreach ($route_data as $k => $v) {
                    $route_ids[] = $v[0]['dist_route_id'];
                }


                $route = $this->DistRoute->find('all', array(
                    'conditions' => array('DistRoute.id' => $route_ids),
                    'recursive' => 0
                ));

                if ($route) {
                    foreach ($route as $key => $data) {
                        $k = $data['DistRoute']['id'];
                        $v = $data['DistRoute']['name'];
                        $pre_routes[$k] = $v;
                    }
                }
            }
            //$this->loadModel('DistRoute');
            //$distRoutes = $this->DistRoute->find('list', array('conditions' => array('office_id' => $from_outlet['DistOutlet']['office_id']), 'order' => array('name' => 'asc')));


            $this->loadModel('DistMarket');
            $markets = $this->DistMarket->find('list', array('conditions' => array('dist_route_id' => $from_outlet['DistOutlet']['dist_route_id']), 'order' => array('name' => 'asc')));
            $pre_markets = $markets;

            $market_id = $from_outlet['DistOutlet']['dist_market_id'];

            if ($market_id) {
                $info = $this->DistMarket->find('first', array(
                    'conditions' => array('DistMarket.id' => $market_id),
                    'recursive' => -1
                ));

                $territory_id = $info['DistMarket']['territory_id'];
                $thana_id = $info['DistMarket']['thana_id'];

                $this->loadModel('DistTsoMappingHistory');

                $ae_id = "";
                $tso_id = "";


                if ($market_order_date && $distributor_id && $office_id && $market_order_date) {

                    $qry = "select distinct dist_tso_id from dist_tso_mapping_histories
                      where office_id=$office_id and is_change=1 and dist_distributor_id=$distributor_id and 
                        '" . $market_order_date . "' between effective_date and 
                        case 
                        when end_date is not null then 
                         end_date
                        else 
                        getdate()
                        end";

                    $dist_data = $this->DistTsoMappingHistory->query($qry);
                    $dist_ids = array();

                    foreach ($dist_data as $k => $v) {
                        $dist_ids[] = $v[0]['dist_tso_id'];
                    }
                    $tso_id = "";
                    if ($dist_ids) {
                        $tso_id = $dist_ids[0];
                    }


                    $qry2 = "select distinct dist_area_executive_id from dist_tso_histories where tso_id=$tso_id and (is_added=1 or is_transfer=1) and 
                    '" . $market_order_date . "' between effective_date and 
                    case 
                    when effective_end_date is not null then 
                     effective_end_date
                    else 
                    getdate()
                    end";

                    $ae_data = $this->DistTsoMappingHistory->query($qry2);
                    $ae_ids = array();

                    foreach ($ae_data as $k => $v) {
                        $ae_ids[] = $v[0]['dist_area_executive_id'];
                    }
                    $ae_id = "";

                    if ($ae_ids) {
                        $ae_id = $ae_ids[0];
                    }
                }
            }

            $this->set(compact('ae_id', 'tso_id', 'territory_id', 'thana_id'));


            $this->loadModel('DistOutlet');
            $outlets = array();

            if (array_key_exists('dist_market_id', $from_outlet['DistOutlet'])) {
                $outlets = $this->DistOutlet->find('list', array(
                    'conditions' => array(
                        'dist_route_id' => $from_outlet['DistOutlet']['dist_route_id'],
                        'dist_market_id' => $from_outlet['DistOutlet']['dist_market_id']
                    ),
                    'order' => array('name' => 'asc')
                ));
            }
            $pre_outlets = $outlets;
            $outlet_id = $from_outlet['DistOutlet']['dist_outlet_id'];
        }
        $locationTypes = $this->DistOutlet->DistMarket->LocationType->find('list');
        $categories = $this->DistOutlet->OutletCategory->find('list', array('conditions' => array('is_active' => 1)));
        unset($categories[17]);



        if (!empty($pre_submit_data)) {


            ///////////////*********************** for Distributor start *********///////////////////
            $this->loadModel('DistTsoMappingHistory');
            $this->loadModel('DistDistributor');
            $office_id = $pre_submit_data['office_id'];
            $distributor_id = $pre_submit_data['distributor_id'];
            $sr_id = $pre_submit_data['sr_id'];

            $territory_id = $pre_submit_data['territory_id'];
            $dist_route_id = $pre_submit_data['dist_route_id'];
            $thana_id = $pre_submit_data['thana_id'];
            $market_id = $pre_submit_data['market_id'];

            $order_date = date("Y-m-d H:i:s", strtotime($pre_submit_data['order_date']));
            $qry = "select distinct dist_distributor_id from dist_tso_mapping_histories
                  where office_id=$office_id and is_change=1 and 
                    '" . $order_date . "' between effective_date and 
                    case 
                    when end_date is not null then 
                     end_date
                    else 
                    getdate()
                    end";

            $dist_data = $this->DistTsoMappingHistory->query($qry);
            $dist_ids = array();

            foreach ($dist_data as $k => $v) {
                $dist_ids[] = $v[0]['dist_distributor_id'];
            }


            if ($office_parent_id == 0) {
                $conditions = array(
                    'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.id' => $dist_ids, 'DistDistributor.is_active' => 1), 'order' => array('DistDistributor.name' => 'asc'), 'recursive' => 0
                );
            } else {
                $conditions = array(
                    'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.is_active' => 1), 'order' => array('DistDistributor.name' => 'asc'), 'recursive' => 0
                );
            }


            //if ($office_id) {
            $dist_all = $this->DistDistributor->find('all', $conditions);
            //}


            if ($dist_all) {
                foreach ($dist_all as $key => $data) {
                    $k = $data['DistDistributor']['id'];
                    $v = $data['DistDistributor']['name'];
                    $distributors[$k] = $v;
                }
            }

            ///////////////*********************** for Distributor End *********///////////////////


            ///////////////*********************** for SR Start *********///////////////////
            $pre_srs = $this->DistSalesRepresentative->find('list', array(
                'conditions' => array('DistSalesRepresentative.dist_distributor_id' => $distributor_id, 'DistSalesRepresentative.is_active' => 1), 'order' => array('DistSalesRepresentative.name' => 'asc')
            ));


            ///////////////*********************** for SR End *********///////////////////

            ///////////////*********************** for Route List Start *********///////////////////   

            $this->loadModel('DistRouteMapping');
            $this->loadModel('DistRoute');
            /*$qry_route="select distinct dist_route_id from dist_sr_route_mapping_histories
                    where dist_sr_id=$sr_id and dist_distributor_id=$distributor_id and is_change=1 and 
                    '".$order_date."' between effective_date and 
                    case 
                    when end_date is not null then 
                     end_date
                    else 
                    getdate()
                    end";*/
            $qry_route = "select dist_route_id from dist_sr_route_mappings
                    where dist_sr_id=$sr_id and dist_distributor_id=$distributor_id ";
            $route_data = $this->DistRouteMapping->query($qry_route);
            $route_ids = array();

            foreach ($route_data as $k => $v) {
                $route_ids[] = $v[0]['dist_route_id'];
            }


            $route = $this->DistRoute->find('all', array(
                'conditions' => array('DistRoute.id' => $route_ids),
                'recursive' => 0
            ));

            if ($route) {
                foreach ($route as $key => $data) {
                    $k = $data['DistRoute']['id'];
                    $v = $data['DistRoute']['name'];
                    $pre_routes[$k] = $v;
                }
            }


            ///////////////*********************** for Route List End *********///////////////////       


            ///////////////*********************** for Market Start *********///////////////////  

            $conditions = array('is_active' => 1);

            if ($territory_id) {
                $conditions['territory_id'] = $territory_id;
            }

            if ($dist_route_id) {
                $conditions['dist_route_id'] = $dist_route_id;
            }

            if ($thana_id) {
                $conditions['thana_id'] = $thana_id;
            }

            $this->loadModel('DistMarket');

            $pre_markets = $this->DistMarket->find('list', array(
                'conditions' => $conditions,
                'order' => array('DistMarket.name' => 'ASC'),
                'recursive' => -1
            ));

            ///////////////*********************** for Market End *********///////////////////   


            ///////////////*********************** for Outlet Start *********///////////////////   
            $this->loadModel('DistOutlet');

            $pre_outlets = $this->DistOutlet->find('list', array(
                'conditions' => array('DistOutlet.dist_market_id' => $market_id)
            ));


            ///////////////*********************** for Outlet End *********///////////////////    

            $this->Session->delete('from_outlet');
            $this->Session->delete('from_market');
        }



        //pr($distributors);die();
        $this->set(compact('offices', 'locationTypes', 'distributors', 'distSalesRepresentatives', 'srs', 'categories', 'territories', 'product_list', 'outlets', 'current_date', 'sale_type_list', 'office_parent_id', 'sales_person', 'distRoutes', 'markets', 'outlets', 'pre_submit_data', 'pre_srs', 'pre_routes', 'pre_outlets', 'pre_markets'));
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
        $this->loadModel('DistSrProductCombination');
        $this->loadModel('DistSrCombination');
        $this->loadModel('CombinationDetailsV2');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');

        //$company_id= $this->Session->read('Office.company_id');
        //$user_group_id =$this->Session->read('Office.group_id');
        /****** tso and ae list start  *****/
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $this->loadModel('DistTso');
        if ($office_parent_id == 0) {
            $tso_conditions = array();
            $ae_conditions = array();
        } else {
            $tso_conditions = array('DistTso.office_id' => $this->Session->read('Office.id'));
            $ae_conditions = array('DistAreaExecutive.office_id' => $this->Session->read('Office.id'));
        }

        $tso_list = $this->DistTso->find('list', array('conditions' => $tso_conditions, 'order' => array('DistTso.name' => 'asc'), 'recursive' => -1));

        $this->loadModel('DistAreaExecutive');
        $ae_list = $this->DistAreaExecutive->find('list', array('order' => array('DistAreaExecutive.name' => 'asc'), 'recursive' => -1));
        $this->set(compact('tso_list', 'ae_list'));

        /****** tso and ae list end  *****/



        /*
          echo "<pre>";
          print_r($this->request->data); exit;
          exit;
         * 
         */


        $this->loadModel('Product');


        $current_date = date('d-m-Y', strtotime($this->current_date()));



        /* ------- start get edit data -------- */
        $this->DistOrder->recursive = 1;
        $options = array(
            'conditions' => array('DistOrder.id' => $id)
        );

        $existing_record = $this->DistOrder->find('first', $options);
        //pr($existing_record);die();
        $details_data = array();
        foreach ($existing_record['DistOrderDetail'] as $detail_val) {
            $product = $detail_val['product_id'];
            $combined_product = $this->CombinationDetailsV2->find('all', array(
                'conditions' => array(
                    'CombinationDetailsV2.combination_id = (SELECT top 1 com.id from 
                                                                                combinations_v2 com 
                                                                                INNER JOIN combination_details_v2 comd on com.id=comd.combination_id 
                                                                                WHERE comd.product_id=' . $product . ' group by com.id,com.effective_date order by com.effective_date desc)'
                ),
                'fields' => array('product_id'),
                'recursive' => -1
            ));
            $combined_product = array_map(function ($val) {
                return $val['CombinationDetailsV2']['product_id'];
            }, $combined_product);
            $combined_product = implode(',', $combined_product);
            $detail_val['combined_product'] = $combined_product;

            $detail_val['bonus_product_name'] =  '';
            if (!empty($detail_val['bonus_product_id'])) {
                $bonus_product_name = $this->Product->find('first', array('conditions' => array('Product.id' => $detail_val['bonus_product_id']), 'fields' => array('Product.name'), 'recursive' => -1));
                $detail_val['bonus_product_name'] = $bonus_product_name['Product']['name'];
            }
            $details_data[] = $detail_val;
        }

        $existing_record['DistOrderDetail'] = $details_data;

        $this->loadModel('MeasurementUnit');
        for ($i = 0; $i < count($details_data); $i++) {
            $measurement_unit_id = $details_data[$i]['measurement_unit_id'];
            $measurement_unit_name = $this->MeasurementUnit->find('all', array(
                'conditions' => array('MeasurementUnit.id' => $measurement_unit_id),
                'fields' => array('name'),
                'recursive' => -1
            ));
            $existing_record['DistOrderDetail'][$i]['measurement_unit_name'] = $measurement_unit_name[0]['MeasurementUnit']['name'];
        }

        $existing_record['office_id'] = $existing_record['DistOrder']['office_id'];
        $existing_record['distributor_id'] = $existing_record['DistOrder']['distributor_id'];
        $existing_record['sr_id'] = $existing_record['DistOrder']['sr_id'];
        $existing_record['dist_route_id'] = $existing_record['DistOrder']['dist_route_id'];
        $existing_record['territory_id'] = $existing_record['DistOrder']['territory_id'];
        $existing_record['thana_id'] = $existing_record['DistOrder']['thana_id'];
        $existing_record['market_id'] = $existing_record['DistOrder']['market_id'];
        $existing_record['outlet_id'] = $existing_record['DistOrder']['outlet_id'];
        $existing_record['order_time'] = date('d-m-Y', strtotime($existing_record['DistOrder']['order_time']));
        $existing_record['order_date'] = date('d-m-Y', strtotime($existing_record['DistOrder']['order_date']));
        $existing_record['dist_order_no'] = $existing_record['DistOrder']['dist_order_no'];
        $existing_record['order_reference_no'] = $existing_record['DistOrder']['order_reference_no'];



        //pr($existing_record);
        $this->LoadModel('Product');
        $this->LoadModel('DistStore');
        $store_id = $this->DistStore->find('first', array(
            'fields' => array('DistStore.id'),
            'conditions' => array('DistStore.dist_distributor_id' => $existing_record['distributor_id']),
            'recursive' => -1
        ));
        $open_bonus_product = $this->Product->find('all', array(
            'fields' => array('Product.id', 'Product.name'),
            'joins' => array(
                array(
                    'table' => 'dist_current_inventories',
                    'alias' => 'DistCurrentInventory',
                    'type' => 'Inner',
                    'conditions' => 'DistCurrentInventory.product_id=Product.id'
                ),
                /*array(
                    'table' => 'dist_open_combination_products',
                    'alias' => 'DistOpenCombinationProduct',
                    'type' => 'Inner',
                    'conditions' => 'DistOpenCombinationProduct.product_id=Product.id'
                ),
                array(
                    'table' => 'dist_open_combinations',
                    'alias' => 'DistOpenDistSrCombination',
                    'type' => 'Inner',
                    'conditions' => 'DistOpenCombinationProduct.combination_id=DistOpenDistSrCombination.id'
                ),*/
            ),
            'conditions' => array(
                'DistCurrentInventory.bonus_qty >' => 0,
                'DistCurrentInventory.store_id' => $store_id['DistStore']['id'],
                /*'DistOpenDistSrCombination.start_date <=' => $existing_record['DistOrder']['order_date'],
                'DistOpenDistSrCombination.end_date >=' => $existing_record['DistOrder']['order_date'],*/
            ),
            'recursive' => -1
        ));


        $open_bonus_product_option = array();
        foreach ($open_bonus_product as $bonus_product) {
            $open_bonus_product_option[$bonus_product['Product']['id']] = $bonus_product['Product']['name'];
        }

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $this->set('office_id', $existing_record['office_id']);
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

        $office_id = $existing_record['office_id'];
        $territory_id = $existing_record['territory_id'];
        $market_id = $existing_record['market_id'];
        $distributor_id = $existing_record['distributor_id'];
        $sr_id = $existing_record['sr_id'];
        $thana_id = $existing_record['thana_id'];
        $dist_route_id = $existing_record['dist_route_id'];
        $outlets = array();
        $distributors = array();
        $srs = array();
        $thanas = array();
        $distRoutes = array();

        /* fetch distributor info start */

        if ($office_id) {
            $distributors = $this->DistDistributor->find('list', array(
                'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.is_active' => 1)
            ));
        }

        /* fetch distributor info end */

        /* fetch SR info start */

        if ($distributor_id) {
            $srs = $this->DistSalesRepresentative->find('list', array(
                'conditions' => array('DistSalesRepresentative.dist_distributor_id' => $distributor_id)
            ));
        }

        /* fetch SR info end */

        $this->loadModel('Territory');
        $territories_list = $this->Territory->find('all', array(
            'conditions' => array('Territory.office_id' => $office_id),
            'order' => array('Territory.name' => 'asc')
        ));
        $territories = array();
        foreach ($territories_list as $t_result) {
            $territories[$t_result['Territory']['id']] = $t_result['Territory']['name'] . ' (' . $t_result['SalesPerson']['name'] . ')';
        }


        /* Fetch Thanas information start */
        if ($territory_id) {
            $thanas = $this->Thana->find('list', array(
                'conditions' => array('ThanaTerritory.territory_id' => $territory_id),
                'joins' => array(
                    array(
                        'table' => 'thana_territories',
                        'alias' => 'ThanaTerritory',
                        'conditions' => 'ThanaTerritory.thana_id=Thana.id'
                    )
                )
            ));
        }

        /* Fetch Thanas Information End */


        //for spo user
        $spo_territories = $this->Territory->find('list', array(
            'conditions' => array('Territory.office_id' => $office_id, 'User.user_group_id' => 1008),
            'joins' => array(
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'SalesPerson.id = User.sales_person_id'
                )
            ),
            'order' => array('Territory.name' => 'asc'),
            'recursive' => 0
        ));
        $this->set(compact('spo_territories'));



        //get spo territories id
        $this->loadModel('Usermgmt.User');
        $user_info = $this->User->find('all', array(
            'fields' => array('User.id', 'User.sales_person_id', 'User.username', 'SalesPerson.id', 'SalesPerson.name', 'SalesPerson.office_id', 'SalesPerson.territory_id', 'UserGroup.id', 'UserGroup.name', 'UserTerritoryList.territory_id'),
            'conditions' => array('User.active' => 1),
            'joins' => array(
                array(
                    'alias' => 'UserTerritoryList',
                    'table' => 'user_territory_lists',
                    'type' => 'INNER',
                    'conditions' => 'User.id = UserTerritoryList.user_id'
                )
            ),
            'recursive' => 0
        ));




        $territory_ids = array($territory_id);
        $user_group_id = 0;
        if ($user_info) {
            foreach ($user_info as $u_result) {
                //echo $result['UserTerritoryList']['territory_id'].'<br>';
                array_push($territory_ids, $u_result['UserTerritoryList']['territory_id']);
            }

            $user_group_id = $u_result['UserGroup']['id'];
        }


        /* pr($existing_record);
          exit; */

        if ($user_group_id == 1008) {
            $sale_type_id = 3;
        } elseif ($existing_record['DistOrder']['is_program'] == 1) {
            $sale_type_id = 4;
        } else {
            $sale_type_id = 10;
        }


        $this->set(compact('user_group_id', 'sale_type_id'));

        //pr($territory_ids);
        //end for spo user

        /* Fetch Market information start */

        if ($thana_id) {
            $markets = $this->DistMarket->find('list', array(
                'conditions' => array('DistMarket.thana_id' => $thana_id, 'DistMarket.dist_route_id' => $dist_route_id),
                'order' => array('name' => 'asc')
            ));
        } else {

            $markets = $this->DistMarket->find('list', array(
                'conditions' => array('DistMarket.territory_id' => $territory_id, 'DistMarket.dist_route_id' => $dist_route_id),
                'order' => array('name' => 'asc')
            ));
        }

        /* Fetch Market Information End */



        if ($market_id) {
            $outlets = $this->DistOutlet->find('list', array(
                'conditions' => array('dist_market_id' => $market_id, 'dist_route_id' => $dist_route_id),
                'order' => array('name' => 'asc')
            ));
        }

        $this->loadModel('DistStore');
        $this->loadModel('Product');
        $this->loadModel('DistCurrentInventory');
        $store_info = $this->DistStore->find('first', array(
            'conditions' => array(
                'DistStore.dist_distributor_id' => $distributor_id
            ),
            'recursive' => -1
        ));

        $store_id = $store_info['DistStore']['id'];


        foreach ($existing_record['DistOrderDetail'] as $key => $single_product) {

            $total_qty_arr = $this->DistCurrentInventory->find('all', array(
                'conditions' => array('store_id' => $store_id, 'product_id' => $single_product['product_id']),
                'fields' => array('sum(qty) as total'),
                'recursive' => -1
            ));

            $total_qty = $total_qty_arr[0][0]['total'];

            $sales_total_qty = $this->unit_convertfrombase($single_product['product_id'], $single_product['measurement_unit_id'], $total_qty);

            $existing_record['DistOrderDetail'][$key]['stock_qty'] = $sales_total_qty;
        }


        $products_from_ci = $this->DistCurrentInventory->find('all', array(
            'fields' => array('DISTINCT DistCurrentInventory.product_id', 'DistCurrentInventory.booking_qty', 'DistCurrentInventory.bonus_qty', 'DistCurrentInventory.bonus_booking_qty'),
            'conditions' => array('DistCurrentInventory.store_id' => $store_id, 'DistCurrentInventory.qty > ' => 0, 'inventory_status_id' => 1)
        ));
        //pr($products_from_ci);die();
        $product_ci = array();
        foreach ($products_from_ci as $each_ci) {
            $product_ci[] = $each_ci['DistCurrentInventory']['product_id'];
        }
        foreach ($existing_record['DistOrderDetail'] as $value) {
            $product_ci[] = $value['product_id'];
        }

        $product_ci_in = implode(",", $product_ci);

        $product_list = $this->Product->find('list', array('conditions' => array('Product.id' => $product_ci), 'order' => array('order' => 'asc')));

        /* ------- end get edit data -------- */


        /* -----------My Work-------------- */
        $this->loadModel('DistSrProductPrice');
        $this->loadModel('DistSrProductCombination');

        $selected_bonus = array();
        $selected_set = array();
        $selected_policy_type = array();
        foreach ($existing_record['DistOrderDetail'] as $key => $value) {
            $existing_product_category_id_array = $this->Product->find('all', array(
                'conditions' => array('Product.id' => $value['product_id']),
                'fields' => array('product_category_id'),
                'recursive' => -1
            ));

            $existing_product_category_id = $existing_product_category_id_array[0]['Product']['product_category_id'];
            if ($value['discount_amount'] && $value['policy_type'] == 3) {
                $selected_policy_type[$value['policy_id']] = 1;
            }
            if ($value['is_bonus'] == 3) {
                if ($value['policy_type'] == 3) {
                    $selected_policy_type[$value['policy_id']] = 2;
                }
                if ($value['other_info']) {
                    $other_info = json_decode($value['other_info'], 1);
                    $selected_set[$value['policy_id']] = $other_info['selected_set'];
                    $selected_bonus[$value['policy_id']][$other_info['selected_set']][$value['product_id']] = $value['sales_qty'];
                } else {
                    $selected_bonus[$value['policy_id']][1][$value['product_id']] = $value['sales_qty'];
                }
            }
        }
        $this->set(compact('selected_bonus', 'selected_set', 'selected_policy_type'));

        $this->set('page_title', 'Edit SR Order');
        $this->DistOrder->id = $id;
        if (!$this->DistOrder->exists($id)) {
            throw new NotFoundException(__('Invalid Order'));
        }


        global $cart_data, $matched_array;
        $cart_data = array();
        $matched_array = array();
        $qty_session_data = array();

        /* ---------creating prepare data---------- */
        $prepare_cart_data = array();
        if (!empty($filter_product['Product']['id'])) {
            $prepare_cart_data[$filter_product['Product']['id']]['Product'] = $filter_product['Product'];
            $prepare_cart_data[$filter_product['Product']['id']]['DistSrProductPrice'] = $filter_product['DistSrProductPrice'];
            foreach ($filter_product['Individual_slab'] as $individual_slab_val) {
                $prepare_cart_data[$filter_product['Product']['id']]['Individual_slab'][$individual_slab_val['min_qty']] = $individual_slab_val['price'];
            }
            if (!empty($filter_product['DistSrCombination'])) {
                $prepare_cart_data[$filter_product['Product']['id']]['DistSrCombination'] = $filter_product['DistSrCombination'];
            }
            if (!empty($filter_product['combination_id'])) {

                /* ---- start ------- */
                $this->DistSrCombination->recursive = 2;
                $condition_value1['DistSrCombination.id'] = $filter_product['combination_id']; //$filter_product['DistSrCombination']['id'];
                $DistSrCombination_slab_data_option = array(
                    'conditions' => array($condition_value1),
                );
                $DistSrCombination_slab_data = $this->DistSrCombination->find('all', $DistSrCombination_slab_data_option);
                /* ----- end -------- */

                $DistSrCombination_slab = array();
                foreach ($DistSrCombination_slab_data as $combine_group) {
                    foreach ($combine_group['DistSrProductCombination'] as $combine_val) {
                        $DistSrCombination_slab[$combine_val['min_qty']][$combine_val['product_id']] = $combine_val['Childrel']['price'];
                    }
                }
                if (!empty($DistSrCombination_slab)) {
                    $prepare_cart_data[$filter_product['Product']['id']]['Combined_slab'] = $DistSrCombination_slab;
                }
            }
        }
        /* ----------start create cart data and matched data ----------- */
        /* ---------------- cart data store in session ---------------- */

        /* ------ unset cart data ------- */

        $user_office_id = $this->UserAuth->getOfficeId();

        /* ------ start code of sale type list ------ */
        $sale_type_list = array(
            1 => 'SO Sales',
            2 => 'CSA Sales',
            3 => 'SPO Sales',
            4 => 'Program Sales'
        );
        /* ------ end code of sale type list ------ */
        /* ----- start code of product list ----- */
        //$product_list = $this->Product->find('list');
        /* ----- start code of product list ----- */
        /* ----- start code of sales person ----- */
        $user_office_id = $this->UserAuth->getOfficeId();
        /* $sales_person_list = $this->SalesPerson->find('list', array(
          'conditions' => array('SalesPerson.office_id' => $user_office_id, 'User.user_group_id' => 4)
          )); */
        $sales_person_list = $this->SalesPerson->find('list', array(
            'conditions' => array('SalesPerson.office_id' => $user_office_id)
        ));
        /*      echo "<pre>";
          print_r($sales_person_list);
          exit; */
        /* ----- end code of sales person ----- */
        /* ----- start code of market list ----- */
        //$market_list = $this->Market->find('list');
        /* ----- end code of market list ----- */
        /* ----- start code of outlet list ----- */
        //$outlet_list = $this->Outlet->find('list');
        /* ----- end code of outlet list ----- */
        /* ------------ code for update Order ---------------- */
        //$this->Order->id = $id;
        $pre_order_data = array();
        if ($this->request->is('post')) {
            //pr($this->request->data);//die();
            $pre_order_data = $this->request->data['DistOrder'];
            $sale_type_id = $this->request->data['DistOrder']['sale_type_id'];

            $sr_id = $this->request->data['DistOrder']['sr_id'];

            $this->loadmodel('DistSalesRepresentative');
            $sr_info = $this->DistSalesRepresentative->find('first', array('conditions' => array('DistSalesRepresentative.id' => $sr_id), 'recursive' => -1));

            $sr_code = $sr_info['DistSalesRepresentative']['code'];

            $outlet_is_within_group = $this->outletGroupCheck($this->request->data['DistOrder']['outlet_id']);
            $product_is_injectable = $this->productInjectableCheck($this->request->data['OrderDetail']['product_id']);

            if ($product_is_injectable && !$outlet_is_within_group && $sale_type_id != 2) {
                $this->Session->setFlash(__("Special product can't sell to ordinary outlet!"), 'flash/error');
                $this->redirect(array('action' => 'create_Order'));
                exit;
            }

            $order_id = $id;

            $dist_order_no = '';
            $user_id = $this->UserAuth->getUserId();
            /* START ADD NEW */
            //get office id 
            $office_id = $this->request->data['DistOrder']['office_id'];

            //get thana id 
            $this->loadModel('DistMarket');
            $market_info = $this->DistMarket->find(
                'first',
                array(
                    'conditions' => array('DistMarket.id' => $this->request->data['DistOrder']['market_id']),
                    'fields' => 'DistMarket.thana_id',
                    'order' => array('DistMarket.id' => 'asc'),
                    'recursive' => -1,
                    //'limit' => 100
                )
            );
            $thana_id = $market_info['DistMarket']['thana_id'];
            /* END ADD NEW */

            $sale_type_id = $this->request->data['DistOrder']['sale_type_id'];
            $sale_type_id = 10;

            //pr($this->request->data['DistOrder']);
            //exit;


            $this->request->data['DistOrder']['is_active'] = 1;

            if (array_key_exists('draft', $this->request->data)) {
                $this->request->data['DistOrder']['status'] = 0;
                $message = "Order Has Been Saved as Draft";
                //$message = "Order Has Been Saved";
            } else {
                $message = "Order Has Been Saved";
                $this->request->data['DistOrder']['status'] = 1;
                //$this->request->data['DistOrder']['status'] = ($this->request->data['DistOrder']['credit_amount'] != 0) ? 1 : 2;
            }

            $sales_person = $this->SalesPerson->find('list', array(
                'conditions' => array('territory_id' => $this->request->data['DistOrder']['territory_id']),
                'order' => array('name' => 'asc')
            ));

            $this->request->data['DistOrder']['sales_person_id'] = key($sales_person);

            $this->request->data['DistOrder']['entry_date'] = date('Y-m-d', strtotime($this->request->data['DistOrder']['entry_date']));
            $this->request->data['DistOrder']['order_date'] = date('Y-m-d', strtotime($this->request->data['DistOrder']['order_date']));

            //if($this->dist_order_delivery_schedule($this->request->data['DistOrder']['sales_person_id'], $this->request->data['DistOrder']['dist_order_no']))
            $this->admin_delete($order_id, 0);

            $OrderData['id'] = $order_id;

            $OrderData['office_id'] = $this->request->data['DistOrder']['office_id'];
            $OrderData['distributor_id'] = $this->request->data['DistOrder']['distributor_id'];
            $OrderData['sr_id'] = $this->request->data['DistOrder']['sr_id'];
            $OrderData['dist_route_id'] = $this->request->data['DistOrder']['dist_route_id'];
            $OrderData['sale_type_id'] = $this->request->data['DistOrder']['sale_type_id'];
            $OrderData['territory_id'] = $this->request->data['DistOrder']['territory_id'];
            $OrderData['market_id'] = $this->request->data['DistOrder']['market_id'];
            $OrderData['outlet_id'] = $this->request->data['DistOrder']['outlet_id'];
            $OrderData['entry_date'] = $this->request->data['DistOrder']['entry_date'];
            $OrderData['order_date'] = $this->request->data['DistOrder']['order_date'];
            $dist_order_no = $OrderData['dist_order_no'] = $this->request->data['DistOrder']['dist_order_no'];
            $OrderData['gross_value'] = $this->request->data['DistOrder']['gross_value'] - $this->request->data['DistOrder']['total_discount'];
            //$OrderData['gross_value'] = $after_discount_gross_value;
            $OrderData['cash_recieved'] = $this->request->data['DistOrder']['cash_recieved'];
            $OrderData['credit_amount'] = $this->request->data['DistOrder']['credit_amount'];
            $OrderData['is_active'] = $this->request->data['DistOrder']['is_active'];
            //$OrderData['status'] = $this->request->data['DistOrder']['status'];
            $OrderData['status'] = 2;
            //$OrderData['order_time'] = $this->current_datetime();   
            $OrderData['order_time'] = $this->current_datetime();
            $OrderData['sales_person_id'] = $this->request->data['DistOrder']['sales_person_id'];
            $OrderData['ae_id'] = $this->request->data['DistOrder']['ae_id'];
            $OrderData['tso_id'] = $this->request->data['DistOrder']['tso_id'];
            $OrderData['discount_percent'] = $this->request->data['DistOrder']['discount_percent'];
            $OrderData['discount_value'] = $this->request->data['DistOrder']['discount_value'];
            $OrderData['total_discount'] = $this->request->data['DistOrder']['total_discount'];
            $OrderData['from_app'] = 0;
            $OrderData['action'] = 1;
            $OrderData['order_type'] = 1;

            $OrderData['is_program'] = ($sale_type_id == 4) ? 1 : 0;


            $OrderData['order_reference_no'] = $this->request->data['DistOrder']['order_reference_no'];


            $OrderData['created_at'] = $this->current_datetime();
            $OrderData['created_by'] = $this->UserAuth->getUserId();
            $OrderData['updated_at'] = $this->current_datetime();
            $OrderData['updated_by'] = $this->UserAuth->getUserId();


            $OrderData['office_id'] = $office_id ? $office_id : 0;
            $OrderData['thana_id'] = $thana_id ? $thana_id : 0;


            $this->DistOrder->create();

            if ($this->DistOrder->save($OrderData)) {

                $order_id = $this->DistOrder->getLastInsertId();

                $this->dist_ec_calculation($OrderData['gross_value'], $OrderData['outlet_id'], $sr_code, $office_id, $OrderData['order_date'], 1);
                // oc calculation 
                $this->dist_oc_calculation($sr_code, $office_id, $OrderData['gross_value'], $OrderData['outlet_id'], $OrderData['order_date'], $OrderData['order_time'], 1);


                $order_info_arr = $this->DistOrder->find('first', array(
                    'conditions' => array(
                        'DistOrder.id' => $order_id
                    )
                ));

                $this->loadModel('DistStore');
                $dist_id = $this->request->data['DistOrder']['distributor_id'];
                $store_id_arr = $this->DistStore->find('first', array(
                    'conditions' => array(
                        'DistStore.dist_distributor_id' => $dist_id
                    )
                ));

                $store_id = $store_id_arr['DistStore']['id'];


                if ($order_id) {
                    $all_product_id = $this->request->data['OrderDetail']['product_id'];

                    if (!empty($this->request->data['OrderDetail'])) {
                        $total_product_data = array();
                        $order_details = array();
                        //$order_details['DistOrderDetail']['dist_order_id'] = $order_id;

                        foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) {
                            if ($val == NULL || !$this->request->data['OrderDetail']['sales_qty'][$key]) {
                                continue;
                            }
                            $sales_price = $this->request->data['OrderDetail']['Price'][$key];
                            $order_details['DistOrderDetail']['dist_order_id'] = $order_id;
                            if ($sales_price > 0) {
                                $product_id = $order_details['DistOrderDetail']['product_id'] = $val;
                                $order_details['DistOrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
                                $order_details['DistOrderDetail']['price'] =  $sales_price - $this->request->data['OrderDetail']['discount_amount'][$key];
                                $order_details['DistOrderDetail']['actual_price'] = $sales_price;
                                $sales_qty = $order_details['DistOrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
                                $order_details['DistOrderDetail']['product_price_id'] = $this->request->data['OrderDetail']['product_price_id'][$key];

                                // $order_details['DistOrderDetail']['is_bonus'] = 0;
                                $order_details['DistOrderDetail']['discount_type'] = $this->request->data['OrderDetail']['disccount_type'][$key];
                                $order_details['DistOrderDetail']['discount_amount'] = $this->request->data['OrderDetail']['discount_amount'][$key];
                                $order_details['DistOrderDetail']['policy_type'] = $this->request->data['OrderDetail']['policy_type'][$key];
                                $order_details['DistOrderDetail']['policy_id'] = $this->request->data['OrderDetail']['policy_id'][$key];
                                if ($this->request->data['OrderDetail']['is_bonus'][$key] == 3) $order_details['DistOrderDetail']['is_bonus'] = 3;
                                else $order_details['DistOrderDetail']['is_bonus'] = $this->request->data['OrderDetail']['is_bonus'][$key];

                                //$order_details['DistOrderDetail']['bonus_qty']=0;
                                if ($this->request->data['OrderDetail']['bonus_product_qty'][$key] > 0) {
                                    $bonus_product_id = $order_details['DistOrderDetail']['bonus_product_id'] = $this->request->data['OrderDetail']['bonus_product_id'][$key];
                                    $bonus_product_qty = $order_details['DistOrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
                                } else {
                                    $order_details['DistOrderDetail']['bonus_product_id'] = NULL;
                                    $order_details['DistOrderDetail']['bonus_qty'] = NULL;
                                }
                                //Start for bonus
                                $order_date = date('Y-m-d', strtotime($this->request->data['DistOrder']['order_date']));
                                $bonus_product_id = $this->request->data['OrderDetail']['bonus_product_id'];
                                $bonus_product_qty = $this->request->data['OrderDetail']['bonus_product_qty'];
                                //$bonus_product_id=NULL;
                                //$bonus_product_qty=NULL;

                                if ($bonus_product_qty[$key] > 0) {
                                    $b_product_id = $bonus_product_id[$key];
                                    $bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $order_date);
                                    $order_details['DistOrderDetail']['bonus_id'] = $bonus_result['bonus_id'];
                                }

                                //End for bouns

                                $total_product_data[] = $order_details;
                                unset($order_details);
                            } else {
                                $order_details['DistOrderDetail']['dist_order_id'] = $order_id;
                                $product_id = $order_details['DistOrderDetail']['product_id'] = $val;
                                $order_details['DistOrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
                                $sales_qty = $order_details['DistOrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
                                $order_details['DistOrderDetail']['price'] = 0;

                                $order_details['DistOrderDetail']['discount_type'] = $this->request->data['OrderDetail']['disccount_type'][$key];
                                $order_details['DistOrderDetail']['discount_amount'] = $this->request->data['OrderDetail']['discount_amount'][$key];
                                $order_details['DistOrderDetail']['policy_type'] = $this->request->data['OrderDetail']['policy_type'][$key];
                                $order_details['DistOrderDetail']['policy_id'] = $this->request->data['OrderDetail']['policy_id'][$key];
                                if ($this->request->data['OrderDetail']['is_bonus'][$key] == 3) $order_details['DistOrderDetail']['is_bonus'] = 3;
                                else $order_details['DistOrderDetail']['is_bonus'] = $this->request->data['OrderDetail']['is_bonus'][$key];
                                $selected_set = '';
                                if (isset($this->request->data['OrderDetail']['selected_set'][$order_details['DistOrderDetail']['policy_id']])) {
                                    $selected_set = $this->request->data['OrderDetail']['selected_set'][$order_details['DistOrderDetail']['policy_id']];
                                }
                                $other_info = array();
                                if ($selected_set) {
                                    $other_info = array(
                                        'selected_set' => $selected_set
                                    );
                                }
                                if ($other_info)
                                    $order_details['DistOrderDetail']['other_info'] = json_encode($other_info);
                                $total_product_data[] = $order_details;
                                unset($order_details);
                            }
                            //pr($total_product_data);die();
                            //update inventory




                            // sales calculation
                            $tt_price = $sales_qty * $sales_price;
                            if ($sales_price > 0) {
                                $this->dist_sales_calculation($product_id, $sr_code, $office_id, $sales_qty, $tt_price,  $OrderData['order_date'], 1);
                            }
                        }
                        //die();
                        $last_order =  $this->request->data['DistOrder'];
                        $this->DistOrderDetail->saveAll($total_product_data);
                        $this->Session->write('last_order',  $last_order);
                        //die();
                    }
                }
            }
            if ($this->dist_order_delivery_schedule($sr_id, $this->request->data['DistOrder']['dist_order_no'])) {
                $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
                $product_list = Set::extract($products, '{n}.Product');

                $total_product_data = array();
                $MemoData = array();
                $MemoData['office_id'] = $this->request->data['DistOrder']['office_id'];
                $MemoData['distributor_id'] = $this->request->data['DistOrder']['distributor_id'];
                $MemoData['sr_id'] = $this->request->data['DistOrder']['sr_id'];
                $MemoData['dist_route_id'] = $this->request->data['DistOrder']['dist_route_id'];
                $MemoData['sale_type_id'] = $this->request->data['DistOrder']['sale_type_id'];
                $MemoData['territory_id'] = $this->request->data['DistOrder']['territory_id'];

                $MemoData['thana_id'] = $thana_id;
                $MemoData['market_id'] = $this->request->data['DistOrder']['market_id'];
                $MemoData['outlet_id'] = $this->request->data['DistOrder']['outlet_id'];
                $MemoData['entry_date'] = date('Y-m-d', strtotime($this->request->data['DistOrder']['order_date']));
                $MemoData['memo_date'] = date('Y-m-d', strtotime($this->request->data['DistOrder']['order_date']));

                $MemoData['dist_memo_no'] = 'M' . $user_id . date('d') . date('m') . date('h') . date('i') . date('s');
                $MemoData['dist_order_no'] = $this->request->data['DistOrder']['dist_order_no'];

                $MemoData['gross_value'] = $this->request->data['DistOrder']['gross_value'] - $this->request->data['DistOrder']['total_discount'];

                $MemoData['total_vat'] = 0;
                $MemoData['discount_type'] = 0;

                $MemoData['cash_recieved'] = $this->request->data['DistOrder']['cash_recieved'];
                $MemoData['credit_amount'] = $this->request->data['DistOrder']['credit_amount'];

                $MemoData['memo_time'] = $this->current_datetime();
                $MemoData['sales_person_id'] = $this->request->data['DistOrder']['sales_person_id'];
                $MemoData['ae_id'] = $this->request->data['DistOrder']['ae_id'];
                $MemoData['tso_id'] = $this->request->data['DistOrder']['tso_id'];
                $MemoData['discount_percent'] = $this->request->data['DistOrder']['discount_percent'];
                $MemoData['discount_value'] = $this->request->data['DistOrder']['discount_value'];
                $MemoData['total_discount'] = $this->request->data['DistOrder']['total_discount'];


                $MemoData['is_active'] = 0;
                $MemoData['from_app'] = 0;
                $MemoData['status'] = 1;
                $MemoData['action'] = 0;
                $MemoData['is_program'] = 0;
                $MemoData['memo_reference_no'] = '';

                $MemoData['latitude'] = '';
                $MemoData['longitude'] = '';

                $MemoData['created_at'] = $this->current_datetime();
                $MemoData['created_by'] = $this->UserAuth->getUserId();
                $MemoData['updated_at'] = $this->current_datetime();
                $MemoData['updated_by'] = $this->UserAuth->getUserId();

                $this->loadmodel('DistMemo');
                $this->DistMemo->create();

                if ($this->DistMemo->save($MemoData)) {

                    $memo_id = $this->DistMemo->getLastInsertId();
                    $total_product_data = array();
                    if ($memo_id) {
                        $all_product_id = $this->request->data['OrderDetail']['product_id'];
                        //pr($this->request->data['OrderDetail']['product_id']);
                        foreach ($this->request->data['OrderDetail']['product_id'] as $key => $val) { {
                                if ($val == NULL || !$this->request->data['OrderDetail']['sales_qty'][$key]) {
                                    continue;
                                }
                                $memo_details = array();
                                $sales_price = $this->request->data['OrderDetail']['Price'][$key];
                                $product_id = $memo_details['DistMemoDetail']['product_id'] = $val;
                                $memo_details['DistMemoDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
                                $memo_details['DistMemoDetail']['dist_memo_id'] = $memo_id;
                                $memo_details['DistMemoDetail']['price'] = $sales_price - $this->request->data['OrderDetail']['discount_amount'][$key];
                                $memo_details['DistMemoDetail']['actual_price'] = $sales_price;
                                $sales_qty =  $this->request->data['OrderDetail']['sales_qty'][$key];
                                $memo_details['DistMemoDetail']['vat'] = 0;
                                $memo_details['DistMemoDetail']['sales_qty'] = $sales_qty;
                                $invoice_qty = $sales_qty;

                                if ($sales_price == 0) {
                                    $is_bonus = 1;
                                    $memo_details['DistMemoDetail']['bonus_qty'] = $sales_qty;
                                    $memo_details['DistMemoDetail']['bonus_product_id'] = NULL;
                                } else {
                                    $is_bonus = 0;
                                    $memo_details['DistMemoDetail']['bonus_qty'] = NULL;
                                    $memo_details['DistMemoDetail']['bonus_product_id'] = NULL;
                                }

                                $memo_details['DistMemoDetail']['product_price_id'] = $this->request->data['OrderDetail']['product_price_id'][$key];

                                $memo_details['DistMemoDetail']['discount_type'] = $this->request->data['OrderDetail']['disccount_type'][$key];
                                $memo_details['DistMemoDetail']['discount_amount'] = $this->request->data['OrderDetail']['discount_amount'][$key];
                                $memo_details['DistMemoDetail']['policy_type'] = $this->request->data['OrderDetail']['policy_type'][$key];
                                $memo_details['DistMemoDetail']['policy_id'] = $this->request->data['OrderDetail']['policy_id'][$key];
                                if ($this->request->data['OrderDetail']['is_bonus'][$key] == 3) $memo_details['DistMemoDetail']['is_bonus'] = 3;
                                else $memo_details['DistMemoDetail']['is_bonus'] = $this->request->data['OrderDetail']['is_bonus'][$key];
                                $selected_set = '';
                                if (isset($this->request->data['OrderDetail']['selected_set'][$memo_details['DistMemoDetail']['policy_id']])) {
                                    $selected_set = $this->request->data['OrderDetail']['selected_set'][$memo_details['DistMemoDetail']['policy_id']];
                                }
                                if ($selected_set) {
                                    $other_info = array(
                                        'selected_set' => $selected_set
                                    );
                                }
                                if ($other_info)
                                    $memo_details['DistMemoDetail']['other_info'] = json_encode($other_info);

                                //Start for bonus

                                $memo_details['DistMemoDetail']['bonus_id'] = 0;
                                $memo_details['DistMemoDetail']['bonus_scheme_id'] = 0;
                                //End for bouns

                                $total_product_data[] = $memo_details;
                                //unset($memo_details);
                                if ($this->request->data['OrderDetail']['bonus_product_qty'][$key] > 0) {
                                    $memo_details_bonus['DistMemoDetail']['dist_memo_id'] = $memo_id;
                                    $memo_details_bonus['DistMemoDetail']['is_bonus'] = 1;
                                    $memo_details_bonus['DistMemoDetail']['vat'] = 0;
                                    $memo_product_id = $memo_details_bonus['DistMemoDetail']['product_id'] = $this->request->data['OrderDetail']['bonus_product_id'][$key];
                                    $memo_details_bonus['DistMemoDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
                                    $memo_details_bonus['DistMemoDetail']['price'] = 0.0;
                                    $bonus_product_qty = $memo_details_bonus['DistMemoDetail']['sales_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];

                                    $invoice_bonus_qty = $bonus_product_qty;
                                    $total_product_data[] = $memo_details_bonus;


                                    $punits_pre = $this->search_array($memo_product_id, 'id', $product_list);
                                    //update inventoryd
                                    if ($invoice_bonus_qty < $bonus_product_qty) {
                                        $n_b_sales_qty = $bonus_product_qty - $invoice_bonus_qty;
                                        $update_type = 'deduct';
                                        $tran_type_id = 3;
                                    } else {
                                        $n_b_sales_qty = $invoice_bonus_qty - $bonus_product_qty;
                                        $update_type = 'add';
                                        $tran_type_id = 4;
                                    }

                                    if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                                        $bonus_base_quantity = $n_b_sales_qty;
                                        $invoice_bonus_qty = $invoice_bonus_qty;
                                    } else {
                                        $bonus_base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $n_b_sales_qty);
                                        $invoice_bonus_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $invoice_bonus_qty);
                                    }

                                    $this->dist_memo_update_current_inventory($bonus_base_quantity, $product_id, $store_id, $update_type, $tran_type_id, date('Y-m-d'), $invoice_bonus_qty);
                                    unset($memo_details_bonus);
                                }
                                $punits_pre = $this->search_array($product_id, 'id', $product_list);
                                //update inventory
                                if ($invoice_qty < $sales_qty) {
                                    $n_sales_qty = $sales_qty - $invoice_qty;
                                    $update_type = 'deduct';
                                    $tran_type_id = 3;
                                } else {
                                    $n_sales_qty = $invoice_qty - $sales_qty;
                                    $update_type = 'add';
                                    $tran_type_id = 4;
                                }

                                if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                                    $base_quantity = $n_sales_qty;
                                    $invoice_qty = $invoice_qty;
                                } else {
                                    $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $n_sales_qty);
                                    $invoice_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $invoice_qty);
                                }

                                $this->dist_memo_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, $tran_type_id, date('Y-m-d'), $invoice_qty);
                            }
                        }
                        $this->loadmodel('DistMemoDetail');
                        $this->DistMemoDetail->saveAll($total_product_data);
                        //die();


                        $res['status'] = 1;
                        //$res['memo_no'] = $result['memo_number'];
                        $res['message'] = 'Memos has been created successfuly.';
                        $this->Session->setFlash(__('Delivery has been completed successfuly.'), 'flash/success');

                        //update dist order table
                        $this->DistOrder->updateAll(array('DistOrder.processing_status' => 2), array('DistOrder.dist_order_no' => $dist_order_no));
                        $this->DistOrderDeliveryScheduleOrder->updateAll(array('DistOrderDeliveryScheduleOrder.processing_status' => 2), array('DistOrderDeliveryScheduleOrder.dist_order_no' => $dist_order_no));
                    }
                }
                $this->Session->setFlash(__('Order Delivery Done'), 'flash/success');
                $this->redirect(array("controller" => "DistOrderDeliveries", 'action' => 'create_order'));
            } else {
                $this->DistOrder->updateAll(array('DistOrder.status' => 0), array('DistOrder.dist_order_no' => $dist_order_no));
                $this->Session->setFlash(__('The Process failed!'), 'flash/error');
                $this->redirect(array("controller" => "DistOrderDeliveries", 'action' => 'index'));
            }

            $this->loadModel('ProductMeasurement');
            $product_measurement_units = $this->ProductMeasurement->find('list', array('fields' => array('product_id', 'measurement_unit_id')));
            $product_category_id_list = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));


            $distRoutes = $this->DistRoute->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));
            $this->set(compact('offices', 'distributors', 'srs', 'territories', 'thanas', 'markets', 'outlets', 'current_date', 'product_list', 'sale_type_list', 'existing_record', 'office_parent_id', 'product_measurement_units', 'product_category_id_list', 'open_bonus_product_option', 'distRoutes'));


            /* $this->set(compact('offices', 'territories', 'product_list', 'markets', 'outlets', 'current_date', 'sale_type_list', 'office_parent_id', 'sales_person')); */
        }

        $this->loadModel('ProductMeasurement');
        $product_measurement_units = $this->ProductMeasurement->find('list', array('fields' => array('product_id', 'measurement_unit_id')));
        $product_category_id_list = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));


        $distRoutes = $this->DistRoute->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));
        $this->set(compact('offices', 'distributors', 'srs', 'territories', 'thanas', 'markets', 'outlets', 'current_date', 'product_list', 'sale_type_list', 'existing_record', 'office_parent_id', 'product_measurement_units', 'product_category_id_list', 'open_bonus_product_option', 'distRoutes'));


        /* $this->set(compact('offices', 'territories', 'product_list', 'markets', 'outlets', 'current_date', 'sale_type_list', 'office_parent_id', 'sales_person')); */
    }

    public function dist_order_delivery_schedule($sr_id, $order_no)
    {
        $this->loadModel('DistOrder');
        $this->loadModel('DistOrderDetail');

        $this->loadModel('DistOrderDeliverySchedule');
        $this->loadModel('DistOrderDeliveryScheduleOrder');
        $this->loadModel('DistOrderDeliveryScheduleOrderDetail');

        $this->loadModel('SalesPerson');


        $all_inserted = true;
        $relation_array = array();



        //user info
        $so_info = $this->SalesPerson->find('first', array(
            'fields' => array('SalesPerson.name', 'SalesPerson.id', 'SalesPerson.dist_sales_representative_id', 'DistSalesRepresentative.id', 'DistSalesRepresentative.name', 'DistSalesRepresentative.dist_distributor_id', 'DistDistributor.id', 'DistDistributor.name', 'DistStore.id', 'Office.id', 'Office.office_name'),
            'conditions' => array('DistSalesRepresentative.id' => $sr_id),
            'joins' => array(
                array(
                    'alias' => 'DistSalesRepresentative',
                    'table' => 'dist_sales_representatives',
                    'type' => 'INNER',
                    'conditions' => 'SalesPerson.dist_sales_representative_id = DistSalesRepresentative.id'
                ),
                array(
                    'alias' => 'DistDistributor',
                    'table' => 'dist_distributors',
                    'type' => 'INNER',
                    'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistDistributor.id'
                ),
                array(
                    'alias' => 'DistStore',
                    'table' => 'dist_stores',
                    'type' => 'INNER',
                    'conditions' => 'DistSalesRepresentative.dist_distributor_id = DistStore.dist_distributor_id'
                ),
                array(
                    'alias' => 'Office',
                    'table' => 'offices',
                    'type' => 'INNER',
                    'conditions' => 'SalesPerson.office_id = Office.id'
                )
            ),
            'recursive' => -1
        ));
        if ($so_info) {
            $office_id = $so_info['Office']['id'];
            $distributor_id = $so_info['DistDistributor']['id'];
            $store_id = $so_info['DistStore']['id'];
            $sales_person_id = $so_info['SalesPerson']['id'];


            $this->loadModel('Product');
            $product_measurement_units = $this->Product->find('list', array('fields' => array('id', 'sales_measurement_unit_id')));
            //pr($product_measurement_units);
            $product_category_ids = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));
            //pr($product_category_ids);

            $this->loadModel('DistCurrentInventory');
            $inventory_results = $this->DistCurrentInventory->find('all', array(
                'conditions' => array(
                    'DistCurrentInventory.store_id' => $store_id,
                    'DistCurrentInventory.inventory_status_id' => 1
                ),
                'fields' => array('product_id', 'sum(qty) as qty'),
                'group' => array('product_id'),
                //'order' => array('DistCurrentInventory.product_id' => 'asc'),
                'recursive' => -1

            ));

            $inventory_qty_info = array();
            $inventory_bouns_qty_info = array();
            foreach ($inventory_results as $inventory_result) {
                $inventory_qty_info[$inventory_result['DistCurrentInventory']['product_id']] = $inventory_result[0]['qty'];
            }


            $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
            $product_list = Set::extract($products, '{n}.Product');


            $order_data_list = array();
            $process_table_status = 1;
            $process_table_status_p = 0;

            $order_results = $this->DistOrder->find('all', array(
                'conditions' => array(
                    'DistOrder.dist_order_no' => $order_no,
                    //'DistOrder.processing_status !=' => 1,
                ),
                'order' => array('DistOrder.order_time' => 'asc'),
                'recursive' => 1
            ));

            //pr($order_results);
            //exit;

            $order_products_list = array();
            foreach ($order_results as $key => $order_info) {
                $order_date = $order_info['DistOrder']['order_date'];
                $order_id = $order_info['DistOrder']['id'];
                $processing_status = $order_info['DistOrder']['processing_status'];
                $dist_order_no = $order_info['DistOrder']['dist_order_no'];
                $this->DistOrderDeliveryScheduleOrder->deleteAll(array('DistOrderDeliveryScheduleOrder.dist_order_no' => $dist_order_no));
                $this->DistOrderDeliveryScheduleOrderDetail->deleteAll(array('DistOrderDeliveryScheduleOrderDetail.dist_order_no' => $dist_order_no));


                $stock_deduction[$order_id] = 1;
                $previous_order_status = $this->DistOrder->find('first', array(
                    'conditions' => array(
                        'DistOrder.id' => $order_id,
                    ),
                    'order' => array('DistOrder.order_time' => 'asc'),
                    'recursive' => 1
                ));

                if ($previous_order_status['DistOrder']['status'] == 2 && $previous_order_status['DistOrder']['processing_status'] == 1) {
                    $previous_submitted_order['DistOrderDeliveryScheduleOrderDetail']['status'] = 1;
                    $total_order_detail_new[$dist_order_no][] = $previous_submitted_order;
                    continue;
                }

                $this->DistOrder->id = $order_id;
                if ($this->DistOrder->id) {
                    $this->DistOrder->updateAll(
                        array('DistOrder.processing_status' => 1, 'DistOrder.status' => 2, 'DistOrder.action' => 0),   //fields to update
                        array('DistOrder.id' => $order_id) //condition
                    );
                }

                foreach ($order_info['DistOrderDetail'] as $order_detail_result) {
                    $product_id = $order_detail_result['product_id'];
                    $order_details_id = $order_detail_result['id'];
                    $sales_total_qty = $order_detail_result['sales_qty'];
                    $measurement_unit_id = $order_detail_result['measurement_unit_id'];
                    $price = $order_detail_result['price'];
                    $order_id = $order_id;

                    $order_products_list[$order_details_id][$order_id] = array(
                        'order_id'              => $order_id,
                        'dist_order_no'         => $dist_order_no,
                        'product_id'            => $product_id,
                        'measurement_unit_id'   => $measurement_unit_id,
                        'processing_status'     => $processing_status,
                        'sales_qty'             => $sales_total_qty,
                        'price'                 => $price,
                        'order_date'            => $order_date,
                        'is_bonus'              => $order_detail_result['is_bonus'],
                    );
                }
            }



            //pr($order_products_list);exit;

            foreach ($order_products_list as $order_details_id => $order_info) {
                //$order_id = $order_info['DistOrder']['id'];

                //pr($order_info);exit;

                $i = 0;

                foreach ($order_info as $order_id => $order_detail_result) {
                    $product_id = $order_detail_result['product_id'];
                    $order_id = $order_detail_result['order_id'];
                    $sales_total_qty = $order_detail_result['sales_qty'];
                    $order_total_qty = $order_detail_result['sales_qty'];
                    $price = $order_detail_result['price'];

                    $product_id = $order_detail_result['product_id'];
                    $order_date = $order_detail_result['order_date'];

                    $is_bonus = $price > 0 ? 0 : 1;
                    $dist_order_no = $order_detail_result['dist_order_no'];
                    $measurement_unit_id = $order_detail_result['measurement_unit_id'];
                    //$order_id = $order_detail_result['dist_order_id'];


                    //$sales_qty=$this->unit_convert($product_id, $product_measurement_units[$product_id], $sales_total_qty);

                    $punits_pre = $this->search_array($product_id, 'id', $product_list);
                    if ($measurement_unit_id > 0) {
                        if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
                            $sales_qty = round($sales_total_qty);
                        } else {
                            $sales_qty = $this->unit_convert($product_id, $measurement_unit_id, $sales_total_qty);
                        }
                    } else {
                        if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                            $sales_qty = round($sales_total_qty);
                        } else {
                            $sales_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_total_qty);
                        }
                    }
                    $product_stock_qty = @$inventory_qty_info[$product_id];



                    if ($product_stock_qty >= $sales_qty) {
                        $process_table_status_p = 2;

                        $n_product_stock_qty = $product_stock_qty - $sales_qty;
                        $inventory_qty_info[$product_id] = $n_product_stock_qty;

                        $order_data_list['DistOrderDeliveryScheduleOrderDetail'][$dist_order_no][$order_details_id] = array(
                            'order_id'      => $order_id,
                            'dist_order_no' => $dist_order_no,
                            'product_id'    => $product_id,
                            'measurement_unit_id' => $measurement_unit_id,
                            'order_qty'     => $order_total_qty,
                            'invoice_qty'   => $order_total_qty,
                            'price'         => $price,
                            'status'        => 1,
                            'order_date'    => $order_date,
                            'is_bonus'      => $is_bonus,
                        );
                    } else {
                        $stock_deduction[$order_id] = 0;
                        $process_table_status = 0;
                        $invoice_qty = $this->unit_convertfrombase($product_id, $product_measurement_units[$product_id], $product_stock_qty);

                        $punits_pre = $this->search_array($product_id, 'id', $product_list);
                        if ($measurement_unit_id > 0) {
                            $invoice_qty = $this->unit_convertfrombase($product_id, $measurement_unit_id, $product_stock_qty);
                        } else {
                            $invoice_qty = $this->unit_convertfrombase($product_id, $punits_pre['sales_measurement_unit_id'], $product_stock_qty);
                        }

                        $invoice_qty = $i == 0 ? $invoice_qty : 0;
                        $order_data_list['DistOrderDeliveryScheduleOrderDetail'][$dist_order_no][$order_details_id] = array(
                            'order_id'      => $order_id,
                            'dist_order_no' => $dist_order_no,
                            'product_id'    => $product_id,
                            'order_qty'     => $order_total_qty,
                            'invoice_qty'   => $invoice_qty > 0 ? $invoice_qty : 0,
                            //'invoice_qty' => 0,
                            'price'         => $price,
                            'status'        => 0,
                            'order_date'    => $order_date,
                            'is_bonus'      => $is_bonus,
                            'measurement_unit_id' => $measurement_unit_id,
                        );
                        $i++;
                    }
                }
            }
            //pr($order_data_list);
            //exit;

            $process_table_status = $process_table_status ? $process_table_status : $process_table_status_p;


            //insert schedule data
            $order_data_list['DistOrderDeliverySchedule'] = array(
                'office_id'             => $office_id,
                'distributor_id'        => $distributor_id,
                //'order_id'            => $order_id,
                'sr_id'                 => $sr_id,
                'sales_person_id'       => $sales_person_id,
                'process_status'        => 1,
                'status'                => $process_table_status,
                'process_date_time'     => $this->current_datetime(),
                'created_at'            => $this->current_datetime(),
                'created_by'            => $sales_person_id,
                'updated_at'            => $this->current_datetime(),
                'updated_by'            => $sales_person_id,
            );

            //pr($order_data_list);exit;

            $this->DistOrderDeliverySchedule->create();
            if ($this->DistOrderDeliverySchedule->save($order_data_list)) {
                $schedule_id = $this->DistOrderDeliverySchedule->getLastInsertID();

                foreach ($order_data_list['DistOrderDeliveryScheduleOrderDetail'] as $dist_order_no => $order_details) {
                    $schedule_order_status = 1;

                    //stock check real time
                    foreach ($order_details as $o_result);
                    $dist_stock_check = $this->dist_stock_check($store_id, $o_result['order_id']);

                    //insert into schedule order details table
                    foreach ($order_details as $key => $result) {
                        $product_id = $result['product_id'];
                        $dist_order_id = $result['order_id'];
                        $measurement_unit_id = $result['measurement_unit_id'];
                        $order_qty = $result['order_qty'];
                        $is_bonus = $result['is_bonus'];
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['dist_order_delivery_schedule_id'] = $schedule_id;
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['dist_order_id']  = $result['order_id'];
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['dist_order_no']  = $dist_order_no;
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['measurement_unit_id']    = $measurement_unit_id;
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['product_id']     = $product_id;
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['order_qty']      = $order_qty;
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['invoice_qty']    = $result['invoice_qty'];
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['status']         = $result['status'];
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['is_bonus']       = $result['is_bonus'];

                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['created_at']     = $this->current_datetime();
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['created_by']     = $sales_person_id;
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['updated_at']     = $this->current_datetime();
                        $order_detail['DistOrderDeliveryScheduleOrderDetail']['updated_by']     = $sales_person_id;

                        $total_order_detail[] = $order_detail;
                        $this->DistOrderDeliveryScheduleOrderDetail->saveAll($order_detail);

                        $total_order_detail_new[$dist_order_no][] = $order_detail;

                        //update dist current invetory
                        if ($dist_stock_check && $result['status'] && $stock_deduction[$result['order_id']] == 1) {
                            $punits_pre = $this->search_array($product_id, 'id', $product_list);
                            if ($measurement_unit_id > 0) {
                                if ($measurement_unit_id == $punits_pre['base_measurement_unit_id']) {
                                    $base_quantity = ROUND($order_qty);
                                    $invoice_qty = ROUND($result['invoice_qty']);
                                } else {
                                    $base_quantity = $this->unit_convert($product_id, $measurement_unit_id, $order_qty);
                                    $invoice_qty = $this->unit_convert($product_id, $measurement_unit_id, $result['invoice_qty']);
                                }
                            } else {
                                if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                                    $base_quantity = ROUND($order_qty);
                                    $invoice_qty = ROUND($result['invoice_qty']);
                                } else {
                                    $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $order_qty);
                                    $invoice_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $result['invoice_qty']);
                                }
                            }

                            $update_type = 'deduct';
                            $this->dist_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $result['order_date'], $invoice_qty);


                            $this->dist_booking_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $order_date);
                        } else {
                            $schedule_order_status = 0;
                        }
                    }


                    //update dist_orders table
                    if ($schedule_order_status == 0) {
                        $this->DistOrder->id = $dist_order_id;
                        if ($this->DistOrder->id) {
                            $this->DistOrder->updateAll(
                                array(
                                    'DistOrder.processing_status' => 3,
                                    'DistOrder.status' => 1,
                                    'DistOrder.action' => 1
                                ),   //fields to update
                                array('DistOrder.id' => $dist_order_id) //condition
                            );
                        }
                    }
                    //end update dist_orders table

                    //insert into schedule order table
                    $order_into = array();
                    $order_into['DistOrderDeliveryScheduleOrder']['dist_order_delivery_schedule_id'] = $schedule_id;
                    //$order_into['DistOrderDeliveryScheduleOrder']['dist_order_id']    = $order_id;
                    $order_into['DistOrderDeliveryScheduleOrder']['dist_order_no']  = $dist_order_no;
                    $order_into['DistOrderDeliveryScheduleOrder']['processing_status'] = $schedule_order_status;
                    $this->DistOrderDeliveryScheduleOrder->saveAll($order_into);
                }

                //pr($total_order_detail);exit;



            }


            $f_order_datas = array();
            // pr($total_order_detail_new);exit;
            foreach ($total_order_detail_new as $key => $f_datas) {
                $order_status = 1;
                foreach ($f_datas as $f_data) {
                    if ($f_data['DistOrderDeliveryScheduleOrderDetail']['status'] != 1) $order_status = 0;
                }
                $f_order_datas[$key] = array(
                    //'dist_order_id' => $f_data['DistOrderDeliveryScheduleOrderDetail']['dist_order_id'],
                    'dist_order_no' => $key,
                    'status' => $order_status,
                );
            }

            $res_data = array();
            foreach ($f_order_datas as $r) {
                if ($r['status']) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }


    private function dist_stock_check($store_id, $order_id)
    {
        $this->loadModel('DistCurrentInventory');
        $this->loadModel('DistOrderDetail');
        $this->loadModel('Product');
        $this->loadModel('ProductMeasurement');

        $order_datas = $this->DistOrderDetail->find('all', array(
            'conditions' => array(
                'DistOrderDetail.dist_order_id' => $order_id
            ),
            'fields' => array('SUM(DistOrderDetail.sales_qty) as sales_qty', 'DistOrderDetail.product_id', 'DistOrderDetail.measurement_unit_id'),
            'group' => array('DistOrderDetail.product_id, DistOrderDetail.measurement_unit_id'),
            'recursive' => -1
        ));

        $order_detail = array();
        foreach ($order_datas as $data) {
            if ($data['DistOrderDetail']['measurement_unit_id']) {
                $product_info = $this->Product->find('first', array(
                    'conditions' => array(
                        'Product.id' => $data['DistOrderDetail']['product_id'],
                    ),
                    'joins' => array(
                        array(
                            'table' => 'product_measurements',
                            'alias' => 'ProductMeasurement',
                            'conditions' => 'ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id AND Product.id=ProductMeasurement.product_id'
                        )
                    ),
                    'fields' => array('ProductMeasurement.qty_in_base'),
                    'recursive' => -1
                ));
                //pr($product_info);

                $pro_measurement_info = $this->ProductMeasurement->find('first', array(
                    'conditions' => array(
                        'ProductMeasurement.product_id' => $data['DistOrderDetail']['product_id'],
                        'ProductMeasurement.measurement_unit_id' => $data['DistOrderDetail']['measurement_unit_id']
                    ),
                    'fields' => array('ProductMeasurement.qty_in_base'),
                    'recursive' => -1
                ));

                if ($pro_measurement_info && $pro_measurement_info['ProductMeasurement']['qty_in_base'] && $product_info) {
                    $prev_qty = isset($order_detail[$data['DistOrderDetail']['product_id']]) ? $order_detail[$data['DistOrderDetail']['product_id']] : 0;
                    $order_detail[$data['DistOrderDetail']['product_id']] = $prev_qty + ($data['0']['sales_qty'] * $pro_measurement_info['ProductMeasurement']['qty_in_base']) / $product_info['ProductMeasurement']['qty_in_base'];
                } elseif (empty($pro_measurement_info) && $product_info) {
                    $prev_qty = isset($order_detail[$data['DistOrderDetail']['product_id']]) ? $order_detail[$data['DistOrderDetail']['product_id']] : 0;
                    $order_detail[$data['DistOrderDetail']['product_id']] = $prev_qty + ($data['0']['sales_qty']) / $product_info['ProductMeasurement']['qty_in_base'];
                } else {
                    $prev_qty = isset($order_detail[$data['DistOrderDetail']['product_id']]) ? $order_detail[$data['DistOrderDetail']['product_id']] : 0;
                    $order_detail[$data['DistOrderDetail']['product_id']] = $prev_qty + $data['0']['sales_qty'];
                }
            } else {
                $prev_qty = isset($order_detail[$data['DistOrderDetail']['product_id']]) ? $order_detail[$data['DistOrderDetail']['product_id']] : 0;
                $order_detail[$data['DistOrderDetail']['product_id']] = $prev_qty + $data['0']['sales_qty'];
            }
        }
        foreach ($order_detail as $product_id => $qty) {
            //pr($result);
            /*$product_id = $result['DistOrderDetail']['product_id'];
            $qty = $result['0']['sales_qty'];
            */

            $current_inventory = $this->DistCurrentInventory->find('all', array(
                'conditions' => array(
                    'DistCurrentInventory.store_id' => $store_id,
                    'DistCurrentInventory.product_id' => $product_id
                ),
                'joins' => array(
                    array(
                        'table' => 'product_measurements',
                        'alias' => 'ProductMeasurement',
                        'type' => 'LEFT',
                        'conditions' => 'ProductMeasurement.product_id=Product.id AND ProductMeasurement.measurement_unit_id=Product.sales_measurement_unit_id'
                    )
                ),
                'group' => array('DistCurrentInventory.store_id', 'ProductMeasurement.qty_in_base', 'DistCurrentInventory.product_id HAVING (sum(DistCurrentInventory.qty))  >= ROUND((case when ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base end) *' . $qty . ',0)'),
                'fields' => array('DistCurrentInventory.store_id', 'DistCurrentInventory.product_id', 'sum(DistCurrentInventory.qty) as qty')
            ));

            if (!$current_inventory) {
                return false;
            }
        }

        return true;
    }

    public function dist_update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '', $invoice_qty = 0)
    {

        $this->loadModel('DistCurrentInventory');

        $find_type = 'all';
        if ($update_type == 'add')
            $find_type = 'first';

        $inventory_info = $this->DistCurrentInventory->find($find_type, array(
            'conditions' => array(
                //'CurrentInventory.qty >=' => 0,
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1,
                'DistCurrentInventory.product_id' => $product_id
            ),
            'order' => array('DistCurrentInventory.qty' => 'desc'),
            'recursive' => -1
        ));

        if ($update_type == 'deduct') {
            foreach ($inventory_info as $val) {
                if ($quantity <= 0) {
                    break;
                }
                if ($quantity <= $val['DistCurrentInventory']['qty']) {
                    $this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
                    $this->DistCurrentInventory->updateAll(
                        array(
                            'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $quantity,
                            'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty + ' . $invoice_qty,
                            'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                            'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                            'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                        ),
                        array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                    );
                    break;
                } else {

                    if ($val['DistCurrentInventory']['id'] > 0) {

                        $this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
                        if ($val['DistCurrentInventory']['qty'] <= 0) {
                            $this->DistCurrentInventory->updateAll(
                                array(
                                    'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $quantity,
                                    'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty + ' . $invoice_qty,
                                    'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                                    'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                    'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                                ),
                                array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                            );
                            $quantity = 0;
                            break;
                        } else {
                            $quantity = $quantity - $val['DistCurrentInventory']['qty'];
                            $this->DistCurrentInventory->updateAll(
                                array(
                                    'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $val['DistCurrentInventory']['qty'],
                                    'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty + ' . $invoice_qty,
                                    'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                                    'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                    'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                                ),
                                array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                            );
                        }
                    }
                }
            }
        } else {

            if (!empty($inventory_info)) {

                $this->DistCurrentInventory->updateAll(
                    array(
                        'DistCurrentInventory.qty' => 'DistCurrentInventory.qty + ' . $quantity,
                        //'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
                        'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                        'DistCurrentInventory.store_id' => $store_id,
                        'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                    ),
                    array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                );

                //for invoice qty
                if ($invoice_qty <= $inventory_info['DistCurrentInventory']['invoice_qty']) {
                    $this->DistCurrentInventory->updateAll(
                        array(
                            //'DistCurrentInventory.qty' => 'DistCurrentInventory.qty + ' . $quantity,
                            'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
                            'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                            'DistCurrentInventory.store_id' => $store_id,
                            'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                        ),
                        array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                    );
                } else {
                    $this->DistCurrentInventory->updateAll(
                        array(
                            'DistCurrentInventory.invoice_qty' => 0,
                            'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                            'DistCurrentInventory.store_id' => $store_id,
                            'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                        ),
                        array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                    );
                }
            }
        }


        return true;
    }
    public function dist_memo_update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '', $invoice_qty = 0)
    {

        $this->loadModel('DistCurrentInventory');

        $find_type = 'all';
        if ($update_type == 'add')
            $find_type = 'first';

        $inventory_info = $this->DistCurrentInventory->find($find_type, array(
            'conditions' => array(
                //'CurrentInventory.qty >=' => 0,
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1,
                'DistCurrentInventory.product_id' => $product_id
            ),
            'order' => array('DistCurrentInventory.qty' => 'desc'),
            'recursive' => -1
        ));



        if ($update_type == 'deduct') {
            foreach ($inventory_info as $val) {
                if ($quantity <= 0) {
                    break;
                }

                if ($quantity <= $val['DistCurrentInventory']['qty']) {
                    $this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
                    $this->DistCurrentInventory->updateAll(
                        array(
                            'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $quantity,
                            'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
                            'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                            'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                            'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                        ),
                        array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                    );
                    break;
                } else {

                    if ($val['DistCurrentInventory']['id'] > 0) {

                        $this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
                        if ($val['DistCurrentInventory']['qty'] <= 0) {
                            $this->DistCurrentInventory->updateAll(
                                array(
                                    'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $quantity,
                                    'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
                                    'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                                    'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                    'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                                ),
                                array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                            );
                            $quantity = 0;
                            break;
                        } else {
                            $quantity = $quantity - $val['DistCurrentInventory']['qty'];
                            $this->DistCurrentInventory->updateAll(
                                array(
                                    'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $val['DistCurrentInventory']['qty'],
                                    'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
                                    'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                                    'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                    'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                                ),
                                array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                            );
                        }
                    }
                }
            }
        } else {

            if (!empty($inventory_info)) {

                $this->DistCurrentInventory->updateAll(
                    array(
                        'DistCurrentInventory.qty' => 'DistCurrentInventory.qty + ' . $quantity,
                        //'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
                        'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                        'DistCurrentInventory.store_id' => $store_id,
                        'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                    ),
                    array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                );

                //for invoice qty
                if ($invoice_qty <= $inventory_info['DistCurrentInventory']['invoice_qty']) {
                    $this->DistCurrentInventory->updateAll(
                        array(
                            //'DistCurrentInventory.qty' => 'DistCurrentInventory.qty + ' . $quantity,
                            'DistCurrentInventory.invoice_qty' => 'DistCurrentInventory.invoice_qty - ' . $invoice_qty,
                            'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                            'DistCurrentInventory.store_id' => $store_id,
                            'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                        ),
                        array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                    );
                } else {
                    $this->DistCurrentInventory->updateAll(
                        array(
                            'DistCurrentInventory.invoice_qty' => 0,
                            'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                            'DistCurrentInventory.store_id' => $store_id,
                            'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                        ),
                        array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                    );
                }
            }
        }

        //exit;
        return true;
    }



    //for bonus and bouns schema
    public function bouns_and_scheme_id_set($b_product_id = 0, $order_date = '')
    {
        $this->loadModel('Bonus');
        //$this->loadModel('OpenDistSrCombination');
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


        /* echo 'Bonus = '.$bonus_id;
          echo '<br>';
          echo 'Bonus Scheme = '. $bonus_scheme_id;
          echo '<br>';
          echo '<br>';
          echo '<br>'; */

        $bonus_result['bonus_id'] = $bonus_id;
        $bonus_result['bonus_scheme_id'] = $bonus_scheme_id;

        return $bonus_result;
    }

    /* ----- ajax methods ----- */

    public function market_list()
    {
        $rs = array(array('id' => '', 'name' => '---- Select Market -----'));
        $thana_id = $this->request->data['thana_id'];
        //$thana_id = 2;
        $market_list = $this->DistMarket->find('all', array(
            'conditions' => array('DistMarket.thana_id' => $thana_id)
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
                'fields' => array('SalesPerson.id', 'SalesPerson.name')
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

    public function get_outlet_list()
    {
        $rs = array(array('id' => '', 'name' => '---- Select outlet -----'));
        $market_id = $this->request->data['market_id'];
        $outlet_list = $this->DistOutlet->find('all', array(
            'conditions' => array('DistOutlet.dist_market_id' => $market_id)
        ));
        $data_array = Set::extract($outlet_list, '{n}.Outlet');

        if (!empty($data_array)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_product_unit()
    {
        // $current_date = $this->current_date();
        $current_date = $this->request->data['order_date'] ? date('Y-m-d', strtotime($this->request->data['order_date'])) : date('Y-m-d');
        $distributor_id = $this->request->data['distributor_id'];
        $this->loadModel('DistStore');
        $this->DistStore->recursive = -1;
        $store_info = $this->DistStore->find('first', array(
            'conditions' => array('dist_distributor_id' => $distributor_id)
        ));

        $store_id = $store_info['DistStore']['id'];
        $product_id = $this->request->data['product_id'];


        $this->loadModel('DistCurrentInventory');
        $this->DistCurrentInventory->recursive = -1;
        $total_qty_arr = $this->DistCurrentInventory->find('all', array(
            'conditions' => array('store_id' => $store_id, 'product_id' => $product_id),
            'fields' => array('sum(qty) as total', 'sum(booking_qty) as booking_total')
        ));
        $total_qty = $total_qty_arr[0][0]['total'];
        $booking_total_qty = $total_qty_arr[0][0]['booking_total'];


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

        $sales_total_qty = $this->unit_convertfrombase($this->request->data['product_id'], $measurement_unit_id, $total_qty);
        $Sales_booking_total_qty = $this->unit_convertfrombase($this->request->data['product_id'], $measurement_unit_id, $booking_total_qty);

        $data_array['product_unit']['name'] = $measurement_unit_name;
        $data_array['product_unit']['id'] = $measurement_unit_id;

        if (!empty($sales_total_qty)) {
            $data_array['total_qty'] = $sales_total_qty;
        } else {
            $data_array['total_qty'] = '';
        }
        echo json_encode($data_array);
        $this->autoRender = false;


        $this->autoRender = false;
    }

    /* ------- set_combind_or_individual_price --------- */
    public function get_combine_or_individual_price_without_bonus()
    {
        // pr($this->request->data);die();
        $product_items_id = explode(',', rtrim($this->request->data['product_id_list'], ","));

        /* ---- read session data ----- */
        $cart_data = $this->Session->read('cart_session_data');
        $matched_data = $this->Session->read('matched_session_data');

        /*  echo "<pre>";
          echo "Cart data ----------------";
          print_r($cart_data);
          echo "Cart data ----------------";
          print_r($matched_data); exit; */


        /* ---- read session data ----- */
        $combined_product = $this->request->data['combined_product'];
        $min_qty = $this->request->data['min_qty'];
        $product_id = $this->request->data['product_id'];
        $order_date = $this->request->data['order_date'] ? date('Y-m-d', strtotime($this->request->data['order_date'])) : date('Y-m-d');
        /* ---------Bonus----------- */
        /* $this->loadModel('Bonus');
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
          } */

        /* ---------Bonus----------- */
        $this->loadModel('Bonus');
        $this->loadModel('Product');

        /*$this->Bonus->recursive = -1;
        $bonus_info = $this->Bonus->find('all', array(
            'conditions' => array(
                'mother_product_id' => $product_id,
                'effective_date <=' => $order_date,
                'end_date >=' => $order_date,
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
         }*/

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
                /* echo '<pre>';
                  pr($qty_data);exit; */
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
        if ($combined_product) {

            foreach ($matched_data as $combined_product_key => $combined_product_val) {
                if ($combined_product_key == $combined_product) {
                    /* if ($combined_product_val['is_matched_yet'] == 'NO') {
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
                      } */
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
                                                /*  echo "<pre>";
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

        /* if(!empty($result_data) && !empty($individual_less_qty)){
            $result_data['individual_slab'] = $individual_less_qty;
        }*/
        //pr($result_data);die();
        if (!empty($result_data)) {
            echo json_encode($result_data);
        } elseif (!empty($individual_less_qty)) {
            echo json_encode($individual_less_qty);
        } elseif (!empty($combined_less_qty)) {
            echo json_encode($combined_less_qty);
        }
        $this->autoRender = false;
    }
    public function get_combine_or_individual_price()
    {
        //pr($this->request->data);die();
        $product_items_id = explode(',', rtrim($this->request->data['product_id_list'], ","));

        /* ---- read session data ----- */
        $cart_data = $this->Session->read('cart_session_data');
        $matched_data = $this->Session->read('matched_session_data');

        /*echo "<pre>";
          echo "Cart data ----------------";
          print_r($cart_data);
          echo "Cart data ----------------";
          print_r($matched_data); exit;*/


        /* ---- read session data ----- */
        $combined_product = $this->request->data['combined_product'];
        $min_qty = $this->request->data['min_qty'];
        $product_id = $this->request->data['product_id'];
        $order_date = $this->request->data['order_date'] ? date('Y-m-d', strtotime($this->request->data['order_date'])) : date('Y-m-d');
        /* ---------Bonus----------- */
        /* $this->loadModel('Bonus');
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
          } */

        /* ---------Bonus----------- */
        $this->loadModel('Bonus');
        $this->loadModel('Product');

        $this->Bonus->recursive = -1;
        $bonus_info = $this->Bonus->find('all', array(
            'conditions' => array(
                'mother_product_id' => $product_id,
                'effective_date <=' => $order_date,
                'end_date >=' => $order_date,
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
                /* echo '<pre>';
                  pr($qty_data);exit; */
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
        if ($combined_product) {

            foreach ($matched_data as $combined_product_key => $combined_product_val) {
                if ($combined_product_key == $combined_product) {
                    /* if ($combined_product_val['is_matched_yet'] == 'NO') {
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
                      } */
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
                                                /*  echo "<pre>";
                                                  echo "dsfsdf";
                                                  print_r($individual_actual_data);
                                                  print_r($cart_data[$combined_val]); */
                                            }
                                            $individual_less_qty[$combined_val]['unit_rate'] = sprintf("%1\$.6f", $individual_actual_data);
                                            $individual_less_qty[$combined_val]['total_value'] = sprintf("%1\$.6f", $individual_actual_data) * $current_qty[$combined_val];
                                            $result_data[$combined_val]['unit_rate'] = $individual_less_qty[$combined_val]['unit_rate'];
                                            $result_data[$combined_val]['total_value'] = $individual_less_qty[$combined_val]['total_value'];
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

        /* if(!empty($result_data) && !empty($individual_less_qty)){
            $result_data['individual_slab'] = $individual_less_qty;
        }*/
        //pr($result_data);die();
        if (!empty($result_data)) {
            echo json_encode($result_data);
        } elseif (!empty($individual_less_qty)) {
            echo json_encode($individual_less_qty);
        } elseif (!empty($combined_less_qty)) {
            echo json_encode($combined_less_qty);
        }
        $this->autoRender = false;
    }

    public function delete_Order()
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
    public function check_current_inventory($product_id, $store_id, $booking_bonus_qty)
    {
        $this->loadModel('DistCurrentInventory');
        $inventory_info = $this->DistCurrentInventory->find('all', array(
            'conditions' => array(
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1,
                'DistCurrentInventory.product_id' => $product_id
            ),
            /*'joins' => array(
                array(
                    'table' => 'products',
                    'alias' => 'pr',
                    'conditions' => 'pr.id=DistCurrentInventory.product_id'
                ),
            ),*/
            'fields' => array('sum(qty) as total', 'sum(booking_qty) as booking_total'),

            'recursive' => -1
        ));
        //pr($inventory_info);
        $total_qty = $inventory_info[0][0]['total'];
        $booking_total_qty = $inventory_info[0][0]['booking_total'];
        //$product_name = 
        // pr($inventory_info);die();
        //pr($total_qty);pr($booking_total_qty);
        $msg = "";
        if ($total_qty < $booking_total_qty) {
            $msg = $msg . "Stock has been crossed the limits";
        }
        // pr($msg);
        return $msg;
        //pr($inventory_info );
    }

    public function dist_booking_update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '')
    {

        $this->loadModel('DistCurrentInventory');

        $find_type = 'first';

        $inventory_info = $this->DistCurrentInventory->find($find_type, array(
            'conditions' => array(
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1,
                'DistCurrentInventory.product_id' => $product_id
            ),
            'order' => array('DistCurrentInventory.qty' => 'desc'),
            'recursive' => -1
        ));

        //pr($inventory_info);

        if ($inventory_info) {
            if ($update_type == 'deduct') {

                /*$update_array = array('DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'");
                
                if($inventory_info['DistCurrentInventory']['booking_qty'] < $quantity){
                    $update_array['DistCurrentInventory.booking_qty']=0;
                }else{
                    $update_array['DistCurrentInventory.booking_qty']='DistCurrentInventory.booking_qty - ' . $quantity;
                }
                
                if($inventory_info['DistCurrentInventory']['bonus_booking_qty'] < $booking_bonus_qty){
                    $update_array['DistCurrentInventory.bonus_booking_qty']=0;
                }else{
                    $update_array['DistCurrentInventory.bonus_booking_qty']='DistCurrentInventory.bonus_booking_qty - ' . $booking_bonus_qty;
                }
                
                $this->DistCurrentInventory->id = $inventory_info['DistCurrentInventory']['id'];
                
                $this->DistCurrentInventory->updateAll(
                        $update_array, 
                        array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                );*/


                $update_array = array();
                $update_array['DistCurrentInventory']['id'] = $inventory_info['DistCurrentInventory']['id'];
                if ($inventory_info['DistCurrentInventory']['booking_qty'] < $quantity) {
                    $update_array['DistCurrentInventory']['booking_qty'] = 0;
                } else {
                    $update_array['DistCurrentInventory']['booking_qty'] = $inventory_info['DistCurrentInventory']['booking_qty'] - $quantity;
                }

                $update_array['DistCurrentInventory']['updated_at'] = $this->current_datetime();

                //pr($update_array);


                $this->DistCurrentInventory->save($update_array);




                /*$this->DistCurrentInventory->updateAll(
                        array(
                            'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty - ' . $quantity,
                            'DistCurrentInventory.bonus_booking_qty' => 'DistCurrentInventory.bonus_booking_qty - ' . $booking_bonus_qty,
                            'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                        ), 
                        array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                );*/
            } else {


                $this->DistCurrentInventory->updateAll(
                    array(
                        'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty + ' . $quantity,

                        'DistCurrentInventory.store_id' => $store_id,
                    ),
                    array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                );
            }
        }
        return true;
    }



    public function update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '', $booking_bonus_qty)
    {

        $this->loadModel('DistCurrentInventory');

        $find_type = 'all';
        if ($update_type == 'add')
            $find_type = 'first';

        $inventory_info = $this->DistCurrentInventory->find($find_type, array(
            'conditions' => array(
                //'CurrentInventory.qty >=' => 0,
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1,
                'DistCurrentInventory.product_id' => $product_id
            ),
            'order' => array('DistCurrentInventory.qty' => 'desc'),
            'recursive' => -1
        ));

        /* pr($inventory_info);
       pr($quantity);
       pr($product_id);
       pr($store_id);
       pr($update_type);
       pr($transaction_type_id);
       pr($transaction_date);
       pr($booking_bonus_qty);*/

        if ($update_type == 'deduct') {
            foreach ($inventory_info as $val) {
                if ($quantity <= 0) {
                    break;
                }
                if ($quantity <= $val['DistCurrentInventory']['booking_qty']) {
                    $this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
                    $this->DistCurrentInventory->updateAll(
                        array(
                            //'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' . $quantity,
                            'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty - ' . $quantity,
                            'DistCurrentInventory.bonus_booking_qty' => 'DistCurrentInventory.bonus_booking_qty - ' . $booking_bonus_qty,
                            'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                            'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                            'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                        ),
                        array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                    );
                    break;
                } else {


                    if ($val['DistCurrentInventory']['id'] > 0) {

                        $this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
                        if ($val['DistCurrentInventory']['qty'] <= 0) {

                            $this->DistCurrentInventory->updateAll(
                                array(
                                    //'DistCurrentInventory.qty' => 'DistCurrentInventory.qty - ' .$quantity ,
                                    'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty - ' . $quantity,
                                    'DistCurrentInventory.bonus_booking_qty' => 'DistCurrentInventory.bonus_booking_qty - ' . $booking_bonus_qty,
                                    'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                                    'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                    'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                                ),
                                array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                            );
                            $quantity = 0;
                            break;
                        } else {
                            //$quantity = $quantity - $val['DistCurrentInventory']['qty'];
                            $quantity = $quantity - $val['DistCurrentInventory']['booking_qty'];
                            $booking_bonus_qty = $booking_bonus_qty - $val['DistCurrentInventory']['bonus_booking_qty'];
                            $this->DistCurrentInventory->updateAll(
                                array(
                                    'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty - ' . $val['DistCurrentInventory']['booking_qty'],
                                    'DistCurrentInventory.bonus_booking_qty' => 'DistCurrentInventory.bonus_booking_qty - ' . $val['DistCurrentInventory']['bonus_booking_qty'],
                                    'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                                    'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                    'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                                ),
                                array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                            );
                        }
                    }
                }
            }
        } else {
            /* $this->CurrentInventory->updateAll(array('CurrentInventory.qty' => 'CurrentInventory.qty + '.$inventory_info['CurrentInventory']['qty']),array('CurrentInventory.id' => $inventory_info['CurrentInventory']['id'])); */
            if (!empty($inventory_info)) {

                $this->DistCurrentInventory->updateAll(
                    array(
                        'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty + ' . $quantity,
                        'DistCurrentInventory.bonus_booking_qty' => 'DistCurrentInventory.bonus_booking_qty + ' . $booking_bonus_qty,
                        'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                        'DistCurrentInventory.store_id' => $store_id,
                        'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                    ),
                    array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                );
            }
        }
        /*$inventory_info = $this->DistCurrentInventory->find($find_type, array(
            'conditions' => array(
                //'CurrentInventory.qty >=' => 0,
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1,
                'DistCurrentInventory.product_id' => $product_id
            ),
            'order' => array('DistCurrentInventory.qty' => 'desc'),
            'recursive' => -1
        ));*/
        //pr($inventory_info);//die();
        return true;
    }

    // it will be called from Order not from order_details 
    // cal_type=1 means increment and 2 means deduction 

    public function ec_calculation($gross_value, $outlet_id, $terrority_id, $order_date, $cal_type)
    {
        // from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
        // check gross_value >0

        if ($gross_value > 0) {
            $this->loadModel('DistOutlet');
            // from outlet_id, retrieve pharma or non-pharma
            $outlet_info = $this->DistOutlet->find('first', array(
                'conditions' => array(
                    'DistOutlet.id' => $outlet_id
                ),
                'recursive' => -1
            ));

            if (!empty($outlet_info)) {
                $is_pharma_type = $outlet_info['DistOutlet']['is_pharma_type'];
                // from order_date , split month and get month name and compare month table with Order year
                $OrderDate = strtotime($order_date);
                $month = date("n", $OrderDate);
                $year = date("Y", $OrderDate);
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
        // from order_date , split month and get month name and compare month table with Order year
        $OrderDate = strtotime($order_date);
        $month = date("n", $OrderDate);
        $year = date("Y", $OrderDate);
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
    // it will be called from Order not from order_details 
    public function oc_calculation($terrority_id, $gross_value, $outlet_id, $order_date, $order_time, $cal_type)
    {

        // from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
        // check gross_value >0
        if ($gross_value > 0) {
            $this->loadModel('DistOrder');
            // this will be updated monthly , if done then increment else no action
            $month_first_date = date('Y-m-01', strtotime($order_date));
            $count = $this->DistOrder->find('count', array(
                'conditions' => array(
                    'DistOrder.outlet_id' => $outlet_id,
                    'DistOrder.order_date >= ' => $month_first_date,
                    'DistOrder.order_time < ' => $order_time
                )
            ));

            if ($count == 0) {

                $this->loadModel('DistOutlet');
                // from outlet_id, retrieve pharma or non-pharma
                $outlet_info = $this->DistOutlet->find('first', array(
                    'conditions' => array(
                        'DistOutlet.id' => $outlet_id
                    ),
                    'recursive' => -1
                ));

                if (!empty($outlet_info)) {
                    $is_pharma_type = $outlet_info['DistOutlet']['is_pharma_type'];
                    // from order_date , split month and get month name and compare month table with Order year
                    $OrderDate = strtotime($order_date);
                    $month = date("n", $OrderDate);
                    $year = date("Y", $OrderDate);
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

        $this->loadModel('DistOutlet');
        // from outlet_id, retrieve pharma or non-pharma
        $outlet_info = $this->DistOutlet->find('first', array(
            'conditions' => array(
                'DistOutlet.id' => $outlet_id
            ),
            'recursive' => -1
        ));

        if (!empty($outlet_info) && $gross_amount > 0) {
            $bonus_type_id = $outlet_info['DistOutlet']['bonus_type_id'];
            if (($bonus_type_id === NULL) || (empty($bonus_type_id))) {
                // no action 
            } else {
                // from order_date , split month and get month name and compare month table with Order year (get fascal year id)
                $OrderDate = strtotime($order_date);
                $month = date("n", $OrderDate);
                $year = date("Y", $OrderDate);
                $this->loadModel('Month');
                $fasical_info = $this->Month->find('first', array(
                    'conditions' => array(
                        'Month.month' => $month,
                        'Month.year' => $year
                    ),
                    'recursive' => -1
                ));

                if (!empty($fasical_info)) {
                    // check bonus card table , where is_active,and others  and get min qty per Order
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

                    // if exist min qty per Order , then stamp_no=mod(quantity/min qty per Order)
                    if (!empty($bonus_card_info)) {
                        $min_qty_per_Order = $bonus_card_info['BonusCard']['min_qty_per_Order'];
                        if ($min_qty_per_Order && $min_qty_per_Order <= $quantity) {
                            $stamp_no = floor($quantity / $min_qty_per_Order);
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
        $this->loadModel('DistOrder');
        if ($this->request->is('post')) {
            $order_no = $this->request->data['order_no'];
            $sale_type_id = $this->request->data['sale_type'];

            if ($sale_type_id == 1 || $sale_type_id == 3 || $sale_type_id == 4) {
                $order_list = $this->DistOrder->find('list', array(
                    'conditions' => array('DistOrder.dist_order_no' => $order_no),
                    'fields' => array('dist_order_no'),
                    'recursive' => -1
                ));
            } else {
                $order_list = $this->DistOrder->find('list', array(
                    'conditions' => array('DistOrder.dist_order_no' => $order_no),
                    'fields' => array('dist_order_no'),
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
        $this->loadModel('DistStore');
        $this->loadModel('Product');
        $this->loadModel('DistCurrentInventory');
        $distributor_id = $this->request->data['distributor_id'];
        $rs = array(array('id' => '', 'name' => '---- Select Product -----'));
        $store_info = $this->DistStore->find('first', array(
            'conditions' => array(
                'DistStore.dist_distributor_id' => $distributor_id
            ),
            'recursive' => -1
        ));

        if (isset($store_info['DistStore']['id']) && $store_info['DistStore']['id']) {
            $store_id = $store_info['DistStore']['id'];

            if (isset($this->request->data['distributor_id']) && $this->request->data['distributor_id'] != 0) {
                $conditions = array('DistCurrentInventory.store_id' => $store_id, 'inventory_status_id' => 1);
            } else {
                $conditions = array('DistCurrentInventory.store_id' => $store_id, 'DistCurrentInventory.qty > ' => 0, 'inventory_status_id' => 1);
            }

            $products_from_ci = $this->DistCurrentInventory->find('all', array(
                'fields' => array('DISTINCT DistCurrentInventory.product_id'),
                'conditions' => $conditions,
            ));

            $product_ci = array();
            foreach ($products_from_ci as $each_ci) {
                $product_ci[] = $each_ci['DistCurrentInventory']['product_id'];
            }

            $product_ci_in = implode(",", $product_ci);
            $products = $this->Product->find('all', array(
                'conditions' => array('Product.id' => $product_ci), 'order' => array('order' => 'asc'),
                'fields' => array('Product.id as id', 'Product.name as name'),
                'recursive' => -1
            ));

            $data_array = Set::extract($products, '{n}.0');

            if (!empty($products)) {
                echo json_encode(array_merge($rs, $data_array));
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

    public function admin_order_editable($id = null)
    {
        if ($id) {
            $this->DistOrder->id = $id;
            if ($this->DistOrder->id) {
                if ($this->DistOrder->saveField('order_editable', 1)) {
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
        $distributor_id = $this->request->data['distributor_id'];

        $product_details = $this->Product->find('first', array(
            'fields' => array('MIN(Product.product_category_id) as category_id', 'MIN(Product.sales_measurement_unit_id) as measurement_unit_id', 'MIN(MeasurementUnit.name) as measurement_unit_name', 'SUM(DistCurrentInventory.qty) as total_qty'),
            'conditions' => array('Product.id' => $product_id, 'DistStore.dist_distributor_id' => $distributor_id),
            'joins' => array(
                array(
                    'table' => 'measurement_units',
                    'alias' => 'MeasurementUnit',
                    'conditions' => 'MeasurementUnit.id=Product.sales_measurement_unit_id'
                ),
                array(
                    'table' => 'dist_current_inventories',
                    'alias' => 'DistCurrentInventory',
                    'type' => 'Inner',
                    'conditions' => 'DistCurrentInventory.product_id=Product.id'
                ),
                array(
                    'table' => 'dist_stores',
                    'alias' => 'DistStore',
                    'type' => 'Inner',
                    'conditions' => 'DistCurrentInventory.store_id=DistStore.id'
                )
            ),
            'group' => array('Product.id', 'DistStore.id', 'DistStore.dist_distributor_id'),
            'recursive' => -1
        ));
        // pr($product_details);exit;
        $data['category_id'] = $product_details[0]['category_id'];
        $data['measurement_unit_id'] = $product_details[0]['measurement_unit_id'];
        $data['measurement_unit_name'] = $product_details[0]['measurement_unit_name'];
        $data['total_qty'] = $this->unit_convertfrombase($product_id, $data['measurement_unit_id'], $product_details[0]['total_qty']);
        echo json_encode($data);
        $this->autoRender = false;
    }

    public function get_bonus_product()
    {
        $this->LoadModel('Product');
        $this->LoadModel('DistStore');

        $distributor_id = $this->request->data['distributor_id'];
        $order_date = $this->request->data['order_date'] ? date('Y-m-d', strtotime($this->request->data['order_date'])) : date('Y-m-d');
        $store_id = $this->DistStore->find('first', array(
            'fields' => array('DistStore.id'),
            'conditions' => array('DistStore.dist_distributor_id' => $distributor_id),
            'recursive' => -1
        ));

        $product_list = $this->Product->find('all', array(
            'fields' => array('Product.id', 'Product.name'),
            'joins' => array(
                array(
                    'table' => 'dist_current_inventories',
                    'alias' => 'DistCurrentInventory',
                    'type' => 'Inner',
                    'conditions' => 'DistCurrentInventory.product_id=Product.id'
                ),
                array(
                    'table' => 'dist_open_combination_products',
                    'alias' => 'OpenCombinationProduct',
                    'type' => 'Inner',
                    'conditions' => 'OpenCombinationProduct.product_id=Product.id'
                ),
                array(
                    'table' => 'dist_open_combinations',
                    'alias' => 'OpenDistSrCombination',
                    'type' => 'Inner',
                    'conditions' => 'OpenDistSrCombination.id=OpenCombinationProduct.combination_id
                    and is_bonus=1 and \'' . $order_date . '\'BETWEEN OpenDistSrCombination.start_date AND OpenDistSrCombination.end_date
                    '
                ),
            ),
            'conditions' => array(
                'DistCurrentInventory.store_id' => $store_id['DistStore']['id'],
                'DistCurrentInventory.qty >' => 0,
                /*'OpenDistSrCombination.start_date <=' => $order_date,
                'OpenDistSrCombination.end_date >=' => $order_date,*/
            ),
            'group' => array('Product.id', 'Product.name'),
            'recursive' => -1
        ));
        //pr($product_list);die();
        echo json_encode($product_list);
        $this->autoRender = false;
    }

    public function get_product_price_id($product_id, $dist_sr_product_prices, $all_product_id)
    {
        // echo $product_id.'--'.$dist_sr_product_prices.'<br>';
        $this->LoadModel('DistSrProductCombination');
        $this->LoadModel('DistSrCombination');
        $data = array();
        $product_price = $this->DistSrProductCombination->find('first', array(
            'conditions' => array(
                'DistSrProductCombination.product_id' => $product_id,
                'DistSrProductCombination.price' => $dist_sr_product_prices,
                'DistSrProductCombination.effective_date <=' => $this->current_date(),
            ),
            'order' => array('DistSrProductCombination.id' => 'DESC'),
            'recursive' => -1
        ));

        // pr($product_price);exit;
        // echo $this->DistSrProductCombination->getLastquery().'<br>';
        if ($product_price) {
            $is_combine = 0;
            if ($product_price['DistSrProductCombination']['combination_id'] != 0) {
                $DistSrCombination = $this->DistSrCombination->find('first', array(
                    'conditions' => array('DistSrCombination.id' => $product_price['DistSrProductCombination']['combination_id']),
                    'recursive' => -1
                ));
                $DistSrCombination_product = explode(',', $DistSrCombination['DistSrCombination']['all_products_in_combination']);
                foreach ($DistSrCombination_product as $DistSrCombination_prod) {
                    if ($product_id != $DistSrCombination_prod && in_array($DistSrCombination_prod, $all_product_id)) {
                        $data['combination_id'] = $product_price['DistSrProductCombination']['combination_id'];
                        $data['product_price_id'] = $product_price['DistSrProductCombination']['id'];
                        $is_combine = 1;
                        break;
                    }
                }
            }
            if ($is_combine == 0) {
                $product_price = $this->DistSrProductCombination->find('first', array(
                    'conditions' => array(
                        'DistSrProductCombination.product_id' => $product_id,
                        'DistSrProductCombination.price' => $dist_sr_product_prices,
                        'DistSrProductCombination.effective_date <=' => $this->current_date(),
                        'DistSrProductCombination.parent_slab_id' => 0
                    ),
                    'order' => array('DistSrProductCombination.id DESC'),
                    'recursive' => -1
                ));
                $data['combination_id'] = '';
                $data['product_price_id'] = $product_price['DistSrProductCombination']['id'];
            }
            //pr($data);die();
            return $data;
        } else {
            $data['combination_id'] = '';
            $data['product_price_id'] = '';
            //pr($data);die();
            return $data;
        }
    }

    function get_dist_list_by_office_id()
    {

        $office_id = $this->request->data['office_id'];
        $order_date = $this->request->data['order_date'];
        $output = "<option value=''>--- Select Distributor ---</option>";

        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        if ($user_group_id == 1029) {
            $dist_tso_info = $this->DistTso->find('first', array(
                'conditions' => array('DistTso.user_id' => $user_id),
                'recursive' => -1,
            ));
            $dist_tso_id = $dist_tso_info['DistTso']['id'];
            if ($order_date && $office_id) {
                $order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));
                $qry = "select distinct dist_distributor_id from dist_tso_mapping_histories
                      where dist_tso_id=$dist_tso_id and is_change=1 and 
                        '" . $order_date . "' between effective_date and 
                        case 
                        when end_date is not null then 
                         end_date
                        else 
                        getdate()
                        end";
            }
        } elseif ($user_group_id == 1028) {
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
            if ($order_date && $office_id) {
                $order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));
                $qry = "select distinct dist_distributor_id from dist_tso_mapping_histories
                      where dist_tso_id IN $dist_tso_id and is_change=1 and 
                        '" . $order_date . "' between effective_date and 
                        case 
                        when end_date is not null then 
                         end_date
                        else 
                        getdate()
                        end";
            }
        } elseif ($user_group_id == 1034) {
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');
            $distributor = $this->DistUserMapping->find('first', array(
                'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
            if ($order_date && $office_id) {
                $order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));
                $qry = "select distinct dist_distributor_id from dist_tso_mapping_histories
                      where dist_distributor_id=$distributor_id and is_change=1 and 
                        '" . $order_date . "' between effective_date and 
                        case 
                        when end_date is not null then 
                         end_date
                        else 
                        getdate()
                        end";
            }
        } else {
            if ($order_date && $office_id) {
                $order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));
                $qry = "select distinct dist_distributor_id from dist_tso_mapping_histories
                      where office_id=$office_id and is_change=1 and 
                        '" . $order_date . "' between effective_date and 
                        case 
                        when end_date is not null then 
                         end_date
                        else 
                        getdate()
                        end";
            }
        }

        $this->loadModel('DistTsoMappingHistory');
        $this->loadModel('DistDistributor');

        if ($order_date && $office_id) {
            $dist_data = $this->DistTsoMappingHistory->query($qry);
            $dist_ids = array();

            foreach ($dist_data as $k => $v) {
                $dist_ids[] = $v[0]['dist_distributor_id'];
            }

            $conditions = array(
                'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.id' => $dist_ids, 'DistDistributor.is_active' => 1), 'order' => array('DistDistributor.name' => 'asc'), 'recursive' => 0
            );

            if ($office_id) {
                $dist_all = $this->DistDistributor->find('all', $conditions);
            }


            if ($dist_all) {
                foreach ($dist_all as $key => $data) {
                    $k = $data['DistDistributor']['id'];
                    $v = $data['DistDistributor']['name'];
                    $output .= "<option value='$k'>$v</option>";
                }
            }
        }
        //spr($output);die();
        echo $output;
        $this->autoRender = false;
    }

    function get_tso_list_by_office_id()
    {

        $office_id = $this->request->data['office_id'];
        $order_date = $this->request->data['order_date'];
        $output = "<option value=''>--- Select Distributor ---</option>";

        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        if ($user_group_id == 1029) {
            $dist_tso_info = $this->DistTso->find('first', array(
                'conditions' => array('DistTso.user_id' => $user_id),
                'recursive' => -1,
            ));
            $dist_tso_id = $dist_tso_info['DistTso']['id'];
            if ($order_date && $office_id) {
                $order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));
                $qry = "select distinct dist_distributor_id from dist_tso_mapping_histories
                      where dist_tso_id=$dist_tso_id and is_change=1 and 
                        '" . $order_date . "' between effective_date and 
                        case 
                        when end_date is not null then 
                         end_date
                        else 
                        getdate()
                        end";
            }
        } elseif ($user_group_id == 1028) {
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
            if ($order_date && $office_id) {
                $order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));
                $qry = "select distinct dist_distributor_id from dist_tso_mapping_histories
                      where dist_tso_id IN $dist_tso_id and is_change=1 and 
                        '" . $order_date . "' between effective_date and 
                        case 
                        when end_date is not null then 
                         end_date
                        else 
                        getdate()
                        end";
            }
        } elseif ($user_group_id == 1034) {
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');
            $distributor = $this->DistUserMapping->find('first', array(
                'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];
            if ($order_date && $office_id) {
                $order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));
                $qry = "select distinct dist_distributor_id from dist_tso_mapping_histories
                      where dist_distributor_id=$distributor_id and is_change=1 and 
                        '" . $order_date . "' between effective_date and 
                        case 
                        when end_date is not null then 
                         end_date
                        else 
                        getdate()
                        end";
            }
        } else {
            if ($order_date && $office_id) {
                $order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));
                $qry = "select distinct dist_distributor_id from dist_tso_mapping_histories
                      where office_id=$office_id and is_change=1 and 
                        '" . $order_date . "' between effective_date and 
                        case 
                        when end_date is not null then 
                         end_date
                        else 
                        getdate()
                        end";
            }
        }

        $this->loadModel('DistTsoMappingHistory');
        $this->loadModel('DistDistributor');

        if ($order_date && $office_id) {
            $dist_data = $this->DistTsoMappingHistory->query($qry);
            $dist_ids = array();

            foreach ($dist_data as $k => $v) {
                $dist_ids[] = $v[0]['dist_distributor_id'];
            }

            $conditions = array(
                'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.id' => $dist_ids, 'DistDistributor.is_active' => 1), 'order' => array('DistDistributor.name' => 'asc'), 'recursive' => 0
            );

            if ($office_id) {
                $dist_all = $this->DistDistributor->find('all', $conditions);
            }


            if ($dist_all) {
                foreach ($dist_all as $key => $data) {
                    $k = $data['DistDistributor']['id'];
                    $v = $data['DistDistributor']['name'];
                    $output .= "<option value='$k'>$v</option>";
                }
            }
        }
        //spr($output);die();
        echo $output;
        $this->autoRender = false;
    }

    function get_dist_list_by_office_id_and_date_range()
    {

        $office_id = $this->request->data['office_id'];
        $order_date_from = $this->request->data['order_date_from'];
        $order_date_to = $this->request->data['order_date_to'];
        $dist_id = $this->request->data['distributor_id'];
        $output = "<option value=''>--- Select Distributor ---</option>";

        /*$company_id = $this->Session->read('Office.company_id');
        $user_group_id=$this->Session->read('Office.group_id');*/

        $this->loadModel('DistTsoMappingHistory');
        $this->loadModel('DistDistributor');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistAreaExecutive');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        if ($order_date_from && $office_id && $order_date_to) {

            $order_date_from = date("Y-m-d", strtotime($this->request->data['order_date_from']));
            $order_date_to = date("Y-m-d", strtotime($this->request->data['order_date_to']));
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
                $dist_conditions = array('DistOrder.distributor_id' => array_keys($tso_dist_list), 'DistOrder.order_date >=' => $order_date_from, 'DistOrder.order_date <=' => $order_date_to, 'DistDistributor.is_active' => 1);
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];

                $dist_conditions = array('DistOrder.distributor_id' => $distributor_id, 'DistOrder.order_date >=' => $order_date_from, 'DistOrder.order_date <=' => $order_date_to, 'DistDistributor.is_active' => 1);
            } else {
                $dist_conditions = array('DistOrder.office_id' => $office_id, 'DistOrder.order_date >=' => $order_date_from, 'DistOrder.order_date <=' => $order_date_to, 'DistDistributor.is_active' => 1);
            }
            $distDistributors = $this->DistDistributor->find('list', array(
                'conditions' => $dist_conditions,
                'joins' => array(array('table' => 'dist_Orders', 'alias' => 'DistOrder', 'conditions' => 'DistOrder.distributor_id=DistDistributor.id')),
                'order' => array('DistDistributor.name' => 'asc'),
            ));


            if ($distDistributors) {
                $selected = "";
                foreach ($distDistributors as $key => $data) {
                    $selected = ($key == $dist_id) ? "selected" : "";
                    $output .= "<option $selected value='$key'>$data</option>";
                }
            }
        }
        echo $output;
        $this->autoRender = false;
    }

    function get_sr_list_by_distributot_id()
    {
        $this->LoadModel('Territory');
        $distributor_id = $this->request->data['distributor_id'];
        $order_date = $this->request->data['order_date'];
        $output = "<option value=''>--- Select SR ---</option>";
        $this->loadModel('DistSrRouteMappingHistory');
        if ($distributor_id) {
            $order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));

            /*$sr = $this->DistSalesRepresentative->find('list', array(
                'conditions' => array('DistSalesRepresentative.dist_distributor_id' => $distributor_id,'DistSalesRepresentative.is_active' => 1),'order' => array('DistSalesRepresentative.name' => 'asc')
            ));*/
            $sr = $this->DistSrRouteMappingHistory->find('all', array(
                'conditions' => array(
                    'DistSrRouteMappingHistory.dist_distributor_id' => $distributor_id,
                    //'DistSrRouteMappingHistory.effective_date >=' => $order_date,
                    'DistSrRouteMappingHistory.effective_date <=' => $order_date,
                ),
                'fields' => array('DistSalesRepresentative.id', 'DistSalesRepresentative.name'),
                'group' => array('DistSalesRepresentative.id', 'DistSalesRepresentative.name'),
                'order' => array('DistSalesRepresentative.name' => 'asc')
            ));

            if ($sr) {
                foreach ($sr as $key => $data) {
                    $id = $data['DistSalesRepresentative']['id'];
                    $name = $data['DistSalesRepresentative']['name'];
                    $output .= "<option value='$id'>$name</option>";
                }
            }
        }

        echo $output;
        $this->autoRender = false;
    }

    function get_sr_list_by_distributot_id_date_range()
    {
        $this->LoadModel('Territory');
        $distributor_id = $this->request->data['distributor_id'];
        //$order_date = $this->request->data['order_date'];
        $output = "<option value=''>--- Select SR ---</option>";
        /***************** this is for find SR in daterange *********************/
        /*if ($distributor_id) {
            //$order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));
            $order_date_from = date("Y-m-d H:i:s", strtotime($this->request->data['order_date_from']));
            $order_date_to = date("Y-m-d H:i:s", strtotime($this->request->data['order_date_to']));

            $sr = $this->DistSalesRepresentative->find('list', array(
                'conditions' => array(
                    'DistOrder.distributor_id' => $distributor_id,
                    'DistOrder.order_date >='=>$order_date_from,
                    'DistOrder.order_date <='=>$order_date_to,
                    ),
                'joins'=>array(array('table'=>'dist_Orders','alias'=>'DistOrder','conditions'=>'DistOrder.sr_id=DistSalesRepresentative.id')), 
                'order' => array('DistSalesRepresentative.name' => 'asc')
            ));

            if ($sr) {
                foreach ($sr as $key => $data) {
                    $output .= "<option value='$key'>$data</option>";
                }
            }
        }*/
        /***************** this is for find SR in daterange end *********************/
        if ($distributor_id) {
            //$order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));

            $sr = $this->DistSalesRepresentative->find('list', array(
                'conditions' => array('DistSalesRepresentative.dist_distributor_id' => $distributor_id, 'DistSalesRepresentative.is_active' => 1), 'order' => array('DistSalesRepresentative.name' => 'asc')
            ));

            if ($sr) {
                foreach ($sr as $key => $data) {
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
            $market = $this->DistMarket->find('list', array(
                'conditions' => array('DistMarket.thana_id' => $thana_id)
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
            $result = $this->DistOutlet->find('first', array(
                'fields' => array('is_within_group'),
                'conditions' => array('DistOutlet.id' => $outlet_id),
                'recursive' => -1
            ));
            if ($result) {
                return $result['DistOutlet']['is_within_group'];
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

    public function get_market()
    {
        $territory_id = $this->request->data['territory_id'];
        $rs = array(array('id' => '', 'name' => '---- Select Market -----'));
        $market_list = $this->DistMarket->find('all', array(
            'fields' => array('DistMarket.id as id', 'DistMarket.name as name'),
            'conditions' => array(
                'DistMarket.territory_id' => $territory_id
            ),
            'order' => array('DistMarket.name' => 'asc'),
            'recursive' => -1
        ));
        $data_array = Set::extract($market_list, '{n}.0');
        if (!empty($market_list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_outlet()
    {
        //$company_id = $this->Session->read('Office.company_id');
        //$user_group_id = $this->Session->read('Office.group_id');

        $rs = array(array('id' => '', 'name' => '---- Select Outlet -----'));
        $market_id = $this->request->data['market_id'];
        $outlet_list = $this->DistOutlet->find('all', array(
            'fields' => array('DistOutlet.id as id', 'DistOutlet.name as name'),
            'conditions' => array(
                'DistOutlet.dist_market_id' => $market_id,
                //'DistOutlet.company_id'=>$company_id,
            ),
            'order' => array('DistOutlet.name' => 'asc'),
            'recursive' => -1
        ));
        $data_array = Set::extract($outlet_list, '{n}.0');
        if (!empty($outlet_list)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function get_territory_thana_info()
    {
        $market_id = $this->request->data['market_id'];
        $distributor_id = $this->request->data['distributor_id'];
        $office_id = $this->request->data['office_id'];
        $order_date = $this->request->data['order_date'];

        $output = "";
        if ($market_id) {
            $info = $this->DistMarket->find('first', array(
                'conditions' => array('DistMarket.id' => $market_id),
                'recursive' => -1
            ));

            $territory_id = $info['DistMarket']['territory_id'];
            $thana_id = $info['DistMarket']['thana_id'];

            $this->loadModel('DistTsoMappingHistory');


            if ($order_date && $distributor_id) {

                $order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));
                $qry = "select distinct dist_tso_id from dist_tso_mapping_histories
                      where office_id=$office_id and is_change=1 and dist_distributor_id=$distributor_id and 
                        '" . $order_date . "' between effective_date and 
                        case 
                        when end_date is not null then 
                         end_date
                        else 
                        getdate()
                        end";

                $dist_data = $this->DistTsoMappingHistory->query($qry);
                //pr($dist_data);
                $dist_ids = array();

                foreach ($dist_data as $k => $v) {
                    $dist_ids[] = $v[0]['dist_tso_id'];
                }
                $tso_id = "";
                if ($dist_ids) {
                    $tso_id = $dist_ids[0];
                }


                $qry2 = "select distinct dist_area_executive_id from dist_tso_histories where tso_id=$tso_id and (is_added=1 or is_transfer=1) and 
                    '" . $order_date . "' between effective_date and 
                    case 
                    when effective_end_date is not null then 
                     effective_end_date
                    else 
                    getdate()
                    end";

                $ae_data = $this->DistTsoMappingHistory->query($qry2);
                //pr($ae_data);die();
                $ae_ids = array();

                foreach ($ae_data as $k => $v) {
                    $ae_ids[] = $v[0]['dist_area_executive_id'];
                }
                $ae_id = "";

                if ($ae_ids) {
                    $ae_id = $ae_ids[0];
                }

                echo $territory_id . "||" . $thana_id . "||" . $ae_id . "||" . $tso_id;
            } else {
                echo $territory_id . "||" . $thana_id;
            }
        } else {
            echo "";
        }

        $this->autoRender = false;
    }

    public function order_reference_no_validation()
    {
        $this->loadModel('DistOrder');
        if ($this->request->is('post')) {
            $order_reference_no = $this->request->data['order_reference_no'];
            $office_id = $this->request->data['office_id'];
            $order_list = array();

            if ($office_id && $order_reference_no) {
                $order_list = $this->DistOrder->find('list', array(
                    'conditions' => array('DistOrder.order_reference_no' => $order_reference_no, 'DistOrder.office_id' => $office_id),
                    'fields' => array('order_reference_no'),
                    'recursive' => -1
                ));
            }
            $order_exist = count($order_list);
            echo json_encode($order_exist);
        }

        $this->autoRender = false;
    }

    public function get_route_list()
    {
        //$company_id = $this->Session->read('Office.company_id');
        //$user_group_id = $this->Session->read('Office.group_id');
        $this->loadModel('DistRouteMapping');
        $distributor_id = $this->request->data['distributor_id'];
        $output = "<option value=''>--- Select ---</option>";
        if ($distributor_id) {
            $route = $this->DistRouteMapping->find('all', array(
                'conditions' => array('DistRouteMapping.dist_distributor_id' => $distributor_id),
                'recursive' => 0
            ));

            if ($route) {
                foreach ($route as $key => $data) {
                    $k = $data['DistRoute']['id'];
                    $v = $data['DistRoute']['name'];
                    $output .= "<option value='$k'>$v</option>";
                }
            }
        }
        echo $output;
        //pr($output);die();
        $this->autoRender = false;
    }

    public function get_route_list_from_order_date_backup_23_10_2019()
    {
        $this->loadModel('DistRouteMapping');
        $distributor_id = $this->request->data['distributor_id'];
        $sr_id = $this->request->data['sr_id'];
        $office_id = $this->request->data['office_id'];
        $order_date = $this->request->data['order_date'];
        $order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));

        $output = "<option value=''>--- Select ---</option>";
        if ($order_date && $sr_id) {

            $qry = "select distinct dist_route_id from dist_sr_route_mapping_histories
                    where dist_sr_id=$sr_id and dist_distributor_id=$distributor_id and is_change=1 and 
                    '" . $order_date . "' between effective_date and 
                    case 
                    when end_date is not null then 
                     end_date
                    else 
                    getdate()
                    end";

            $route_data = $this->DistRouteMapping->query($qry);
            $route_ids = array();

            foreach ($route_data as $k => $v) {
                $route_ids[] = $v[0]['dist_route_id'];
            }


            $route = $this->DistRoute->find('all', array(
                'conditions' => array('DistRoute.id' => $route_ids),
                'order' => array('DistRoute.name'),
                'recursive' => 0
            ));

            if ($route) {
                foreach ($route as $key => $data) {
                    $k = $data['DistRoute']['id'];
                    $v = $data['DistRoute']['name'];
                    $output .= "<option value='$k'>$v</option>";
                }
            }
        }
        echo $output;
        $this->autoRender = false;
    }
    public function get_route_list_from_order_date()
    {
        //$this->loadModel('DistRouteMapping');
        $this->loadModel('DistSrRouteMapping');
        $distributor_id = $this->request->data['distributor_id'];
        $sr_id = $this->request->data['sr_id'];
        $office_id = $this->request->data['office_id'];
        //$order_date = $this->request->data['order_date'];
        $order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));
        $output = "<option value=''>--- Select ---</option>";
        if ($distributor_id) {
            /* $route = $this->DistRouteMapping->find('all', array(
                'conditions' => array('DistRouteMapping.dist_distributor_id' => $distributor_id),
                'recursive' => 0
            ));*/

            $route = $this->DistSrRouteMapping->find('all', array(
                'conditions' => array(
                    'DistSrRouteMapping.dist_distributor_id' => $distributor_id,
                    'DistSrRouteMapping.dist_sr_id' => $sr_id,
                    'DistSrRouteMapping.office_id' => $office_id,
                    'DistSrRouteMapping.effective_date <=' => $order_date,
                ),
                'recursive' => 0
            ));

            if ($route) {
                foreach ($route as $key => $data) {
                    $k = $data['DistRoute']['id'];
                    $v = $data['DistRoute']['name'];
                    $output .= "<option value='$k'>$v</option>";
                }
            }
        }
        echo $output;
        //pr($output);die();
        $this->autoRender = false;
    }

    function get_dist_list_by_office_id_for_adjustment()
    {

        $office_id = $this->request->data['office_id'];
        $output = "<option value=''>--- Select Distributor ---</option>";


        $conditions = array(
            'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.is_active' => 1), 'order' => array('DistDistributor.name' => 'asc'),
        );

        if ($office_id) {
            $dist_list = $this->DistDistributor->find('list', $conditions);

            if ($dist_list) {
                foreach ($dist_list as $key => $data) {
                    $output .= "<option value='$key'>$data</option>";
                }
            }
        }
        echo $output;
        $this->autoRender = false;
    }
    /*
        get territory and thana id by distributor id
     */
    function get_territory_and_thana_id_by_distributor_id()
    {
        $this->loadModel('DistOutletMap');
        $dist_id = $this->request->data['dist_id'];
        $territory_thana_id = $this->DistOutletMap->find('first', array(

            'conditions' => array('DistOutletMap.dist_distributor_id' => $dist_id),
            'joins' => array(
                array(
                    'table' => 'markets',
                    'alias' => 'Market',
                    'type' => 'Inner',
                    'conditions' => 'DistOutletMap.market_id=Market.id'
                )
            ),
            'fields' => array('Market.id', 'Market.territory_id', 'Market.thana_id'),
            'recursive' => -1
        ));
        //echo $this->DistOutletMap->getLastquery();
        echo json_encode($territory_thana_id['Market']);
        $this->autoRender = false;
    }

    function get_territory_and_thana_id_by_route_id()
    {
        $this->loadModel('DistRoute');
        $this->loadModel('ThanaTerritory');
        $dist_route_id = $this->request->data['dist_route_id'];
        $territory_thana_id = $this->DistRoute->find('first', array(
            'conditions' => array('DistRoute.id' => $dist_route_id),
            'fields' => array('DistRoute.thana_id'),
            'recursive' => -1
        ));
        $territory = $this->ThanaTerritory->find('first', array(
            'conditions' => array('ThanaTerritory.thana_id' => $territory_thana_id['DistRoute']['thana_id'])
        ));
        $territory_id =  $territory['ThanaTerritory']['territory_id'];
        $data['territory_id'] = $territory_id;
        $data['thana_id'] = $territory_thana_id['DistRoute']['thana_id'];
        //echo $this->DistOutletMap->getLastquery();
        echo json_encode($data);
        $this->autoRender = false;
    }
    /*------------------ Last Order Info :Start ---------------------------*/
    function get_last_order_info()
    {
        $office_id = $this->request->data['office_id'];
        $last_Order = $this->DistOrder->find('first', array(
            'conditions' => array('DistOrder.office_id' => $office_id),
            /*'recursive'=>-1,*/
            'order' => array('DistOrder.id' => 'DESC')
        ));
        $output = '';
        $output .= '<p>Distributor : ' . $last_Order['Distributor']['name'] . '</p>';
        $output .= '<p>Order no : ' . $last_Order['DistOrder']['order_reference_no'] . '</p>';
        $output .= '<p>Order value : ' . $last_Order['DistOrder']['gross_value'] . '</p>';
        echo $output;
        $this->autoRender = false;
    }
    /*------------------ Last Order Info :END -----------------------------*/

    /*--------------------- Get Sr Name by sr id : STart -------------------*/
    public function get_sr_name_by_sr_id($sr_id)
    {
        $this->LoadModel('DistSalesRepresentative');
        $sr_data = $this->DistSalesRepresentative->find('first', array(
            'conditions'    => array('DistSalesRepresentative.id' => $sr_id),
            'recursive' => -1
        ));
        return $sr_data['DistSalesRepresentative']['name'];
    }
    /*--------------------- Get Sr Name by sr id : END ---------------------*/

    /*--------------------- Get Distributor Name by distributor id : STart -------------------*/
    public function get_dist_name_by_dist_id($dist_id)
    {
        $this->LoadModel('DistDistributor');
        $sr_data = $this->DistDistributor->find('first', array(
            'conditions'    => array('DistDistributor.id' => $dist_id, 'DistDistributor.is_active' => 1),
            'recursive' => -1
        ));
        return $sr_data['DistDistributor']['name'];
    }
    /*--------------------- Get Distributor Name by distributor id : END ---------------------*/

    /*--------------------- Get outlet-market-thana Name by outlet,market,thana id : STart -------------------*/
    public function get_outlet_market_thana_name_by_outlet_market_thana_id($outlet_id, $market_id, $thana_id)
    {
        $this->LoadModel('DistDistributor');
        $sql = "select do.name as outlet_name,dm.name as market_name,th.name as thana_name from dist_outlets do,dist_markets dm,thanas th 
        where do.id=$outlet_id and dm.id=$market_id and th.id=$thana_id";
        $outlet_market_thana_data = $this->DistDistributor->query($sql);

        return $outlet_market_thana_data[0][0]['outlet_name'] . '-' . $outlet_market_thana_data[0][0]['market_name'] . '-' . $outlet_market_thana_data[0][0]['thana_name'];
    }
    /*--------------------- Get outlet-market-thana Name by outlet,market,thana : END ---------------------*/

    public function getOfficeList()
    {
        //$company_id = $this->Session->read('Office.company_id');
        //$user_group_id=$this->Session->read('Office.group_id');
        //echo "Here";
        $parent_office_id = $this->Session->read('Office.parent_office_id');
        $this->loadmodel('Office');
        if ($parent_office_id == 0) {
            $conditions = array('office_type_id' => 2);
        } else {
            $conditions = array('id' => $this->Session->read('Office.id'));
        }

        $offices = $this->Office->find('list', array('conditions' => $conditions));
        $lists = array_keys($offices);
        return $lists;
        // pr($offices);die();
    }

    public function get_distributer()
    {
        //$company_id=$this->Session->read('Office.company_id');
        //$user_group_id=$this->Session->read('Office.group_id');
        $rs = array('------ Select ------');
        $office_id = $this->request->data['office_id'];

        $this->loadmodel('DistDistributor');

        $distributers = $this->DistDistributor->find('all', array(
            'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.is_active' => 1),
            'fields' => array('DistDistributor.id', 'DistDistributor.name'),
            'recursive' => -1
        ));


        $data_array = array();
        foreach ($distributers as $key => $value) {
            $data_array[] = array(
                'id' => $value['DistDistributor']['id'],
                'name' => $value['DistDistributor']['name'],
            );
        }
        if (!empty($distributers)) {
            echo json_encode(array_merge($rs, $data_array));
        } else {
            echo json_encode($rs);
        }
        $this->autoRender = false;
    }

    public function check_discount_amount()
    {

        $this->loadmodel('DistDiscount');
        $this->loadmodel('DistDiscountDetail');

        $order_date = date('Y-m-d', strtotime($this->request->data['order_date']));
        $gross_value = $this->request->data['gross_value'];

        $discount_details = $this->DistDiscountDetail->find('first', array(
            'conditions' => array(
                'DistDiscountDetail.date_from <=' => $order_date,
                'DistDiscountDetail.date_to  >=' => $order_date,
                'DistDiscountDetail.memo_value  <=' => $gross_value,
                'DistDiscount.is_active ' => 1,
            ),
            'order' => array('DistDiscountDetail.id DESC'),

        ));
        //echo $this->DistDiscountDetail->getLastquery();
        //pr($discount_details);die();
        $data_array = array();
        if (!empty($discount_details)) {
            $discount = $discount_details['DistDiscountDetail']['discount_percent'];
            $discount_type = $discount_details['DistDiscountDetail']['discount_type'];

            $data_array = array(
                'discount_percent' => $discount,
                'discount_type' => $discount_type,
            );
            echo json_encode($data_array);
        } else {
            $data_array = array(
                'discount_percent' => 0,
                'discount_type' => 0,
            );
            echo json_encode($data_array);
        }
        $this->autoRender = false;
    }

    /*************************** OC Calculations *******************************************/
    // it will be called from order not from order_details 
    public function dist_oc_calculation($sr_code, $office_id, $gross_value, $outlet_id, $order_date, $order_time, $cal_type)
    {

        // from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
        // check gross_value >0
        if ($gross_value > 0) {
            $this->loadModel('DistOrder');
            // this will be updated monthly , if done then increment else no action
            $month_first_date = date('Y-m-01', strtotime($order_date));
            $count = $this->DistOrder->find('count', array(
                'conditions' => array(
                    'DistOrder.outlet_id' => $outlet_id,
                    'DistOrder.order_date >= ' => $month_first_date,
                    'DistOrder.order_time < ' => $order_time
                )
            ));

            if ($count == 0) {

                $this->loadModel('DistOutlet');
                // from outlet_id, retrieve pharma or non-pharma
                $outlet_info = $this->DistOutlet->find('first', array(
                    'conditions' => array(
                        'DistOutlet.id' => $outlet_id
                    ),
                    'recursive' => -1
                ));

                if (!empty($outlet_info)) {
                    $is_pharma_type = $outlet_info['DistOutlet']['is_pharma_type'];
                    // from order_date , split month and get month name and compare month table with order year
                    $orderDate = strtotime($order_date);
                    $month = date("n", $orderDate);
                    $year = date("Y", $orderDate);
                    $order_date = date('Y-m-d', $orderDate);
                    $this->loadModel('Month');
                    $this->loadModel('FiscalYear');
                    // from outlet_id, retrieve pharma or non-pharma
                    $fasical_month = $this->Month->find('first', array(
                        'conditions' => array(
                            'Month.month' => $month,
                            /* 'Month.year' => $year */
                        ),
                        'recursive' => -1
                    ));
                    $fasical_info = $this->FiscalYear->find('first', array(
                        'conditions' => array(
                            'FiscalYear.start_date <=' => $order_date,
                            'FiscalYear.end_date >=' => $order_date,
                        ),
                        'recursive' => -1
                    ));

                    if (!empty($fasical_info)) {
                        $this->loadModel('DistSaleTargetMonth');
                        if ($cal_type == 1) {
                            if ($is_pharma_type == 1) {
                                $update_fields_arr = array('DistSaleTargetMonth.outlet_coverage_pharma_achievement' => "DistSaleTargetMonth.outlet_coverage_pharma_achievement+1");
                            } else if ($is_pharma_type == 0) {
                                $update_fields_arr = array('DistSaleTargetMonth.outlet_coverage_non_pharma_achievement' => "DistSaleTargetMonth.outlet_coverage_non_pharma_achievement+1");
                            }
                        } else {
                            if ($is_pharma_type == 1) {
                                $update_fields_arr = array('DistSaleTargetMonth.outlet_coverage_pharma_achievement' => "DistSaleTargetMonth.outlet_coverage_pharma_achievement-1");
                            } else if ($is_pharma_type == 0) {
                                $update_fields_arr = array('DistSaleTargetMonth.outlet_coverage_non_pharma_achievement' => "DistSaleTargetMonth.outlet_coverage_non_pharma_achievement-1");
                            }
                        }

                        $conditions_arr = array('DistSaleTargetMonth.product_id' => 0, 'DistSaleTargetMonth.dist_sales_representative_code' => $sr_code, 'DistSaleTargetMonth.aso_id' => $office_id, 'DistSaleTargetMonth.target_type' => 5, 'DistSaleTargetMonth.fiscal_year_id' => $fasical_info['FiscalYear']['id'], 'DistSaleTargetMonth.month_id' => $fasical_month['Month']['id']);
                        $this->DistSaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
                    }
                }
            }
        }
    }

    /*************************** EC Calculations *******************************************/
    public function dist_ec_calculation($gross_value, $outlet_id, $sr_code, $office_id, $order_date, $cal_type)
    {
        // from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
        // check gross_value >0

        if ($gross_value > 0) {
            $this->loadModel('DistOutlet');
            // from outlet_id, retrieve pharma or non-pharma
            $outlet_info = $this->DistOutlet->find('first', array(
                'conditions' => array(
                    'DistOutlet.id' => $outlet_id
                ),
                'recursive' => -1
            ));

            if (!empty($outlet_info)) {
                $is_pharma_type = $outlet_info['DistOutlet']['is_pharma_type'];
                // from order_date , split month and get month name and compare month table with order year
                $orderDate = strtotime($order_date);
                $month = date("n", $orderDate);
                $year = date("Y", $orderDate);
                $order_date = date('Y-m-d', $orderDate);
                $this->loadModel('Month');
                $this->loadModel('FiscalYear');

                // from outlet_id, retrieve pharma or non-pharma
                $fasical_month = $this->Month->find('first', array(
                    'conditions' => array(
                        'Month.month' => $month,
                        /* 'Month.year' => $year */
                    ),
                    'recursive' => -1
                ));
                $fasical_info = $this->FiscalYear->find('first', array(
                    'conditions' => array(
                        'FiscalYear.start_date <=' => $order_date,
                        'FiscalYear.end_date >=' => $order_date,
                    ),
                    'recursive' => -1
                ));

                if (!empty($fasical_info)) {
                    $this->loadModel('DistSaleTargetMonth');
                    if ($cal_type == 1) {
                        if ($is_pharma_type == 1) {
                            $update_fields_arr = array('DistSaleTargetMonth.effective_call_pharma_achievement' => "DistSaleTargetMonth.effective_call_pharma_achievement+1");
                        } else if ($is_pharma_type == 0) {
                            $update_fields_arr = array('DistSaleTargetMonth.effective_call_non_pharma_achievement' => "DistSaleTargetMonth.effective_call_non_pharma_achievement+1");
                        }
                    } else {
                        if ($is_pharma_type == 1) {
                            $update_fields_arr = array('DistSaleTargetMonth.effective_call_pharma_achievement' => "DistSaleTargetMonth.effective_call_pharma_achievement-1");
                        } else if ($is_pharma_type == 0) {
                            $update_fields_arr = array('DistSaleTargetMonth.effective_call_non_pharma_achievement' => "DistSaleTargetMonth.effective_call_non_pharma_achievement-1");
                        }
                    }

                    $conditions_arr = array('DistSaleTargetMonth.product_id' => 0, 'DistSaleTargetMonth.dist_sales_representative_code' => $sr_code, 'DistSaleTargetMonth.aso_id' => $office_id, 'DistSaleTargetMonth.target_type' => 5, 'DistSaleTargetMonth.fiscal_year_id' => $fasical_info['FiscalYear']['id'], 'DistSaleTargetMonth.month_id' => $fasical_month['Month']['id']);

                    $this->DistSaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
                }
            }
        }
    }
    /********************************* Sales Target Calculations **************************************************/
    // it will be called from  order_details 
    public function dist_sales_calculation($product_id, $sr_code, $office_id, $quantity, $gross_value, $order_date, $cal_type)
    {
        // from so_id , retrieve aso_id,terrority_id : no need as we have territory_id
        // from order_date , split month and get month name and compare month table with order year
        $orderDate = strtotime($order_date);
        $month = date("n", $orderDate);
        $year = date("Y", $orderDate);
        $order_date = date('Y-m-d', $orderDate);
        $this->loadModel('Month');
        $this->loadModel('FiscalYear');
        // from outlet_id, retrieve pharma or non-pharma
        $fasical_month = $this->Month->find('first', array(
            'conditions' => array(
                'Month.month' => $month,
                /* 'Month.year' => $year */
            ),
            'recursive' => -1
        ));
        $fasical_info = $this->FiscalYear->find('first', array(
            'conditions' => array(
                'FiscalYear.start_date <=' => $order_date,
                'FiscalYear.end_date >=' => $order_date,
            ),
            'recursive' => -1
        ));

        if (!empty($fasical_info)) {
            $this->loadModel('DistSaleTargetMonth');
            if ($cal_type == 1) {
                $update_fields_arr = array('DistSaleTargetMonth.target_quantity_achievement' => "DistSaleTargetMonth.target_quantity_achievement+$quantity", 'DistSaleTargetMonth.target_amount_achievement' => "DistSaleTargetMonth.target_amount_achievement+$gross_value");
            } else {
                $update_fields_arr = array('DistSaleTargetMonth.target_quantity_achievement' => "DistSaleTargetMonth.target_quantity_achievement-$quantity", 'DistSaleTargetMonth.target_amount_achievement' => "DistSaleTargetMonth.target_amount_achievement-$gross_value");
            }

            $conditions_arr = array('DistSaleTargetMonth.product_id' => $product_id, 'DistSaleTargetMonth.dist_sales_representative_code' => $sr_code, 'DistSaleTargetMonth.aso_id' => $office_id, 'DistSaleTargetMonth.target_type' => 2, 'DistSaleTargetMonth.fiscal_year_id' => $fasical_info['FiscalYear']['id'], 'DistSaleTargetMonth.month_id' => $fasical_month['Month']['id']);
            $this->DistSaleTargetMonth->updateAll($update_fields_arr, $conditions_arr);
        }
    }

    function get_tso_list()
    {
        $office_id = $this->request->data['office_id'];

        $output = "<option value=''>---- Select TSO ----</option>";

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
                $k = $data['DistTso']['id'];
                $v = $data['DistTso']['name'];
                $output .= "<option value='$k'>$v</option>";
            }
        }
        echo $output;
        $this->autoRender = false;
    }

    function get_dist_list_by_tso_id_and_date_range()
    {
        $tso_id = $this->request->data['tso_id'];
        $dist_id = $this->request->data['distributor_id'];
        $date_from = $this->request->data['order_date_from'];
        $order_date_from = date("Y-m-d", strtotime($date_from));
        $date_to = $this->request->data['order_date_to'];
        $order_date_to = date("Y-m-d", strtotime($date_to));
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $this->loadModel('DistTsoMapping');

        $output = "<option value=''>--- Select Distributor ---</option>";
        if ($user_group_id == 1034) {
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');

            $distributor = $this->DistUserMapping->find('first', array(
                'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];

            $dist_conditions = array(
                'DistTsoMapping.dist_tso_id' => $tso_id,
                'DistTsoMapping.dist_distributor_id' => $distributor_id,

            );
        } else {
            $dist_conditions = array(
                'DistTsoMapping.dist_tso_id' => $tso_id,

            );
        }

        $distDistributors = $this->DistTsoMapping->find('all', array(
            'conditions' => $dist_conditions,
            'fields' => array('DistDistributor.id', 'DistDistributor.name'),
        ));

        $dist_ids = array();
        foreach ($distDistributors as $key => $value) {
            $dist_ids[$key] = $value['DistDistributor']['id'];
        }

        /*$this->loadModel('DistOrder');
        $distributor_list = $this->DistOrder->find('all',array(
            'conditions'=> array(
                'DistOrder.distributor_id'=>$dist_ids,
                'DistOrder.order_date >=' => $order_date_from,
                'DistOrder.order_date <=' => $order_date_to,
            ),
            'fields'=>array('DistOrder.distributor_id','Distributor.name'),
            'recursive'=> 0
        ));*/

        $distDistributors = $this->DistDistributor->find('all', array(
            'conditions' => array(
                'DistOrder.distributor_id' => $dist_ids,
                'DistOrder.order_date >=' => $order_date_from,
                'DistOrder.order_date <=' => $order_date_to,
                'DistDistributor.is_active' => 1
            ),
            'joins' => array(array('table' => 'dist_Orders', 'alias' => 'DistOrder', 'conditions' => 'DistOrder.distributor_id=DistDistributor.id')),
            'recursive' => -1,
            'fields' => array('DISTINCT DistDistributor.id', 'DistDistributor.name'),
            'order' => array('DistDistributor.name' => 'asc'),
        ));



        if ($distDistributors) {
            $selected = "";
            foreach ($distDistributors as $key => $data) {
                // $selected=($key==$dist_id)?"selected":"";
                $k = $data['DistDistributor']['id'];
                $v = $data['DistDistributor']['name'];
                $output .= "<option $selected value='$k'>$v</option>";
            }
        }
        echo $output;
        $this->autoRender = false;
    }
    public function get_market_list()
    {
        $dist_route_id = $this->request->data['dist_route_id'];
        $conditions = array('is_active' => 1);
        if ($dist_route_id) {
            $conditions['dist_route_id'] = $dist_route_id;
        }
        $this->loadModel('DistMarket');
        $output = "<option value=''>--- Select ---</option>";

        $market_list = $this->DistMarket->find('list', array(
            'conditions' => $conditions,
            'order' => array('DistMarket.name' => 'ASC'),
            'recursive' => -1
        ));

        if ($market_list) {
            foreach ($market_list as $key => $data) {
                $output .= "<option value='$key'>$data</option>";
            }
        }

        echo $output;
        $this->autoRender = false;
    }

    public function admin_add_outlet()
    {
        if ($this->request->is('post')) {
            $this->request->data['DistOutlet']['created_at'] = $this->current_datetime();
            $this->request->data['DistOutlet']['updated_at'] = $this->current_datetime();
            $this->request->data['DistOutlet']['created_by'] = $this->UserAuth->getUserId();
            $this->DistOutlet->create();
            if ($this->DistOutlet->save($this->request->data)) {
                $this->Session->delete('from_outlet');
                $dist_outlet_id = $this->DistOutlet->getLastInsertID();
                if (array_key_exists('tag', $this->request->data['DistOutlet'])) {
                    /* --------------Add dist_distributor_id in index----------------- */

                    $this->request->data['DistOutlet']['dist_distributor_id'] = $this->request->data['DistOutlet']['market_distributor_id'];
                    unset($this->request->data['DistOutlet']['market_distributor_id']);
                    $this->loadModel('DistSalesRepresentative');
                    $this->request->data['DistOutlet']['dist_sales_representative_id'] = $this->request->data['DistOutlet']['market_sr_id'];
                    unset($this->request->data['DistOutlet']['market_sr_id']);

                    $this->request->data['DistOutlet']['dist_route_id'] = $this->request->data['DistOutlet']['market_route_id'];
                    unset($this->request->data['DistOutlet']['market_route_id']);

                    $this->request->data['DistOutlet']['memo_date'] = $this->request->data['DistOutlet']['market_memo_date'];
                    unset($this->request->data['DistOutlet']['market_memo_date']);

                    $this->request->data['DistOutlet']['ae_id'] = $this->request->data['DistOutlet']['market_ae_id'];
                    unset($this->request->data['DistOutlet']['market_ae_id']);

                    $this->request->data['DistOutlet']['tso_id'] = $this->request->data['DistOutlet']['market_tso_id'];
                    unset($this->request->data['DistOutlet']['market_tso_id']);

                    $this->request->data['DistOutlet']['dist_market_id'] = $this->request->data['DistOutlet']['market_market_id'];
                    unset($this->request->data['DistOutlet']['market_market_id']);

                    $this->request->data['DistOutlet']['memo_reference_no'] = $this->request->data['DistOutlet']['market_memo_reference_no'];
                    unset($this->request->data['DistOutlet']['market_memo_reference_no']);

                    $this->request->data['DistOutlet']['dist_outlet_id'] = $dist_outlet_id;
                    $this->request->data['DistOutlet']['identity'] = 'from_outlet';
                    $this->Session->write('from_outlet', $this->request->data);
                    $this->redirect(array('controller' => 'DistOrderDeliveries', 'action' => 'admin_create_order'));
                }
            }
        }
    }

    //add new for new policy calclusion
    public function get_policy_data()
    {

        $products = $this->Product->find('list', array());
        $measurement_unit = $this->MeasurementUnit->find('list', array());

        //pr($this->request->data);

        $product_items_id = explode(',', rtrim($this->request->data['product_id_list'], ","));
        $product_ids = join(",", $product_items_id);
        $memo_product_ids = explode(',', $product_ids);
        //pr($memo_product_ids);

        $product_qty_list = explode(',', rtrim($this->request->data['product_qty_list'], ","));
        $product_qty_list = join(",", $product_qty_list);
        $product_qty_list = explode(',', $product_qty_list);
        //pr($product_qty_list);

        $product_rate_list = explode(',', rtrim($this->request->data['product_rate_list'], ","));
        $product_rate_list = join(",", $product_rate_list);
        $product_rate_list = explode(',', $product_rate_list);
        //pr($product_rate_list);

        $memo_product_qty = array();
        $memo_product_price = array();
        foreach ($memo_product_ids as $key => $val) {
            $memo_product_qty[$val] = $product_qty_list[$key];
            $memo_product_price[$val] = $product_rate_list[$key];
        }
        //pr($memo_product_qty);
        //pr($memo_product_price);


        $office_id = $this->request->data['office_id'];
        $outlet_id = $this->request->data['outlet_id'];
        $db_id = $this->request->data['distributor_id'];


        $memo_date = date('Y-m-d', strtotime($this->request->data['memo_date']));

        $this->LoadModel('GroupWiseDiscountBonusPolicy');
        $this->LoadModel('GroupWiseDiscountBonusPolicyProduct');
        $this->LoadModel('GroupWiseDiscountBonusPolicyOption');
        $this->LoadModel('ProductCombination');
        $this->LoadModel('GroupWiseDiscountBonusPolicyOptionBonusProduct');



        $policy_allow = 0;

        $conditions = array(
            'GroupWiseDiscountBonusPolicy.is_distributor' => 1
        );


        //$current_date = date('Y-m-d');
        $current_date = $memo_date;

        $policy_ids = array();
        //get policy ids by office wise
        $sql = "SELECT id FROM group_wise_discount_bonus_policies where is_distributor=1 
                AND start_date<='$current_date' AND end_date>='$current_date'
                AND id IN(SELECT group_wise_discount_bonus_policy_id FROM group_wise_discount_bonus_policy_to_offices WHERE office_id=$office_id)";
        $office_datas = $this->GroupWiseDiscountBonusPolicy->query($sql);
        foreach ($office_datas as $result) {
            array_push($policy_ids, $result[0]['id']);
        }

        //get policy ids by outlet group wise
        $sql = "SELECT id FROM group_wise_discount_bonus_policies 
        where is_distributor=1
        AND start_date<='$current_date' AND end_date>='$current_date'
        AND id IN(SELECT group_wise_discount_bonus_policy_id FROM group_wise_discount_bonus_policy_to_outlet_groups WHERE outlet_group_id IN(SELECT outlet_group_id FROM outlet_group_to_outlets WHERE outlet_id=$outlet_id AND is_distributor=1))";
        $group_datas = $this->GroupWiseDiscountBonusPolicy->query($sql);
        foreach ($group_datas as $result) {
            array_push($policy_ids, $result[0]['id']);
        }

        //get policy ids by outlet category wise
        $sql = "SELECT id FROM group_wise_discount_bonus_policies 
        where is_distributor=1
        AND start_date<='$current_date' AND end_date>='$current_date'
        AND id IN(SELECT group_wise_discount_bonus_policy_id FROM group_wise_discount_bonus_policy_to_outlet_categories WHERE outlet_category_id IN(SELECT category_id FROM outlets WHERE id=$outlet_id))";
        $policy_data = $this->GroupWiseDiscountBonusPolicy->query($sql);
        foreach ($policy_data as $result) {
            array_push($policy_ids, $result[0]['id']);
        }
        $policy_ids = array_unique($policy_ids);
        //pr($policy_ids);

        $final_data = array();
        $b_data = array();
        $d_data = array();

        if ($policy_ids) {


            $conditions = array(
                'GroupWiseDiscountBonusPolicyProduct.group_wise_discount_bonus_policy_id' => $policy_ids,
                'GroupWiseDiscountBonusPolicyProduct.product_id' => $memo_product_ids
            );
            $p_policy_data = $this->GroupWiseDiscountBonusPolicyProduct->find('all', array(
                'conditions' => $conditions,
                'fields' => array('DISTINCT GroupWiseDiscountBonusPolicyProduct.group_wise_discount_bonus_policy_id as policy_id'),
                'recursive' => -1
            ));

            $p_policy_ids = array();
            foreach ($p_policy_data as $result) {
                array_push($p_policy_ids, $result[0]['policy_id']);
            }

            //pr($p_policy_data);
            //exit;

            $b_data = array();
            $d_data = array();
            if ($p_policy_ids) {

                foreach ($p_policy_ids as $p_key => $policy_id) {
                    $policy_info = $this->GroupWiseDiscountBonusPolicy->find('first', array(
                        'conditions' => array(
                            'id' => $policy_id,
                        ),
                        'fields' => array('name'),
                        'recursive' => -1
                    ));

                    $final_data['policy_info']['policy_id']             = $policy_id;
                    $final_data['policy_info']['policy_name']           = $policy_info['GroupWiseDiscountBonusPolicy']['name'];

                    $policy_product_datas = $this->GroupWiseDiscountBonusPolicyProduct->find('all', array(
                        'conditions' => array(
                            'GroupWiseDiscountBonusPolicyProduct.group_wise_discount_bonus_policy_id' => $policy_id,
                        ),
                        'recursive' => 1
                    ));
                    $total_qty = 0;
                    foreach ($policy_product_datas as $p_p_result) {
                        //pr($p_p_result);
                        $p_product_id = $p_p_result['GroupWiseDiscountBonusPolicyProduct']['product_id'];
                        if (in_array($p_product_id, $memo_product_ids)) {
                            $key = array_search($p_product_id, $memo_product_ids);
                            if (isset($key)) $total_qty += $product_qty_list[$key];
                        }
                    }


                    //pr($p_policy_ids);
                    $checking_for_discount = 1;
                    while ($total_qty > 0) {
                        $policy_options = $this->GroupWiseDiscountBonusPolicyOption->find('first', array(
                            'conditions' => array(
                                'GroupWiseDiscountBonusPolicyOption.group_wise_discount_bonus_policy_id' => $policy_id,
                                'GroupWiseDiscountBonusPolicyOption.min_qty <=' => $total_qty,
                            ),
                            'order' => 'GroupWiseDiscountBonusPolicyOption.min_qty desc',
                            //'limit' => 1,
                            'recursive' => 1
                        ));

                        if ($policy_options) {
                            $policy_name        = $policy_info['GroupWiseDiscountBonusPolicy']['name'];
                            $min_qty            = $policy_options['GroupWiseDiscountBonusPolicyOption']['min_qty'];
                            $option_id          = $policy_options['GroupWiseDiscountBonusPolicyOption']['id'];
                            $policy_type        = $policy_options['GroupWiseDiscountBonusPolicyOption']['policy_type'];
                            /*$bonus_product_id     = $policy_options['GroupWiseDiscountBonusPolicyOption']['bonus_product_id'];
                            $bonus_qty          = $policy_options['GroupWiseDiscountBonusPolicyOption']['bonus_qty'];
                            $measurement_unit_id= $policy_options['GroupWiseDiscountBonusPolicyOption']['measurement_unit_id'];*/
                            $discount_amount    = $policy_options['GroupWiseDiscountBonusPolicyOption']['discount_amount'];
                            $disccount_type     = $policy_options['GroupWiseDiscountBonusPolicyOption']['disccount_type'];



                            if ($policy_type == 0 && $checking_for_discount == 1) //only discount
                            {
                                $OptionPriceSlabs = $policy_options['GroupWiseDiscountBonusPolicyOptionPriceSlab'];
                                foreach ($OptionPriceSlabs as $slabs) {
                                    $discount_product_id = $slabs['discount_product_id'];
                                    $slab_info = $this->ProductCombination->find('first', array(
                                        'conditions' => array(
                                            'ProductCombination.id' => $slabs['slab_id']
                                        ),
                                        'recursive' => -1
                                    ));

                                    $total_price = @$memo_product_price[$discount_product_id] * @$memo_product_qty[$discount_product_id];
                                    if ($disccount_type == 0) {
                                        $slab_price = $slab_info['ProductCombination']['price'];
                                        $each_price = ($slab_price / 100) * $discount_amount;
                                        $discount_amount1 = $each_price * @$memo_product_qty[$discount_product_id];
                                        $final_value = $total_price - $discount_amount1;
                                    } else {
                                        $final_value = $total_price - $discount_amount;
                                        $discount_amount1 = $discount_amount;
                                        $each_price = $discount_amount;
                                    }

                                    $d_data[$discount_product_id]['policy_id']           = $policy_id;
                                    $d_data[$discount_product_id]['policy_type']         = $policy_type;
                                    $d_data[$discount_product_id]['policy_name']         = $policy_name;
                                    $d_data[$discount_product_id]['discount_product_id'] = $discount_product_id;
                                    //$d_data[$discount_product_id]['final_value']       = $final_value;
                                    $d_data[$discount_product_id]['discount_amount']     = $discount_amount1;
                                    $d_data[$discount_product_id]['main_discount_amount']    = $each_price;
                                    $d_data[$discount_product_id]['disccount_type']      = $disccount_type;
                                }
                            } elseif ($policy_type == 1) //only bonus
                            {

                                //for bonus
                                $OptionBonusProducts = $policy_options['GroupWiseDiscountBonusPolicyOptionBonusProduct'];
                                //pr($OptionBonusProducts);exit;

                                foreach ($OptionBonusProducts as $b_product) {
                                    $bonus_product_id = $b_product['bonus_product_id'];
                                    $measurement_unit_id = $b_product['measurement_unit_id'];
                                    $bonus_qty = $b_product['bonus_qty'];
                                    $b_data[$bonus_product_id]['policy_id']          = $policy_id;
                                    $b_data[$bonus_product_id]['policy_name']        = $policy_name;
                                    $b_data[$bonus_product_id]['policy_type']        = $policy_type;

                                    $b_data[$bonus_product_id]['option_bonus_id']   = $b_product['id'];
                                    $b_data[$bonus_product_id]['option_id']   = $b_product['group_wise_discount_bonus_policy_option_id'];

                                    $b_data[$bonus_product_id]['bonus_product_id']   = $bonus_product_id;
                                    $b_data[$bonus_product_id]['bonus_product_name'] = $products[$bonus_product_id];
                                    if (isset($b_data[$bonus_product_id]['bonus_qty']) && $b_data[$bonus_product_id]['bonus_qty'] > 0) {
                                        $b_data[$bonus_product_id]['bonus_qty'] += $bonus_qty;
                                    } else {
                                        $b_data[$bonus_product_id]['bonus_qty'] = $bonus_qty;
                                    }
                                    $b_data[$bonus_product_id]['measurement_unit_id'] = $measurement_unit_id;
                                    $b_data[$bonus_product_id]['measurement_unit_name'] = $measurement_unit[$measurement_unit_id];
                                    $b_data[$bonus_product_id]['relation'] = $b_product['relation'];
                                    //pr($b_data);
                                    //exit;
                                }

                                //for discount
                                $OptionPriceSlabs = $policy_options['GroupWiseDiscountBonusPolicyOptionPriceSlab'];
                                //pr($OptionPriceSlabs);
                                //exit;
                                foreach ($OptionPriceSlabs as $slabs) {
                                    $discount_product_id = $slabs['discount_product_id'];
                                    $d_data[$discount_product_id]['policy_id']           = $policy_id;
                                    $d_data[$discount_product_id]['policy_name']         = $policy_name;
                                    $d_data[$discount_product_id]['policy_type']         = $policy_type;
                                    $discount_product_id = $slabs['discount_product_id'];
                                    $d_data[$discount_product_id]['discount_product_id'] = $discount_product_id;
                                    $d_data[$discount_product_id]['discount_amount']     = 0;
                                    $d_data[$discount_product_id]['main_discount_amount']    = 0;
                                    $d_data[$discount_product_id]['disccount_type']      = '';
                                }
                            } elseif ($policy_type == 2 || $policy_type == 3) //discount and bonus
                            {
                                if ($checking_for_discount == 1) {
                                    //for discount
                                    $OptionPriceSlabs = $policy_options['GroupWiseDiscountBonusPolicyOptionPriceSlab'];
                                    foreach ($OptionPriceSlabs as $slabs) {
                                        $discount_product_id = $slabs['discount_product_id'];
                                        $slab_info = $this->ProductCombination->find('first', array(
                                            'conditions' => array(
                                                'ProductCombination.id' => $slabs['slab_id']
                                            ),
                                            'recursive' => -1
                                        ));

                                        $total_price = @$memo_product_price[$discount_product_id] * @$memo_product_qty[$discount_product_id];
                                        if ($disccount_type == 0) {
                                            $slab_price = $slab_info['ProductCombination']['price'];
                                            $each_price = ($slab_price / 100) * $discount_amount;
                                            $discount_amount1 = $each_price * @$memo_product_qty[$discount_product_id];
                                            $final_value = $total_price - $discount_amount1;
                                        } else {
                                            $final_value = $total_price - $discount_amount;
                                            $discount_amount1 = $discount_amount;
                                            $each_price = $discount_amount;
                                        }

                                        $d_data[$discount_product_id]['policy_id']           = $policy_id;
                                        $d_data[$discount_product_id]['policy_name']         = $policy_name;
                                        $d_data[$discount_product_id]['policy_type']         = $policy_type;
                                        $d_data[$discount_product_id]['discount_product_id'] = $discount_product_id;
                                        $d_data[$discount_product_id]['discount_amount']     = $discount_amount1;
                                        $d_data[$discount_product_id]['main_discount_amount']    = $each_price;
                                        $d_data[$discount_product_id]['disccount_type']      = $disccount_type;
                                    }
                                }

                                //for bonus
                                $OptionBonusProducts = $policy_options['GroupWiseDiscountBonusPolicyOptionBonusProduct'];
                                //pr($OptionBonusProducts);exit;
                                foreach ($OptionBonusProducts as $b_product) {
                                    $bonus_product_id = $b_product['bonus_product_id'];
                                    $measurement_unit_id = $b_product['measurement_unit_id'];
                                    $bonus_qty = $b_product['bonus_qty'];
                                    $b_data[$bonus_product_id]['policy_id']          = $policy_id;
                                    $b_data[$bonus_product_id]['policy_name']        = $policy_name;
                                    $b_data[$bonus_product_id]['policy_type']        = $policy_type;

                                    $b_data[$bonus_product_id]['option_bonus_id']    = $b_product['id'];
                                    $b_data[$bonus_product_id]['option_id']   = $b_product['group_wise_discount_bonus_policy_option_id'];

                                    $b_data[$bonus_product_id]['bonus_product_id']   = $bonus_product_id;
                                    $b_data[$bonus_product_id]['bonus_product_name'] = $products[$bonus_product_id];
                                    if (isset($b_data[$bonus_product_id]['bonus_qty']) && $b_data[$bonus_product_id]['bonus_qty'] > 0) {
                                        $b_data[$bonus_product_id]['bonus_qty'] += $bonus_qty;
                                    } else {
                                        $b_data[$bonus_product_id]['bonus_qty'] = $bonus_qty;
                                    }
                                    $b_data[$bonus_product_id]['measurement_unit_id'] = $measurement_unit_id;
                                    $b_data[$bonus_product_id]['measurement_unit_name'] = $measurement_unit[$measurement_unit_id];
                                    $b_data[$bonus_product_id]['relation'] = $b_product['relation'];
                                    //pr($b_data);
                                    //exit;
                                }
                            } else {
                            }

                            $total_qty = $total_qty - $min_qty;
                            $checking_for_discount++;
                        } else {
                            foreach ($memo_product_ids as $key => $discount_product_id) {
                                if (!isset($d_data[$discount_product_id]['discount_product_id'])) $d_data[$discount_product_id]['discount_product_id'] = $discount_product_id;
                                if (!isset($d_data[$discount_product_id]['discount_amount'])) $d_data[$discount_product_id]['discount_amount']     = 0;
                                if (!isset($d_data[$discount_product_id]['main_discount_amount'])) $d_data[$discount_product_id]['main_discount_amount']   = 0;
                                if (!isset($d_data[$discount_product_id]['disccount_type'])) $d_data[$discount_product_id]['disccount_type']   = 0;
                                if (!isset($d_data[$discount_product_id]['policy_type'])) $d_data[$discount_product_id]['policy_type']         = 0;
                                if (!isset($d_data[$discount_product_id]['policy_name'])) $d_data[$discount_product_id]['policy_name']         = 0;
                                if (!isset($d_data[$discount_product_id]['policy_id'])) $d_data[$discount_product_id]['policy_id']         = 0;
                            }
                            break;
                        }
                    }
                }



                if ($d_data) {
                    $i = 0;
                    foreach ($d_data as $d_d) {
                        $final_data['discount'][$i]['policy_name']          = $d_d['policy_name'];
                        $final_data['discount'][$i]['policy_id']            = $d_d['policy_id'];
                        $final_data['discount'][$i]['policy_type']          = $d_d['policy_type'];

                        $final_data['discount'][$i]['discount_product_id']  = $d_d['discount_product_id'];
                        $final_data['discount'][$i]['discount_amount']      = $d_d['discount_amount'];
                        $final_data['discount'][$i]['main_discount_amount'] = $d_d['main_discount_amount'];
                        $final_data['discount'][$i]['disccount_type']       = $d_d['disccount_type'];
                        $i++;
                    }
                }

                //pr($b_data);exit;

                //$qty_equal_check = array();
                if ($b_data) {

                    $i = 0;
                    foreach ($b_data as $b_d) {
                        $p_id = $b_d['policy_id'];
                        $final_data['bonus'][$p_id][$i]['policy_id']                = $p_id;
                        $final_data['bonus'][$p_id][$i]['policy_name']              = $b_d['policy_name'];
                        $final_data['bonus'][$p_id][$i]['policy_type']              = $b_d['policy_type'];
                        $final_data['bonus'][$p_id][$i]['option_bonus_id']          = $b_d['option_bonus_id'];
                        $final_data['bonus'][$p_id][$i]['option_id']                = $b_d['option_id'];

                        $final_data['bonus'][$p_id][$i]['bonus_product_id']         = $b_d['bonus_product_id'];
                        $final_data['bonus'][$p_id][$i]['bonus_product_name']       = $products[$b_d['bonus_product_id']];
                        $final_data['bonus'][$p_id][$i]['bonus_qty']                = $b_d['bonus_qty'];
                        $final_data['bonus'][$p_id][$i]['relation']                 = $b_d['relation'];
                        $final_data['bonus'][$p_id][$i]['measurement_unit_id']      = $b_d['measurement_unit_id'];
                        $final_data['bonus'][$p_id][$i]['measurement_unit_name']    = $measurement_unit[$b_d['measurement_unit_id']];
                        $i++;
                    }


                    $qty_equal_check = array();
                    $q_temp = 0;
                    foreach ($b_data as $b_d) {
                        $qty_equal_check[$b_d['option_id']] = 0;
                        if ($b_d['relation'] == 0) {
                            if ($q_temp == $b_d['bonus_qty']) {
                                $qty_equal_check[$b_d['option_id']] = 1;
                            }
                            $q_temp = $b_d['bonus_qty'];
                        }
                    }
                    //pr($qty_equal_check);
                    //exit;


                }
            }
        }

        $this->Session->write('b_data', $b_data);

        /*pr($d_data);
        pr($b_data);
        
        pr($qty_equal_check);exit;*/


        //$b_html = '<tr class="n_bonus_row"><th colspan="4">Policy Bonus</th><tr>';
        $b_html = '';

        foreach ($final_data['bonus'] as $policy_id => $b_datas) {
            // pr($b_datas);exit;
            $option_html = '';
            foreach ($b_datas as $b_f_data) {
                $product_details = $this->GroupWiseDiscountBonusPolicyOptionBonusProduct->find('first', array(
                    'fields' => array('SUM(CurrentInventory.qty) as total_qty'),
                    'conditions' => array(
                        'GroupWiseDiscountBonusPolicyOptionBonusProduct.group_wise_discount_bonus_policy_option_id' => $b_f_data['option_id'],
                        'GroupWiseDiscountBonusPolicyOptionBonusProduct.bonus_product_id' => $b_f_data['bonus_product_id'],
                        'Store.dist_distributor_id' => $db_id
                    ),
                    'joins' => array(
                        array(
                            'table' => 'dist_current_inventories',
                            'alias' => 'CurrentInventory',
                            'type' => 'Inner',
                            'conditions' => 'CurrentInventory.product_id=GroupWiseDiscountBonusPolicyOptionBonusProduct.bonus_product_id'
                        ),
                        array(
                            'table' => 'dist_stores',
                            'alias' => 'Store',
                            'type' => 'Inner',
                            'conditions' => 'CurrentInventory.store_id=Store.id'
                        )
                    ),
                    'group' => array('bonus_product_id', 'Store.id', 'Store.dist_distributor_id'),
                    'recursive' => -1
                ));
                $total_qty = $this->unit_convertfrombase($b_f_data['bonus_product_id'], $b_f_data['measurement_unit_id'], $product_details[0]['total_qty']);

                if ($total_qty >= $b_f_data['bonus_qty']) {
                    $option_html .= '<option id="b_option_' . $b_f_data['bonus_product_id'] . '" value="' . $b_f_data['bonus_product_id'] . '">' . $b_f_data['bonus_product_name'] . '</option>';
                } else {
                    $option_html .= '<option id="b_option_' . $b_f_data['bonus_product_id'] . '" disabled value="' . $b_f_data['bonus_product_id'] . '">' . $b_f_data['bonus_product_name'] . '</option>';
                }
            }

            $b_html .= '<tr class="n_bonus_row"><th colspan="4">' . $b_f_data['policy_name'] . '</th><tr>';
            $b_bonus_qty = 0;
            $k = 1;
            foreach ($b_datas as $b_f_data) {
                if ($b_f_data['policy_type'] != 3) {
                    if ($b_f_data['relation'] == 0) {
                        if (@$qty_equal_check[$b_f_data['option_id']] == 1) {
                            $product_details = $this->GroupWiseDiscountBonusPolicyOptionBonusProduct->find('first', array(
                                'fields' => array('SUM(CurrentInventory.qty) as total_qty'),
                                'conditions' => array(
                                    'GroupWiseDiscountBonusPolicyOptionBonusProduct.group_wise_discount_bonus_policy_option_id' => $b_f_data['option_id'],
                                    'GroupWiseDiscountBonusPolicyOptionBonusProduct.bonus_product_id' => $b_f_data['bonus_product_id'],
                                    'Store.dist_distributor_id' => $db_id
                                ),
                                'joins' => array(
                                    array(
                                        'table' => 'dist_current_inventories',
                                        'alias' => 'CurrentInventory',
                                        'type' => 'Inner',
                                        'conditions' => 'CurrentInventory.product_id=GroupWiseDiscountBonusPolicyOptionBonusProduct.bonus_product_id'
                                    ),
                                    array(
                                        'table' => 'dist_stores',
                                        'alias' => 'Store',
                                        'type' => 'Inner',
                                        'conditions' => 'CurrentInventory.store_id=Store.id'
                                    )
                                ),
                                'group' => array('bonus_product_id', 'Store.id', 'Store.dist_distributor_id'),
                                'recursive' => -1
                            ));
                            $total_qty = $this->unit_convertfrombase($b_f_data['bonus_product_id'], $b_f_data['measurement_unit_id'], $product_details[0]['total_qty']);

                            if ($total_qty >= $b_f_data['bonus_qty']) {
                                $s_disabled = '';
                            } else {
                                $s_disabled = 'disabled';
                            }
                            if ($b_bonus_qty < $b_f_data['bonus_qty'])
                                $b_bonus_qty = $b_f_data['bonus_qty'];
                            if ($total_qty >= $b_f_data['bonus_qty']) {
                                $b_html .= '
                                <tr class="bonus_row n_bonus_row bonus_policy_id_' . $b_f_data['policy_id'] . '">
                                    <th class="text-center" id="bonus_product_list">
                                        <div class="input select">
                                            <select ' . $s_disabled . ' name="data[OrderDetail][product_id][]" class="form-control width_100 policy_bonus_product_id">
                                            <option value="' . $b_f_data['bonus_product_id'] . '">' . $b_f_data['bonus_product_name'] . '</option>
                                            </select>
                                        </div>
                                    </th>
                                    <th class="text-center" width="12%">
                                        <input ' . $s_disabled . ' type="text" name="" class="form-control width_100 open_bonus_product_unit_name" value="' . $b_f_data['measurement_unit_name'] . '" readonly disabled="">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="open_bonus_product_unit_id" value="' . $b_f_data['measurement_unit_id'] . '">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type" value="' . $b_f_data['policy_type'] . '">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id" value="' . $b_f_data['policy_id'] . '">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus" value="3">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0">
                                    </th>
                                    <th class="text-center" width="12%">
                                        <input ' . $s_disabled . ' type="number" min="1" max="' . $b_f_data['bonus_qty'] . '" name="data[OrderDetail][sales_qty][]" value="' . ($k == 1 ? $b_f_data['bonus_qty'] : 0) . '" class="form-control width_100 open_bonus_min_qty open_bonus_option_id_' . $b_f_data['option_id'] . ' b_option_id_' . $b_f_data['option_id'] . '_' . $k . '" onKeyUp="checkBonusProductQty(' . $b_f_data['option_id'] . ', ' . $b_f_data['bonus_qty'] . ')">
                                    </th>
                                    <th class="text-center" width="10%">
                                        <a class="btn btn-danger btn-xs remove_policy_bonus"><i class="glyphicon glyphicon-remove"></i></a>
                                        <input type="hidden" value="' . $b_bonus_qty . '" class="bonus_option_id_' . $b_f_data['option_id'] . ' h_option_id_' . $b_f_data['option_id'] . '_' . $k . '">
                                    </th>
                                </tr>';
                                $k++;
                            }
                        } else {
                            $b_html .= '
                            <tr class="bonus_row n_bonus_row bonus_policy_id_' . $b_f_data['policy_id'] . '">
                                <th class="text-center" id="bonus_product_list">
                                    <div class="input select">
                                        <select onChange="get_bonus_product_info(' . $b_f_data['option_id'] . ', ' . $b_f_data['policy_id'] . ')" name="data[OrderDetail][product_id][]" class="form-control width_100 policy_bonus_product_id">' . $option_html . '</select>
                                    </div>
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name" value="' . $b_f_data['measurement_unit_name'] . '" readonly disabled="">
                                    <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="open_bonus_product_unit_id" value="' . $b_f_data['measurement_unit_id'] . '">
                                    <input type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type" value="' . $b_f_data['policy_type'] . '">
                                    <input type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id" value="' . $b_f_data['policy_id'] . '">
                                    <input type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus" value="3"><input type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0">
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="number" min="1" name="data[OrderDetail][sales_qty][]" readonly value="' . $b_f_data['bonus_qty'] . '" class="form-control width_100 open_bonus_min_qty">
                                </th>
                                <th class="text-center" width="10%">
                                    <a class="btn btn-danger btn-xs remove_policy_bonus"><i class="glyphicon glyphicon-remove"></i></a>
                                </th>
                            </tr>';
                            break;
                        }
                    } else {
                        $product_details = $this->GroupWiseDiscountBonusPolicyOptionBonusProduct->find('first', array(
                            'fields' => array('SUM(CurrentInventory.qty) as total_qty'),
                            'conditions' => array(
                                'GroupWiseDiscountBonusPolicyOptionBonusProduct.group_wise_discount_bonus_policy_option_id' => $b_f_data['option_id'],
                                'GroupWiseDiscountBonusPolicyOptionBonusProduct.bonus_product_id' => $b_f_data['bonus_product_id'],
                                'Store.dist_distributor_id' => $db_id
                            ),
                            'joins' => array(
                                array(
                                    'table' => 'dist_current_inventories',
                                    'alias' => 'CurrentInventory',
                                    'type' => 'Inner',
                                    'conditions' => 'CurrentInventory.product_id=GroupWiseDiscountBonusPolicyOptionBonusProduct.bonus_product_id'
                                ),
                                array(
                                    'table' => 'dist_stores',
                                    'alias' => 'Store',
                                    'type' => 'Inner',
                                    'conditions' => 'CurrentInventory.store_id=Store.id'
                                )
                            ),
                            'group' => array('bonus_product_id', 'Store.id', 'Store.dist_distributor_id'),
                            'recursive' => -1
                        ));
                        $total_qty = $this->unit_convertfrombase($b_f_data['bonus_product_id'], $b_f_data['measurement_unit_id'], $product_details[0]['total_qty']);

                        if ($total_qty >= $b_f_data['bonus_qty']) {
                            $s_disabled = '';
                        } else {
                            $s_disabled = 'disabled';
                        }
                        if ($total_qty >= $b_f_data['bonus_qty']) {
                            $b_html .= '
                                <tr class="bonus_row n_bonus_row bonus_policy_id_' . $b_f_data['policy_id'] . '">
                                    <th class="text-center" id="bonus_product_list">
                                        <div class="input select">
                                            <select ' . $s_disabled . ' name="data[OrderDetail][product_id][]" class="form-control width_100 policy_bonus_product_id">
                                                <option value="' . $b_f_data['bonus_product_id'] . '">' . $b_f_data['bonus_product_name'] . '</option>
                                            </select>
                                        </div>
                                    </th>
                                    <th class="text-center" width="12%">
                                        <input ' . $s_disabled . ' type="text" name="" class="form-control width_100 open_bonus_product_unit_name" value="' . $b_f_data['measurement_unit_name'] . '" readonly disabled="">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="open_bonus_product_unit_id" value="' . $b_f_data['measurement_unit_id'] . '">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type" value="' . $b_f_data['policy_type'] . '">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id" value="' . $b_f_data['policy_id'] . '">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus" value="3">
                                        <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0">
                                    </th>
                                    <th class="text-center" width="12%">
                                        <input ' . $s_disabled . ' type="number" min="1" name="data[OrderDetail][sales_qty][]" readonly value="' . $b_f_data['bonus_qty'] . '" class="form-control width_100 open_bonus_min_qty">
                                    </th>
                                    <th class="text-center" width="10%">
                                        <a class="btn btn-danger btn-xs remove_policy_bonus"><i class="glyphicon glyphicon-remove"></i></a>
                                    </th>
                                </tr>';
                        }
                    }
                } else {
                    $product_details = $this->GroupWiseDiscountBonusPolicyOptionBonusProduct->find('first', array(
                        'fields' => array('SUM(CurrentInventory.qty) as total_qty'),
                        'conditions' => array(
                            'GroupWiseDiscountBonusPolicyOptionBonusProduct.group_wise_discount_bonus_policy_option_id' => $b_f_data['option_id'],
                            'GroupWiseDiscountBonusPolicyOptionBonusProduct.bonus_product_id' => $b_f_data['bonus_product_id'],
                            'Store.dist_distributor_id' => $db_id
                        ),
                        'joins' => array(
                            array(
                                'table' => 'dist_current_inventories',
                                'alias' => 'CurrentInventory',
                                'type' => 'Inner',
                                'conditions' => 'CurrentInventory.product_id=GroupWiseDiscountBonusPolicyOptionBonusProduct.bonus_product_id'
                            ),
                            array(
                                'table' => 'dist_stores',
                                'alias' => 'Store',
                                'type' => 'Inner',
                                'conditions' => 'CurrentInventory.store_id=Store.id'
                            )
                        ),
                        'group' => array('bonus_product_id', 'Store.id', 'Store.dist_distributor_id'),
                        'recursive' => -1
                    ));
                    $total_qty = $this->unit_convertfrombase($b_f_data['bonus_product_id'], $b_f_data['measurement_unit_id'], $product_details[0]['total_qty']);

                    if ($total_qty >= $b_f_data['bonus_qty']) {
                        $i_disabled = 'i_disabled';
                    } else {
                        $i_disabled = '';
                    }

                    if ($b_f_data['relation'] == 0) {
                        if (@$qty_equal_check[$b_f_data['option_id']] == 1) {
                            $product_details = $this->GroupWiseDiscountBonusPolicyOptionBonusProduct->find('first', array(
                                'fields' => array('SUM(CurrentInventory.qty) as total_qty'),
                                'conditions' => array(
                                    'GroupWiseDiscountBonusPolicyOptionBonusProduct.group_wise_discount_bonus_policy_option_id' => $b_f_data['option_id'],
                                    'GroupWiseDiscountBonusPolicyOptionBonusProduct.bonus_product_id' => $b_f_data['bonus_product_id'],
                                    'Store.dist_distributor_id' => $db_id
                                ),
                                'joins' => array(
                                    array(
                                        'table' => 'dist_current_inventories',
                                        'alias' => 'CurrentInventory',
                                        'type' => 'Inner',
                                        'conditions' => 'CurrentInventory.product_id=GroupWiseDiscountBonusPolicyOptionBonusProduct.bonus_product_id'
                                    ),
                                    array(
                                        'table' => 'dist_stores',
                                        'alias' => 'Store',
                                        'type' => 'Inner',
                                        'conditions' => 'CurrentInventory.store_id=Store.id'
                                    )
                                ),
                                'group' => array('bonus_product_id', 'Store.id', 'Store.dist_distributor_id'),
                                'recursive' => -1
                            ));
                            $total_qty = $this->unit_convertfrombase($b_f_data['bonus_product_id'], $b_f_data['measurement_unit_id'], $product_details[0]['total_qty']);

                            if ($total_qty >= $b_f_data['bonus_qty']) {
                                $s_disabled = '';
                            } else {
                                $s_disabled = 'disabled';
                            }

                            if ($b_bonus_qty < $b_f_data['bonus_qty'])
                                $b_bonus_qty = $b_f_data['bonus_qty'];

                            if ($total_qty >= $b_f_data['bonus_qty']) {
                                $b_html .= '
                                    <tr class="bonus_row n_bonus_row bonus_policy_id_' . $b_f_data['policy_id'] . '">
                                        <th class="text-center" id="bonus_product_list">
                                            <div class="input select">
                                                <select ' . $s_disabled . ' name="data[OrderDetail][product_id][]" class="form-control width_100 policy_bonus_product_id">
                                                    <option value="' . $b_f_data['bonus_product_id'] . '">' . $b_f_data['bonus_product_name'] . '</option>
                                                </select>
                                            </div>
                                        </th>
                                        <th class="text-center" width="12%">
                                            <input ' . $s_disabled . ' type="text" name="" class="form-control width_100 open_bonus_product_unit_name" value="' . $b_f_data['measurement_unit_name'] . '" readonly disabled="">
                                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="open_bonus_product_unit_id" value="' . $b_f_data['measurement_unit_id'] . '">
                                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type" value="' . $b_f_data['policy_type'] . '">
                                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id" value="' . $b_f_data['policy_id'] . '">
                                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus" value="3">
                                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate" value="0.0">
                                        </th>
                                        <th class="text-center" width="12%">
                                            <input ' . $s_disabled . ' type="number" min="1" max="' . $b_f_data['bonus_qty'] . '" name="data[OrderDetail][sales_qty][]" value="' . ($k == 1 ? $b_f_data['bonus_qty'] : 0) . '" class="form-control width_100 open_bonus_min_qty open_bonus_option_id_' . $b_f_data['option_id'] . ' b_option_id_' . $b_f_data['option_id'] . '_' . $k . '" onKeyUp="checkBonusProductQty(' . $b_f_data['option_id'] . ', ' . $b_f_data['bonus_qty'] . ')">
                                        </th>
                                        <th class="text-center" width="10%">
                                            <a class="btn btn-danger btn-xs remove_policy_bonus"><i class="glyphicon glyphicon-remove"></i></a>
                                            <input type="hidden" value="' . $b_bonus_qty . '" class="bonus_option_id_' . $b_f_data['option_id'] . ' h_option_id_' . $b_f_data['option_id'] . '_' . $k . '">
                                        </th>
                                    </tr>';
                                $k++;
                            }
                        } else {
                            $b_html .= '
                            <tr class="bonus_row n_bonus_row bonus_dis_row bonus_policy_id_' . $b_f_data['policy_id'] . '">
                                <th class="text-center" id="bonus_product_list">
                                    <div class="input select">
                                        <select onChange="get_bonus_product_info(' . $b_f_data['option_id'] . ', ' . $b_f_data['policy_id'] . ')"  disabled="" name="data[OrderDetail][product_id][]" class="form-control width_100 policy_bonus_product_id ' . $i_disabled . '">
                                            ' . $option_html . '
                                        </select>
                                    </div>
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name ' . $i_disabled . '" readonly value="' . $b_f_data['measurement_unit_name'] . '" disabled="">
                                    <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="open_bonus_product_unit_id ' . $i_disabled . '" value="' . $b_f_data['measurement_unit_id'] . '" disabled="">
                                    <input type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type ' . $i_disabled . '" value="' . $b_f_data['policy_type'] . '" disabled="">
                                    <input type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id ' . $i_disabled . '" value="' . $b_f_data['policy_id'] . '" disabled="">
                                    <input type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus ' . $i_disabled . '" value="3" disabled=""><input type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate ' . $i_disabled . '" value="0.0" disabled="">
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="number" min="1" name="data[OrderDetail][sales_qty][]" readonly value="' . $b_f_data['bonus_qty'] . '" class="form-control width_100 open_bonus_min_qty ' . $i_disabled . '" disabled="">
                                </th>
                                <th class="text-center" width="10%">
                                    <a class="btn btn-danger btn-xs remove_policy_bonus"><i class="glyphicon glyphicon-remove"></i></a>
                                </th>
                            </tr>';

                            break;
                        }
                    } else {


                        $b_html .= '
                            <tr class="bonus_row n_bonus_row bonus_dis_row bonus_policy_id_' . $b_f_data['policy_id'] . '">
                                <th class="text-center" id="bonus_product_list">
                                    <div class="input select">
                                        <select disabled="" name="data[OrderDetail][product_id][]" class="form-control width_100 policy_bonus_product_id ' . $i_disabled . '">
                                            <option value="' . $b_f_data['bonus_product_id'] . '">' . $b_f_data['bonus_product_name'] . '</option>
                                        </select>
                                    </div>
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="text" name="" class="form-control width_100 open_bonus_product_unit_name ' . $i_disabled . '" readonly value="' . $b_f_data['measurement_unit_name'] . '" disabled="">
                                    <input type="hidden" name="data[OrderDetail][measurement_unit_id][]" class="open_bonus_product_unit_id ' . $i_disabled . '" value="' . $b_f_data['measurement_unit_id'] . '" disabled="">
                                    <input type="hidden" name="data[OrderDetail][policy_type][]" class="policy_type ' . $i_disabled . '" value="' . $b_f_data['policy_type'] . '" disabled="">
                                    <input type="hidden" name="data[OrderDetail][policy_id][]" class="policy_id ' . $i_disabled . '" value="' . $b_f_data['policy_id'] . '" disabled="">
                                    <input type="hidden" name="data[OrderDetail][is_bonus][]" class="is_bonus ' . $i_disabled . '" value="3" disabled="">
                                    <input type="hidden" name="data[OrderDetail][Price][]" class="form-control width_100 open_bonus_product_rate ' . $i_disabled . '" value="0.0" disabled="">
                                </th>
                                <th class="text-center" width="12%">
                                    <input type="number" min="1" name="data[MemoDetail][sales_qty][]" readonly value="' . $b_f_data['bonus_qty'] . '" class="form-control width_100 open_bonus_min_qty ' . $i_disabled . '" disabled="">
                                </th>
                                <th class="text-center" width="10%">
                                    <a class="btn btn-danger btn-xs remove_policy_bonus"><i class="glyphicon glyphicon-remove"></i></a>
                                </th>
                            </tr>';
                    }
                }
            }
        }


        $final_data['bonus'] = '';
        $final_data['bonus'] = $b_html;



        //pr($final_data);
        //exit;

        echo json_encode($final_data);



        $this->autoRender = false;
        exit;
    }

    public function get_product_price()
    {
        $this->LoadModel('ProductCombinationsV2');
        $this->LoadModel('CombinationsV2');
        $this->LoadModel('CombinationDetailsV2');
        $order_date = $this->request->data['memo_date'];
        $product_id = $this->request->data['product_id'];
        $min_qty = $this->request->data['min_qty'];
        $outlet_category_id = $this->request->data['outlet_category_id'];
        $special_group = json_decode($this->request->data['special_group'], 1);
        if (empty($special_group)) {
            $special_group[] = 0;
        }
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
                    'conditions' => 'PriceSection.id=ProductCombinationsV2.section_id and PriceSection.is_sr=1'
                ),
                array(
                    'table' => 'product_price_other_for_slabs_v2',
                    'alias' => 'SpecialPrice',
                    'type' => 'Left',
                    'conditions' => '
                        SpecialPrice.product_combination_id=ProductCombinationsV2.id 
                        and SpecialPrice.price_for=2
                        and SpecialPrice.type=1
                        and SpecialPrice.reffrence_id in (' . implode(',', $special_group) . ')'
                ),
                array(
                    'table' => 'product_price_other_for_slabs_v2',
                    'alias' => 'OutletCategoryPrice',
                    'type' => 'Left',
                    'conditions' => '
                        OutletCategoryPrice.product_combination_id=ProductCombinationsV2.id
                        and OutletCategoryPrice.price_for=2
                        and OutletCategoryPrice.type=1
                        and OutletCategoryPrice.reffrence_id=' . $outlet_category_id

                )
            ),
            'fields' => array(
                'ProductCombinationsV2.id',
                'ProductCombinationsV2.effective_date',
                'ProductCombinationsV2.min_qty',
                'ProductCombinationsV2.sr_price',
                'SpecialPrice.price',
                'SpecialPrice.reffrence_id',
                'OutletCategoryPrice.price',
                'OutletCategoryPrice.reffrence_id',
            ),
            'group' => array(
                'ProductCombinationsV2.id',
                'ProductCombinationsV2.effective_date',
                'ProductCombinationsV2.min_qty',
                'ProductCombinationsV2.sr_price',
                'SpecialPrice.price',
                'SpecialPrice.reffrence_id',
                'OutletCategoryPrice.price',
                'OutletCategoryPrice.reffrence_id',
            ),
            'order' => array(
                'ProductCombinationsV2.effective_date desc',
                'ProductCombinationsV2.min_qty desc',
                'SpecialPrice.reffrence_id desc',
                'OutletCategoryPrice.reffrence_id desc',
            ),
            'recursive' => -1
        ));
        $product_price_array = array();
        if ($price_slab) {
            if ($price_slab['SpecialPrice']['price']) {
                $product_price_array['price'] = $price_slab['SpecialPrice']['price'];
                $reffrence_id = $price_slab['SpecialPrice']['reffrence_id'];
                $other_combination_joins = array(
                    'table' => 'product_price_other_for_slabs_v2',
                    'alias' => 'Other',
                    'type' => 'Left',
                    'conditions' => '
                            Other.product_combination_id=PC.id 
                            and Other.price_for=2
                            and Other.type=1
                            and Other.reffrence_id=' . $reffrence_id
                );
                $combination_data_group = array(
                    'CombinationDetailsV2.product_id',
                    'PC.id',
                    'PC.price',
                    'Other.price',
                );
                $combination_data_fields = array(
                    'CombinationDetailsV2.product_id',
                    'PC.id',
                    'PC.price',
                    'Other.price',
                );
            } elseif ($price_slab['OutletCategoryPrice']['price']) {
                $product_price_array['price'] = $price_slab['OutletCategoryPrice']['price'];
                $reffrence_id = $price_slab['OutletCategoryPrice']['reffrence_id'];
                $other_combination_joins = array(
                    'table' => 'product_price_other_for_slabs_v2',
                    'alias' => 'Other',
                    'type' => 'Left',
                    'conditions' => '
                        Other.product_combination_id=PC.id
                        and Other.price_for=2 
                        and Other.type=1
                        and Other.reffrence_id=' . $reffrence_id

                );
                $combination_data_group = array(
                    'CombinationDetailsV2.product_id',
                    'PC.id',
                    'PC.price',
                    'Other.price',
                );
                $combination_data_fields = array(
                    'CombinationDetailsV2.product_id',
                    'PC.id',
                    'PC.price',
                    'Other.price',
                );
            } else {
                $product_price_array['price'] = $price_slab['ProductCombinationsV2']['sr_price'];
                $reffrence_id = 0;
                $other_combination_joins = '';
                $combination_data_group = array(
                    'CombinationDetailsV2.product_id',
                    'PC.id',
                    'PC.sr_price',
                );
                $combination_data_fields = array(
                    'CombinationDetailsV2.product_id',
                    'PC.id',
                    'PC.sr_price',
                );
            }
            $product_price_array['price_id'] = $price_slab['ProductCombinationsV2']['id'];
            $product_price_array['total_value'] = sprintf('%.2f', $product_price_array['price'] * $product_wise_cart_qty[$product_id]);
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
                                    and pcs.is_sr=1
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
                                'table' => 'product_price_section_v2',
                                'alias' => 'PriceSection',
                                'conditions' => 'PriceSection.id=pc.section_id and PriceSection.is_sr=1'
                            ),
                            $other_combination_joins
                        ),
                        'group' => $combination_data_group,
                        'fields' => $combination_data_fields,
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
                            if (isset($details_data['Other']['price']))
                                $price = $details_data['Other']['price'];
                            else
                                $price = $details_data['PC']['sr_price'];
                        } else {
                            if (isset($product_wise_cart_qty[$details_data['CombinationDetailsV2']['product_id']])) {
                                $combine_product[] = $details_data['CombinationDetailsV2']['product_id'];
                                if (isset($details_data['Other']['price']))
                                    $com_price = $details_data['Other']['price'];
                                else
                                    $com_price = $details_data['PC']['sr_price'];
                                $combination_product_array[] = array(
                                    'product_id' => $details_data['CombinationDetailsV2']['product_id'],
                                    'price' => $com_price,
                                    'total_value' => sprintf('%.2f', $com_price * $product_wise_cart_qty[$details_data['CombinationDetailsV2']['product_id']]),
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
        $this->LoadModel('DiscountBonusPolicy');
        $this->LoadModel('DistStore');
        $this->LoadModel('DiscountBonusPolicyProduct');
        $this->LoadModel('DiscountBonusPolicyOption');
        $this->LoadModel('DiscountBonusPolicyOptionExclusionInclusionProduct');
        $order_date = date('Y-m-d', strtotime($this->request->data['order_date']));
        $outlet_id = $this->request->data['outlet_id'];
        $office_id = $this->request->data['office_id'];
        $distributor_id = $this->request->data['distributor_id'];
        $product_id = $this->request->data['product_id'];
        $memo_total = $this->request->data['memo_total'];
        $min_qty = $this->request->data['min_qty'];
        $product_wise_cart_qty = $this->request->data['cart_product'];
        $outlet_category_id = $this->request->data['outlet_category_id'];
        $special_group = json_decode($this->request->data['special_group'], 1);
        if (empty($special_group)) {
            $special_group[] = 0;
        }
        $old_selected_bonus = json_decode($this->request->data['selected_bonus'], 1);
        $old_selected_set = json_decode($this->request->data['selected_set'], 1);
        $old_selected_policy_type = json_decode($this->request->data['selected_policy_type'], 1);
        $old_selected_option_id = json_decode($this->request->data['selected_option_id'], 1);
        $store_info = $this->DistStore->find('first', array(
            'conditions' => array(
                'DistStore.dist_distributor_id' => $distributor_id,
            ),
            'recursive' => -1
        ));
        $store_id = $store_info['DistStore']['id'];
        $conditions = array();
        $conditions['DiscountBonusPolicy.start_date <='] = $order_date;
        $conditions['DiscountBonusPolicy.end_date >='] = $order_date;
        $conditions['DiscountBonusPolicy.is_sr'] = 1;
        $conditions['DiscountBonusPolicyOption.is_sr'] = 1;
        $conditions['DiscountBonusPolicyProduct.product_id'] = array_keys($product_wise_cart_qty);

        $excluding_policy_id = $this->DiscountBonusPolicy->find('all', array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'discount_bonus_policy_settings',
                    'alias' => 'DiscountBonusPolicyOutletGroup',
                    'type' => 'inner',
                    'conditions' => '
                        DiscountBonusPolicyOutletGroup.discount_bonus_policy_id=DiscountBonusPolicy.id 
                        and DiscountBonusPolicyOutletGroup.for_so_sr=2
                        and DiscountBonusPolicyOutletGroup.create_for=5
                    '
                ),
                array(
                    'table' => 'outlet_group_to_outlets',
                    'alias' => 'OutletGroup',
                    'type' => 'inner',
                    'conditions' => '
                        DiscountBonusPolicyOutletGroup.reffrence_id=OutletGroup.outlet_group_id
                        and OutletGroup.outlet_id=' . $outlet_id
                ),
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

        $excluding_policy_id = array_map(function ($data) {
            return $data['DiscountBonusPolicy']['id'];
        }, $excluding_policy_id);


        $conditions['NOT']['DiscountBonusPolicy.id'] = $excluding_policy_id;

        $conditions['OR'] = array(
            'OutletGroup.id <>' => null,
            'DiscountBonusPolicySpecialGroup.id <>' => null,
            'AND' => array(
                'DiscountBonusPolicyOffice.reffrence_id' => $office_id,
                'DiscountBonusPolicyOutletCategory.reffrence_id' => $outlet_category_id,
                'DiscountBonusPolicyOutletCategory.id <>' => null,
            )
        );
        $policy_data = $this->DiscountBonusPolicy->find('all', array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'discount_bonus_policy_settings',
                    'alias' => 'DiscountBonusPolicyOffice',
                    'type' => 'left',
                    'conditions' => '
                        DiscountBonusPolicyOffice.discount_bonus_policy_id=DiscountBonusPolicy.id 
                        and DiscountBonusPolicyOffice.for_so_sr=2
                        and DiscountBonusPolicyOffice.create_for=2
                    '
                ),
                array(
                    'table' => 'discount_bonus_policy_settings',
                    'alias' => 'DiscountBonusPolicyOutletCategory',
                    'type' => 'left',
                    'conditions' => '
                        DiscountBonusPolicyOutletCategory.discount_bonus_policy_id=DiscountBonusPolicy.id 
                        and DiscountBonusPolicyOutletCategory.for_so_sr=2
                        and DiscountBonusPolicyOutletCategory.create_for=4
                    '
                ),
                array(
                    'table' => 'discount_bonus_policy_settings',
                    'alias' => 'DiscountBonusPolicyOutletGroup',
                    'type' => 'left',
                    'conditions' => '
                        DiscountBonusPolicyOutletGroup.discount_bonus_policy_id=DiscountBonusPolicy.id 
                        and DiscountBonusPolicyOutletGroup.for_so_sr=2
                        and DiscountBonusPolicyOutletGroup.create_for=3
                    '
                ),
                array(
                    'table' => 'outlet_group_to_outlets',
                    'alias' => 'OutletGroup',
                    'type' => 'Left',
                    'conditions' => '
                        DiscountBonusPolicyOutletGroup.reffrence_id=OutletGroup.outlet_group_id
                        and OutletGroup.outlet_id=' . $outlet_id
                ),
                array(
                    'table' => 'discount_bonus_policy_settings',
                    'alias' => 'DiscountBonusPolicySpecialGroup',
                    'type' => 'left',
                    'conditions' => '
                        DiscountBonusPolicySpecialGroup.discount_bonus_policy_id=DiscountBonusPolicy.id 
                        and DiscountBonusPolicySpecialGroup.for_so_sr=2
                        and DiscountBonusPolicySpecialGroup.create_for=1
                        and DiscountBonusPolicySpecialGroup.reffrence_id in (' . implode(',', $special_group) . ')
                    '
                ),
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
        $policy_wise_product_data = array();
        foreach ($policy_product as $data) {
            $policy_wise_product_data[$data['DiscountBonusPolicyProduct']['discount_bonus_policy_id']][] = $data['DiscountBonusPolicyProduct']['product_id'];
        }
        $discount_array = array();
        $total_discount = 0;
        $bonus_html = '';
        $selected_bonus = array();
        $selected_set = array();
        $selected_policy_type = array();
        $selected_option_id = array();
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
                    'DiscountBonusPolicyOption.is_sr' => 1,
                    'DiscountBonusPolicyOption.min_qty_sale_unit <=' => $cart_combined_qty,
                ),
                'order' => array('DiscountBonusPolicyOption.min_qty desc'),
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
            $policy_id = $p_data['DiscountBonusPolicy']['id'];
            $option_id = $policy_option[$effective_slab_index]['DiscountBonusPolicyOption']['id'];
            $policy_type = $policy_option[$effective_slab_index]['DiscountBonusPolicyOption']['policy_type'];
            if (isset($old_selected_option_id[$policy_id]) && $old_selected_option_id[$policy_id] != $option_id) {
                unset($old_selected_bonus[$policy_id]);
                unset($old_selected_set[$policy_id]);
                unset($old_selected_policy_type[$policy_id]);
            }
            $selected_option_id[$policy_id] = $option_id;
            if ($policy_type == 0 || $policy_type == 2) {
                $discount_array[] = $this->create_discount_array($policy_option[$effective_slab_index], $product_wise_cart_qty, $total_discount);
            }
            if ($policy_type == 1 || $policy_type == 2) {
                $bonus_html .= '<tr class="n_bonus_row"><th colspan="4">' . $p_data['DiscountBonusPolicy']['name'] . '</th><tr>';
                $bonus_html .= $this->create_bonus_html($store_id, $product_wise_cart_qty, $policy_option[$effective_slab_index], $cart_combined_qty, $selected_bonus, $old_selected_bonus, $selected_set, $old_selected_set);
            } else if ($policy_type == 3) {

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
                    $bonus_html .= $this->create_bonus_html($store_id, $product_wise_cart_qty, $policy_option[$effective_slab_index], $cart_combined_qty, $selected_bonus, $old_selected_bonus, $selected_set, $old_selected_set);
            }
        }
        $result = array();
        $result['discount'] = $discount_array;
        $result['total_discount'] = $total_discount;
        $result['bonus_html'] = $bonus_html;
        $result['selected_bonus'] = $selected_bonus;
        $result['selected_set'] = $selected_set;
        $result['selected_policy_type'] = $selected_policy_type;
        $result['selected_option_id'] = $selected_option_id;
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
                    'conditions' => 'PC.id=DiscountBonusPolicyOptionPriceSlab.sr_slab_id'
                )
            ),
            'fields' => array('PC.sr_price', 'DiscountBonusPolicyOptionPriceSlab.discount_product_id', 'PC.id'),
            'recursive' => -1
        ));
        $discount_amount = $policy_option['DiscountBonusPolicyOption']['discount_amount'];
        $policy_id = $policy_option['DiscountBonusPolicyOption']['discount_bonus_policy_id'];
        $discount_type = $policy_option['DiscountBonusPolicyOption']['disccount_type'];
        $policy_type = $policy_option['DiscountBonusPolicyOption']['policy_type'];
        $discount_array = array();
        foreach ($price_slabs as $pr_slab_data) {
            if (isset($product_wise_cart_qty[$pr_slab_data['DiscountBonusPolicyOptionPriceSlab']['discount_product_id']])) {
                if ($discount_type == 0)
                    $discount_amount_price = ($pr_slab_data['PC']['sr_price'] * $discount_amount / 100);
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
                    'price' => $pr_slab_data['PC']['sr_price'],
                    'total_value' => sprintf("%0.2f", $pr_slab_data['PC']['sr_price'] * $product_wise_cart_qty[$pr_slab_data['DiscountBonusPolicyOptionPriceSlab']['discount_product_id']]),
                    'price_id' => $pr_slab_data['PC']['id'],
                    'total_discount_value' => sprintf("%0.2f", $discount_value),
                );
            }
        }
        return $discount_array;
    }
    private function create_bonus_html($store_id, $cart_qty, $policy_option, $combined_qty, &$selected_bonus, $old_selected_bonus, &$selected_set, $old_selected_set)
    {
        $this->loadModel('DiscountBonusPolicyOptionBonusProduct');
        $this->loadModel('DistCurrentInventory');
        $this->loadModel('Product');
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
            // $i=0;
            $given_qty = 0;
            foreach ($bonus_product as $data) {
                $product_id = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id'];
                $bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
                $provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
                $stock_qty = $this->DistCurrentInventory->find('first', array(
                    'conditions' => array(
                        'store_id' => $store_id,
                        'product_id' => $product_id
                    ),
                    'fields' => array('sum(qty) as qty'),
                    'recursive' => -1
                ));
                $prod_info = $this->Product->find('first', array(
                    'conditions' => array('id' => $product_id),
                    'recursive' => -1
                ));
                $cart_qty = isset($cart_qty[$product_id]) ? $cart_qty[$product_id] : 0;
                $cart_qty = $this->unit_convert($product_id, $prod_info['Product']['sales_measurement_unit_id'], $cart_qty);
                $stock_qty = $this->unit_convertfrombase($product_id, $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'], ($stock_qty[0]['qty'] - $cart_qty));
                if (isset($old_selected_bonus[$policy_id])) {
                    if (isset($old_selected_bonus[$policy_id][1][$product_id]) && $old_selected_bonus[$policy_id][1][$product_id] > 0) {
                        $value = $old_selected_bonus[$policy_id][1][$product_id];
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

                    if ($stock_qty > 0 && $given_qty != $provide_bonus_qty) {
                        if ($stock_qty < ($provide_bonus_qty - $given_qty)) {
                            $given_qty += $stock_qty;
                            $value = $stock_qty;
                        } else if ($stock_qty > ($provide_bonus_qty - $given_qty)) {
                            $value = ($provide_bonus_qty - $given_qty);
                            $given_qty += ($provide_bonus_qty - $given_qty);
                        }
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
                $selected_bonus[$policy_id]['1'][$product_id] = $value;
                $b_html .= '
                    <tr class="bonus_row n_bonus_row bonus_policy_id_' . $policy_id . '">
                        <th class="text-center" id="bonus_product_list">
                            <div class="input select">
                                <select ' . $s_disabled . ' name="data[OrderDetail][product_id][]" class="form-control width_100_this policy_bonus_product_id">
                                <option value="' . $product_id . '">' . $data['Product']['name'] . '</option>
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
                            <input ' . $s_disabled . ' type="hidden" name="data[OrderDetail][selected_set][' . $policy_id . ']" step="any" class="set_id" value="1">
                        </th>
                        <th class="text-center" width="12%">
                            <input ' . $min_qty_disabled . ' ' . $min_qty_required . ' data-set="1" data-stock="' . $stock_qty . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[OrderDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_1" value="' . $value . '">
                        </th>
                        <th class="text-center" width="10%">
                            <input type="checkbox" ' . $min_qty_checked . ' class="is_bonus_checked" name="data[OrderDetail][bonus_check][]" value="' . $product_id . '">
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
                        $s_disabled = 'readonly';
                        $given_qty = 0;
                        foreach ($formula_product as $product_id) {
                            $data = $product_wise_bonus[$product_id];
                            $bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];
                            $provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
                            $stock_qty = $this->DistCurrentInventory->find('first', array(
                                'conditions' => array(
                                    'store_id' => $store_id,
                                    'product_id' => $product_id
                                ),
                                'fields' => array('sum(qty) as qty'),
                                'recursive' => -1
                            ));
                            $prod_info = $this->Product->find('first', array(
                                'conditions' => array('id' => $product_id),
                                'recursive' => -1
                            ));
                            $cart_qty = isset($cart_qty[$product_id]) ? $cart_qty[$product_id] : 0;
                            $cart_qty = $this->unit_convert($product_id, $prod_info['Product']['sales_measurement_unit_id'], $cart_qty);
                            $stock_qty = $this->unit_convertfrombase($product_id, $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'], ($stock_qty[0]['qty'] - $cart_qty));
                            if (isset($old_selected_bonus[$policy_id])) {
                                if (
                                    isset($old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']])
                                    && $old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] > 0
                                ) {
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
                                if ($stock_qty > 0 && $given_qty != $provide_bonus_qty) {
                                    if ($stock_qty < ($provide_bonus_qty - $given_qty)) {
                                        $given_qty += $stock_qty;
                                        $value = $stock_qty;
                                    } else if ($stock_qty > ($provide_bonus_qty - $given_qty)) {
                                        $value = ($provide_bonus_qty - $given_qty);
                                        $given_qty += ($provide_bonus_qty - $given_qty);
                                    }
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
                            $selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
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
                                        <input ' . $min_qty_disabled . ' ' . $min_qty_required . ' data-set="' . $set . '" data-stock="' . $stock_qty . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[OrderDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_' . $set . '" value="' . $value . '">
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

                            $provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);

                            $value = $provide_bonus_qty;
                            $min_qty_disabled = 'readonly';
                            $min_qty_checked = 'checked';
                            $min_qty_required = 'required';



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
                        $s_disabled = 'readonly';
                        $given_qty = 0;
                        foreach ($formula_product as $product_id) {
                            $data = $product_wise_bonus[$product_id];
                            $bonus_qty = $data['DiscountBonusPolicyOptionBonusProduct']['bonus_qty'];

                            $provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);
                            $stock_qty = $this->DistCurrentInventory->find('first', array(
                                'conditions' => array(
                                    'store_id' => $store_id,
                                    'product_id' => $product_id
                                ),
                                'fields' => array('sum(qty) as qty'),
                                'recursive' => -1
                            ));
                            $prod_info = $this->Product->find('first', array(
                                'conditions' => array('id' => $product_id),
                                'recursive' => -1
                            ));
                            $cart_qty = isset($cart_qty[$product_id]) ? $cart_qty[$product_id] : 0;
                            $cart_qty = $this->unit_convert($product_id, $prod_info['Product']['sales_measurement_unit_id'], $cart_qty);
                            $stock_qty = $this->unit_convertfrombase($product_id, $data['DiscountBonusPolicyOptionBonusProduct']['measurement_unit_id'], ($stock_qty[0]['qty'] - $cart_qty));
                            if (isset($old_selected_bonus[$policy_id])) {
                                if (
                                    isset($old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']])
                                    && $old_selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] > 0
                                ) {
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

                                if ($stock_qty > 0 && $given_qty != $provide_bonus_qty) {
                                    if ($stock_qty < ($provide_bonus_qty - $given_qty)) {
                                        $given_qty += $stock_qty;
                                        $value = $stock_qty;
                                    } else if ($stock_qty > ($provide_bonus_qty - $given_qty)) {
                                        $value = ($provide_bonus_qty - $given_qty);
                                        $given_qty += ($provide_bonus_qty - $given_qty);
                                    }
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
                            $selected_bonus[$policy_id][$set][$data['DiscountBonusPolicyOptionBonusProduct']['bonus_product_id']] = $value;
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
                                        <input ' . $min_qty_disabled . ' ' . $disabled . ' ' . $min_qty_required . ' data-set="' . $set . '"  data-stock="' . $stock_qty . '" type="number" min="1" max="' . $provide_bonus_qty . '" name="data[OrderDetail][sales_qty][]" class="width_100_this policy_min_qty ' . $policy_id . '_policy_set_' . $set . '" value="' . $value . '">
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
                            $provide_bonus_qty = floor(($combined_qty * $bonus_qty) / $min_qty);

                            $value = $provide_bonus_qty;
                            $min_qty_disabled = 'readonly';
                            $min_qty_checked = 'checked';
                            $min_qty_required = 'required';



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
            if (!isset($formula_element_relation[$set])) {
                $formula_element_relation[$set] = 'AND';
            }
        }
        $parse_formula = array(
            'set_relation' => $set_relation,
            'formula_product' => $formula_product,
            'element_relation' => $formula_element_relation
        );
        return $parse_formula;
    }
    public function get_spcial_group_and_outlet_category_id()
    {
        $this->loadModel('SpecialGroup');
        $this->loadModel('SpecialGroupOtherSetting');
        $memo_date = date('Y-m-d', strtotime($this->request->data['memo_date']));
        $outlet_id = $this->request->data['outlet_id'];
        $office_id = $this->request->data['office_id'];
        $outlet_info = $this->DistOutlet->find('first', array(
            'conditions' => array(
                'DistOutlet.id' => $outlet_id
            ),
            'recursive' => -1
        ));
        $outlet_category_id = $outlet_info['DistOutlet']['category_id'];
        $conditions[] = array('SpecialGroup.start_date <=' => $memo_date);
        $conditions[] = array('SpecialGroup.end_date >=' => $memo_date);
        $conditions[] = array('SPO.reffrence_id' => $office_id);
        $conditions[] = array('SPOC.reffrence_id' => $outlet_category_id);
        $conditions[] = array('SpecialGroup.is_dist' => 1);

        $special_group_data = $this->SpecialGroup->find('all', array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'special_group_other_settings',
                    'alias' => 'SPO',
                    'conditions' => 'SpecialGroup.id=SPO.special_group_id and SPO.create_for=1'
                ),
                array(
                    'table' => 'special_group_other_settings',
                    'alias' => 'SPOC',
                    'conditions' => 'SpecialGroup.id=SPOC.special_group_id and SPOC.create_for=3'
                ),
            ),
            'recursive' => -1
        ));
        $special_gorup_id = array_map(function ($data) {
            return $data['SpecialGroup']['id'];
        }, $special_group_data);

        $conditions = array();
        $conditions[] = array('SpecialGroup.start_date <=' => $memo_date);
        $conditions[] = array('SpecialGroup.end_date >=' => $memo_date);
        $conditions[] = array('SpecialGroup.is_dist' => 1);
        $conditions[] = array('OutletGroup.outlet_id' => $outlet_id);
        $special_group_data = $this->SpecialGroup->find('all', array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'special_group_other_settings',
                    'alias' => 'GrpOg',
                    'conditions' => 'SpecialGroup.id=GrpOg.special_group_id and GrpOg.create_for=4'
                ),
                array(
                    'table' => 'outlet_group_to_outlets',
                    'alias' => 'OutletGroup',
                    'type' => 'Left',
                    'conditions' => 'GrpOg.reffrence_id=OutletGroup.outlet_group_id'
                ),
            ),
            'recursive' => -1
        ));
        $special_gorup_id = array_merge($special_gorup_id, array_map(function ($data) {
            return $data['SpecialGroup']['id'];
        }, $special_group_data));
        $result['outlet_category_id'] = $outlet_category_id;
        $result['special_group_id'] = $special_gorup_id;
        echo json_encode($result);
        exit;
        $this->autoRender = false;
    }
    public function memo_stock_check()
    {

        $this->loadModel('CurrentInventory');
        $this->loadModel('Store');
        $this->loadModel('Product');
        $product = json_decode($this->request->data['product_qty'], 1);
        $territory_id = $this->request->data['territory_id'];

        $store_info = $this->Store->find('first', array(
            'conditions' => array(
                'Store.territory_id' => $territory_id,
                'Store.store_type_id' => 3
            ),
            'recursive' => -1
        ));
        $store_id = $store_info['Store']['id'];
        $product_wise_qty = array();
        foreach ($product as $pid => $p_data) {
            foreach ($p_data as $unit_id => $qty) {
                $prev_qty = 0;
                if (isset($product_wise_qty[$pid])) {
                    $prev_qty = $product_wise_qty[$pid];
                }
                $product_wise_qty[$pid] = $prev_qty + $this->unit_convert($pid, $unit_id, $qty);
            }
        }
        $prev_memo_array = array();
        if (isset($this->request->data['memo_id'])) {
            $memo_id = $this->request->data['memo_id'];
            $prev_memo_data = $this->MemoDetail->find('all', array(
                'conditions' => array('memo_id' => $memo_id, 'product_id' => array_keys($product_wise_qty)),
                'recursive' => -1
            ));
            foreach ($prev_memo_data as $data) {
                $prev_qty = 0;
                if (isset($prev_memo_array[$data['MemoDetail']['product_id']])) {
                    $prev_qty = $prev_memo_array[$data['MemoDetail']['product_id']];
                }
                $prev_memo_array[$data['MemoDetail']['product_id']] = $prev_qty + $this->unit_convert($data['MemoDetail']['product_id'], $data['MemoDetail']['measurement_unit_id'], $data['MemoDetail']['sales_qty']);
            }
        }
        $this->CurrentInventory->virtualFields = array('qty_sum' => 'sum(CurrentInventory.qty)');
        $inventory_data = $this->CurrentInventory->find('list', array(
            'conditions' => array(
                'store_id' => $store_id,
                'product_id' => array_keys($product_wise_qty)
            ),
            'group' => array('product_id'),
            'fields' => array('product_id', 'qty_sum'),
            'recursive' => -1
        ));
        $short_stock_product = array();
        $stock_available = 1;
        foreach ($product_wise_qty as $pid => $qty) {
            $prev_memo_qty = 0;
            if (isset($prev_memo_array[$pid])) {
                $prev_memo_qty = $prev_memo_array[$pid];
            }
            if (!isset($inventory_data[$pid]) || ($qty - $prev_memo_qty) > $inventory_data[$pid]) {
                $short_stock_product[] = $pid;
                $stock_available = 0;
            }
        }
        $res['status'] = $stock_available;
        if ($stock_available == 0) {
            $product = $this->Product->find('list', array(
                'conditions' => array('id' => $short_stock_product),
                'fields' => array('id', 'name'),
            ));
            $res['msg'] = 'Stock Short For ' . implode(',', $product);
        }
        echo json_encode($res);
        $this->autoRender = false;
    }

    public function download_xl()
    {

        //$this->Session->delete('from_outlet');
        //$this->Session->delete('from_market');
        

        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 99999); //300 seconds = 5 minutes
        
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $params = $this->request->query['data'];
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        
        

        $office_parent_id = $this->UserAuth->getOfficeParentId();
       
        $conditions = array();
        
        // echo $params['DistOrder.date_from'];exit;
        if(!empty($params['DistOrder']['office_id']))
        {
            $conditions[] = array('DistOrder.office_id' => $params['DistOrder']['office_id']);
        
        }
        if(empty($params['DistOrder']['office_id'])){
        	if($office_parent_id != 0)
			{
				$conditions[] = array('DistOrder.office_id' => $office_parent_id);
			}
        }
        if (!empty($params['DistOrder']['order_reference_no'])) 
        {
            $conditions[] = array('DistOrder.dist_order_no Like' => "%".$params['DistOrder']['order_reference_no']."%");
        }
		if (!empty($params['DistOrder']['dist_route_id'])) 
		{
            $conditions[] = array('DistOrder.dist_route_id' => $params['DistOrder']['dist_route_id']);
        }
		if (!empty($params['DistOrder']['outlet_id'])) 
		{
            $conditions[] = array('DistOrder.outlet_id' => $params['DistOrder']['outlet_id']);
        }
		if(!empty($params['DistOrder']['market_id'])) 
		{
            $conditions[] = array('DistOrder.market_id' => $params['DistOrder']['market_id']);
        }
        if (!empty($params['DistOrder']['thana_id'])) 
        {
            $conditions[] = array('Market.thana_id' => $params['DistOrder']['thana_id']);
        }
		if (!empty($params['DistOrder']['territory_id'])) 
		{
            $conditions[] = array('DistOrder.territory_id' => $params['DistOrder']['territory_id']);
        }
        if (!empty($params['DistOrder']['tso_id'])) 
		{
            $conditions[] = array('DistOrder.tso_id' => $params['DistOrder']['tso_id']);
        }else{
        	if($user_group_id == 1029){
               
                // App::import('Model', 'DistTso');
                // $this->DistTso = new DistTso();
                $user_id = $this->UserAuth->getUserId();

	            $dist_tso_info = $this->DistTso->find('first',array(
                        'conditions'=>array('DistTso.user_id'=>$user_id),
                        'fields'=> array('DistTso.id','DistTso.name'),
                        'recursive'=> -1,
                    ));
                $dist_tso_id = $dist_tso_info['DistTso']['id'];
                $conditions[] = array('DistOrder.tso_id' => $dist_tso_id);
            }
        }
        if (!empty($params['DistOrder']['distributor_id'])) 
        {
            $conditions[] = array('DistOrder.distributor_id' => $params['DistOrder']['distributor_id']);
        }else{
        	if($user_group_id == 1034){
                // App::import('Model', 'DistUserMapping');
                $sp_id = CakeSession::read('UserAuth.User.sales_person_id');
                // $this->DistUserMapping = new DistUserMapping();
                $data = $this->DistUserMapping->find('first',array('conditions'=>array('DistUserMapping.sales_person_id'=>$sp_id)));
                $distributor_id = $data['DistUserMapping']['dist_distributor_id'];
                $conditions[] = array('DistOrder.distributor_id' => $distributor_id);
            }
        }
        
        if (!empty($params['DistOrder']['sr_id'])) 
        {
            $conditions[] = array('DistOrder.sr_id' => $params['DistOrder']['sr_id']);
        }

		if (isset($params['DistOrder']['status'])!='' && !empty($params['DistOrder']['status']))
		{
			if($params['DistOrder']['status'] == 2 || $params['DistOrder']['status'] == 4){
				if($params['DistOrder']['status'] == 2){
					$conditions[] = array('DistOrder.processing_status' => 1);
				}
				else{
					$conditions[] = array('DistOrder.processing_status' => 2);
				}
			}else{
				$conditions[] = array('DistOrder.status' => $params['DistOrder']['status']);
			}
            
        }
		if (isset($params['DistOrder']['date_from'])!='') 
		{
            $conditions[] = array('DistOrder.order_date >=' => Date('Y-m-d H:i:s',strtotime($params['DistOrder']['date_from'])));
        }
		if (isset($params['DistOrder']['date_to'])!='') 
		{
            $conditions[] = array('DistOrder.order_date <=' => Date('Y-m-d H:i:s',strtotime($params['DistOrder']['date_to'].' 23:59:59')));
        }   
        if (isset($params['DistOrder']['from_app'])) 
		{
            $conditions[] = array('DistOrder.from_app' => 0);
        }
		
        // pr($conditions);exit;


        /*start here */
         
            // $this->DistOrder->recursive = 0;
            $result = $this->DistOrder->find('all',array(
                'fields' => array('DistOrder.*', 'DistOutlet.name', 'DistMarket.name', 'DistDistributor.name', 'DistRoute.name','DistTso.name','Office.office_name','DistAE.name'),
                'conditions' => $conditions,
                'joins' => array(
                    array(
                        'alias' => 'DistOutlet',
                        'table' => 'dist_outlets',
                        'type' => 'INNER',
                        'conditions' => 'DistOutlet.id = DistOrder.outlet_id'
                    ),
                    array(
                        'alias' => 'DistMarket',
                        'table' => 'dist_markets',
                        'type' => 'INNER',
                        'conditions' => 'DistMarket.id = DistOrder.market_id'
                    ),
                    array(
                        'alias' => 'DistDistributor',
                        'table' => 'dist_distributors',
                        'type' => 'INNER',
                        'conditions' => 'DistDistributor.id = DistOrder.distributor_id'
                    ),
                    array(
                        'alias' => 'DistRoute',
                        'table' => 'dist_routes',
                        'type' => 'INNER',
                        'conditions' => 'DistRoute.id = DistOrder.dist_route_id'
                    ),
                    array(
                        'table'=>'dist_tso_mappings',
                        'alias'=>'DistTsoMapping',
                        'type' => 'LEFT',
                        'conditions' => 'DistTsoMapping.dist_distributor_id = DistOrder.distributor_id'
                        
                    ),
                    array(
                        'table'=>'dist_tsos',
                        'alias'=>'DistTso',
                        'type' => 'LEFT',
                        'conditions' => 'DistTso.id = DistTsoMapping.dist_tso_id'
                        
                    ),
                    array(
                        'table'=>'dist_area_executives',
                        'alias'=>'DistAE',
                        'type' => 'LEFT',
                        'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'
                        
                    ),
                    

                ),

                'order' => array('DistOrder.id' => 'desc'),
                'recursive' => 0

                
            ));
            
        
        
        $orders = $result;
        // echo $this->DistOrder->getLastquery().'<br>';
        
        $sr_names = array();
        //pr($orders);die();
        $total_order_value_find = $this->DistOrder->find('all', array(
            'fields' => array('SUM(DistOrder.gross_value) as total_order'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'alias' => 'DistOutlet',
                    'table' => 'dist_outlets',
                    'type' => 'INNER',
                    'conditions' => 'DistOutlet.id = DistOrder.outlet_id'
                ),
                array(
                    'alias' => 'DistMarket',
                    'table' => 'dist_markets',
                    'type' => 'INNER',
                    'conditions' => 'DistMarket.id = DistOrder.market_id'
                ),
                array(
                    'alias' => 'DistDistributor',
                    'table' => 'dist_distributors',
                    'type' => 'INNER',
                    'conditions' => 'DistDistributor.id = DistOrder.distributor_id'
                )
            ),
            'reccursive' => -1
        ));
        // echo $this->DistOrder->getLastquery();exit;
        $total_value_of_order = $total_order_value_find[0][0]['total_order'];
        if ($orders) {

            foreach ($orders as $key => $value) {
                $sr_lists[$key] = $value['DistOrder']['sr_id'];
                // $total_value_of_order = $total_value_of_order + $value['DistOrder']['gross_value'];
            }
            $count_sr = count($sr_lists);
            $this->loadmodel('DistSalesRepresentative');
            if ($count_sr == 1) {
                $sr_info = $this->DistSalesRepresentative->find('all', array(
                    'conditions' => array('DistSalesRepresentative.id' => $sr_lists),
                    'recursive' => -1,
                ));
            } else {
                $sr_info = $this->DistSalesRepresentative->find('all', array(
                    'conditions' => array('DistSalesRepresentative.id IN' => $sr_lists),
                    'recursive' => -1,
                ));
            }
            foreach ($sr_info as $key => $value) {
                $sr_names[$value['DistSalesRepresentative']['id']] = $value['DistSalesRepresentative']['name'];
            }
        }
        


        

        

        // $this->set(compact('offices', 'distributors', 'distributor_id', 'srs', 'sr_id', 'territories', 'markets', 'outlets', 'current_date', 'requested_data', 'product_name', 'sr_wise_sales_bonus_report', 'product_list', 'p_wise_sales_bonus_report', 'sr_names', 'status_list', 'routes', 'tso_id', 'total_value_of_order'));
        
                        
        $table = '<table border="1">
							<thead>
								<tr>
									<td><b>Id</b></td>
									<td><b>Order No</b></td>
                                    <td><b>Area Office</b></td>
                                    <td><b>Area Executive</b></td>
                                    <td><b>TSO</b></td>
									<td><b>Distributor</b></td>
									<td><b>SR Name</b></td>
									<td><b>Outlet</b></td>
									<td><b>Market</b></td>
									<td><b>Route</b></td>				
									<td><b>Amount</b></td>
									<td><b>Order date</b></td>
									<td><b>Status<b></td>
									
									
								</tr>
							</thead>
							<tbody>';
							
							$total_amount = 0;
							foreach ($orders as $Order): 
							$Order['DistOrder']['from_app']=0;
						    
							$table .= '<tr>
								<td>'.h($Order['DistOrder']['id']).'</td>
								<td>'. h($Order['DistOrder']['dist_order_no']).'</td>
                                <td>'. h($Order['Office']['office_name']).'</td>
                                <td>'. h($Order['DistAE']['name']).'</td>
                                <td>'. h($Order['DistTso']['name']).'</td>
								<td>'. h($Order['DistDistributor']['name']).'</td>
								<td>'. h($sr_names[$Order['DistOrder']['sr_id']]).'</td>
								<td>'. h($Order['DistOutlet']['name']).'</td>
								<td>'. h($Order['DistMarket']['name']).'</td>
								<td>'. h($Order['DistRoute']['name']).'</td>
								<td>'. sprintf('%.2f',$Order['DistOrder']['gross_value']).'</td>
								<td>'.date("d-m-Y",strtotime($Order['DistOrder']['order_date'])).'</td>';
								
                            $table .= '<td>';
								
								if($Order['DistOrder']['status']==0){
                                    $table .= 'Draft';	
								}elseif($Order['DistOrder']['status']==1){
									
                                    $table .= 'Pending';
								}elseif($Order['DistOrder']['processing_status']==1){
									
                                    $table .= 'Invoice Created';	
								}elseif($Order['DistOrder']['status']==3){
										
                                    $table .= 'Cancel';
								}elseif($Order['DistOrder']['processing_status']==2){
									
                                    $table .= 'Delivered';
								}
								
								$table .= '</td>';
								
								
								
								
								
                            $table .= '</tr>';
							
							$total_amount = $total_amount + $Order['DistOrder']['gross_value'];
							endforeach; 					
							
							$table .= '
							<tr>
								<td align="right" colspan="10"><b>Total Amount :</b></td>
								<td>'. sprintf('%.2f',$total_value_of_order).'</td>
								<td class="text-center" colspan="2"></td>
							</tr>
							</tbody>
						</table>';

            header("Content-Type: application/vnd.ms-excel");
            header('Content-Disposition: attachment; filename="DistOrderDeliveries.xls"');
            header("Cache-Control: ");
            header("Pragma: ");
            header("Expires: 0");
            echo "\xEF\xBB\xBF"; //UTF-8 BOM
            echo $table;
            $this->autoRender = false;
                    
                    
    }
}
