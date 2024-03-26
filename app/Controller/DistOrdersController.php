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
class DistOrdersController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $uses = array('DistOrder', 'DistDistributor', 'DistSalesRepresentative', 'Thana', 'SalesPerson', 'DistMarket', 'DistSalesRepresentative', 'DistOutlet', 'Product', 'MeasurementUnit', 'DistSrProductPrice', 'DistSrProductCombination', 'DistSrCombination', 'DistOrderDetail', 'MeasurementUnit', 'DistRoute', 'DistTsoMappingHistory', '');
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
        $status_list = array(0 => 'Draft', 1 => ' Pending', 2 => 'Invoice Created', 3 => 'Cancel', 4 => 'Delivered');
        $requested_data = $this->request->data;
        // print_r($requested_data);exit();
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
                /*$report_conditions = array();
            
            $params=$this->request->data;
            if(!empty($params['DistOrder']['office_id']))
            {
                
                $report_conditions[] = array('DistOrder.office_id' => $params['DistOrder']['office_id']);
            }
            else
            {

                if ($office_parent_id == 0) {
                   
                } else {

                    $report_conditions[] = array('DistOrder.office_id' => $this->UserAuth->getOfficeId());
                }

                
            }

            if (!empty($params['DistOrder']['outlet_id'])) {
                $report_conditions[] = array('DistOrder.outlet_id' => $params['DistOrder']['outlet_id']);
            }
            elseif(!empty($params['DistOrder']['market_id'])) {
                $report_conditions[] = array('DistOrder.market_id' => $params['DistOrder']['market_id']);
            }
            elseif (!empty($params['DistOrder']['thana_id'])) {
                $report_conditions[] = array('Market.thana_id' => $params['DistOrder']['thana_id']);
            }
            elseif (!empty($params['DistOrder']['territory_id'])) 
            {
                $report_conditions[] = array('DistOrder.territory_id' => $params['DistOrder']['territory_id']);
            }

            if (!empty($params['DistOrder']['sr_id'])) {
                $report_conditions[] = array('DistOrder.sr_id' => $params['DistOrder']['sr_id']);
            }
            elseif (!empty($params['DistOrder']['distributor_id'])) {
                $report_conditions[] = array('DistOrder.distributor_id' => $params['DistOrder']['distributor_id']);
            }

            if (isset($params['DistOrder']['date_from'])!='') {
                $report_conditions[] = array('DistOrder.order_date >=' => Date('Y-m-d H:i:s',strtotime($params['DistOrder']['date_from'])));
            }
            if (isset($params['DistOrder']['date_to'])!='') {
                $report_conditions[] = array('DistOrder.order_date <=' => Date('Y-m-d H:i:s',strtotime($params['DistOrder']['date_to'].' 23:59:59')));
            }
            
            pr($report_conditions);*/

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
                /*echo $this->DistOrder->getLastquery();
            pr($product_wise_sales_bonus_report);exit;*/

                // $sql = "select product_id,COUNT(DISTINCT dist_order_details.dist_order_id) as ec,sum(dist_order_details.bonus_qty) as bonus_amount from dist_order_details left join dist_Orders on dist_Orders.id=dist_order_details.dist_order_id where dist_Orders.order_date BETWEEN '".$date_from."' and '".$date_to."' group by dist_order_details.product_id";               
                // $product_wise_sales_bonus_report = $this->DistOrder->query($sql);
                // $product_list = $this->Product->find('list', array('order' => array('Product.id' => 'asc'),'recursive' => -1));
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
            // print_r($conditions);exit;
            $this->DistOrder->recursive = 0;
            $this->paginate = array(
                'fields' => array('DistOrder.*', 'DistOutlet.name', 'DistMarket.name', 'DistDistributor.name', 'DistRoute.name', 'DistTso.name', 'Office.office_name', 'DistAE.name'),
                'conditions' => $conditions,
                'joins' => array(
                    array(
                        'alias' => 'DistOutlet',
                        'table' => 'dist_outlets',
                        'type' => 'left',
                        'conditions' => 'DistOutlet.id = DistOrder.outlet_id'
                    ),
                    array(
                        'alias' => 'DistMarket',
                        'table' => 'dist_markets',
                        'type' => 'left',
                        'conditions' => 'DistMarket.id = DistOrder.market_id'
                    ),
                    array(
                        'alias' => 'DistDistributor',
                        'table' => 'dist_distributors',
                        'type' => 'left',
                        'conditions' => 'DistDistributor.id = DistOrder.distributor_id'
                    ),
                    array(
                        'alias' => 'DistRoute',
                        'table' => 'dist_routes',
                        'type' => 'left',
                        'conditions' => 'DistRoute.id = DistOrder.dist_route_id'
                    ),

                    array(
                        'table' => 'dist_tsos',
                        'alias' => 'DistTso',
                        'type' => 'INNER',
                        'conditions' => array('DistTso.id=DistOrder.tso_id')
                    ),
                    array(
                        'table' => 'dist_area_executives',
                        'alias' => 'DistAE',
                        'type' => 'INNER',
                        'conditions' => array('DistAE.id=DistTso.dist_area_executive_id')


                    ),
                    /*array(
                        'alias' => 'DistSalesRepresentative',
                        'table' => 'dist_sales_representatives',
                        'type' => 'INNER',
                        'conditions' => 'DistSalesRepresentative.id = DistOrder.sr_id'
                    ),*/

                ),

                'order' => array('DistOrder.id' => 'desc'),

                'limit' => 100
            );
            $this->set('dist_Orders', $this->paginate());
            // echo $this->DistOrder->getLastquery();exit;
            // print_r($this->paginate());die();

        }




        $orders = $this->paginate();
        // echo '<pre>';
        // print_r($orders);
        // echo '</pre>';exit;

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
                'conditions' => array(
                    'DistOrder.distributor_id' => $distributor_id,
                    'DistOrder.order_date >=' => $order_date_from,
                    'DistOrder.order_date <=' => $order_date_to,
                ),
                'joins' => array(array('table' => 'dist_Orders', 'alias' => 'DistOrder', 'conditions' => 'DistOrder.sr_id=DistSalesRepresentative.id')),
                'order' => array('DistSalesRepresentative.name' => 'asc')
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

            // pr($this->request->data); 
            // exit;

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
                    //pr($this->request->data);exit;
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
                        $OrderData['gross_value'] = $this->request->data['DistOrder']['gross_value'];
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

                        $OrderData['order_reference_no'] = $this->request->data['DistOrder']['order_reference_no'];

                        $OrderData['created_at'] = $this->current_datetime();
                        $OrderData['created_by'] = $this->UserAuth->getUserId();
                        $OrderData['updated_at'] = $this->current_datetime();
                        $OrderData['updated_by'] = $this->UserAuth->getUserId();

                        $OrderData['office_id'] = $office_id ? $office_id : 0;
                        $OrderData['thana_id'] = $thana_id ? $thana_id : 0;
                        //pr($this->request->data);//die();
                        if ($this->request->data['DistOrder']['order_date'] >= date('Y-m-d', strtotime('-4 month'))) {
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
                                            if ($val == NULL) {
                                                continue;
                                            }

                                            $product_details = $this->Product->find('first', array(
                                                'fields' => array('id', 'is_virtual', 'parent_id'),
                                                'conditions' => array('Product.id' => $val),
                                                'recursive' => -1
                                            ));


                                            $sales_price = $this->request->data['OrderDetail']['Price'][$key];
                                            if ($sales_price > 0.00) {

                                                //$product_id = $order_details['DistOrderDetail']['product_id'] = $val;
                                                
                                                if ($product_details['Product']['is_virtual'] == 1) {
                                                    $product_id = $order_details['DistOrderDetail']['virtual_product_id'] = $val;
                                                    $order_details['DistOrderDetail']['product_id'] = $product_details['Product']['parent_id'];
                                                } else {
                                                    $order_details['DistOrderDetail']['virtual_product_id'] = 0;
                                                    $product_id = $order_details['DistOrderDetail']['product_id'] = $product_details['Product']['id'];
                                                }

                                                $order_details['DistOrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
                                                $order_details['DistOrderDetail']['price'] = $sales_price;
                                                $sales_qty = $order_details['DistOrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];

                                                $product_price_slab_id = 0;

                                                if ($sales_price > 0) {
                                                    $product_price_slab_id = $this->get_product_price_id($val, $sales_price, $all_product_id);
                                                    // pr($product_price_slab_id);exit;
                                                }
                                                $order_details['DistOrderDetail']['product_price_id'] = $product_price_slab_id['product_price_id'];
                                                $order_details['DistOrderDetail']['product_combination_id'] = $product_price_slab_id['combination_id'];

                                                //Start for bonus
                                                $order_date = date('Y-m-d', strtotime($this->request->data['DistOrder']['order_date']));

                                                $bonus_product_qty = $this->request->data['OrderDetail']['bonus_product_qty'];
                                                $order_details['DistOrderDetail']['is_bonus'] = 0;

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
                                            } else {
                                                $bonus_order_details['DistOrderDetail']['dist_order_id'] = $dist_order_id;
                                                //$product_id = $bonus_order_details['DistOrderDetail']['product_id'] = $val;

                                                if ($product_details['Product']['is_virtual'] == 1) {
                                                    $product_id = $order_details['DistOrderDetail']['virtual_product_id'] = $val;
                                                    $order_details['DistOrderDetail']['product_id'] = $product_details['Product']['parent_id'];
                                                } else {
                                                    $order_details['DistOrderDetail']['virtual_product_id'] = 0;
                                                    $product_id = $order_details['DistOrderDetail']['product_id'] = $product_details['Product']['id'];
                                                }

                                                $bonus_order_details['DistOrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
                                                $bonus_order_details['DistOrderDetail']['price'] = 0.0;
                                                $bonus_order_details['DistOrderDetail']['is_bonus'] = 1;
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

                            $this->redirect(array("controller" => "DistOrders", 'action' => 'index'));
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
                $qry_route = "select distinct dist_route_id from dist_sr_route_mapping_histories
                    where dist_sr_id=$sr_id and dist_distributor_id=$distributor_id and is_change=1 and 
                    '" . $market_order_date . "' between effective_date and 
                    case 
                    when end_date is not null then 
                     end_date
                    else 
                    getdate()
                    end";

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
            $qry_route = "select distinct dist_route_id from dist_sr_route_mapping_histories
                    where dist_sr_id=$sr_id and dist_distributor_id=$distributor_id and is_change=1 and 
                    '" . $order_date . "' between effective_date and 
                    case 
                    when end_date is not null then 
                     end_date
                    else 
                    getdate()
                    end";

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
            $this->DistSrCombination->unbindModel(
                array('hasMany' => array('DistSrProductCombination'))
            );
            $DistSrCombination_list = $this->DistSrCombination->find('all', array(
                'conditions' => array('DistSrProductCombination.product_id' => $product),
                'joins' => array(
                    array(
                        'alias' => 'DistSrProductCombination',
                        'table' => 'dist_sr_product_combinations',
                        'type' => 'INNER',
                        'conditions' => 'DistSrCombination.id = DistSrProductCombination.combination_id'
                    )
                ),
                'fields' => array('DistSrCombination.all_products_in_combination'),
                'limit' => 1
            ));

            if (!empty($DistSrCombination_list)) {
                $combined_product = $DistSrCombination_list[0]['DistSrCombination']['all_products_in_combination'];
                $detail_val['combined_product'] = $combined_product;
            }
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


        foreach ($existing_record['DistOrderDetail'] as $key => $value) {
            $existing_product_category_id_array = $this->Product->find('all', array(
                'conditions' => array('Product.id' => $value['product_id']),
                'fields' => array('product_category_id'),
                'recursive' => -1
            ));

            $existing_product_category_id = $existing_product_category_id_array[0]['Product']['product_category_id'];

            if ($existing_product_category_id != 32) {
                $individual_slab = array();
                $combined_slab = array();
                $all_combination_id = array();

                $retrieve_price_DistSrCombination[$value['product_id']] = $this->DistSrProductPrice->find('all', array(
                    'conditions' => array('DistSrProductPrice.product_id' => $value['product_id'], 'DistSrProductPrice.has_combination' => 0)
                ));
                //pr($retrieve_price_DistSrCombination[$value['product_id']]);
                foreach ($retrieve_price_DistSrCombination[$value['product_id']][0]['DistSrProductCombination'] as $key => $value2) {
                    $individual_slab[$value2['min_qty']] = $value2['price'];
                }


                $DistSrCombination_info = $this->DistSrProductCombination->find('first', array(
                    'conditions' => array('DistSrProductCombination.product_id' => $value['product_id'], 'DistSrProductCombination.combination_id !=' => 0)
                ));

                if (!empty($DistSrCombination_info['DistSrProductCombination']['combination_id'])) {
                    $combination_id = $DistSrCombination_info['DistSrProductCombination']['combination_id'];
                    $all_combination_id_info = $this->DistSrProductCombination->find('all', array(
                        'conditions' => array('DistSrProductCombination.combination_id' => $combination_id)
                    ));

                    $combined_product = '';
                    foreach ($all_combination_id_info as $key => $individual_combination_id) {
                        $all_combination_id[$individual_combination_id['DistSrProductCombination']['product_id']] = $individual_combination_id['DistSrProductCombination']['price'];

                        $individual_combined_product_id = $individual_combination_id['DistSrProductCombination']['product_id'];

                        $combined_product = $combined_product . ',' . $individual_combined_product_id;
                    }
                    $trimmed_combined_product = ltrim($combined_product, ',');

                    $combined_slab[$DistSrCombination_info['DistSrProductCombination']['min_qty']] = $all_combination_id;

                    $matched_combined_product_id_array = explode(',', $trimmed_combined_product);
                    asort($matched_combined_product_id_array);
                    $matched_combined_product_id = implode(',', $matched_combined_product_id_array);
                } else {
                    $combined_slab = array();
                    $matched_combined_product_id = '';
                }



                $edited_cart_data[$value['product_id']] = array(
                    'product_price' => array(
                        'id' => $retrieve_price_DistSrCombination[$value['product_id']][0]['DistSrProductPrice']['id'],
                        'product_id' => $value['product_id'],
                        'general_price' => $retrieve_price_DistSrCombination[$value['product_id']][0]['DistSrProductPrice']['general_price'],
                        'effective_date' => $retrieve_price_DistSrCombination[$value['product_id']][0]['DistSrProductPrice']['effective_date']
                    ),
                    'individual_slab' => $individual_slab,
                    'combined_slab' => $combined_slab,
                    'combined_product' => $matched_combined_product_id
                );



                if (!empty($matched_combined_product_id)) {
                    $edited_matched_data[$matched_combined_product_id] = array(
                        'count' => '4',
                        'is_matched_yet' => 'NO',
                        'matched_count_so_far' => '2',
                        'matched_id_so_far' => '63,65'
                    );

                    $edited_current_qty_data[$value['product_id']] = $value['sales_qty'];
                }
            }
        }
        //die();
        if (!empty($edited_cart_data)) {
            $this->Session->write('cart_session_data', $edited_cart_data);
        }
        if (!empty($edited_matched_data)) {
            $this->Session->write('matched_session_data', $edited_matched_data);
        }
        if (!empty($edited_current_qty_data)) {
            $this->Session->write('combintaion_qty_data', $edited_current_qty_data);
        }


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

        if ($this->request->is('post')) {
            //pr($this->request->data);die();
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

            $this->admin_delete($order_id, 0);

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
            if ($sale_type_id == 10) {
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

                $OrderData['id'] = $order_id;
                /*$sr_id=$this->request->data['DistOrder']['sr_id'];
                $sp_id=null;
                if(!empty($sr_id)){
                    $this->loadModel('SalesPerson');
                    $sp_data=$this->find->SalesPerson('first',array('conditions'=>array('SalesPerson.dist_sales_representative_id'=>$sr_id)));

                    $sp_id=$sp_data['SalesPerson']['id'];
                }*/
                /*$this->loadmodel('DistDiscountDetail');
                $order_date = $this->request->data['DistOrder']['order_date'];
                $gross_value = $this->request->data['DistOrder']['gross_value'];
                $discount_details = $this->DistDiscountDetail->find('first',array(
                    'conditions'=>array(
                        'DistDiscountDetail.date_from <=' => $order_date,
                        'DistDiscountDetail.date_to  >=' => $order_date,
                        'DistDiscountDetail.memo_value  <=' => $gross_value,
                        'DistDiscountDetail.memo_value  >=' => $gross_value,
                    ),

                ));
                //pr( $discount_details);die();
                $discount = $discount_details['DistDiscountDetail']['discount_percent'];
                $discount = $discount /100;
                $after_discount_gross_value = $gross_value - ($gross_value * $discount);*/
                //$discount_amount = $discount_details[''][''];
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
                $OrderData['dist_order_no'] = $this->request->data['DistOrder']['dist_order_no'];
                $OrderData['gross_value'] = $this->request->data['DistOrder']['gross_value'];
                //$OrderData['gross_value'] = $after_discount_gross_value;
                $OrderData['cash_recieved'] = $this->request->data['DistOrder']['cash_recieved'];
                $OrderData['credit_amount'] = $this->request->data['DistOrder']['credit_amount'];
                $OrderData['is_active'] = $this->request->data['DistOrder']['is_active'];
                $OrderData['status'] = $this->request->data['DistOrder']['status'];
                //$OrderData['order_time'] = $this->current_datetime();   
                $OrderData['order_time'] = $this->current_datetime();
                $OrderData['sales_person_id'] = $this->request->data['DistOrder']['sales_person_id'];
                $OrderData['ae_id'] = $this->request->data['DistOrder']['ae_id'];
                $OrderData['tso_id'] = $this->request->data['DistOrder']['tso_id'];
                $OrderData['discount_percent'] = $this->request->data['DistOrder']['discount_percent'];
                $OrderData['discount_value'] = $this->request->data['DistOrder']['discount_value'];
                $OrderData['from_app'] = 0;
                $OrderData['action'] = 1;

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
                                if ($val == NULL) {
                                    continue;
                                }

                                $product_details = $this->Product->find('first', array(
                                    'fields' => array('id', 'is_virtual', 'parent_id'),
                                    'conditions' => array('Product.id' => $val),
                                    'recursive' => -1
                                ));

                                $sales_price = $this->request->data['OrderDetail']['Price'][$key];
                                $order_details['DistOrderDetail']['dist_order_id'] = $order_id;
                                if ($sales_price > 0) {

                                   // $product_id = $order_details['DistOrderDetail']['product_id'] = $val;
                                    if ($product_details['Product']['is_virtual'] == 1) {
                                        $product_id = $order_details['DistOrderDetail']['virtual_product_id'] = $val;
                                        $order_details['DistOrderDetail']['product_id'] = $product_details['Product']['parent_id'];
                                    } else {
                                        $order_details['DistOrderDetail']['virtual_product_id'] = 0;
                                        $product_id = $order_details['DistOrderDetail']['product_id'] = $product_details['Product']['id'];
                                    }

                                    $order_details['DistOrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
                                    $order_details['DistOrderDetail']['price'] = $sales_price;
                                    $sales_qty = $order_details['DistOrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
                                    $order_details['DistOrderDetail']['product_price_id'] = $this->request->data['OrderDetail']['product_price_id'][$key];

                                    //$order_details['DistOrderDetail']['bonus_qty']=0;
                                    $product_price_slab_id = 0;
                                    if ($sales_price > 0) {
                                        $product_price_slab_id = $this->get_product_price_id($val, $sales_price, $all_product_id);
                                        $order_details['DistOrderDetail']['is_bonus'] = 0;
                                        // pr($product_price_slab_id);exit;
                                    } else {
                                        $order_details['DistOrderDetail']['is_bonus'] = 1;
                                    }
                                    $order_details['DistOrderDetail']['product_price_id'] = $product_price_slab_id['product_price_id'];
                                    $order_details['DistOrderDetail']['product_combination_id'] = $product_price_slab_id['combination_id'];

                                    //$order_details['DistOrderDetail']['bonus_qty']=0;
                                    if ($this->request->data['OrderDetail']['bonus_product_qty'][$key] > 0) {
                                        $order_details_bonus['DistOrderDetail']['dist_order_id'] = $order_id;
                                        $order_details_bonus['DistOrderDetail']['is_bonus'] = 0;
                                        $order_details_bonus['DistOrderDetail']['product_id'] = $product_id;
                                        $order_details_bonus['DistOrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
                                        $order_details_bonus['DistOrderDetail']['price'] = 0.0;
                                        $order_details_bonus['DistOrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
                                        $total_product_data[] = $order_details_bonus;
                                        unset($order_details_bonus);
                                    }

                                    if ($this->request->data['OrderDetail']['bonus_product_qty'][$key] > 0) {
                                        $order_details['DistOrderDetail']['bonus_product_id'] = $this->request->data['OrderDetail']['bonus_product_id'][$key];
                                        $order_details['DistOrderDetail']['bonus_qty'] = $this->request->data['OrderDetail']['bonus_product_qty'][$key];
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

                                    //$product_id = $order_details['DistOrderDetail']['product_id'] = $val;

                                    if ($product_details['Product']['is_virtual'] == 1) {
                                        $product_id = $order_details['DistOrderDetail']['virtual_product_id'] = $val;
                                        $order_details['DistOrderDetail']['product_id'] = $product_details['Product']['parent_id'];
                                    } else {
                                        $order_details['DistOrderDetail']['virtual_product_id'] = 0;
                                        $product_id = $order_details['DistOrderDetail']['product_id'] = $product_details['Product']['id'];
                                    }

                                    $order_details['DistOrderDetail']['measurement_unit_id'] = $this->request->data['OrderDetail']['measurement_unit_id'][$key];
                                    $sales_qty = $order_details['DistOrderDetail']['sales_qty'] = $this->request->data['OrderDetail']['sales_qty'][$key];
                                    $order_details['DistOrderDetail']['price'] = 0;
                                    $order_details['DistOrderDetail']['is_bonus'] = 1;

                                    $total_product_data[] = $order_details;
                                    unset($order_details);
                                }

                                //update inventory
                                $stock_hit = 1;
                                if ($stock_hit) {
                                    $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
                                    $product_list = Set::extract($products, '{n}.Product');

                                    $punits_pre = $this->search_array($product_id, 'id', $product_list);
                                    //pr($punits_pre);//die();

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

                                    $update_type = 'add';
                                    $this->dist_booking_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $this->request->data['DistOrder']['order_date'], $booking_bonus_qty);
                                }



                                // sales calculation
                                $tt_price = $sales_qty * $sales_price;
                                if ($sales_price > 0) {
                                    $this->dist_sales_calculation($product_id, $sr_code, $office_id, $sales_qty, $tt_price,  $OrderData['order_date'], 1);
                                }
                            }
                            //die();
                            $this->DistOrderDetail->saveAll($total_product_data);
                            //die();
                        }
                    }
                }
                $this->Session->setFlash(__('The Order has been Updated'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            }
        }

        $this->loadModel('ProductMeasurement');
        $product_measurement_units = $this->ProductMeasurement->find('list', array('fields' => array('product_id', 'measurement_unit_id')));
        $product_category_id_list = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));


        $distRoutes = $this->DistRoute->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));
        $this->set(compact('offices', 'distributors', 'srs', 'territories', 'thanas', 'markets', 'outlets', 'current_date', 'product_list', 'sale_type_list', 'existing_record', 'office_parent_id', 'product_measurement_units', 'product_category_id_list', 'open_bonus_product_option', 'distRoutes'));


        /* $this->set(compact('offices', 'territories', 'product_list', 'markets', 'outlets', 'current_date', 'sale_type_list', 'office_parent_id', 'sales_person')); */
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
    public function get_outlet_after_create()
    {
        $output = "<option value=''>--- Select Outlet ---</option>";
        $market_id = $this->request->data['market_id'];
        $this->loadmodel('DistOutlet');
        $outlet_list = $this->DistOutlet->find('all', array(
            'conditions' => array('DistOutlet.dist_market_id' => $market_id)
        ));
        //$data_array = Set::extract($outlet_list, '{n}.Outlet');
        if ($outlet_list) {
            foreach ($outlet_list as $key => $data) {
                $k = $data['DistOutlet']['id'];
                $v = $data['DistOutlet']['name'];
                $output .= "<option value='$k'>$v</option>";
            }
        }
        echo $output;
        $this->autoRender = false;
    }
    public function get_product_unit()
    {
        // $current_date = $this->current_date();
        $current_date = $this->request->data['order_date'] ? date('Y-m-d', strtotime($this->request->data['order_date'])) : date('Y-m-d');
        /* $this->Session->delete('cart_session_data');
          $this->Session->delete('matched_session_data');
          $this->Session->delete('combintaion_qty_data');
          exit; */
        $distributor_id = $this->request->data['distributor_id'];
        $this->loadModel('DistStore');
        $this->DistStore->recursive = -1;
        $store_info = $this->DistStore->find('first', array(
            'conditions' => array('dist_distributor_id' => $distributor_id)
        ));

        $store_id = $store_info['DistStore']['id'];
        $product_id = $this->request->data['product_id'];
        $min_qty = $this->request->data['min_qty'];

        $this->loadModel('Product');
        $this->Product->recursive = -1;
        $product_category_arr = $this->Product->find('first', array(
            'conditions' => array('Product.id' => $product_id),
            'fields' => array('product_category_id', 'sales_measurement_unit_id', 'product_type_id')
        ));
        // pr($product_category_arr);exit;
        $product_category_id = $product_category_arr['Product']['product_category_id'];
        $product_type_id = $product_category_arr['Product']['product_type_id'];
        if ($product_type_id == 3) {
            $product_category_id = 32;
        }
        $measurement_unit_id = $product_category_arr['Product']['sales_measurement_unit_id'];

        $this->loadModel('DistCurrentInventory');
        $this->DistCurrentInventory->recursive = -1;
        $total_qty_arr = $this->DistCurrentInventory->find('all', array(
            'conditions' => array('store_id' => $store_id, 'product_id' => $product_id),
            'fields' => array('sum(qty) as total', 'sum(booking_qty) as booking_total')
        ));
        $total_qty = $total_qty_arr[0][0]['total'];
        $booking_total_qty = $total_qty_arr[0][0]['booking_total'];
        //pr($booking_total_qty);die();
        //$product_id = 20;
        $product_items_id = explode(',', rtrim($this->request->data['product_id_list'], ","));
        //$product_items_id = array(20);
        /* ------- start get prev effective date and next effective date --------- */
        $effective_prev_date = $this->DistSrProductPrice->find(
            'all',
            array(
                'conditions' => array('DistSrProductPrice.product_id' => $product_id, 'DistSrProductPrice.effective_date <=' => $current_date, 'DistSrProductPrice.institute_id' => 0, 'DistSrProductPrice.has_combination' => 0),
                'fields' => array('DistSrProductPrice.effective_date')
            )
        );
        if (!empty($effective_prev_date)) {
            $prev_short_list = array();
            foreach ($effective_prev_date as $prev_date_key => $prev_date_val) {
                array_push($prev_short_list, $prev_date_val['DistSrProductPrice']['effective_date']);
            }
            asort($prev_short_list);
            $prev_date = array_pop($prev_short_list);
        }
        $effective_next_date = $this->DistSrProductPrice->find(
            'all',
            array(
                'conditions' => array('DistSrProductPrice.product_id' => $product_id, 'DistSrProductPrice.effective_date >' => $current_date, 'DistSrProductPrice.institute_id' => 0, 'DistSrProductPrice.has_combination' => 0),
                'fields' => array('DistSrProductPrice.effective_date')
            )
        );
        if (!empty($effective_next_date)) {
            $next_short_list = array();
            foreach ($effective_next_date as $next_date_key => $next_date_val) {
                array_push($next_short_list, $next_date_val['DistSrProductPrice']['effective_date']);
            }
            rsort($next_short_list);
            $next_date = array_pop($next_short_list);
        }
        /* ------- end get prev effective date and next effective date --------- */
        if (isset($next_date)) {
            $condition_value['DistSrProductPrice.effective_date <'] = $next_date;
        }
        if (isset($prev_date)) {
            $condition_value['DistSrProductPrice.effective_date >='] = $prev_date;
        }
        $condition_value['Product.id'] = $product_id;
        $condition_value['DistSrProductPrice.institute_id'] = 0;
        $condition_value['DistSrProductPrice.has_combination'] = 0;
        $product_option = array(
            'conditions' => array($condition_value),
            'joins' => array(
                array(
                    'alias' => 'DistSrProductPrice',
                    'table' => 'dist_sr_product_prices',
                    'type' => 'LEFT',
                    'conditions' => 'Product.id = DistSrProductPrice.product_id'
                ),
                array(
                    'alias' => 'MeasurementUnit',
                    'table' => 'measurement_units',
                    'type' => 'LEFT',
                    'conditions' => 'Product.sales_measurement_unit_id = MeasurementUnit.id'
                ),
                array(
                    'alias' => 'DistSrProductCombination',
                    'table' => 'dist_sr_product_combinations',
                    'type' => 'LEFT',
                    'conditions' => 'DistSrProductPrice.id = DistSrProductCombination.product_price_id'
                ),
                array(
                    'alias' => 'DistSrCombination',
                    'table' => 'dist_sr_combinations',
                    'type' => 'LEFT',
                    'conditions' => 'DistSrCombination.id = DistSrProductCombination.combination_id'
                )
            ),
            'fields' => array('Product.id', 'Product.product_code', 'Product.name', 'DistSrProductPrice.id', 'DistSrProductPrice.product_id', 'DistSrProductPrice.general_price', 'DistSrProductPrice.effective_date', 'MeasurementUnit.id', 'MeasurementUnit.name', 'DistSrProductCombination.id', 'DistSrProductCombination.price', 'DistSrProductCombination.min_qty', 'DistSrProductCombination.combination_id', 'DistSrCombination.id', 'DistSrCombination.all_products_in_combination'),
            'order' => array('DistSrProductPrice.effective_date' => 'asc')
        );
        $product_list = $this->Product->find('all', $product_option);
        /* ------- start get DistSrCombination data -------- */
        /* ------- start get prev effective date and next effective date for DistSrCombinations --------- */
        $effective_prev_date_for_DistSrCombinations = $this->DistSrProductPrice->find(
            'all',
            array(
                'conditions' => array('DistSrProductPrice.product_id' => $product_id, 'DistSrProductPrice.effective_date <=' => $current_date, 'DistSrProductPrice.institute_id' => 0, 'DistSrProductPrice.has_combination !=' => 0),
                'fields' => array('DistSrProductPrice.effective_date')
            )
        );
        if (!empty($effective_prev_date_for_DistSrCombinations)) {
            $prev_short_list_for_DistSrCombinations = array();
            foreach ($effective_prev_date_for_DistSrCombinations as $prev_date_key_com => $prev_date_val_com) {
                array_push($prev_short_list_for_DistSrCombinations, $prev_date_val_com['DistSrProductPrice']['effective_date']);
            }
            asort($prev_short_list_for_DistSrCombinations);
            $prev_date_for_DistSrCombinations = array_pop($prev_short_list_for_DistSrCombinations);
        }
        $effective_next_date_for_DistSrCombinations = $this->DistSrProductPrice->find(
            'all',
            array(
                'conditions' => array('DistSrProductPrice.product_id' => $product_id, 'DistSrProductPrice.effective_date >' => $current_date, 'DistSrProductPrice.institute_id' => 0, 'DistSrProductPrice.has_combination !=' => 0),
                'fields' => array('DistSrProductPrice.effective_date')
            )
        );
        if (!empty($effective_next_date_for_DistSrCombinations)) {
            $next_short_list_for_DistSrCombinations = array();
            foreach ($effective_next_date_for_DistSrCombinations as $next_date_key_com => $next_date_val_com) {
                array_push($next_short_list_for_DistSrCombinations, $next_date_val_com['DistSrProductPrice']['effective_date']);
            }
            rsort($next_short_list_for_DistSrCombinations);
            $next_date_for_DistSrCombinations = array_pop($next_short_list_for_DistSrCombinations);
        }
        /* ------- end get prev effective date and next effective date --------- */
        if (!empty($next_date_for_DistSrCombinations)) {
            $condition_value_for_DistSrCombinations['DistSrProductPrice.effective_date <'] = $next_date_for_DistSrCombinations;
        }
        if (!empty($prev_date_for_DistSrCombinations)) {
            $condition_value_for_DistSrCombinations['DistSrProductPrice.effective_date >='] = $prev_date_for_DistSrCombinations;
        }
        $condition_value_for_DistSrCombinations['Product.id'] = $product_id;
        $condition_value_for_DistSrCombinations['DistSrProductPrice.institute_id'] = 0;
        $condition_value_for_DistSrCombinations['DistSrProductPrice.has_combination !='] = 0;

        $product_option_for_DistSrCombinations = array(
            'conditions' => array($condition_value_for_DistSrCombinations),
            'joins' => array(
                array(
                    'alias' => 'DistSrProductPrice',
                    'table' => 'dist_sr_product_prices',
                    'type' => 'LEFT',
                    'conditions' => 'Product.id = DistSrProductPrice.product_id'
                ),
                array(
                    'alias' => 'MeasurementUnit',
                    'table' => 'measurement_units',
                    'type' => 'LEFT',
                    'conditions' => 'Product.sales_measurement_unit_id = MeasurementUnit.id'
                ),
                array(
                    'alias' => 'DistSrProductCombination',
                    'table' => 'dist_sr_product_combinations',
                    'type' => 'LEFT',
                    'conditions' => 'DistSrProductPrice.id = DistSrProductCombination.product_price_id'
                ),
                array(
                    'alias' => 'DistSrCombination',
                    'table' => 'dist_sr_combinations',
                    'type' => 'LEFT',
                    'conditions' => 'DistSrCombination.id = DistSrProductCombination.combination_id'
                )
            ),
            'fields' => array('Product.id', 'Product.product_code', 'Product.name', 'DistSrProductPrice.id', 'DistSrProductPrice.product_id', 'DistSrProductPrice.general_price', 'DistSrProductPrice.effective_date', 'MeasurementUnit.id', 'MeasurementUnit.name', 'DistSrProductCombination.id', 'DistSrProductCombination.price', 'DistSrProductCombination.min_qty', 'DistSrProductCombination.combination_id', 'DistSrCombination.id', 'DistSrCombination.all_products_in_combination'),
            'order' => array('DistSrProductPrice.effective_date' => 'asc')
        );
        $product_list_for_DistSrCombinations = $this->Product->find('all', $product_option_for_DistSrCombinations);
        /*echo $this->Product->getLastquery();
          pr($product_list_for_DistSrCombinations);exit;*/
        if (!empty($product_list_for_DistSrCombinations)) {
            foreach ($product_list_for_DistSrCombinations as $data) {
                $product_list[] = $data;
            }
        }
        /* pr($product_list);exit; */
        /* ------- end get DistSrCombination data -------- */
        $product_count = count($product_list);
        $filter_product = array();
        $check_product_id = array();
        for ($i = 0; $i < $product_count; $i++) {
            if (!in_array($product_list[$i]['Product']['id'], $check_product_id)) {
                array_push($check_product_id, $product_list[$i]['Product']['id']);
                $filter_product = $product_list[$i];
                unset($filter_product['DistSrCombination']);
                unset($filter_product['ProductMeasurement']);
                if (!empty($product_list[$i]['DistSrCombination']['id'])) {
                    $filter_product['Combined_min_qty'][] = $product_list[$i]['DistSrProductCombination']['min_qty'];
                    $filter_product['combination_id'][] = $product_list[$i]['DistSrCombination']['id'];
                    $filter_product['DistSrCombination'] = $product_list[$i]['DistSrCombination']['all_products_in_combination'];
                } else {
                    $filter_product['Individual_slab'][] = $product_list[$i]['DistSrProductCombination'];
                }
            } else {
                if ($product_list[$i]['DistSrProductPrice']['general_price'] > 0) {
                    $filter_product['DistSrProductPrice'] = $product_list[$i]['DistSrProductPrice'];
                }
                if (!empty($product_list[$i]['DistSrCombination']['id'])) {
                    $filter_product['Combined_min_qty'][] = $product_list[$i]['DistSrProductCombination']['min_qty'];
                    $filter_product['combination_id'][] = $product_list[$i]['DistSrCombination']['id'];
                    $filter_product['DistSrCombination'] = $product_list[$i]['DistSrCombination']['all_products_in_combination'];
                } else {
                    $filter_product['Individual_slab'][] = $product_list[$i]['DistSrProductCombination'];
                }
            }
        }
        //pr($filter_product);exit; 
        /* ---- creating cart data ---- */
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
                //pr($DistSrCombination_slab_data);die();
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
            /* echo "<pre>";
              echo "Prepare cart data";
              print_r($prepare_cart_data);
              exit; */

            /* ---------------- cart data store in session ---------------- */
            global $cart_data, $matched_array;
            $cart_data = array();
            $matched_array = array();
            /* -------- create individual Product data --------- */

            function build_cart_data($product_id, $product_price = array(), $individual_slab = array(), $combined_slab = array(), $all_products = '', &$cart_data = array())
            {
                $cart_data[$product_id]['product_price'] = $product_price;

                $cart_data[$product_id]['individual_slab'] = $individual_slab;

                $cart_data[$product_id]['combined_slab'] = $combined_slab;

                $cart_data[$product_id]['combined_product'] = $all_products;
            }

            /* -------- create Matched Product data --------- */
            if (!empty($prepare_cart_data[$product_id]['DistSrCombination'])) {
                $products_count = count(explode(',', $prepare_cart_data[$product_id]['DistSrCombination']));
            }

            function build_matched_array($product_id, $all_products = '', $products_count = 0, &$matched_array)
            {

                if (!array_key_exists($all_products, $matched_array)) {


                    $matched_array[$all_products] = array(
                        'count' => $products_count,
                        'is_matched_yet' => 'NO',
                        'matched_count_so_far' => 1,
                        'matched_id_so_far' => $product_id
                    );
                } else {
                    $updated_matched_count_so_far = $matched_array[$all_products]['matched_count_so_far'] + 1;
                    $updated_matched_id_so_far = $matched_array[$all_products]['matched_id_so_far'] . ',' . $product_id;

                    /* if ($matched_array[$all_products]['count'] == $updated_matched_count_so_far) {
                      $is_matched_yet_value = 'YES';
                      } else {
                      $is_matched_yet_value = 'NO';
                      } */
                    $matched_array[$all_products] = array(
                        'count' => $products_count,
                        'is_matched_yet' => 'NO',
                        'matched_count_so_far' => $updated_matched_count_so_far,
                        'matched_id_so_far' => $updated_matched_id_so_far
                    );
                }
            }

            if ($this->Session->read('cart_session_data') == NULL) {
                $cart_data = array();
            } else {
                $cart_data = $this->Session->read('cart_session_data');
            }
            if ($this->Session->read('matched_session_data') == NULL) {
                $matched_array = array();
            } else {
                $matched_array = $this->Session->read('matched_session_data');
            }
            if (!empty($prepare_cart_data[$product_id]['DistSrCombination'])) {

                build_cart_data($product_id, $prepare_cart_data[$product_id]['DistSrProductPrice'], $prepare_cart_data[$product_id]['Individual_slab'], $prepare_cart_data[$product_id]['Combined_slab'], $prepare_cart_data[$product_id]['DistSrCombination'], $cart_data);
                $this->Session->write('cart_session_data', $cart_data);
                //session array updated

                build_matched_array($product_id, $prepare_cart_data[$product_id]['DistSrCombination'], $products_count, $matched_array);
                $this->Session->write('matched_session_data', $matched_array);
            } else {
                build_cart_data($product_id, $prepare_cart_data[$product_id]['DistSrProductPrice'], $prepare_cart_data[$product_id]['Individual_slab'], array(), '', $cart_data);
                $this->Session->write('cart_session_data', $cart_data);
            }

            $current_session_data = $this->Session->read('cart_session_data');
            $current_matched_data = $this->Session->read('matched_session_data');
            /*          echo "<pre>";
              print_r($current_session_data);
              print_r($current_matched_data);
              exit; */

            $prev_data = array();
            foreach ($current_session_data as $key => $val) {
                $prev_data[] = $key;
            }
            $diff_product_id = array_diff($prev_data, $product_items_id);

            foreach ($diff_product_id as $d_key => $d_val) {
                /* -------- code for matched array ---------- */
                $combined_index = $current_session_data[$d_val]['combined_product'];

                if (!empty($combined_index)) {
                    if (array_key_exists($combined_index, $current_matched_data)) {
                        $combine_count_product = $current_matched_data[$combined_index]['matched_count_so_far'];
                    } else {
                        $combine_count_product = 0;
                    }
                    if ($combine_count_product == 1) {

                        unset($current_matched_data[$combined_index]);
                        $this->Session->write('matched_session_data', $current_matched_data);
                    } else {
                        $combine_all_product = explode(',', $current_matched_data[$combined_index]['matched_id_so_far']);
                        if (($key = array_search($d_val, $combine_all_product)) !== false) {
                            unset($combine_all_product[$key]);
                            $combine_all_product = implode(',', $combine_all_product);
                            $current_matched_data[$combined_index]['matched_count_so_far']--;
                            $current_matched_data[$combined_index]['is_matched_yet'] = 'NO';
                            $current_matched_data[$combined_index]['matched_id_so_far'] = $combine_all_product;
                            $this->Session->write('matched_session_data', $current_matched_data);
                        }
                    }
                }
                unset($current_session_data[$d_val]);
                //$this->Session->write('cart_session_data',$current_session_data);
            }

            $cart_data = $this->Session->read('cart_session_data');
            $matched_data = $this->Session->read('matched_session_data');
            $this->set('matched_data', $matched_data);

            /* ------------- end for cart data store in session ------------- */
            //echo "<pre>";
            //print_r($current_session_data);
            //print_r($current_matched_data);
            //exit; 
        }


        //32 is the promotional product category id OR gift product category id. 

        if ($product_category_id != 32) {
            // if ($product_type_id != 3) {booking_total_qty

            $sales_total_qty = $this->unit_convertfrombase($this->request->data['product_id'], $filter_product['MeasurementUnit']['id'], $total_qty);
            $Sales_booking_total_qty = $this->unit_convertfrombase($this->request->data['product_id'], $filter_product['MeasurementUnit']['id'], $booking_total_qty);

            /* -----------------Bonus---------------- */
            $this->loadModel('Bonus');
            $this->Bonus->recursive = -1;
            $bonus_info = $this->Bonus->find('first', array(
                'conditions' => array('mother_product_id' => $product_id, 'mother_product_quantity' => $filter_product['DistSrProductCombination']['min_qty']),
                'fields' => array('bonus_product_id', 'bonus_product_quantity')
            ));

            //pr($product_id);pr($filter_product['DistSrProductCombination']['min_qty']);die();
            if (count($bonus_info) != 0) {
                $bonus_product_id = $bonus_info['Bonus']['bonus_product_id'];
                $this->loadModel('Product');
                $this->Product->recursive = -1;
                $bonus_product_name = $this->Product->find('first', array(
                    'conditions' => array('id' => $bonus_product_id),
                    'fields' => array('name', 'sales_measurement_unit_id')
                ));
                $data_array['bonus_product_id'] = $bonus_product_id;
                $data_array['bonus_product_name'] = $bonus_product_name['Product']['name'];
                $data_array['bonus_measurement_unit_id'] = $bonus_product_name['Product']['sales_measurement_unit_id'];
                $data_array['bonus_product_qty'] = $bonus_info['Bonus']['bonus_product_quantity'];
            }

            /* ----- set measurement unit ----- */
            if (!empty($filter_product['MeasurementUnit'])) {
                $data_array['product_unit']['id'] = $filter_product['MeasurementUnit']['id'];
                $data_array['product_unit']['name'] = $filter_product['MeasurementUnit']['name'];
            } else {
                $data_array['product_unit']['id'] = '';
                $data_array['product_unit']['name'] = '';
            }

            /* ----- set Product Price ----- */
            if (!empty($filter_product['DistSrProductPrice'])) {
                $data_array['product_price']['id'] = $filter_product['DistSrProductPrice']['id'];
                $data_array['product_price']['general_price'] = sprintf("%1\$.6f", $filter_product['DistSrProductPrice']['general_price']);
            } else {
                $data_array['product_price']['id'] = '';
                $data_array['product_price']['general_price'] = '';
            }
            /* ----- set Product DistSrCombination ----- */
            if (!empty($filter_product['DistSrProductCombination']['id'])) {
                $data_array['product_DistSrCombination']['id'] = $filter_product['DistSrProductCombination']['id'];
                $data_array['product_DistSrCombination']['min_qty'] = $filter_product['DistSrProductCombination']['min_qty'];
            } else {
                $data_array['product_DistSrCombination']['id'] = '';
                $data_array['product_DistSrCombination']['min_qty'] = '';
            }
            /* ------ set combined product ------ */
            if (!empty($filter_product['DistSrCombination'])) {
                $data_array['combined_product'] = $filter_product['DistSrCombination'];
            } else {
                $data_array['combined_product'] = '';
            }
        }
        if ($product_category_id == 32) {
            // if ($product_type_id == 3) {
            $sales_total_qty = $total_qty;
            $Sales_booking_total_qty = $booking_total_qty;
            $this->loadModel('MeasurementUnit');
            $this->MeasurementUnit->recursive = -1;
            $measurement_unit_name_arr = $this->MeasurementUnit->find('first', array(
                'conditions' => array('id' => $measurement_unit_id),
                'fields' => array('name')
            ));
            $data_array['product_unit']['id'] = $measurement_unit_id;
            $data_array['product_unit']['name'] = $measurement_unit_name_arr['MeasurementUnit']['name'];
            $data_array['product_price']['general_price'] = 0.00;
            $data_array['product_DistSrCombination']['min_qty'] = 1;
        }

        if (!empty($Sales_booking_total_qty)) {
            $data_array['total_booking_qty'] = $Sales_booking_total_qty;
        } else {
            $data_array['total_booking_qty'] = '';
        }
        if (!empty($sales_total_qty)) {
            $data_array['total_qty'] = $sales_total_qty;
        } else {
            $data_array['total_qty'] = '';
        }
        if (!empty($product_category_id)) {
            $data_array['product_category_id'] = $product_category_id;
        } else {
            $data_array['product_category_id'] = '';
        }

        if (!empty($data_array)) {
            echo json_encode($data_array);
        }

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

    public function dist_booking_update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '', $booking_bonus_qty)
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

        if ($update_type == 'deduct') {
            $this->DistCurrentInventory->id = $inventory_info['DistCurrentInventory']['id'];
            $this->DistCurrentInventory->updateAll(
                array(
                    'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty - ' . $quantity,
                    'DistCurrentInventory.bonus_booking_qty' => 'DistCurrentInventory.bonus_booking_qty - ' . $booking_bonus_qty,
                    'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                ),
                array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
            );
        } else {
            if (!empty($inventory_info)) {

                $this->DistCurrentInventory->updateAll(
                    array(
                        'DistCurrentInventory.booking_qty' => 'DistCurrentInventory.booking_qty + ' . $quantity,
                        'DistCurrentInventory.bonus_booking_qty' => 'DistCurrentInventory.bonus_booking_qty + ' . $booking_bonus_qty,
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

            /*$product_ci_in = implode(",", $product_ci);
            $products = $this->Product->find('all', array(
                'conditions' => array('Product.id' => $product_ci), 'order' => array('order' => 'asc'),
                'fields' => array('Product.id as id', 'Product.name as name'),
                'recursive' => -1
            ));*/

            $products = $this->Product->find('all', array(
                'conditions' => array('Product.id' => $product_ci), 'order' => array('Product.order' => 'asc'),
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

            $group_product = array();
            foreach ($products as $data) {
                if ($data[0]['p_id']) {
                    $group_product[$data[0]['p_id']][] = $data[0]['id'];
                } else {
                    $group_product[$data[0]['id']][] = $data[0]['id'];
                }
            }

            //echo '<pre>';print_r($group_product);exit;

            $product_array = array();
            foreach ($products as $data) {
                if ($data['0']['p_id'] && count($group_product[$data[0]['p_id']]) == 1) {
                    $name = $data[0]['p_name'];
                } else {
                    $name = $data[0]['name'];
                }
                $product_array[] = array(
                    'id' => $data[0]['id'],
                    'name' => $name
                );
            }

            //$data_array = Set::extract($products, '{n}.0');
            $data_array = $product_array;

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
                'conditions' => array('DistAreaExecutive.user_id' => $user_id, 'DistAreaExecutive.is_active' => 1),
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
        if ($distributor_id) {
            //$order_date = date("Y-m-d H:i:s", strtotime($this->request->data['order_date']));
            $order_date_from = date("Y-m-d H:i:s", strtotime($this->request->data['order_date_from']));
            $order_date_to = date("Y-m-d H:i:s", strtotime($this->request->data['order_date_to']));

            $sr = $this->DistSalesRepresentative->find('list', array(
                'conditions' => array(
                    'DistOrder.distributor_id' => $distributor_id,
                    'DistOrder.order_date >=' => $order_date_from,
                    'DistOrder.order_date <=' => $order_date_to,
                ),
                'joins' => array(array('table' => 'dist_Orders', 'alias' => 'DistOrder', 'conditions' => 'DistOrder.sr_id=DistSalesRepresentative.id')),
                'order' => array('DistSalesRepresentative.name' => 'asc')
            ));

            if ($sr) {
                foreach ($sr as $key => $data) {
                    $output .= "<option value='$key'>$data</option>";
                }
            }
        }
        /***************** this is for find SR in daterange end *********************/

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

    public function get_route_list_by_thana_id()
    {
        //$company_id = $this->Session->read('Office.company_id');
        //$user_group_id = $this->Session->read('Office.group_id');
        $this->loadModel('DistRoute');
        $thana_id = $this->request->data['thana_id'];
        $output = "<option value=''>--- Select ---</option>";
        if ($thana_id) {
            $route = $this->DistRoute->find('all', array(
                'conditions' => array('DistRoute.thana_id' => $thana_id),
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
                    //'DistSrRouteMapping.effective_date <='=>$order_date,
                    'DistSrRouteMapping.effective_date <=' => date('Y-m-d'),
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
                'conditions' => array('DistAreaExecutive.user_id' => $user_id, 'DistAreaExecutive.is_active' => 1),
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

    public function admin_order_map()
    {
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $this->set('page_title', 'Order List on Map');
        $message = '';
        $map_data = array();
        $conditions = array();
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $this->set('office_parent_id', $office_parent_id);
        if ($office_parent_id == 0) {
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
            } else {
                $tsos = $this->DistTso->find('list', array(
                    'conditions' => array(
                        'office_id' => $this->UserAuth->getOfficeId(),
                        'is_active' => 1
                    ),
                ));
            }

            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }
        $this->set(compact('tsos'));

        // Custome Search
        if (($this->request->is('post') || $this->request->is('put'))) {

            if ($this->request->data['DistOrder']['office_id'] != '') {
                $conditions[] = array('DistOrder.office_id' => $this->request->data['DistOrder']['office_id']);
            }
            if ($this->request->data['DistOrder']['tso_id'] != '') {
                $conditions[] = array('DistOrder.tso_id' => $this->request->data['DistOrder']['tso_id']);
            }
            if ($this->request->data['DistOrder']['distributor_id'] != '') {
                $conditions[] = array('DistOrder.distributor_id' => $this->request->data['DistOrder']['distributor_id']);
            }
            if ($this->request->data['DistOrder']['sr_id'] != '') {
                $conditions[] = array('DistOrder.sr_id' => $this->request->data['DistOrder']['sr_id']);
            }
            if ($this->request->data['DistOrder']['dist_route_id'] != '') {
                $conditions[] = array('DistOrder.dist_route_id' => $this->request->data['DistOrder']['dist_route_id']);
            }
            if ($this->request->data['DistOrder']['market_id'] != '') {
                $conditions[] = array('DistOrder.market_id' => $this->request->data['DistOrder']['market_id']);
            }
            if ($this->request->data['DistOrder']['outlet_id'] != '') {
                $conditions[] = array('DistOrder.outlet_id' => $this->request->data['DistOrder']['outlet_id']);
            }
            if ($this->request->data['DistOrder']['date_from'] != '') {
                $conditions[] = array('DistOrder.order_date >=' => Date('Y-m-d', strtotime($this->request->data['DistOrder']['date_from'])));
            }
            if ($this->request->data['DistOrder']['date_to'] != '') {
                $conditions[] = array('DistOrder.order_date <=' => Date('Y-m-d', strtotime($this->request->data['DistOrder']['date_to'])));
            }

            $this->DistOrder->recursive = 0;
            $order_list = $this->DistOrder->find('all', array(
                'conditions' => $conditions,
                'order' => array('DistOrder.id' => 'desc'),
                'recursive' => 0
            ));

            if (!empty($order_list)) {
                foreach ($order_list as $val) {
                    if ($val['DistOrder']['latitude'] > 0 and $val['DistOrder']['longitude'] > 0) {
                        $data['title'] = $val['Outlet']['name'];
                        $data['lng'] = $val['DistOrder']['longitude'];
                        $data['lat'] = $val['DistOrder']['latitude'];
                        $data['description'] = '<p><b>Outlet : ' . $val['Outlet']['name'] . '</b></br>' .
                            'Distributor : </b>' . $val['Distributor']['name'] . '</br>' .
                            'Route : </b>' . $val['Route']['name'] . '</br>' .
                            'Market : </b>' . $val['Market']['name'] . '</p>' .
                            '<p>Order No. : ' . $val['DistOrder']['dist_order_no'] . '</br>' .
                            'Order Date : ' . date('d-M-Y', strtotime($val['DistOrder']['order_date'])) . '</br>' .
                            'order Amount : ' . sprintf('%.2f', $val['DistOrder']['gross_value']) . '</p>' .
                            '<a class="btn btn-primary btn-xs" href="' . BASE_URL . 'admin/dist_orders/view/' . $val['DistOrder']['id'] . '" target="_blank">Order Details</a>';
                        $map_data[] = $data;
                    }
                }
            }
            if (!empty($map_data))
                $message = '';
            else
                $message = '<div class="alert alert-danger">No memo found.</div>';

            $this->set(compact('map_data', 'message'));
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
            $tsos = $this->DistTso->find('list', array(
                'conditions' => array(
                    'office_id' => $office_id,
                    'is_active' => 1
                ),
            ));
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
                'conditions' => array(
                    'DistOrder.distributor_id' => $distributor_id,
                    'DistOrder.order_date >=' => $order_date_from,
                    'DistOrder.order_date <=' => $order_date_to,
                ),
                'joins' => array(array('table' => 'dist_Orders', 'alias' => 'DistOrder', 'conditions' => 'DistOrder.sr_id=DistSalesRepresentative.id')),
                'order' => array('DistSalesRepresentative.name' => 'asc')
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

        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        $current_date = date('d-m-Y', strtotime($this->current_date()));
        $this->set(compact('offices', 'distributors', 'distributor_id', 'srs', 'sr_id', 'territories', 'markets', 'outlets', 'current_date', 'requested_data', 'sr_names', 'routes', 'tso_id', 'current_date', 'tsos'));
    }

    public function download_xl()
    {


        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes
        $params = $this->request->query['data'];


        $this->loadModel('DistTso');
        $this->loadModel('DistUserMapping');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');



        //$requested_data = $this->request->data;



        $office_parent_id = $this->UserAuth->getOfficeParentId();



        /*start here */
        $conditions = array();

        if (!empty($params['DistOrder']['office_id'])) {
            $conditions[] = array('DistOrder.office_id' => $params['DistOrder']['office_id']);
        }
        if (empty($params['DistOrder']['office_id'])) {
            if ($office_parent_id != 0) {
                $conditions[] = array('DistOrder.office_id' => $office_parent_id);
            }
        }
        if (!empty($params['DistOrder']['order_reference_no'])) {
            $conditions[] = array('DistOrder.dist_order_no Like' => "%" . $params['DistOrder']['order_reference_no'] . "%");
        }
        if (!empty($params['DistOrder']['dist_route_id'])) {
            $conditions[] = array('DistOrder.dist_route_id' => $params['DistOrder']['dist_route_id']);
        }
        if (!empty($params['DistOrder']['outlet_id'])) {
            $conditions[] = array('DistOrder.outlet_id' => $params['DistOrder']['outlet_id']);
        }
        if (!empty($params['DistOrder']['market_id'])) {
            $conditions[] = array('DistOrder.market_id' => $params['DistOrder']['market_id']);
        }
        if (!empty($params['DistOrder']['thana_id'])) {
            $conditions[] = array('Market.thana_id' => $params['DistOrder']['thana_id']);
        }
        if (!empty($params['DistOrder']['territory_id'])) {
            $conditions[] = array('DistOrder.territory_id' => $params['DistOrder']['territory_id']);
        }
        if (!empty($params['DistOrder']['tso_id'])) {
            $conditions[] = array('DistOrder.tso_id' => $params['DistOrder']['tso_id']);
        } else {
            if ($user_group_id == 1029) {


                $user_id = CakeSession::read('UserAuth.User.id');

                $dist_tso_info = $this->DistTso->find('first', array(
                    'conditions' => array('DistTso.user_id' => $user_id),
                    'fields' => array('DistTso.id', 'DistTso.name'),
                    'recursive' => -1,
                ));
                $dist_tso_id = $dist_tso_info['DistTso']['id'];
                $conditions[] = array('DistOrder.tso_id' => $dist_tso_id);
            }
        }
        if (!empty($params['DistOrder']['distributor_id'])) {
            $conditions[] = array('DistOrder.distributor_id' => $params['DistOrder']['distributor_id']);
        } else {
            if ($user_group_id == 1034) {

                $sp_id = CakeSession::read('UserAuth.User.sales_person_id');

                $data = $this->DistUserMapping->find('first', array('conditions' => array('DistUserMapping.sales_person_id' => $sp_id)));
                $distributor_id = $data['DistUserMapping']['dist_distributor_id'];
                $conditions[] = array('DistOrder.distributor_id' => $distributor_id);
            }
        }

        if (!empty($params['DistOrder']['sr_id'])) {
            $conditions[] = array('DistOrder.sr_id' => $params['DistOrder']['sr_id']);
        }

        if (isset($params['DistOrder']['status']) != '' && !empty($params['DistOrder']['status'])) {
            if ($params['DistOrder']['status'] == 2 || $params['DistOrder']['status'] == 4) {
                if ($params['DistOrder.status'] == 2) {
                    $conditions[] = array('DistOrder.processing_status' => 1);
                } else {
                    $conditions[] = array('DistOrder.processing_status' => 2);
                }
            } else {
                $conditions[] = array('DistOrder.status' => $params['DistOrder']['status']);
            }
        }
        if (isset($params['DistOrder']['date_from']) != '') {
            $conditions[] = array('DistOrder.order_date >=' => Date('Y-m-d H:i:s', strtotime($params['DistOrder']['date_from'])));
        }
        if (isset($params['DistOrder']['date_to']) != '') {
            $conditions[] = array('DistOrder.order_date <=' => Date('Y-m-d H:i:s', strtotime($params['DistOrder']['date_to'] . ' 23:59:59')));
        }
        if (isset($params['DistOrder']['from_app'])) {
            $conditions[] = array('DistOrder.from_app' => 0);
        }





        $result = $this->DistOrder->find('all', array(
            'fields' => array('DistOrder.*', 'DistOutlet.name', 'DistMarket.name', 'DistDistributor.name', 'DistRoute.name', 'DistTso.name', 'Office.office_name', 'DistAE.name'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'alias' => 'DistOutlet',
                    'table' => 'dist_outlets',
                    'type' => 'left',
                    'conditions' => 'DistOutlet.id = DistOrder.outlet_id'
                ),
                array(
                    'alias' => 'DistMarket',
                    'table' => 'dist_markets',
                    'type' => 'left',
                    'conditions' => 'DistMarket.id = DistOrder.market_id'
                ),
                array(
                    'alias' => 'DistDistributor',
                    'table' => 'dist_distributors',
                    'type' => 'left',
                    'conditions' => 'DistDistributor.id = DistOrder.distributor_id'
                ),
                array(
                    'alias' => 'DistRoute',
                    'table' => 'dist_routes',
                    'type' => 'left',
                    'conditions' => 'DistRoute.id = DistOrder.dist_route_id'
                ),

                array(
                    'table' => 'dist_tsos',
                    'alias' => 'DistTso',
                    'type' => 'INNER',
                    'conditions' => array('DistTso.id=(
                                                          SELECT 
                                                            TOP 1 dist_tso_mappings.dist_tso_id
                                                          FROM dist_tso_mappings
                                                          WHERE dist_tso_mappings.dist_distributor_id = DistOrder.distributor_id order by dist_tso_mappings.id asc
                                                            )
                                                    ')
                ),
                array(
                    'table' => 'dist_area_executives',
                    'alias' => 'DistAE',
                    'type' => 'INNER',
                    'conditions' => array('DistAE.id=(
                                                          SELECT 
                                                            TOP 1 dist_area_executives.id
                                                          FROM dist_area_executives
                                                          WHERE dist_area_executives.id = DistTso.dist_area_executive_id order by dist_area_executives.id asc
                                                            )
                                                    ')
                ),


            ),

            'order' => array('DistOrder.id' => 'desc'),
            'recursive' => 1
        ));


        $dist_Orders = $result;
        // echo '<pre>';
        // print_r($dist_Orders);
        // echo '</pre>';exit;
        $sr_names = array();

        if ($dist_Orders) {

            foreach ($dist_Orders as $key => $value) {
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



        $table = '';
        $table .= '<table border="1">
							
								<tr>
									<th>Id</th>
									<th>Order No</th>
									<th>Area Office</th>
									<th>Area Executive</th>
									<th>TSO</th>
									<th>Distributor</th>
									<th>SR Name</th>
									<th>Outlet</th>
									<th>Market</th>
									<th>Route</th>				
									<th >Amount</th>
									<th>Order Date</th>
									<th>Status</th>
									
									
								</tr>
							
							<tbody>';

        $total_amount = 0;
        foreach ($dist_Orders as $Order) :
            $Order['DistOrder']['from_app'] = 0;

            $table .= '<tr>
								<td>' . h($Order['DistOrder']['id']) . '</td>
								<td>' . h($Order['DistOrder']['dist_order_no']) . '</td>
								<td>' . h($Order['Office']['office_name']) . '</td>
								<td >' . h($Order['DistAE']['name']) . '</td>
								<td>' . h($Order['DistTso']['name']) . '</td>
								<td>' . h($Order['DistDistributor']['name']) . '</td>
								<td>' . h($sr_names[$Order['DistOrder']['sr_id']]) . '</td>
								<td>' . h($Order['DistOutlet']['name']) . '</td>
								<td>' . h($Order['DistMarket']['name']) . '</td>
								<td>' . h($Order['DistRoute']['name']) . '</td>
								<td>' . sprintf('%.2f', $Order['DistOrder']['gross_value']) . '</td>
								<td>' . date("d-m-Y", strtotime($Order['DistOrder']['order_date'])) . '</td>';



            if ($Order['DistOrder']['status'] == 0) {

                $table .= '<td>Draft</td>';
            } elseif ($Order['DistOrder']['status'] == 1) {

                $table .= '<td>Pending</td>';
            } elseif ($Order['DistOrder']['processing_status'] == 1) {

                $table .= '<td>Invoice Created</td>';
            } elseif ($Order['DistOrder']['status'] == 3) {

                $table .= '<td>Cancel</td>';
            } elseif ($Order['DistOrder']['processing_status'] == 2) {

                $table .= '<td>Delivered</td>';
            }


            $table .= '</tr>';

            $total_amount = $total_amount + $Order['DistOrder']['gross_value'];
        endforeach;

        $table .= '<tr>
								<td align="right" colspan="10"><b>Total Amount :</b></td>
								<td align="center"><b>' . sprintf('%.2f', $total_amount) . '</b></td>
								<td class="text-center" colspan="2"></td>
							</tr>
							</tbody>
						</table>';



        header('Content-Type:application/force-download');
        header('Content-Disposition: attachment; filename="DistOrder.xls"');
        header("Cache-Control: ");
        header("Pragma: ");
        echo "\xEF\xBB\xBF"; //UTF-8 BOM
        echo $table;
        $this->autoRender = false;
    }
}
