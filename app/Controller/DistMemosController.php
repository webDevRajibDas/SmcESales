<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'php-excel-reader/excel_reader2');

/**
 * DistMemos Controller
 *
 * @property Memo $DistMemo
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DistMemosController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $uses = array('DistMemo', 'DistDistributor', 'DistSalesRepresentative', 'Thana', 'SalesPerson', 'DistMarket', 'DistSalesRepresentative', 'DistOutlet', 'Product', 'MeasurementUnit', 'DistSrProductPrice', 'DistSrProductCombination', 'DistSrCombination', 'DistMemoDetail', 'MeasurementUnit', 'DistRoute', 'DistTsoMappingHistory');
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
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 99999); //300 seconds = 5 minutes
        $product_wise_sales_bonus_report = array();
        $product_list = array();
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

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
        $status_list = array(0 => 'Draft', 1 => ' Pending', 3 => 'Cancel');
        $requested_data = $this->request->data;

        $this->set('page_title', 'SR Memo List');
        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $this->set('office_parent_id', $office_parent_id);
        if ($office_parent_id == 0) {
            $conditions = array('DistMemo.memo_date >=' => $this->current_date() . ' 00:00:00', 'DistMemo.memo_date <=' => $this->current_date() . ' 23:59:59');
            $office_conditions = array(
                'office_type_id' => 2,
                "NOT" => array("id" => array(30, 31, 37))
            );
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
                $conditions = array('DistMemo.tso_id' => $dist_tso_id, 'DistMemo.memo_date >=' => $this->current_date() . ' 00:00:00', 'DistMemo.memo_date <=' => $this->current_date() . ' 23:59:59');
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];

                $conditions = array('DistMemo.distributor_id' => $distributor_id, 'DistMemo.memo_date >=' => $this->current_date() . ' 00:00:00', 'DistMemo.memo_date <=' => $this->current_date() . ' 23:59:59');
            } else {
                $conditions = array('Territory.office_id' => $this->UserAuth->getOfficeId(), 'DistMemo.memo_date >=' => $this->current_date() . ' 00:00:00', 'DistMemo.memo_date <=' => $this->current_date() . ' 23:59:59');
            }
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }

        /*********************** Check Product Wise Sales Bonus Report Start ***********************/
        if (isset($this->request->data)  && array_key_exists('sr_wise_sales_bonus_report', $this->request->data['DistMemo']) && ($this->request->data['DistMemo']['sr_wise_sales_bonus_report'] == 1)) {
            $params = $this->request->data;
            if (!empty($params['DistMemo']['office_id'])) {
            } else {

                if ($office_parent_id == 0) {
                } else {

                    $this->request->data['DistMemo']['office_id'] = $this->UserAuth->getOfficeId();
                }
            }
            /*$report_conditions = array();
            
            $params=$this->request->data;
            if(!empty($params['DistMemo']['office_id']))
            {
                
                $report_conditions[] = array('DistMemo.office_id' => $params['DistMemo']['office_id']);
            }
            else
            {

                if ($office_parent_id == 0) {
                   
                } else {

                    $report_conditions[] = array('DistMemo.office_id' => $this->UserAuth->getOfficeId());
                }

                
            }

            if (!empty($params['DistMemo']['outlet_id'])) {
                $report_conditions[] = array('DistMemo.outlet_id' => $params['DistMemo']['outlet_id']);
            }
            elseif(!empty($params['DistMemo']['market_id'])) {
                $report_conditions[] = array('DistMemo.market_id' => $params['DistMemo']['market_id']);
            }
            elseif (!empty($params['DistMemo']['thana_id'])) {
                $report_conditions[] = array('Market.thana_id' => $params['DistMemo']['thana_id']);
            }
            elseif (!empty($params['DistMemo']['territory_id'])) 
            {
                $report_conditions[] = array('DistMemo.territory_id' => $params['DistMemo']['territory_id']);
            }

            if (!empty($params['DistMemo']['sr_id'])) {
                $report_conditions[] = array('DistMemo.sr_id' => $params['DistMemo']['sr_id']);
            }
            elseif (!empty($params['DistMemo']['distributor_id'])) {
                $report_conditions[] = array('DistMemo.distributor_id' => $params['DistMemo']['distributor_id']);
            }

            if (isset($params['DistMemo']['date_from'])!='') {
                $report_conditions[] = array('DistMemo.memo_date >=' => Date('Y-m-d H:i:s',strtotime($params['DistMemo']['date_from'])));
            }
            if (isset($params['DistMemo']['date_to'])!='') {
                $report_conditions[] = array('DistMemo.memo_date <=' => Date('Y-m-d H:i:s',strtotime($params['DistMemo']['date_to'].' 23:59:59')));
            }
            
            pr($report_conditions);*/

            $sr_wise_sales_bonus_report = $this->DistMemo->find('all', array(
                /*'conditions'=>$report_conditions,*/
                'joins' => array(
                    array(
                        'table'         => 'dist_memo_details',
                        'alias'         => 'DistMemoDetail',
                        'type'          => 'inner',
                        'conditions'    => 'DistMemo.id=DistMemoDetail.dist_memo_id'
                    ),
                    array(
                        'table'         => 'products',
                        'alias'         => 'Product',
                        'type'          => 'inner',
                        'conditions'    => 'DistMemoDetail.product_id=Product.id'
                    )
                ),
                'fields' => array('DistMemo.distributor_id', 'DistMemo.sr_id', 'Product.name', 'COUNT(DISTINCT(DistMemo.outlet_id)) as no_outlet', 'COUNT(DISTINCT(DistMemo.id)) as no_memo', 'SUM(CASE WHEN price>0 THEN DistMemoDetail.sales_qty END) as sales_qty', 'SUM(CASE WHEN price=0 THEN DistMemoDetail.sales_qty END) as bonus_qty', 'SUM(DistMemoDetail.price * DistMemoDetail.sales_qty) as revenue'),
                'group' => array('DistMemo.distributor_id', 'DistMemo.sr_id', 'DistMemoDetail.product_id', 'Product.name', 'Product.order'),
                'ORDER' => array('DistMemo.distributor_id ASC', 'DistMemo.sr_id ASC', 'Product.order ASC'),
                'recursive' => -1
            ));
            /*echo $this->DistMemo->getLastquery();*/
            //pr($product_wise_sales_bonus_report);exit;

            // $sql = "select product_id,COUNT(DISTINCT dist_memo_details.dist_memo_id) as ec,sum(dist_memo_details.bonus_qty) as bonus_amount from dist_memo_details left join dist_memos on dist_memos.id=dist_memo_details.dist_memo_id where dist_memos.memo_date BETWEEN '".$date_from."' and '".$date_to."' group by dist_memo_details.product_id";               
            // $product_wise_sales_bonus_report = $this->DistMemo->query($sql);
            // $product_list = $this->Product->find('list', array('order' => array('Product.id' => 'asc'),'recursive' => -1));
        }
        /*********************** Check Product Wise Sales Bonus Report End ***********************/
        elseif (isset($this->request->data) && array_key_exists('p_wise_sales_bonus_report', $this->request->data['DistMemo']) && ($this->request->data['DistMemo']['p_wise_sales_bonus_report'] == 1)) {
            $params = $this->request->data;
            if (!empty($params['DistMemo']['office_id'])) {
            } else {

                if ($office_parent_id == 0) {
                } else {

                    $this->request->data['DistMemo']['office_id'] = $this->UserAuth->getOfficeId();
                }
            }
            $p_wise_sales_bonus_report = $this->DistMemo->find('all', array(
                /*'conditions'=>$report_conditions,*/
                'joins' => array(
                    array(
                        'table'         => 'dist_memo_details',
                        'alias'         => 'DistMemoDetail',
                        'type'          => 'inner',
                        'conditions'    => 'DistMemo.id=DistMemoDetail.dist_memo_id'
                    ),
                    array(
                        'table'         => 'products',
                        'alias'         => 'Product',
                        'type'          => 'inner',
                        'conditions'    => 'DistMemoDetail.product_id=Product.id'
                    )
                ),
                'fields' => array('DistMemoDetail.product_id', 'Product.name', 'DistMemo.distributor_id', 'DistMemo.sr_id', 'DistMemo.market_id', 'DistMemo.outlet_id', 'DistMemo.thana_id', 'DistMemo.dist_memo_no', 'DistMemo.memo_date', 'SUM(CASE WHEN price>0 THEN DistMemoDetail.sales_qty END) as sales_qty', 'SUM(CASE WHEN price=0 THEN DistMemoDetail.sales_qty END) as bonus_qty', 'SUM(DistMemoDetail.price * DistMemoDetail.sales_qty) as revenue'),
                'group' => array('DistMemoDetail.product_id', 'Product.name', 'Product.order', 'DistMemo.distributor_id', 'DistMemo.sr_id', 'DistMemo.market_id', 'DistMemo.outlet_id', 'DistMemo.thana_id', 'DistMemo.dist_memo_no', 'DistMemo.memo_date'),
                'order' => array('Product.order ASC', 'DistMemo.distributor_id ASC', 'DistMemo.sr_id ASC', 'DistMemo.memo_date ASC'),
                'recursive' => -1
            ));
            /*echo $this->DistMemo->getLastquery();
            pr($p_wise_sales_bonus_report);exit;*/
        } else {
            $group = array('DistMemo.id', 'DistMemo.dist_memo_no', 'DistMemo.dist_order_no', 'DistMemo.from_app', 'DistMemo.gross_value', 'DistMemo.memo_time', 'DistMemo.status', 'DistMemo.action', 'DistOutlet.name', 'DistMarket.name', 'DistDistributor.name', 'DistRoute.name', 'DistTso.name', 'Office.office_name', 'DistAE.name');

            if (isset($requested_data['DistMemo']['operator_product_count'])) {
                $operator_memo_count_conditions = '';
                if ($requested_data['DistMemo']['operator_product_count'] == 3) {
                    $operator_memo_count_conditions .= "count(case when MemoDetail.price >0 then MemoDetail.id end) BETWEEN '" . $requested_data['DistMemo']['memo_product_count_from'] . "' AND '" . $requested_data['DistMemo']['memo_product_count_to'] . "'";
                } elseif ($requested_data['DistMemo']['operator_product_count'] == 1) {
                    $operator_memo_count_conditions .= "count(case when MemoDetail.price >0 then MemoDetail.id end)  < '" . $requested_data['DistMemo']['memo_product_count'] . "'";
                } elseif ($requested_data['DistMemo']['operator_product_count'] == 2) {
                    $operator_memo_count_conditions .= "count(case when MemoDetail.price >0 then MemoDetail.id end)  > '" . $requested_data['DistMemo']['memo_product_count'] . "'";
                }
                if (strpos($group[14], 'HAVING')) {
                    $group[14] .= ' AND ' . $operator_memo_count_conditions;
                } else {
                    $group[14] .= ' HAVING ' . $operator_memo_count_conditions;
                }
            }

            $this->DistMemo->recursive = 0;
            $this->paginate = array(
                'fields' => array('DistMemo.id', 'DistMemo.dist_memo_no', 'DistMemo.dist_order_no', 'DistMemo.from_app', 'DistMemo.gross_value', 'DistMemo.memo_time', 'DistMemo.status', 'DistMemo.action', 'DistOutlet.name', 'DistMarket.name', 'DistDistributor.name', 'DistRoute.name', 'DistTso.name', 'Office.office_name', 'DistAE.name'),
                'group' => $group,
                'conditions' => $conditions,
                'joins' => array(
                    array(
                        'alias' => 'DistOutlet',
                        'table' => 'dist_outlets',
                        'type' => 'INNER',
                        'conditions' => 'DistOutlet.id = DistMemo.outlet_id'
                    ),
                    array(
                        'alias' => 'DistMarket',
                        'table' => 'dist_markets',
                        'type' => 'INNER',
                        'conditions' => 'DistMarket.id = DistMemo.market_id'
                    ),
                    array(
                        'alias' => 'DistDistributor',
                        'table' => 'dist_distributors',
                        'type' => 'INNER',
                        'conditions' => 'DistDistributor.id = DistMemo.distributor_id'
                    ),
                    array(
                        'alias' => 'DistRoute',
                        'table' => 'dist_routes',
                        'type' => 'INNER',
                        'conditions' => 'DistRoute.id = DistMemo.dist_route_id'
                    ),
                    /*  array(
                        'table' => 'dist_tso_mappings',
                        'alias' => 'DistTsoMapping',
                        'type' => 'LEFT',
                        'conditions' => 'DistTsoMapping.dist_distributor_id = DistMemo.distributor_id'

                    ), */
                    array(
                        'table' => 'dist_tsos',
                        'alias' => 'DistTso',
                        'type' => 'LEFT',
                        'conditions' => 'DistTso.id = DistMemo.tso_id'

                    ),
                    array(
                        'table' => 'dist_area_executives',
                        'alias' => 'DistAE',
                        'type' => 'LEFT',
                        'conditions' => 'DistAE.id = DistTso.dist_area_executive_id'

                    ),
                    array(
                        'table' => 'offices',
                        'alias' => 'Office',
                        'type' => 'LEFT',
                        'conditions' => array('Office.id=DistMemo.office_id')
                    ),
                    array(
                        'table' => 'dist_memo_details',
                        'alias' => 'MemoDetail',
                        'type' => 'inner',
                        'conditions' => array('DistMemo.id=MemoDetail.dist_memo_id')
                    ),
                    /*array(
                        'alias' => 'DistSalesRepresentative',
                        'table' => 'dist_sales_representatives',
                        'type' => 'INNER',
                        'conditions' => 'DistSalesRepresentative.id = DistOrder.sr_id'
                    ),*/

                ),
                'order' => array('DistMemo.id' => 'desc'),
                'limit' => 100
            );
            $this->set('dist_memos', $this->paginate());
        }

        // pr($this->paginate()); exit;
        /*  echo $this->DistMemo->getLastquery();
        //pr($p_wise_sales_bonus_report);
        exit; */
        $this->set('office_id', $this->UserAuth->getOfficeId());
        $this->loadModel('Office');
        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));
        $office_id = isset($this->request->data['DistMemo']['office_id']) != '' ? $this->request->data['DistMemo']['office_id'] : 0;
        $distributor_id = isset($this->request->data['DistMemo']['distributor_id']) != '' ? $this->request->data['DistMemo']['distributor_id'] : 0;

        $distributors = array();


        if ($office_id) {
            $memo_date_from = $this->request->data['DistMemo']['date_from'];
            $memo_date_to = $this->request->data['DistMemo']['date_to'];

            if ($memo_date_from && $office_id && $memo_date_to) {
                $memo_date_from = date("Y-m-d", strtotime($memo_date_from));
                $memo_date_to = date("Y-m-d", strtotime($memo_date_to));


                $distDistributors_raw = $this->DistDistributor->find('list', array(
                    'conditions' => array('DistMemo.office_id' => $office_id, 'DistMemo.memo_date >=' => $memo_date_from, 'DistMemo.memo_date <=' => $memo_date_to),
                    'joins' => array(array('table' => 'dist_memos', 'alias' => 'DistMemo', 'conditions' => 'DistMemo.distributor_id=DistDistributor.id')),
                    'order' => array('DistDistributor.name' => 'asc'),
                ));


                if ($distDistributors_raw) {
                    foreach ($distDistributors_raw as $key => $data) {
                        $distributors[$key] = $data;
                    }
                }
            }
        }

        $territory_id = isset($this->request->data['DistMemo']['territory_id']) != '' ? $this->request->data['DistMemo']['territory_id'] : 0;
        $market_id = isset($this->request->data['DistMemo']['market_id']) != '' ? $this->request->data['DistMemo']['market_id'] : 0;


        $sr_id = isset($this->request->data['DistMemo']['sr_id']) != '' ? $this->request->data['DistMemo']['sr_id'] : 0;
        $dist_route_id = isset($this->request->data['DistMemo']['dist_route_id']) != '' ? $this->request->data['DistMemo']['dist_route_id'] : 0;
        $outlet_id = isset($this->request->data['DistMemo']['outlet_id']) != '' ? $this->request->data['DistMemo']['outlet_id'] : 0;
        $memo_reference_no = isset($this->request->data['DistMemo']['memo_reference_no']) != '' ? $this->request->data['DistMemo']['memo_reference_no'] : 0;

        $srs = array();
        $routes = array();
        if ($sr_id || $distributor_id) {
            $memo_date_from = $this->request->data['DistMemo']['date_from'];
            $memo_date_to = $this->request->data['DistMemo']['date_to'];

            $memo_date_from = date("Y-m-d", strtotime($memo_date_from));
            $memo_date_to = date("Y-m-d", strtotime($memo_date_to));

            $sr_raw = $this->DistSalesRepresentative->find('list', array(
                'conditions' => array(
                    'DistMemo.distributor_id' => $distributor_id,
                    'DistMemo.memo_date >=' => $memo_date_from,
                    'DistMemo.memo_date <=' => $memo_date_to,
                ),
                'joins' => array(array('table' => 'dist_memos', 'alias' => 'DistMemo', 'conditions' => 'DistMemo.sr_id=DistSalesRepresentative.id')),
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
            $markets = $this->DistMemo->Market->find('list', array(
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
        $outlets = $this->DistMemo->Outlet->find('list', array(
            'conditions' => array('Outlet.dist_market_id' => $market_id),
            'order' => array('Outlet.name' => 'asc')
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

        $this->set(compact('offices', 'distributors', 'distributor_id', 'srs', 'sr_id', 'territories', 'markets', 'outlets', 'current_date', 'requested_data', 'product_name', 'sr_wise_sales_bonus_report', 'product_list', 'p_wise_sales_bonus_report', 'status_list', 'routes'));
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
        $this->set('page_title', 'Distributor Memo Details');

        $this->DistMemo->unbindModel(array('hasMany' => array('DistMemoDetail')));
        $memo = $this->DistMemo->find('first', array(
            'conditions' => array('DistMemo.id' => $id)
        ));

        $this->loadModel('DistMemoDetail');
        if (!$this->DistMemo->exists($id)) {
            throw new NotFoundException(__('Invalid district'));
        }
        $this->DistMemoDetail->recursive = 0;
        $memo_details = $this->DistMemoDetail->find(
            'all',
            array(
                'conditions' => array('DistMemoDetail.dist_memo_id' => $id),
                'order' => array('Product.order' => 'asc')
            )
        );
        $this->set(compact('memo', 'memo_details'));
    }

    /**
     * admin_delete method
     *
     * @return void
     */
    public function admin_delete($id = null, $redirect = 1)
    {

        $this->loadModel('Product');
        $this->loadModel('DistMemo');
        $this->loadModel('DistMemoDetail');
        //$this->loadModel('Deposit');
        //$this->loadModel('Collection');
        //start memo setting
        $this->loadModel('MemoSetting');
        $MemoSettings = $this->MemoSetting->find(
            'all',
            array(
                //'conditions' => array('ChallanDetail.challan_id' => $challan['Challan']['id']),
                'order' => array('id' => 'asc'),
                'recursive' => 0,
                //'limit' => 100
            )
        );

        foreach ($MemoSettings as $s_result) {
            //echo $s_result['MemoSetting']['name'].'<br>';
            if ($s_result['MemoSetting']['name'] == 'stock_validation') {
                $stock_validation = $s_result['MemoSetting']['value'];
            }
            if ($s_result['MemoSetting']['name'] == 'stock_hit') {
                $stock_hit = $s_result['MemoSetting']['value'];
            }

            if ($s_result['MemoSetting']['name'] == 'ec_calculation') {
                $ec_calculation = $s_result['MemoSetting']['value'];
            }
            if ($s_result['MemoSetting']['name'] == 'oc_calculation') {
                $oc_calculation = $s_result['MemoSetting']['value'];
            }

            if ($s_result['MemoSetting']['name'] == 'sales_calculation') {
                $sales_calculation = $s_result['MemoSetting']['value'];
            }
            if ($s_result['MemoSetting']['name'] == 'stamp_calculation') {
                $stamp_calculation = $s_result['MemoSetting']['value'];
            }
            //pr($MemoSetting);
        }

        $this->set(compact('stock_validation'));
        //end memo setting


        if ($this->request->is('post')) {


            /*
             * This condition added for data synchronization 
             * Cteated by imrul in 09, April 2017
             * Duplicate memo check
             */
            $count = $this->DistMemo->find('count', array(
                'conditions' => array(
                    'DistMemo.id' => $id
                )
            ));

            $memo_id_arr = $this->DistMemo->find('first', array(
                'conditions' => array(
                    'DistMemo.id' => $id
                )
            ));

            $this->loadModel('DistStore');
            $store_id_arr = $this->DistStore->find('first', array(
                'conditions' => array(
                    'DistStore.dist_distributor_id' => $memo_id_arr['DistMemo']['distributor_id']
                )
            ));

            $store_id = $store_id_arr['DistStore']['id'];



            $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
            $product_list = Set::extract($products, '{n}.Product');



            for ($memo_detail_count = 0; $memo_detail_count < count($memo_id_arr['DistMemoDetail']); $memo_detail_count++) {
                $product_id = $memo_id_arr['DistMemoDetail'][$memo_detail_count]['product_id'];
                $sales_qty = $memo_id_arr['DistMemoDetail'][$memo_detail_count]['sales_qty'];
                $sales_price = $memo_id_arr['DistMemoDetail'][$memo_detail_count]['price'];
                $memo_territory_id = $memo_id_arr['DistMemo']['territory_id'];
                $memo_no = $memo_id_arr['DistMemo']['dist_memo_no'];
                $memo_date = $memo_id_arr['DistMemo']['memo_date'];
                $outlet_id = $memo_id_arr['DistMemo']['outlet_id'];
                $market_id = $memo_id_arr['DistMemo']['market_id'];

                $punits_pre = $this->search_array($product_id, 'id', $product_list);
                if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                    $base_quantity = $sales_qty;
                } else {
                    $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
                }

                $update_type = 'add';
                $this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 4, $memo_id_arr['DistMemo']['memo_date']);



                // subract sales achievement and stamp achievemt 
                // sales calculation
                $t_price = $sales_qty * $sales_price;
            }

            $memo_id = $memo_id_arr['DistMemo']['id'];
            $memo_no = $memo_id_arr['DistMemo']['dist_memo_no'];
            $this->DistMemo->id = $memo_id;
            $this->DistMemoDetail->deleteAll(array('DistMemoDetail.dist_memo_id' => $memo_id));
            $this->DistMemo->delete();

            if ($redirect == 1) {
                $this->flash(__('Distributor Memo was not deleted'), array('action' => 'index'));
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
    public function admin_create_memo()
    {
        $this->loadModel('DistOrder');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $this->set(compact('generate_memo_no'));

        if ($this->request->is('post')) {
            $order_id = $this->request->data['DistMemo']['order_id'];
            $order_info = $this->DistOrder->find('first', array('conditions' => array('DistOrder.id' => $order_id)));
            //pr($order_info);die();
            //pr($this->request->data);die();
            $memo_info = array();
            if (!empty($order_info)) {
                $sr_id = $order_info['DistOrder']['sales_person_id'];
                $generate_memo_no = 'M' . $sr_id . date('yy') . date('m') . date('d') . date('h') . date('i') . date('s');
                $memo_info['DistMemo']['dist_memo_no'] =  $generate_memo_no;
                $memo_info['DistMemo']['dist_order_no'] =  $order_info['DistOrder']['dist_order_no'];
                //$memo_info['DistMemo']['memo_date'] =  $order_info['DistOrder']['order_date']; 

                //$memo_info['DistMemo']['memo_date'] =  $this->current_datetime();        
                $memo_info['DistMemo']['memo_date'] =  date('Y-m-d', strtotime($this->current_datetime()));
                $memo_info['DistMemo']['distributor_id'] =  $order_info['DistOrder']['distributor_id'];
                $memo_info['DistMemo']['sr_id'] =  $order_info['DistOrder']['sr_id'];
                $memo_info['DistMemo']['outlet_id'] =  $order_info['DistOrder']['outlet_id'];
                $memo_info['DistMemo']['market_id'] =  $order_info['DistOrder']['market_id'];
                $memo_info['DistMemo']['territory_id'] =  $order_info['DistOrder']['territory_id'];
                $memo_info['DistMemo']['office_id'] =  $order_info['DistOrder']['office_id'];
                $memo_info['DistMemo']['thana_id'] =  $order_info['DistOrder']['thana_id'];
                $memo_info['DistMemo']['is_ngo'] =  $order_info['DistOrder']['is_ngo'];
                $memo_info['DistMemo']['is_program'] =  $order_info['DistOrder']['is_program'];
                $memo_info['DistMemo']['dist_route_id'] =  $order_info['DistOrder']['dist_route_id'];
                $memo_info['DistMemo']['ae_id'] =  $order_info['DistOrder']['ae_id'];
                $memo_info['DistMemo']['tso_id'] =  $order_info['DistOrder']['tso_id'];

                $memo_info['DistMemo']['action'] =  1;
                $memo_info['DistMemo']['from_app'] =  0;
                $memo_info['DistMemo']['status'] =  0;
                $memo_info['DistMemo']['created_at'] =  $this->current_datetime();
                $memo_info['DistMemo']['created_by'] =  $this->UserAuth->getUserId();
                $memo_info['DistMemo']['updated_at'] =  $this->current_datetime();
                $memo_info['DistMemo']['updated_by'] =  $this->UserAuth->getUserId();
                $memo_info['DistMemo']['order_time'] =  $this->current_datetime();

                $memo_info['DistMemo']['credit_collected_amount'] =  $order_info['DistOrder']['credit_collected_amount'];
                $memo_info['DistMemo']['is_active'] =  1;
                $memo_info['DistMemo']['collection_id'] =  $order_info['DistOrder']['collection_id'];

                $memo_info['DistMemo']['credit_amount'] =  $this->request->data['DistMemo']['credit_amount'];
                $memo_info['DistMemo']['cash_recieved'] =  $this->request->data['DistMemo']['cash_recieved'];
                $memo_info['DistMemo']['gross_value'] =  $this->request->data['DistMemo']['gross_value'];
                $memo_info['DistMemo']['discount_percent'] =  $this->request->data['DistMemo']['discount_percent'];
                $memo_info['DistMemo']['discount_value'] =  $this->request->data['DistMemo']['discount_value'];

                $this->DistMemo->create();
                if ($this->DistMemo->save($memo_info)) {
                    $dist_memo_id = $this->DistMemo->getLastInsertId();
                    $memo_info_arr = $this->DistMemo->find('first', array(
                        'conditions' => array(
                            'DistMemo.id' => $dist_memo_id
                        )
                    ));

                    if (!empty($this->request->data['MemoDetail'])) {
                        $all_product_id = $this->request->data['MemoDetail']['product_id'];
                        foreach ($this->request->data['MemoDetail']['product_id'] as $key => $val) {
                            if ($val == NULL) {
                                continue;
                            }
                            $sales_price = $this->request->data['MemoDetail']['Price'][$key];

                            $product_id = $val;
                            $sales_qty  = $this->request->data['MemoDetail']['sales_qty'][$key];

                            $memo_details['DistMemoDetail']['product_id'] = $product_id;
                            $memo_details['DistMemoDetail']['dist_memo_id'] = $dist_memo_id;

                            $memo_details['DistMemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
                            $memo_details['DistMemoDetail']['price'] = $sales_price;

                            $memo_details['DistMemoDetail']['sales_qty'] = $sales_qty;

                            $product_price_slab_id = 0;
                            if ($sales_price > 0) {
                                $product_price_slab_id = $this->get_product_price_id($val, $sales_price, $all_product_id);
                            }
                            $memo_details['DistMemoDetail']['product_price_id'] = $product_price_slab_id['product_price_id'];
                            $memo_details['DistMemoDetail']['product_combination_id'] = $product_price_slab_id['combination_id'];

                            if ($sales_price == 0) {
                                $is_bonus = $memo_bonus_details['DistMemoDetail']['is_bonus'] = 1;
                                $memo_details['DistMemoDetail']['bonus_qty'] = $sales_qty;
                                $memo_details['DistMemoDetail']['bonus_product_id'] = NULL;
                            } else {
                                $is_bonus = $memo_bonus_details['DistMemoDetail']['is_bonus'] = 0;
                                $memo_details['DistMemoDetail']['bonus_qty'] = NULL;
                                $memo_details['DistMemoDetail']['bonus_product_id'] = NULL;
                            }
                            $memo_date = date('Y-m-d', strtotime($memo_info['DistMemo']['memo_date']));
                            $memo_details['DistMemoDetail']['bonus_scheme_id'] = 0;

                            $total_product_data[] = $memo_details;
                        }

                        $this->loadModel('DistMemoDetail');
                        $this->DistMemoDetail->create();
                        if ($this->DistMemoDetail->saveAll($total_product_data)) {

                            /*$order_data['DistOrder']['id']=$order_id;
                            $order_data['DistOrder']['status']= 2;
                            $order_data['DistOrder']['processing_status']= 2;
                            $this->DistOrder->save($order_data);*/

                            $this->Session->setFlash(__('The Memo has been Updated'), 'flash/success');
                            $this->redirect(array('action' => 'index'));
                        }
                    }
                }
            }
        }


        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $order_conditions = array('DistOrder.processing_status' => 1);
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
                $order_conditions = array('DistOrder.tso_id' => $dist_tso_id, 'DistOrder.processing_status' => 1);
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];

                $order_conditions = array('DistOrder.distributor_id' => $distributor_id, 'DistOrder.processing_status' => 1);
            } else {
                $order_conditions = array('DistOrder.office_id' => $this->UserAuth->getOfficeId(), 'DistOrder.processing_status' => 1);
            }
        }
        $orsers = $this->DistOrder->find('all', array('conditions' => $order_conditions));
        $order_list = array();
        foreach ($orsers as $key => $value) {
            $order_list[$value['DistOrder']['id']] = $value['DistOrder']['dist_order_no'];
        }
        $this->set(compact('order_list'));
    }

    public function admin_create_memo_backup_20_11_2019()
    {

        $distributors = array();
        $pre_srs = array();
        $pre_routes = array();
        $pre_markets = array();
        $pre_outlets = array();

        date_default_timezone_set('Asia/Dhaka');
        $this->set('page_title', 'Create Distributor Memo');
        $this->loadModel('DistMemoDetail');

        /* ------ unset cart data ------- */

        $this->Session->delete('cart_session_data');
        $this->Session->delete('matched_session_data');
        $this->Session->delete('combintaion_qty_data');


        $user_id = $this->UserAuth->getUserId();

        $generate_memo_no = $user_id . date('d') . date('m') . date('h') . date('i') . date('s');
        $this->set(compact('generate_memo_no'));

        /****** tso and ae list start  *****/

        $this->loadModel('DistTso');
        $tso_list = $this->DistTso->find('list', array('order' => array('DistTso.name' => 'asc'), 'recursive' => -1));

        $this->loadModel('DistAreaExecutive');
        $ae_list = $this->DistAreaExecutive->find('list', array('order' => array('DistAreaExecutive.name' => 'asc'), 'recursive' => -1));
        $this->set(compact('tso_list', 'ae_list'));
        /****** tso and ae list end  *****/


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

        //start memo setting
        $this->loadModel('MemoSetting');
        $MemoSettings = $this->MemoSetting->find(
            'all',
            array(
                'order' => array('id' => 'asc'),
                'recursive' => 0
            )
        );

        foreach ($MemoSettings as $s_result) {
            //echo $s_result['MemoSetting']['name'].'<br>';
            if ($s_result['MemoSetting']['name'] == 'stock_validation') {
                $stock_validation = $s_result['MemoSetting']['value'];
            }
            if ($s_result['MemoSetting']['name'] == 'stock_hit') {
                $stock_hit = $s_result['MemoSetting']['value'];
            }

            if ($s_result['MemoSetting']['name'] == 'ec_calculation') {
                $ec_calculation = $s_result['MemoSetting']['value'];
            }
            if ($s_result['MemoSetting']['name'] == 'oc_calculation') {
                $oc_calculation = $s_result['MemoSetting']['value'];
            }

            if ($s_result['MemoSetting']['name'] == 'sales_calculation') {
                $sales_calculation = $s_result['MemoSetting']['value'];
            }
            if ($s_result['MemoSetting']['name'] == 'stamp_calculation') {
                $stamp_calculation = $s_result['MemoSetting']['value'];
            }
            //pr($MemoSetting);
        }

        $this->set(compact('stock_validation'));
        //end memo setting



        $office_parent_id = $this->UserAuth->getOfficeParentId();
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

            $office_id = isset($this->request->data['DistMemo']['office_id']) != '' ? $this->request->data['DistMemo']['office_id'] : 0;
            $territory_id = isset($this->request->data['DistMemo']['territory_id']) != '' ? $this->request->data['DistMemo']['territory_id'] : 0;
            $market_id = isset($this->request->data['DistMemo']['market_id']) != '' ? $this->request->data['DistMemo']['market_id'] : '';
            $distributor_id = isset($this->request->data['DistMemo']['distributor_id']) != '' ? $this->request->data['DistMemo']['distributor_id'] : 0;
            $dist_route_id = isset($this->request->data['DistMemo']['dist_route_id']) != '' ? $this->request->data['DistMemo']['dist_route_id'] : 0;

            $this->loadModel('DistStore');
            $this->DistStore->recursive = -1;
            $store_info = $this->DistStore->find('first', array(
                'conditions' => array('dist_distributor_id' => $distributor_id)
            ));
            $store_id = $store_info['DistStore']['id'];
        } else if ($office_parent_id != 0) {
            reset($offices);
            $office_id = key($offices);
            $product_id = '';
        }


        $this->loadModel('Territory');

        $distRoutes = $this->DistRoute->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));


        $distRoutes = $this->DistRoute->find('list', array('conditions' => array('office_id' => $office_id), 'order' => array('name' => 'asc')));


        $territories_list = $this->Territory->find('all', array(
            'conditions' => array('Territory.office_id' => $office_id, 'User.user_group_id !=' => 1008),
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



        $territories = array();
        foreach ($territories_list as $t_result) {
            $territories[$t_result['Territory']['id']] = $t_result['Territory']['name'] . ' (' . $t_result['SalesPerson']['name'] . ')';
        }

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
        $product_list = $this->Product->find('list', array('order' => array('order' => 'asc')));
        /* ----- start code of product list ----- */

        if ($this->request->is('post')) {
            $sale_type_id = $this->request->data['DistMemo']['sale_type_id'];

            // pr($this->request->data); 
            // exit;

            $outlet_is_within_group = $this->outletGroupCheck($this->request->data['DistMemo']['outlet_id']);
            $product_is_injectable = $this->productInjectableCheck($this->request->data['MemoDetail']['product_id']);

            if ($product_is_injectable && !$outlet_is_within_group && $sale_type_id != 10) {
                $this->Session->setFlash(__("Special product can't sell to ordinary outlet!"), 'flash/error');
                $this->redirect(array('action' => 'create_memo'));
                exit;
            }


            $ae_id = $this->request->data['DistMemo']['ae_id'];
            $tso_id = $this->request->data['DistMemo']['tso_id'];

            if (!$ae_id && !$tso_id) {
                $this->Session->setFlash(__("Area Executive and TSO are not mapped properly. Please map first!"), 'flash/error');
                $this->redirect(array('action' => 'create_memo'));
                exit;
            } else if (!$ae_id) {
                $this->Session->setFlash(__("Area Executive is not mapped properly. Please map first!"), 'flash/error');
                $this->redirect(array('action' => 'create_memo'));
                exit;
            } else if (!$tso_id) {
                $this->Session->setFlash(__("TSO is not mapped properly. Please map first!"), 'flash/error');
                $this->redirect(array('action' => 'create_memo'));
                exit;
            }



            $outlet_info = $this->DistOutlet->find('first', array(
                'conditions' => array('DistOutlet.id' => $this->request->data['DistMemo']['outlet_id']),
                'recursive' => -1
            ));

            $is_dist_outlet = 1;

            $this->loadModel('DistMemo');
            $memo_no = $this->request->data['DistMemo']['memo_no'];


            if ($sale_type_id == 10) {
                $memo_count = $this->DistMemo->find('count', array(
                    'conditions' => array('DistMemo.dist_memo_no' => $memo_no),
                    'fields' => array('dist_memo_no'),
                    'recursive' => -1
                ));
            }

            $pre_submit_data = array();
            if ($memo_count == 0) {
                if ($this->request->data['DistMemo']) {
                    //pr($this->request->data);exit;
                    /* START ADD NEW */
                    //get office id 
                    $office_id = $this->request->data['DistMemo']['office_id'];

                    //get thana id 
                    $this->loadModel('DistMarket');
                    $market_info = $this->DistMarket->find(
                        'first',
                        array(
                            'conditions' => array('DistMarket.id' => $this->request->data['DistMemo']['market_id']),
                            'fields' => 'DistMarket.thana_id',
                            'order' => array('DistMarket.id' => 'asc'),
                            'recursive' => -1,
                            //'limit' => 100
                        )
                    );
                    $thana_id = $market_info['DistMarket']['thana_id'];
                    /* END ADD NEW */

                    $sale_type_id = $this->request->data['DistMemo']['sale_type_id'];

                    if ($sale_type_id == 10) {
                        $this->loadModel('DistMemo');
                        $this->loadModel('DistMemoDetail');

                        $this->request->data['DistMemo']['is_active'] = 1;
                        //$this->request->data['Memo']['status'] = ($this->request->data['Memo']['credit_amount'] != 0) ? 1 : 2;
                        if (array_key_exists('draft', $this->request->data)) {
                            $this->request->data['DistMemo']['status'] = 0;
                            //$message = "Distributor Memo Has Been Saved as Draft";
                            $message = "Distributor Memo Has Been Saved";
                        } else {
                            $message = "Distributor Memo Has Been Saved";
                            $this->request->data['DistMemo']['status'] = 0;
                        }

                        $sales_person = $this->SalesPerson->find('list', array(
                            'conditions' => array('territory_id' => $this->request->data['DistMemo']['territory_id']),
                            'order' => array('name' => 'asc')
                        ));

                        $this->request->data['DistMemo']['sales_person_id'] = key($sales_person);


                        $this->request->data['DistMemo']['entry_date'] = date('Y-m-d', strtotime($this->request->data['DistMemo']['entry_date']));
                        $this->request->data['DistMemo']['memo_date'] = date('Y-m-d', strtotime($this->request->data['DistMemo']['memo_date']));

                        /* echo "<pre>";
                          print_r($this->request->data['Memo']);
                          echo "</pre>";die(); */

                        $memoData['office_id'] = $this->request->data['DistMemo']['office_id'];
                        $memoData['distributor_id'] = $this->request->data['DistMemo']['distributor_id'];
                        $memoData['sr_id'] = $this->request->data['DistMemo']['sr_id'];
                        $memoData['dist_route_id'] = $this->request->data['DistMemo']['dist_route_id'];
                        $memoData['sale_type_id'] = $this->request->data['DistMemo']['sale_type_id'];
                        $memoData['territory_id'] = $this->request->data['DistMemo']['territory_id'];
                        $memoData['market_id'] = $this->request->data['DistMemo']['market_id'];
                        $memoData['outlet_id'] = $this->request->data['DistMemo']['outlet_id'];
                        $memoData['entry_date'] = $this->request->data['DistMemo']['entry_date'];
                        $memoData['memo_date'] = $this->request->data['DistMemo']['memo_date'];
                        $memoData['dist_memo_no'] = $memo_no;
                        $memoData['gross_value'] = $this->request->data['DistMemo']['gross_value'];
                        $memoData['cash_recieved'] = $this->request->data['DistMemo']['cash_recieved'];
                        $memoData['credit_amount'] = $this->request->data['DistMemo']['credit_amount'];
                        $memoData['is_active'] = $this->request->data['DistMemo']['is_active'];
                        $memoData['status'] = $this->request->data['DistMemo']['status'];
                        //$memoData['memo_time'] = $this->current_datetime();   
                        $memoData['memo_time'] = $this->request->data['DistMemo']['entry_date'];
                        $memoData['sales_person_id'] = $this->request->data['DistMemo']['sales_person_id'];
                        $memoData['from_app'] = 0;
                        $memoData['action'] = 1;
                        $memoData['ae_id'] = $this->request->data['DistMemo']['ae_id'];
                        $memoData['tso_id'] = $this->request->data['DistMemo']['tso_id'];

                        $memoData['memo_reference_no'] = $this->request->data['DistMemo']['memo_reference_no'];

                        $memoData['created_at'] = $this->current_datetime();
                        $memoData['created_by'] = $this->UserAuth->getUserId();
                        $memoData['updated_at'] = $this->current_datetime();
                        $memoData['updated_by'] = $this->UserAuth->getUserId();

                        $memoData['office_id'] = $office_id ? $office_id : 0;
                        $memoData['thana_id'] = $thana_id ? $thana_id : 0;

                        if ($this->request->data['DistMemo']['memo_date'] >= date('Y-m-d', strtotime('-5 month'))) {
                            $this->DistMemo->create();
                            if ($this->DistMemo->save($memoData)) {
                                $dist_memo_id = $this->DistMemo->getLastInsertId();

                                $memo_info_arr = $this->DistMemo->find('first', array(
                                    'conditions' => array(
                                        'DistMemo.id' => $dist_memo_id
                                    )
                                ));

                                $this->loadModel('DistStore');

                                $store_id_arr = $this->DistStore->find('first', array(
                                    'conditions' => array(
                                        'DistStore.dist_distributor_id' => $memo_info_arr['DistMemo']['distributor_id']
                                    )
                                ));

                                $store_id = $store_id_arr['DistStore']['id'];

                                if ($dist_memo_id) {
                                    $all_product_id = $this->request->data['MemoDetail']['product_id'];
                                    if (!empty($this->request->data['MemoDetail'])) {
                                        $total_product_data = array();
                                        $memo_details = array();
                                        $memo_details['DistMemoDetail']['dist_memo_id'] = $dist_memo_id;

                                        foreach ($this->request->data['MemoDetail']['product_id'] as $key => $val) {
                                            if ($val == NULL) {
                                                continue;
                                            }
                                            $product_id = $memo_details['DistMemoDetail']['product_id'] = $val;
                                            $memo_details['DistMemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
                                            $sales_price = $memo_details['DistMemoDetail']['price'] = $this->request->data['MemoDetail']['Price'][$key];
                                            $sales_qty = $memo_details['DistMemoDetail']['sales_qty'] = $this->request->data['MemoDetail']['sales_qty'][$key];
                                            // $memo_details['MemoDetail']['product_price_id'] = $this->request->data['MemoDetail']['product_price_id'][$key];
                                            $product_price_slab_id = 0;
                                            if ($sales_price > 0) {
                                                $product_price_slab_id = $this->get_product_price_id($val, $sales_price, $all_product_id);
                                                // pr($product_price_slab_id);exit;
                                            }
                                            $memo_details['DistMemoDetail']['product_price_id'] = $product_price_slab_id['product_price_id'];
                                            $memo_details['DistMemoDetail']['product_combination_id'] = $product_price_slab_id['combination_id'];
                                            ////$memo_details['DistMemoDetail']['bonus_qty'] = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
                                            $memo_details['DistMemoDetail']['bonus_qty'] = NULL;
                                            $memo_details['DistMemoDetail']['bonus_product_id'] = NULL;
                                            /*
                                            if ($this->request->data['MemoDetail']['bonus_product_id'][$key] != 0) {
                                                $memo_details['DistMemoDetail']['bonus_product_id'] = $this->request->data['MemoDetail']['bonus_product_id'][$key];
                                            } else {
                                                $memo_details['DistMemoDetail']['bonus_product_id'] = NULL;
                                            }
                                            */

                                            //Start for bonus
                                            $memo_date = date('Y-m-d', strtotime($this->request->data['DistMemo']['memo_date']));
                                            ////$bonus_product_id = $this->request->data['MemoDetail']['bonus_product_id'];
                                            ////$bonus_product_qty = $this->request->data['MemoDetail']['bonus_product_qty'];
                                            $bonus_product_id = "";
                                            $bonus_product_qty = "";
                                            $memo_details['DistMemoDetail']['bonus_id'] = 0;
                                            $memo_details['DistMemoDetail']['bonus_scheme_id'] = 0;
                                            /*
                                            if ($bonus_product_qty[$key] > 0) {
                                                $b_product_id = $bonus_product_id[$key];
                                                $bonus_result = $this->bouns_and_scheme_id_set($b_product_id, $memo_date);
                                                $memo_details['DistMemoDetail']['bonus_id'] = $bonus_result['bonus_id'];
                                            }
                                             */
                                            //End for bouns
                                            //pr($memo_details);
                                            $total_product_data[] = $memo_details;

                                            /*
                                            if ($bonus_product_qty[$key] > 0) {
                                                $memo_details_bonus['DistMemoDetail']['memo_id'] = $memo_id;
                                                $memo_details_bonus['DistMemoDetail']['is_bonus'] = 1;
                                                $memo_details_bonus['DistMemoDetail']['product_id'] = $product_id;
                                                $memo_details_bonus['DistMemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
                                                $memo_details_bonus['DistMemoDetail']['price'] = 0.0;
                                                $memo_details_bonus['DistMemoDetail']['sales_qty'] = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
                                                $total_product_data[] = $memo_details_bonus;
                                                unset($memo_details_bonus);
                                                //update inventory
                                                if ($stock_hit) {
                                                    $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
                                                    $product_list = Set::extract($products, '{n}.Product');
                                                    $bonus_qty = $this->request->data['MemoDetail']['bonus_product_qty'][$key];
                                                    $punits_pre = $this->search_array($product_id, 'id', $product_list);
                                                    if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                                                        $base_quantity = $bonus_qty;
                                                    } else {
                                                        $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $bonus_qty);
                                                    }

                                                    $update_type = 'deduct';
                                                    $this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $this->request->data['DistMemo']['memo_date']);
                                                }
                                            }
                                             * 
                                             */

                                            //update inventory
                                            if ($stock_hit) {
                                                $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
                                                $product_list = Set::extract($products, '{n}.Product');

                                                $punits_pre = $this->search_array($product_id, 'id', $product_list);
                                                if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                                                    $base_quantity = $sales_qty;
                                                } else {
                                                    $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
                                                }

                                                $update_type = 'deduct';
                                                $this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $this->request->data['DistMemo']['memo_date']);
                                            }

                                            // sales calculation
                                            $tt_price = $sales_qty * $sales_price;
                                        }
                                        //exit; 
                                        // pr($total_product_data);exit;
                                        $this->DistMemoDetail->saveAll($total_product_data);
                                    }
                                }
                            }

                            $this->Session->setFlash(__($message), 'flash/success');
                            $pre_submit_data = $this->request->data['DistMemo'];

                            // $this->redirect(array("controller" => "DistMemos", 'action' => 'index'));
                            // exit;
                        } else {
                            $this->Session->setFlash(__('Distributor Memo Date Should Not Be less Then 3 Months'), 'flash/error');
                        }
                    }
                }
            } else {
                $this->Session->setFlash(__('Distributor Memo number already exist'), 'flash/error');
                $this->redirect(array('action' => 'index'));
            }
        }
        if (!empty($this->Session->read('from_outlet'))) {
            $from_outlet = $this->Session->read('from_outlet');
            $this->loadModel('DistDistributor');
            $distributors = $this->DistDistributor->find('list', array(
                'conditions' => array('DistDistributor.office_id' => $from_outlet['DistOutlet']['office_id']),
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

            //pr($from_outlet);exit;
            $this->loadModel('DistRouteMapping');
            $this->loadModel('DistRoute');
            $office_id = $from_outlet['DistOutlet']['office_id'];
            $sr_id = $from_outlet['DistOutlet']['dist_sales_representative_id'];
            $distributor_id = $from_outlet['DistOutlet']['dist_distributor_id'];
            $dist_route_id = $from_outlet['DistOutlet']['dist_route_id'];
            $market_memo_date = date("Y-m-d", strtotime($from_outlet['DistOutlet']['memo_date']));

            if ($sr_id && $distributor_id && $market_memo_date) {
                $qry_route = "select distinct dist_route_id from dist_sr_route_mapping_histories
                    where dist_sr_id=$sr_id and dist_distributor_id=$distributor_id and is_change=1 and 
                    '" . $market_memo_date . "' between effective_date and 
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


                if ($market_memo_date && $distributor_id && $office_id && $market_memo_date) {

                    $qry = "select distinct dist_tso_id from dist_tso_mapping_histories
                      where office_id=$office_id and is_change=1 and dist_distributor_id=$distributor_id and 
                        '" . $market_memo_date . "' between effective_date and 
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
                    '" . $market_memo_date . "' between effective_date and 
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

            $memo_date = date("Y-m-d H:i:s", strtotime($pre_submit_data['memo_date']));
            $qry = "select distinct dist_distributor_id from dist_tso_mapping_histories
                  where office_id=$office_id and is_change=1 and 
                    '" . $memo_date . "' between effective_date and 
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

            $conditions = array(
                'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.id' => $dist_ids), 'order' => array('DistDistributor.name' => 'asc'), 'recursive' => 0
            );

            if ($office_id) {
                $dist_all = $this->DistDistributor->find('all', $conditions);
            }


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
                    '" . $memo_date . "' between effective_date and 
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
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistTso');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');
        $office_parent_id = $this->UserAuth->getOfficeParentId();

        /*if($office_parent_id == 0){
            $tso_conditions = array();
        }else{
            $tso_conditions = array('office_id'=>$this->UserAuth->getOfficeId());
            $ae_conditions = array('office_id'=>$this->UserAuth->getOfficeId());
        }
        if($user_group_id == 1029){
           
            $tso_conditions = array('user_id'=>$user_id);
            $ae_conditions = array('office_id'=>$this->UserAuth->getOfficeId());
          
        }
        if($user_group_id == 1028){
            $ae_conditions = array('user_id'=>$user_id);
            
        }*/
        /****** tso and ae list start  *****/
        $tso_conditions = array();
        $ae_conditions = array();

        $tso_list = $this->DistTso->find('list', array('conditions' => $tso_conditions, 'order' => array('DistTso.name' => 'asc'), 'recursive' => -1));
        $ae_list = $this->DistAreaExecutive->find('list', array('conditions' => $ae_conditions, 'order' => array('DistAreaExecutive.name' => 'asc'), 'recursive' => -1));


        $this->set(compact('tso_list', 'ae_list'));

        /****** tso and ae list end  *****/



        /*
          echo "<pre>";
          print_r($this->request->data); exit;
          exit;
         * 
         */


        $this->loadModel('Product');
        //start memo setting
        $this->loadModel('MemoSetting');
        $MemoSettings = $this->MemoSetting->find(
            'all',
            array(
                //'conditions' => array('ChallanDetail.challan_id' => $challan['Challan']['id']),
                'order' => array('id' => 'asc'),
                'recursive' => 0,
                //'limit' => 100
            )
        );

        foreach ($MemoSettings as $s_result) {
            //echo $s_result['MemoSetting']['name'].'<br>';
            if ($s_result['MemoSetting']['name'] == 'stock_validation') {
                $stock_validation = $s_result['MemoSetting']['value'];
            }
            if ($s_result['MemoSetting']['name'] == 'stock_hit') {
                $stock_hit = $s_result['MemoSetting']['value'];
            }

            if ($s_result['MemoSetting']['name'] == 'ec_calculation') {
                $ec_calculation = $s_result['MemoSetting']['value'];
            }
            if ($s_result['MemoSetting']['name'] == 'oc_calculation') {
                $oc_calculation = $s_result['MemoSetting']['value'];
            }

            if ($s_result['MemoSetting']['name'] == 'sales_calculation') {
                $sales_calculation = $s_result['MemoSetting']['value'];
            }
            if ($s_result['MemoSetting']['name'] == 'stamp_calculation') {
                $stamp_calculation = $s_result['MemoSetting']['value'];
            }
            //pr($MemoSetting);
        }

        $this->set(compact('stock_validation'));
        //end memo setting

        $current_date = date('d-m-Y', strtotime($this->current_date()));



        /* ------- start get edit data -------- */
        $this->DistMemo->recursive = 1;
        $options = array(
            'conditions' => array('DistMemo.id' => $id)
        );

        $existing_record = $this->DistMemo->find('first', $options);
        // pr($existing_record);exit; 






        // pr($existing_record);die();
        $details_data = array();
        foreach ($existing_record['DistMemoDetail'] as $detail_val) {
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
            $details_data[] = $detail_val;
        }

        $existing_record['DistMemoDetail'] = $details_data;

        $this->loadModel('MeasurementUnit');
        for ($i = 0; $i < count($details_data); $i++) {
            $measurement_unit_id = $details_data[$i]['measurement_unit_id'];
            $measurement_unit_name = $this->MeasurementUnit->find('all', array(
                'conditions' => array('MeasurementUnit.id' => $measurement_unit_id),
                'fields' => array('name'),
                'recursive' => -1
            ));
            $existing_record['DistMemoDetail'][$i]['measurement_unit_name'] = $measurement_unit_name[0]['MeasurementUnit']['name'];
        }

        $existing_record['office_id'] = $existing_record['DistMemo']['office_id'];
        $existing_record['distributor_id'] = $existing_record['DistMemo']['distributor_id'];
        $existing_record['sr_id'] = $existing_record['DistMemo']['sr_id'];
        $existing_record['dist_route_id'] = $existing_record['DistMemo']['dist_route_id'];
        $existing_record['territory_id'] = $existing_record['DistMemo']['territory_id'];
        $existing_record['thana_id'] = $existing_record['DistMemo']['thana_id'];
        $existing_record['market_id'] = $existing_record['DistMemo']['market_id'];
        $existing_record['outlet_id'] = $existing_record['DistMemo']['outlet_id'];
        $existing_record['memo_time'] = date('d-m-Y', strtotime($existing_record['DistMemo']['memo_time']));
        $existing_record['memo_date'] = date('d-m-Y', strtotime($existing_record['DistMemo']['memo_date']));
        $existing_record['dist_memo_no'] = $existing_record['DistMemo']['dist_memo_no'];
        $existing_record['memo_reference_no'] = $existing_record['DistMemo']['memo_reference_no'];
        $dist_order_no = $existing_record['dist_order_no'] = $existing_record['DistMemo']['dist_order_no'];

        //pr($existing_record);die();
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
                array(
                    'table' => 'dist_open_combination_products',
                    'alias' => 'DistOpenCombinationProduct',
                    'type' => 'Inner',
                    'conditions' => 'DistOpenCombinationProduct.product_id=Product.id'
                ),
                array(
                    'table' => 'dist_open_combinations',
                    'alias' => 'DistOpenCombination',
                    'type' => 'Inner',
                    'conditions' => 'DistOpenCombinationProduct.combination_id=DistOpenCombination.id'
                ),
            ),
            'conditions' => array(
                /*'DistCurrentInventory.qty >' => 0,*/
                'DistCurrentInventory.store_id' => $store_id['DistStore']['id'],
                'DistOpenCombination.start_date <=' => $existing_record['DistMemo']['memo_date'],
                'DistOpenCombination.end_date >=' => $existing_record['DistMemo']['memo_date'],
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
        } elseif ($existing_record['DistMemo']['is_program'] == 1) {
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
        $inventory_results = $this->DistCurrentInventory->find('all', array(
            'conditions' => array(
                'DistCurrentInventory.store_id' => $store_id,
                'DistCurrentInventory.inventory_status_id' => 1
            ),
            'fields' => array('product_id', 'sum(qty) as qty', 'sum(bonus_qty) as bonus_qty'),
            'group' => array('product_id'),
            //'order' => array('DistCurrentInventory.product_id' => 'asc'),
            'recursive' => -1
        ));

        $inventory_qty_info = array();
        $inventory_bouns_qty_info = array();
        foreach ($inventory_results as $inventory_result) {
            $inventory_qty_info[$inventory_result['DistCurrentInventory']['product_id']] = $inventory_result[0]['qty'];
            $inventory_bouns_qty_info[$inventory_result['DistCurrentInventory']['product_id']] = $inventory_result[0]['bonus_qty'];
        }


        foreach ($existing_record['DistMemoDetail'] as $key => $single_product) {

            $total_qty_arr = $this->DistCurrentInventory->find('all', array(
                'conditions' => array('store_id' => $store_id, 'product_id' => $single_product['product_id']),
                'fields' => array('sum(qty) as total'),
                'recursive' => -1
            ));

            $total_qty = $total_qty_arr[0][0]['total'];

            $sales_total_qty = $this->unit_convertfrombase($single_product['product_id'], $single_product['measurement_unit_id'], $total_qty);

            $existing_record['DistMemoDetail'][$key]['stock_qty'] = $sales_total_qty;
        }


        $products_from_ci = $this->DistCurrentInventory->find('all', array(
            'fields' => array('DISTINCT DistCurrentInventory.product_id'),
            'conditions' => array('DistCurrentInventory.store_id' => $store_id, 'DistCurrentInventory.qty > ' => 0, 'inventory_status_id' => 1)
        ));

        $product_ci = array();
        foreach ($products_from_ci as $each_ci) {
            $product_ci[] = $each_ci['DistCurrentInventory']['product_id'];
        }
        foreach ($existing_record['DistMemoDetail'] as $value) {
            $product_ci[] = $value['product_id'];
        }

        $product_ci_in = implode(",", $product_ci);

        $product_list = $this->Product->find('list', array('conditions' => array('Product.id' => $product_ci), 'order' => array('order' => 'asc')));

        /* ------- end get edit data -------- */


        /* -----------My Work-------------- */
        $this->loadModel('DistSrProductPrice');
        $this->loadModel('DistSrProductCombination');


        foreach ($existing_record['DistMemoDetail'] as $key => $value) {
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
                //pr($value['product_id']);pr( $existing_product_category_id);die();
                $retrieve_price_DistSrCombination[$value['product_id']] = $this->DistSrProductPrice->find('all', array(
                    'conditions' => array('DistSrProductPrice.product_id' => $value['product_id'], 'DistSrProductPrice.has_combination' => 0)
                ));

                foreach ($retrieve_price_DistSrCombination[$value['product_id']][0]['DistSrProductCombination'] as $key => $value2) {
                    $individual_slab[$value2['min_qty']] = $value2['price'];
                }

                //pr($individual_slab);die();
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

        if (!empty($edited_cart_data)) {
            $this->Session->write('cart_session_data', $edited_cart_data);
        }
        if (!empty($edited_matched_data)) {
            $this->Session->write('matched_session_data', $edited_matched_data);
        }
        if (!empty($edited_current_qty_data)) {
            $this->Session->write('combintaion_qty_data', $edited_current_qty_data);
        }


        $this->set('page_title', 'Edit Distributor Memo');
        $this->DistMemo->id = $id;
        if (!$this->DistMemo->exists($id)) {
            throw new NotFoundException(__('Invalid Memo'));
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
        /* ------------ code for update memo ---------------- */
        //$this->Memo->id = $id;

        if ($this->request->is('post')) {

            $sale_type_id = $this->request->data['DistMemo']['sale_type_id'];


            /*########################### Start Order Details for Invoice #############################*/
            $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
            $product_list = Set::extract($products, '{n}.Product');

            $this->loadModel('DistOrderDetail');
            $order_detials = $this->DistOrderDetail->find('all', array(
                'conditions' => array(
                    'DistOrder.dist_order_no' => $dist_order_no,
                    'DistOrderDeliveryScheduleOrderDetail.dist_order_no' => $dist_order_no
                ),
                'joins' => array(
                    array(
                        'alias' => 'DistOrder',
                        'table' => 'dist_orders',
                        'type' => 'INNER',
                        'conditions' => 'DistOrderDetail.dist_order_id = DistOrder.id'
                    ),
                    array(
                        'alias' => 'DistOrderDeliveryScheduleOrderDetail',
                        'table' => 'dist_order_delivery_schedule_order_details',
                        'type' => 'INNER',
                        'conditions' => 'DistOrderDeliveryScheduleOrderDetail.product_id = DistOrderDetail.product_id'
                    )
                ),
                'fields' => array('DistOrderDetail.dist_order_id', 'DistOrderDetail.product_id', 'DistOrderDetail.measurement_unit_id', 'DistOrderDetail.sales_qty', 'DistOrderDetail.price', 'DistOrderDetail.is_bonus', 'DistOrderDetail.price', 'DistOrderDetail.product_price_id', 'DistOrderDeliveryScheduleOrderDetail.product_id', 'DistOrderDeliveryScheduleOrderDetail.invoice_qty'),
                'recursive' => -1
            ));

            $order_products = array();

            foreach ($order_detials as $order_detial) {
                $order_products[$order_detial['DistOrderDetail']['product_id']] = array(
                    'product_id'            =>  $order_detial['DistOrderDetail']['product_id'],
                    //'measurement_unit_id'     =>  $order_detial['DistOrderDetail']['measurement_unit_id'],
                    'sales_qty'             =>  $order_detial['DistOrderDetail']['sales_qty'],
                    'price'                 =>  $order_detial['DistOrderDetail']['price'],
                    'is_bonus'              =>  $order_detial['DistOrderDetail']['is_bonus'],
                    //'product_price_id'        =>  $order_detial['DistOrderDetail']['product_price_id'],
                    'invoice_qty'           =>  $order_detial['DistOrderDeliveryScheduleOrderDetail']['invoice_qty'],
                );
            }

            $dist_draft_memo_details['DistMemoDetail'] = $existing_record['DistMemoDetail'];

            foreach ($dist_draft_memo_details['DistMemoDetail'] as $val) {
                $memo_products[$val['product_id']] = array(
                    'product_id'            =>  $val['product_id'],
                    //'measurement_unit_id'     =>  $val['measurement_unit_id'],
                    'sales_qty'             =>  $val['sales_qty'],
                    'price'                 =>  $val['price'] ? $val['price'] : 0,
                    'is_bonus'              =>  $val['is_bonus'],
                    //'product_price_id'        =>  0,
                    'order_sales_qty'       =>  isset($order_products[$val['product_id']]['sales_qty']) ? $order_products[$val['product_id']]['sales_qty'] : 0,
                    'invoice_qty'           =>  isset($order_products[$val['product_id']]['invoice_qty']) ? $order_products[$val['product_id']]['invoice_qty'] : 0,
                );
            }
            $inventory_check = 1;
            foreach ($memo_products as $memo_product) {
                $product_id = $memo_product['product_id'];
                $is_bonus = $memo_product['is_bonus'];
                $punits_pre = $this->search_array($product_id, 'id', $product_list);
                if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                    $base_qty = $memo_product['sales_qty'];
                } else {
                    $base_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $memo_product['sales_qty']);
                }

                if ($memo_product['invoice_qty'] < $memo_product['sales_qty']) {
                    $memo_sales_qty = $memo_product['sales_qty'] - $memo_product['invoice_qty'];
                    $memo_base_qty = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $memo_sales_qty);
                    if ($is_bonus) {
                        $product_stock_qty = @$inventory_bonus_qty_info[$product_id];
                    } else {
                        $product_stock_qty = @$inventory_qty_info[$product_id];
                    }

                    /*echo 'P-'.$product_id.'<br>';
                        echo $product_stock_qty;
                        echo '<br>';
                        echo $memo_base_qty;
                        echo '<br>';*/

                    if ($product_stock_qty < $memo_base_qty) {
                        $inventory_check = 0;
                    }
                }

                /*########################### End Order Details for Invoice #############################*/

                //pr($this->request->data);die();
                //exit;

                $outlet_is_within_group = $this->outletGroupCheck($this->request->data['DistMemo']['outlet_id']);
                $product_is_injectable = $this->productInjectableCheck($this->request->data['MemoDetail']['product_id']);

                if ($product_is_injectable && !$outlet_is_within_group && $sale_type_id != 2) {
                    $this->Session->setFlash(__("Special product can't sell to ordinary outlet!"), 'flash/error');
                    $this->redirect(array('action' => 'create_memo'));
                    exit;
                }

                $memo_id = $id;

                $this->admin_delete($memo_id, 0);

                /* START ADD NEW */
                //get office id 
                $office_id = $this->request->data['DistMemo']['office_id'];

                //get thana id 
                $this->loadModel('DistMarket');
                $market_info = $this->DistMarket->find(
                    'first',
                    array(
                        'conditions' => array('DistMarket.id' => $this->request->data['DistMemo']['market_id']),
                        'fields' => 'DistMarket.thana_id',
                        'order' => array('DistMarket.id' => 'asc'),
                        'recursive' => -1,
                        //'limit' => 100
                    )
                );
                $thana_id = $market_info['DistMarket']['thana_id'];
                /* END ADD NEW */

                $sale_type_id = $this->request->data['DistMemo']['sale_type_id'];
                $sale_type_id = 10;
                if ($sale_type_id == 10) {
                    $this->request->data['DistMemo']['is_active'] = 1;

                    if (array_key_exists('draft', $this->request->data)) {
                        $this->request->data['DistMemo']['status'] = 0;
                        $message = "Memo Has Been Saved";
                    } else {
                        $message = "Memo Has Been Saved";
                        $this->request->data['DistMemo']['status'] = 1;
                    }

                    $sales_person = $this->SalesPerson->find('list', array(
                        'conditions' => array('territory_id' => $this->request->data['DistMemo']['territory_id']),
                        'order' => array('name' => 'asc')
                    ));

                    $this->request->data['DistMemo']['sales_person_id'] = key($sales_person);

                    $this->request->data['DistMemo']['entry_date'] = date('Y-m-d', strtotime($this->request->data['DistMemo']['entry_date']));
                    $this->request->data['DistMemo']['memo_date'] = date('Y-m-d', strtotime($this->request->data['DistMemo']['memo_date']));

                    if ($inventory_check == 1) {

                        $memoData['id'] = $memo_id;

                        $memoData['office_id'] = $this->request->data['DistMemo']['office_id'];
                        $memoData['distributor_id'] = $this->request->data['DistMemo']['distributor_id'];
                        $memoData['sr_id'] = $this->request->data['DistMemo']['sr_id'];
                        $memoData['dist_route_id'] = $this->request->data['DistMemo']['dist_route_id'];
                        $memoData['sale_type_id'] = $this->request->data['DistMemo']['sale_type_id'];
                        $memoData['territory_id'] = $this->request->data['DistMemo']['territory_id'];
                        $memoData['market_id'] = $this->request->data['DistMemo']['market_id'];
                        $memoData['outlet_id'] = $this->request->data['DistMemo']['outlet_id'];
                        $memoData['entry_date'] = $this->request->data['DistMemo']['entry_date'];
                        $memoData['memo_date'] = $this->request->data['DistMemo']['memo_date'];
                        $memoData['dist_memo_no'] = $this->request->data['DistMemo']['dist_memo_no'];

                        $memoData['gross_value'] = $this->request->data['DistMemo']['gross_value'];
                        $memoData['cash_recieved'] = $this->request->data['DistMemo']['cash_recieved'];
                        $memoData['credit_amount'] = $this->request->data['DistMemo']['credit_amount'];
                        $memoData['is_active'] = $this->request->data['DistMemo']['is_active'];
                        $memoData['status'] = $this->request->data['DistMemo']['status'];
                        //$memoData['memo_time'] = $this->current_datetime();   
                        $memoData['memo_time'] = $this->request->data['DistMemo']['entry_date'];
                        $memoData['sales_person_id'] = $this->request->data['DistMemo']['sales_person_id'];
                        $memoData['ae_id'] = $this->request->data['DistMemo']['ae_id'];
                        $memoData['tso_id'] = $this->request->data['DistMemo']['tso_id'];
                        $memoData['from_app'] = 0;
                        $memoData['action'] = 1;

                        $memoData['is_program'] = ($sale_type_id == 4) ? 1 : 0;


                        $memoData['memo_reference_no'] = $this->request->data['DistMemo']['memo_reference_no'];


                        $memoData['created_at'] = $this->current_datetime();
                        $memoData['created_by'] = $this->UserAuth->getUserId();
                        $memoData['updated_at'] = $this->current_datetime();
                        $memoData['updated_by'] = $this->UserAuth->getUserId();


                        $memoData['office_id'] = $office_id ? $office_id : 0;
                        $memoData['thana_id'] = $thana_id ? $thana_id : 0;


                        $this->DistMemo->create();

                        if ($this->DistMemo->save($memoData)) {

                            $memo_id = $this->DistMemo->getLastInsertId();


                            $memo_info_arr = $this->DistMemo->find('first', array(
                                'conditions' => array(
                                    'DistMemo.id' => $memo_id
                                )
                            ));

                            $this->loadModel('DistStore');
                            $dist_id = $this->request->data['DistMemo']['distributor_id'];
                            $store_id_arr = $this->DistStore->find('first', array(
                                'conditions' => array(
                                    'DistStore.dist_distributor_id' => $dist_id
                                )
                            ));

                            $store_id = $store_id_arr['DistStore']['id'];


                            if ($memo_id) {
                                $all_product_id = $this->request->data['MemoDetail']['product_id'];

                                if (!empty($this->request->data['MemoDetail'])) {
                                    $total_product_data = array();
                                    $memo_details = array();
                                    $memo_details['DistMemoDetail']['dist_memo_id'] = $memo_id;

                                    foreach ($this->request->data['MemoDetail']['product_id'] as $key => $val) {
                                        if ($val == NULL) {
                                            continue;
                                        }
                                        $sales_price = $this->request->data['MemoDetail']['Price'][$key];

                                        $product_id = $memo_details['DistMemoDetail']['product_id'] = $val;
                                        $memo_details['DistMemoDetail']['measurement_unit_id'] = $this->request->data['MemoDetail']['measurement_unit_id'][$key];
                                        $memo_details['DistMemoDetail']['price'] = $sales_price;
                                        $sales_qty =  $this->request->data['MemoDetail']['sales_qty'][$key];
                                        $memo_details['DistMemoDetail']['product_price_id'] = $this->request->data['MemoDetail']['product_price_id'][$key];
                                        $memo_details['DistMemoDetail']['sales_qty'] = $sales_qty;
                                        $product_price_slab_id = 0;
                                        if ($sales_price > 0) {
                                            $product_price_slab_id = $this->get_product_price_id($val, $sales_price, $all_product_id);
                                        }
                                        $memo_details['DistMemoDetail']['product_price_id'] = $product_price_slab_id['product_price_id'];
                                        $memo_details['DistMemoDetail']['product_combination_id'] = $product_price_slab_id['combination_id'];

                                        if ($sales_price == 0) {
                                            $is_bonus = $memo_bonus_details['DistMemoDetail']['is_bonus'] = 1;
                                            $memo_details['DistMemoDetail']['bonus_qty'] = $sales_qty;
                                            $memo_details['DistMemoDetail']['bonus_product_id'] = NULL;
                                        } else {
                                            $is_bonus = $memo_bonus_details['DistMemoDetail']['is_bonus'] = 0;
                                            $memo_details['DistMemoDetail']['bonus_qty'] = NULL;
                                            $memo_details['DistMemoDetail']['bonus_product_id'] = NULL;
                                        }



                                        $memo_date = date('Y-m-d', strtotime($this->request->data['DistMemo']['memo_date']));

                                        $bonus_product_id = NULL;
                                        $bonus_product_qty = NULL;
                                        $memo_details['DistMemoDetail']['bonus_id'] = 0;
                                        $memo_details['DistMemoDetail']['bonus_scheme_id'] = 0;

                                        $total_product_data[] = $memo_details;


                                        //update inventory
                                        if ($stock_hit) {
                                            $products = $this->Product->find('all', array('fields' => array('id', 'base_measurement_unit_id', 'sales_measurement_unit_id', 'challan_measurement_unit_id'), 'recursive' => -1));
                                            $product_list = Set::extract($products, '{n}.Product');

                                            $punits_pre = $this->search_array($product_id, 'id', $product_list);
                                            if ($punits_pre['sales_measurement_unit_id'] == $punits_pre['base_measurement_unit_id']) {
                                                $base_quantity = $sales_qty;
                                            } else {
                                                $base_quantity = $this->unit_convert($product_id, $punits_pre['sales_measurement_unit_id'], $sales_qty);
                                            }

                                            $update_type = 'deduct';
                                            //$this->update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3, $this->request->data['DistMemo']['memo_date']);
                                            $this->dist_memo_update_current_inventory($base_quantity, $product_id, $store_id, $update_type, 3,  $this->request->data['DistMemo']['memo_date'], $is_bonus, 0);
                                        }
                                        // sales calculation
                                        $tt_price = $sales_qty * $sales_price;
                                    }

                                    $this->DistMemoDetail->saveAll($total_product_data);
                                }
                            }
                        }
                        $this->loadModel('DistOrder');
                        $order_info = $this->DistOrder->find('first', array('conditions' => array('DistOrder.dist_order_no' => $dist_order_no)));
                        $order_id = $order_info['DistOrder']['id'];
                        $order_data['DistOrder']['id'] = $order_id;
                        $order_data['DistOrder']['status'] = 2;
                        $order_data['DistOrder']['processing_status'] = 2;
                        $this->DistOrder->save($order_data);
                        $this->Session->setFlash(__('The Memo has been Updated'), 'flash/success');
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(__('The Memo has not been Updated'), 'flash/error');
                        $this->redirect(array('action' => 'index'));
                    }
                }
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
    public function bouns_and_scheme_id_set($b_product_id = 0, $memo_date = '')
    {
        $this->loadModel('Bonus');
        //$this->loadModel('OpenDistSrCombination');
        //$this->loadModel('OpenDistSrCombinationProduct');

        $bonus_result = array();

        $b_product_qty = 0;
        $bonus_id = 0;
        $bonus_scheme_id = 0;

        $bonus_info = $this->Bonus->find(
            'first',
            array(
                'conditions' => array(
                    'Bonus.effective_date <= ' => $memo_date,
                    'Bonus.end_date >= ' => $memo_date,
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
        $current_date = $this->request->data['memo_date'] ? date('Y-m-d', strtotime($this->request->data['memo_date'])) : date('Y-m-d');
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
            'fields' => array('sum(qty) as total')
        ));
        $total_qty = $total_qty_arr[0][0]['total'];

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
                    'table' => 'product_prices',
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
        $effective_prev_date_for_combinations = $this->DistSrProductPrice->find(
            'all',
            array(
                'conditions' => array('DistSrProductPrice.product_id' => $product_id, 'DistSrProductPrice.effective_date <=' => $current_date, 'DistSrProductPrice.institute_id' => 0, 'DistSrProductPrice.has_combination !=' => 0),
                'fields' => array('DistSrProductPrice.effective_date')
            )
        );
        if (!empty($effective_prev_date_for_combinations)) {
            $prev_short_list_for_combinations = array();
            foreach ($effective_prev_date_for_combinations as $prev_date_key_com => $prev_date_val_com) {
                array_push($prev_short_list_for_combinations, $prev_date_val_com['DistSrProductPrice']['effective_date']);
            }
            asort($prev_short_list_for_combinations);
            $prev_date_for_combinations = array_pop($prev_short_list_for_combinations);
        }
        $effective_next_date_for_combinations = $this->DistSrProductPrice->find(
            'all',
            array(
                'conditions' => array('DistSrProductPrice.product_id' => $product_id, 'DistSrProductPrice.effective_date >' => $current_date, 'DistSrProductPrice.institute_id' => 0, 'DistSrProductPrice.has_combination !=' => 0),
                'fields' => array('DistSrProductPrice.effective_date')
            )
        );
        if (!empty($effective_next_date_for_combinations)) {
            $next_short_list_for_combinations = array();
            foreach ($effective_next_date_for_combinations as $next_date_key_com => $next_date_val_com) {
                array_push($next_short_list_for_combinations, $next_date_val_com['DistSrProductPrice']['effective_date']);
            }
            rsort($next_short_list_for_combinations);
            $next_date_for_combinations = array_pop($next_short_list_for_combinations);
        }
        /* ------- end get prev effective date and next effective date --------- */
        if (!empty($next_date_for_combinations)) {
            $condition_value_for_combinations['DistSrProductPrice.effective_date <'] = $next_date_for_combinations;
        }
        if (!empty($prev_date_for_combinations)) {
            $condition_value_for_combinations['DistSrProductPrice.effective_date >='] = $prev_date_for_combinations;
        }
        $condition_value_for_combinations['Product.id'] = $product_id;
        $condition_value_for_combinations['DistSrProductPrice.institute_id'] = 0;
        $condition_value_for_combinations['DistSrProductPrice.has_combination !='] = 0;

        $product_option_for_combinations = array(
            'conditions' => array($condition_value_for_combinations),
            'joins' => array(
                array(
                    'alias' => 'DistSrProductPrice',
                    'table' => 'product_prices',
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
        $product_list_for_combinations = $this->Product->find('all', $product_option_for_combinations);
        /* echo $this->Product->getLastquery();
          pr($product_list_for_combinations);exit; */
        if (!empty($product_list_for_combinations)) {
            foreach ($product_list_for_combinations as $data) {
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
        /* pr($filter_product);exit; */
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
            // if ($product_type_id != 3) {

            echo $sales_total_qty = $this->unit_convertfrombase($this->request->data['product_id'], $filter_product['MeasurementUnit']['id'], $total_qty);

            /* -----------------Bonus---------------- */
            $this->loadModel('Bonus');
            $this->Bonus->recursive = -1;
            $bonus_info = $this->Bonus->find('first', array(
                'conditions' => array('mother_product_id' => $product_id, 'mother_product_quantity' => $filter_product['DistSrProductCombination']['min_qty']),
                'fields' => array('bonus_product_id', 'bonus_product_quantity')
            ));

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

    public function get_combine_or_individual_price()
    {

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
        $memo_date = $this->request->data['memo_date'] ? date('Y-m-d', strtotime($this->request->data['memo_date'])) : date('Y-m-d');
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
                'effective_date <=' => $memo_date,
                'end_date >=' => $memo_date,
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

    public function delete_memo()
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

    public function update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '')
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
                    array('DistCurrentInventory.qty' => 'DistCurrentInventory.qty + ' . $quantity, 'DistCurrentInventory.transaction_type_id' => $transaction_type_id, 'DistCurrentInventory.store_id' => $store_id, 'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"),
                    array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                );
            }
        }

        return true;
    }

    // it will be called from memo not from memo_details 
    // cal_type=1 means increment and 2 means deduction 

    public function ec_calculation($gross_value, $outlet_id, $terrority_id, $memo_date, $cal_type)
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
                // from memo_date , split month and get month name and compare month table with memo year
                $memoDate = strtotime($memo_date);
                $month = date("n", $memoDate);
                $year = date("Y", $memoDate);
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
    // it will be called from  memo_details 
    public function sales_calculation($product_id, $terrority_id, $quantity, $gross_value, $memo_date, $cal_type)
    {
        // from so_id , retrieve aso_id,terrority_id : no need as we have territory_id
        // from memo_date , split month and get month name and compare month table with memo year
        $memoDate = strtotime($memo_date);
        $month = date("n", $memoDate);
        $year = date("Y", $memoDate);
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
    // it will be called from memo not from memo_details 
    public function oc_calculation($terrority_id, $gross_value, $outlet_id, $memo_date, $memo_time, $cal_type)
    {

        // from so_id , retrieve aso_id,terrority_id :  no need as we have territory_id
        // check gross_value >0
        if ($gross_value > 0) {
            $this->loadModel('DistMemo');
            // this will be updated monthly , if done then increment else no action
            $month_first_date = date('Y-m-01', strtotime($memo_date));
            $count = $this->DistMemo->find('count', array(
                'conditions' => array(
                    'DistMemo.outlet_id' => $outlet_id,
                    'DistMemo.memo_date >= ' => $month_first_date,
                    'DistMemo.memo_time < ' => $memo_time
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
                    // from memo_date , split month and get month name and compare month table with memo year
                    $memoDate = strtotime($memo_date);
                    $month = date("n", $memoDate);
                    $year = date("Y", $memoDate);
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

    // it will be called from memo_details 
    public function stamp_calculation($memo_no, $terrority_id, $product_id, $outlet_id, $quantity, $memo_date, $cal_type, $gross_amount, $market_id)
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
                // from memo_date , split month and get month name and compare month table with memo year (get fascal year id)
                $memoDate = strtotime($memo_date);
                $month = date("n", $memoDate);
                $year = date("Y", $memoDate);
                $this->loadModel('Month');
                $fasical_info = $this->Month->find('first', array(
                    'conditions' => array(
                        'Month.month' => $month,
                        'Month.year' => $year
                    ),
                    'recursive' => -1
                ));

                if (!empty($fasical_info)) {
                    // check bonus card table , where is_active,and others  and get min qty per memo
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

                    // if exist min qty per memo , then stamp_no=mod(quantity/min qty per memo)
                    if (!empty($bonus_card_info)) {
                        $min_qty_per_memo = $bonus_card_info['BonusCard']['min_qty_per_memo'];
                        if ($min_qty_per_memo && $min_qty_per_memo <= $quantity) {
                            $stamp_no = floor($quantity / $min_qty_per_memo);
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
                            $log_data['StoreBonusCard']['memo_no'] = $memo_no;

                            $this->StoreBonusCard->create();
                            $this->StoreBonusCard->save($log_data);
                        }
                    }
                }
            }
        }
    }

    public function admin_memo_no_validation()
    {
        $this->loadModel('DistMemo');
        if ($this->request->is('post')) {
            $memo_no = $this->request->data['memo_no'];
            $sale_type_id = $this->request->data['sale_type'];

            if ($sale_type_id == 1 || $sale_type_id == 3 || $sale_type_id == 4) {
                $memo_list = $this->DistMemo->find('list', array(
                    'conditions' => array('DistMemo.dist_memo_no' => $memo_no),
                    'fields' => array('dist_memo_no'),
                    'recursive' => -1
                ));
            } else {
                $memo_list = $this->DistMemo->find('list', array(
                    'conditions' => array('DistMemo.dist_memo_no' => $memo_no),
                    'fields' => array('dist_memo_no'),
                    'recursive' => -1
                ));
            }
            $memo_exist = count($memo_list);

            echo json_encode($memo_exist);
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

    public function admin_memo_editable($id = null)
    {
        if ($id) {
            $this->DistMemo->id = $id;
            if ($this->DistMemo->id) {
                if ($this->DistMemo->saveField('memo_editable', 1)) {
                    $this->Session->setFlash(__('The setting has been saved!'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('Memo editable failed!'), 'flash/error');
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
        $memo_date = $this->request->data['memo_date'] ? date('Y-m-d', strtotime($this->request->data['memo_date'])) : date('Y-m-d');
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
                    'alias' => 'OpenDistSrCombinationProduct',
                    'type' => 'Inner',
                    'conditions' => 'OpenDistSrCombinationProduct.product_id=Product.id'
                ),
                array(
                    'table' => 'dist_open_combinations',
                    'alias' => 'OpenDistSrCombination',
                    'type' => 'Inner',
                    'conditions' => 'OpenDistSrCombinationProduct.combination_id=OpenDistSrCombination.id'
                ),
            ),
            'conditions' => array(
                'DistCurrentInventory.store_id' => $store_id['DistStore']['id'],
                'OpenDistSrCombination.start_date <=' => $memo_date,
                'OpenDistSrCombination.end_date >=' => $memo_date,
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
        $this->LoadModel('DistSrProductCombination');
        $this->LoadModel('DistSrCombination');
        $data = array();
        $product_price = $this->DistSrProductCombination->find('first', array(
            'conditions' => array(
                'DistSrProductCombination.product_id' => $product_id,
                'DistSrProductCombination.price' => $product_prices,
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
                        'DistSrProductCombination.price' => $product_prices,
                        'DistSrProductCombination.effective_date <=' => $this->current_date(),
                        'DistSrProductCombination.parent_slab_id' => 0
                    ),
                    'order' => array('DistSrProductCombination.id DESC'),
                    'recursive' => -1
                ));
                $data['combination_id'] = '';
                $data['product_price_id'] = $product_price['DistSrProductCombination']['id'];
            }
            return $data;
        } else {
            $data['combination_id'] = '';
            $data['product_price_id'] = '';
            return $data;
        }
    }

    function get_dist_list_by_office_id()
    {

        $office_id = $this->request->data['office_id'];
        $memo_date = $this->request->data['memo_date'];
        $output = "<option value=''>--- Select Distributor ---</option>";

        /*
        $conditions=array(
                'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.is_active' => 1),'order' => array('DistDistributor.name' => 'asc'),
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
        */

        $this->loadModel('DistTsoMappingHistory');
        $this->loadModel('DistDistributor');

        if ($memo_date && $office_id) {
            $memo_date = date("Y-m-d H:i:s", strtotime($this->request->data['memo_date']));
            $qry = "select distinct dist_distributor_id from dist_tso_mapping_histories
                  where office_id=$office_id and is_change=1 and 
                    '" . $memo_date . "' between effective_date and 
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

            $conditions = array(
                'conditions' => array('DistDistributor.office_id' => $office_id, 'DistDistributor.id' => $dist_ids), 'order' => array('DistDistributor.name' => 'asc'), 'recursive' => 0
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
        echo $output;
        $this->autoRender = false;
    }


    function get_dist_list_by_office_id_and_date_range()
    {
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');

        $office_id = $this->request->data['office_id'];
        $memo_date_from = $this->request->data['memo_date_from'];
        $memo_date_to = $this->request->data['memo_date_to'];
        $dist_id = $this->request->data['distributor_id'];
        $output = "<option value=''>--- Select Distributor ---</option>";

        if ($memo_date_from && $office_id && $memo_date_to) {
            $memo_date_from = date("Y-m-d", strtotime($this->request->data['memo_date_from']));
            $memo_date_to = date("Y-m-d", strtotime($this->request->data['memo_date_to']));
        }
        $this->loadModel('DistTsoMappingHistory');
        $this->loadModel('DistDistributor');
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
            $dist_conditions = array('DistMemo.distributor_id' => array_keys($tso_dist_list), 'DistMemo.memo_date >=' => $memo_date_from, 'DistMemo.memo_date <=' => $memo_date_to);
        } elseif ($user_group_id == 1034) {
            $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
            $this->loadModel('DistUserMapping');
            $distributor = $this->DistUserMapping->find('first', array(
                'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
            ));
            $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];

            $dist_conditions = array('DistMemo.distributor_id' => $distributor_id, 'DistMemo.memo_date >=' => $memo_date_from, 'DistMemo.memo_date <=' => $memo_date_to);
        } else {
            $dist_conditions = array('DistMemo.office_id' => $office_id, 'DistMemo.memo_date >=' => $memo_date_from, 'DistMemo.memo_date <=' => $memo_date_to);
        }

        if ($memo_date_from && $office_id && $memo_date_to) {

            $distDistributors = $this->DistDistributor->find('list', array(
                'conditions' => $dist_conditions,
                'joins' => array(array('table' => 'dist_memos', 'alias' => 'DistMemo', 'conditions' => 'DistMemo.distributor_id=DistDistributor.id')),
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
        $memo_date = $this->request->data['memo_date'];
        $output = "<option value=''>--- Select SR ---</option>";
        if ($distributor_id) {
            $memo_date = date("Y-m-d H:i:s", strtotime($this->request->data['memo_date']));

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

    function get_sr_list_by_distributot_id_date_range()
    {
        $this->LoadModel('Territory');
        $distributor_id = $this->request->data['distributor_id'];
        //$memo_date = $this->request->data['memo_date'];
        $output = "<option value=''>--- Select SR ---</option>";
        if ($distributor_id) {
            //$memo_date = date("Y-m-d H:i:s", strtotime($this->request->data['memo_date']));
            $memo_date_from = date("Y-m-d H:i:s", strtotime($this->request->data['memo_date_from']));
            $memo_date_to = date("Y-m-d H:i:s", strtotime($this->request->data['memo_date_to']));
            $sr = $this->DistSalesRepresentative->find('list', array(
                'conditions' => array(
                    'DistMemo.distributor_id' => $distributor_id,
                    'DistMemo.memo_date >=' => $memo_date_from,
                    'DistMemo.memo_date <=' => $memo_date_to,
                ),
                'joins' => array(array('table' => 'dist_memos', 'alias' => 'DistMemo', 'conditions' => 'DistMemo.sr_id=DistSalesRepresentative.id')),
                'order' => array('DistSalesRepresentative.name' => 'asc')
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
        $rs = array(array('id' => '', 'name' => '---- Select Outlet -----'));
        $market_id = $this->request->data['market_id'];
        $outlet_list = $this->DistOutlet->find('all', array(
            'fields' => array('DistOutlet.id as id', 'DistOutlet.name as name'),
            'conditions' => array(
                'DistOutlet.dist_market_id' => $market_id
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
        $memo_date = $this->request->data['memo_date'];

        $output = "";
        if ($market_id) {
            $info = $this->DistMarket->find('first', array(
                'conditions' => array('DistMarket.id' => $market_id),
                'recursive' => -1
            ));

            $territory_id = $info['DistMarket']['territory_id'];
            $thana_id = $info['DistMarket']['thana_id'];

            $this->loadModel('DistTsoMappingHistory');


            if ($memo_date && $distributor_id) {

                $memo_date = date("Y-m-d H:i:s", strtotime($this->request->data['memo_date']));
                $qry = "select distinct dist_tso_id from dist_tso_mapping_histories
                      where office_id=$office_id and is_change=1 and dist_distributor_id=$distributor_id and 
                        '" . $memo_date . "' between effective_date and 
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
                    '" . $memo_date . "' between effective_date and 
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

                echo $territory_id . "||" . $thana_id . "||" . $ae_id . "||" . $tso_id;
            } else {
                echo $territory_id . "||" . $thana_id;
            }
        } else {
            echo "";
        }

        $this->autoRender = false;
    }

    public function memo_reference_no_validation()
    {
        $this->loadModel('DistMemo');
        if ($this->request->is('post')) {
            $memo_reference_no = $this->request->data['memo_reference_no'];
            $office_id = $this->request->data['office_id'];
            $memo_list = array();

            if ($office_id && $memo_reference_no) {
                $memo_list = $this->DistMemo->find('list', array(
                    'conditions' => array('DistMemo.memo_reference_no' => $memo_reference_no, 'DistMemo.office_id' => $office_id),
                    'fields' => array('memo_reference_no'),
                    'recursive' => -1
                ));
            }
            $memo_exist = count($memo_list);
            echo json_encode($memo_exist);
        }

        $this->autoRender = false;
    }

    public function get_route_list()
    {
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
        $this->autoRender = false;
    }

    public function get_route_list_from_memo_date()
    {
        $this->loadModel('DistRouteMapping');
        $distributor_id = $this->request->data['distributor_id'];
        $sr_id = $this->request->data['sr_id'];
        $office_id = $this->request->data['office_id'];
        //$memo_date = $this->request->data['memo_date'];
        $memo_date = date("Y-m-d H:i:s", strtotime($this->request->data['memo_date']));

        $output = "<option value=''>--- Select ---</option>";
        if ($memo_date && $sr_id) {

            $qry = "select distinct dist_route_id from dist_sr_route_mapping_histories
                    where dist_sr_id=$sr_id and dist_distributor_id=$distributor_id and is_change=1 and 
                    '" . $memo_date . "' between effective_date and 
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

        echo json_encode($territory_thana_id['Market']);
        $this->autoRender = false;
    }
    /*------------------ Last Memo Info :Start ---------------------------*/
    function get_last_memo_info()
    {
        $office_id = $this->request->data['office_id'];
        $last_memo = $this->DistMemo->find('first', array(
            'conditions' => array('DistMemo.office_id' => $office_id),
            /*'recursive'=>-1,*/
            'order' => array('DistMemo.id' => 'DESC')
        ));
        $output = '';
        $output .= '<p>Distributor : ' . $last_memo['Distributor']['name'] . '</p>';
        $output .= '<p>Memo no : ' . $last_memo['DistMemo']['memo_reference_no'] . '</p>';
        $output .= '<p>Memo value : ' . $last_memo['DistMemo']['gross_value'] . '</p>';
        echo $output;
        $this->autoRender = false;
    }
    /*------------------ Last Memo Info :END -----------------------------*/

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
            'conditions'    => array('DistDistributor.id' => $dist_id),
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

    public function get_order_info()
    {

        $order_id = $this->request->data['order_id'];

        $this->loadModel('DistStore');
        $this->loadModel('DistOrder');
        $this->loadModel('Product');
        $this->loadModel('DistCurrentInventory');
        $this->loadModel('MeasurementUnit');

        $orders = $this->DistOrder->find('first', array('conditions' => array('DistOrder.id' => $order_id)));
        $this->set(compact('orders'));

        //pr($orders);die();

        $office_id = $orders['DistOrder']['office_id'];
        $distributor_id = $orders['DistOrder']['distributor_id'];

        $store_info = $this->DistStore->find('first', array('conditions' => array(
            'DistStore.office_id' => $office_id,
            'DistStore.dist_distributor_id' => $distributor_id,
        )));

        $store_id = $store_info['DistStore']['id'];

        $product_list = $this->Product->find('list', array('conditions' => array('is_distributor_product' => 1), 'order' => array('Product.name DESC')));

        $inventory_info = array();
        $product_category_id_list = $this->Product->find('list', array('fields' => array('id', 'product_category_id')));

        $measurement_units = $this->MeasurementUnit->find('list');

        $this->set(compact('product_list', 'product_category_id_list', 'measurement_units'));
    }


    public function dist_memo_update_current_inventory($quantity, $product_id, $store_id, $update_type = 'deduct', $transaction_type_id = 0, $transaction_date = '', $is_bonus = 0, $invoice_qty = 0)
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


        if ($is_bonus) {
            if ($update_type == 'deduct') {
                /*echo 'Deduct-'.$product_id;
                echo '<br>';
                echo 'Qty-'. $quantity;
                echo '<br>';
                echo 'In-'. $invoice_qty;
                echo '<br>';*/

                foreach ($inventory_info as $val) {
                    if ($quantity <= 0) {
                        break;
                    }
                    if ($quantity <= $val['DistCurrentInventory']['bonus_qty']) {
                        $this->DistCurrentInventory->id = $val['DistCurrentInventory']['id'];
                        $this->DistCurrentInventory->updateAll(
                            array(
                                'DistCurrentInventory.bonus_qty' => 'DistCurrentInventory.bonus_qty - ' . $quantity,
                                'DistCurrentInventory.bonus_invoice_qty' => 'DistCurrentInventory.bonus_invoice_qty - ' . $invoice_qty,
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
                            if ($val['DistCurrentInventory']['bonus_qty'] <= 0) {
                                $this->DistCurrentInventory->updateAll(
                                    array(
                                        'DistCurrentInventory.bonus_qty' => 'DistCurrentInventory.bonus_qty - ' . $quantity,
                                        'DistCurrentInventory.bonus_invoice_qty' => 'DistCurrentInventory.bonus_invoice_qty - ' . $invoice_qty,
                                        'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                                        'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'",
                                        'DistCurrentInventory.updated_at' => "'" . $this->current_datetime() . "'"
                                    ),
                                    array('DistCurrentInventory.id' => $val['DistCurrentInventory']['id'])
                                );
                                $quantity = 0;
                                break;
                            } else {
                                $quantity = $quantity - $val['DistCurrentInventory']['bonus_qty'];
                                $this->DistCurrentInventory->updateAll(
                                    array(
                                        'DistCurrentInventory.bonus_qty' => 'DistCurrentInventory.bonus_qty - ' . $val['DistCurrentInventory']['bonus_qty'],
                                        'DistCurrentInventory.bonus_invoice_qty' => 'DistCurrentInventory.bonus_invoice_qty - ' . $invoice_qty,
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

                /*echo 'Add-'.$product_id;
                echo '<br>';
                echo 'Qty-'. $quantity;
                echo '<br>';
                echo 'In-'. $invoice_qty;
                echo '<br>';
                exit;*/

                if (!empty($inventory_info)) {

                    $this->DistCurrentInventory->updateAll(
                        array(
                            'DistCurrentInventory.bonus_qty' => 'DistCurrentInventory.bonus_qty + ' . $quantity,
                            'DistCurrentInventory.bonus_invoice_qty' => 'DistCurrentInventory.bonus_invoice_qty - ' . $invoice_qty,
                            'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                            'DistCurrentInventory.store_id' => $store_id,
                            'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                        ),
                        array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                    );


                    //for invoice qty
                    if ($invoice_qty <= $inventory_info['DistCurrentInventory']['bonus_invoice_qty']) {
                        $this->DistCurrentInventory->updateAll(
                            array(
                                //'DistCurrentInventory.qty' => 'DistCurrentInventory.bonus_qty + ' . $quantity, 
                                'DistCurrentInventory.bonus_invoice_qty' => 'DistCurrentInventory.bonus_invoice_qty - ' . $invoice_qty,
                                'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                                'DistCurrentInventory.store_id' => $store_id,
                                'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                            ),
                            array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                        );
                    } else {
                        $this->DistCurrentInventory->updateAll(
                            array(
                                //'DistCurrentInventory.qty' => 'DistCurrentInventory.bonus_qty + ' . $quantity, 
                                'DistCurrentInventory.bonus_invoice_qty' => 0,
                                'DistCurrentInventory.transaction_type_id' => $transaction_type_id,
                                'DistCurrentInventory.store_id' => $store_id,
                                'DistCurrentInventory.transaction_date' => "'" . $transaction_date . "'"
                            ),
                            array('DistCurrentInventory.id' => $inventory_info['DistCurrentInventory']['id'])
                        );
                    }
                }
            }
        } else {

            if ($update_type == 'deduct') {
                /*echo 'Deduct-'.$product_id;
                echo '<br>';
                echo 'Qty-'. $quantity;
                echo '<br>';
                echo 'In-'. $invoice_qty;
                echo '<br>';*/

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

                    /*echo 'Add-'.$product_id;
                    echo '<br>';
                    echo 'Qty-'. $quantity;
                    echo '<br>';
                    echo 'In-'. $invoice_qty;
                    echo '<br>';*/
                    //exit;

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
        }
        //exit;
        return true;
    }

    public function check_discount_amount()
    {
        // pr($this->request->data);die();
        $this->loadmodel('DistDiscountDetail');

        $gross_value = $this->request->data['memo_date'];
        $order_date = $this->request->data['DistOrder']['gross_value'];

        $discount_details = $this->DistDiscountDetail->find('first', array(
            'conditions' => array(
                'DistDiscountDetail.date_from <=' => $order_date,
                'DistDiscountDetail.date_to  >=' => $order_date,
                'DistDiscountDetail.memo_value  <=' => $gross_value,
                'DistDiscount.is_active ' => 1,
            ),
            'order' => array('DistDiscountDetail.id DESC'),
        ));
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

    public function download_xl()
    {

        //$this->Session->delete('from_outlet');
        //$this->Session->delete('from_market');
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 99999); //300 seconds = 5 minutes
        $product_wise_sales_bonus_report = array();
        $product_list = array();
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');
        $this->loadModel('DistUserMapping');
        $params = $this->request->query['data'];
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');


        $office_parent_id = $this->UserAuth->getOfficeParentId();





        $conditions = array();

        if (!empty($params['DistMemo']['office_id'])) {
            $conditions[] = array('DistMemo.office_id' => $params['DistMemo']['office_id']);
        } else {

            if ($office_parent_id == 0) {
            } else {

                $conditions[] = array('DistMemo.office_id' => $office_parent_id);
            }
        }
        if (!empty($params['DistMemo']['memo_reference_no'])) {
            $conditions[] = array('DistMemo.dist_memo_no Like' => "%" . ['DistMemo']['memo_reference_no'] . "%");
        }
        if (!empty($params['DistMemo']['market_id'])) {

            $conditions[] = array('DistMemo.market_id' => $params['DistMemo']['market_id']);
        }
        if (!empty($params['DistMemo']['thana_id'])) {

            $conditions[] = array('Market.thana_id' => $params['DistMemo']['thana_id']);
        }
        if (!empty($params['DistMemo']['territory_id'])) {
            $conditions[] = array('DistMemo.territory_id' => $params['DistMemo']['territory_id']);
        }
        if (!empty($params['DistMemo']['outlet_id'])) {
            $conditions[] = array('DistMemo.outlet_id' => $params['DistMemo']['outlet_id']);
        }
        if (!empty($params['DistMemo']['status'])) {
            $conditions[] = array('DistMemo.status' => $params['DistMemo']['status']);
        }
        if (!empty($params['DistMemo']['sr_id'])) {
            $conditions[] = array('DistMemo.sr_id' => $params['DistMemo']['sr_id']);
        }
        if (!empty($params['DistMemo']['dist_route_id'])) {
            $conditions[] = array('DistMemo.dist_route_id' => $params['DistMemo']['dist_route_id']);
        }
        if (!empty($params['DistMemo']['distributor_id'])) {
            $conditions[] = array('DistMemo.distributor_id' => $params['DistMemo']['distributor_id']);
        } else {
            if ($user_group_id == 1034) {
                // App::import('Model', 'DistUserMapping');
                // $sp_id = CakeSession::read('UserAuth.User.sales_person_id');
                $sp_id = $this->Session->read('UserAuth.User.sales_person_id');
                // $this->DistUserMapping = new DistUserMapping();
                $data = $this->DistUserMapping->find('first', array('conditions' => array('DistUserMapping.sales_person_id' => $sp_id)));
                $distributor_id = $data['DistUserMapping']['dist_distributor_id'];
                $conditions[] = array('DistMemo.distributor_id' => $distributor_id);
            }
        }


        if (isset($params['DistMemo']['date_from']) != '') {
            $conditions[] = array('DistMemo.memo_date >=' => Date('Y-m-d H:i:s', strtotime($params['DistMemo']['date_from'])));
        }
        if (isset($params['DistMemo']['date_to']) != '') {
            $conditions[] = array('DistMemo.memo_date <=' => Date('Y-m-d H:i:s', strtotime($params['DistMemo']['date_to'] . ' 23:59:59')));
        }

        if (isset($params['DistMemo']['operator'])) {
            if ($params['DistMemo']['operator'] == 3) {
                $conditions[] = array('DistMemo.gross_value BETWEEN ? AND ?' => array($params['DistMemo']['memo_value_from'], $params['DistMemo']['memo_value_to']));
            } elseif ($params['DistMemo']['operator'] == 1) {
                $conditions[] = array('DistMemo.gross_value <' => $params['DistMemo']['mamo_value']);
            } elseif ($params['DistMemo']['operator'] == 2) {
                $conditions[] = array('DistMemo.gross_value >' => $params['DistMemo']['mamo_value']);
            }
        }
        $group = array('DistMemo.id', 'DistMemo.dist_memo_no', 'DistMemo.dist_order_no', 'DistMemo.from_app', 'DistMemo.gross_value', 'DistMemo.memo_time', 'DistMemo.status', 'DistMemo.action', 'DistOutlet.name', 'DistMarket.name', 'DistDistributor.name', 'DistRoute.name', 'DistTso.name', 'Office.office_name', 'DistAE.name');

        if (isset($params['DistMemo']['operator_product_count'])) {
            $operator_memo_count_conditions = '';
            if ($params['DistMemo']['operator_product_count'] == 3) {
                $operator_memo_count_conditions .= "count(case when MemoDetail.price >0 then MemoDetail.id end) BETWEEN '" . $params['DistMemo']['memo_product_count_from'] . "' AND '" . $params['DistMemo']['memo_product_count_to'] . "'";
            } elseif ($params['DistMemo']['operator_product_count'] == 1) {
                $operator_memo_count_conditions .= "count(case when MemoDetail.price >0 then MemoDetail.id end)  < '" . $params['DistMemo']['memo_product_count'] . "'";
            } elseif ($params['DistMemo']['operator_product_count'] == 2) {
                $operator_memo_count_conditions .= "count(case when MemoDetail.price >0 then MemoDetail.id end)  > '" . $params['DistMemo']['memo_product_count'] . "'";
            }
            if (strpos($group[14], 'HAVING')) {
                $group[14] .= ' AND ' . $operator_memo_count_conditions;
            } else {
                $group[14] .= ' HAVING ' . $operator_memo_count_conditions;
            }
        }
        $result = $this->DistMemo->find('all', array(
            'fields' => array('DistMemo.id', 'DistMemo.dist_memo_no', 'DistMemo.dist_order_no', 'DistMemo.from_app', 'DistMemo.gross_value', 'DistMemo.memo_time', 'DistMemo.status', 'DistMemo.action', 'DistOutlet.name', 'DistMarket.name', 'DistDistributor.name', 'DistRoute.name', 'DistTso.name', 'Office.office_name', 'DistAE.name'),
            'group' => $group,
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'alias' => 'DistOutlet',
                    'table' => 'dist_outlets',
                    'type' => 'INNER',
                    'conditions' => 'DistOutlet.id = DistMemo.outlet_id'
                ),
                array(
                    'alias' => 'DistMarket',
                    'table' => 'dist_markets',
                    'type' => 'INNER',
                    'conditions' => 'DistMarket.id = DistMemo.market_id'
                ),
                array(
                    'alias' => 'DistDistributor',
                    'table' => 'dist_distributors',
                    'type' => 'INNER',
                    'conditions' => 'DistDistributor.id = DistMemo.distributor_id'
                ),
                array(
                    'alias' => 'DistRoute',
                    'table' => 'dist_routes',
                    'type' => 'INNER',
                    'conditions' => 'DistRoute.id = DistMemo.dist_route_id'
                ),

                array(
                    'table' => 'dist_tso_mappings',
                    'alias' => 'DistTsoMapping',
                    'type' => 'LEFT',
                    'conditions' => 'DistTsoMapping.dist_distributor_id = DistMemo.distributor_id'

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
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'LEFT',
                    'conditions' => array('Office.id=DistMemo.office_id')
                ),
                array(
                    'table' => 'dist_memo_details',
                    'alias' => 'MemoDetail',
                    'type' => 'inner',
                    'conditions' => array('DistMemo.id=MemoDetail.dist_memo_id')
                ),
                /*array(
                        'alias' => 'DistSalesRepresentative',
                        'table' => 'dist_sales_representatives',
                        'type' => 'INNER',
                        'conditions' => 'DistSalesRepresentative.id = DistOrder.sr_id'
                    ),*/

            ),

            'order' => array('DistMemo.id' => 'desc'),
            'recursive' => 0

        ));

        /* echo  $this->DistMemo->getLastQuery();
        exit; */
        $memos = $result;

        //  table part


        $table = '<table border="1">
					<thead>
						<tr>
							<td><b>Id</b></td>
							<td><b>Dist. Memo No</b></td>
							<td><b>Dist. Order No</b></td>
                            <td><b>Area Office</b></td>
                            <td><b>Area Executive</b></td>
                            <td><b>TSO</b></td>
                            <td><b>Distributor</b></td>
							<td><b>Outlet</b></td>
							<td><b>Market</b></td>			
							<td><b>Dist. Memo Total</b></td>
							<td><b>Memo Date</b></td>
							<td><b>Route</b></td>
							
						</tr>
					</thead>
					<tbody>';

        $total_amount = 0;
        foreach ($memos as $memo) :

            $memo['DistMemo']['from_app'] = 0;

            $table .= '<tr>
						<td>' . h($memo['DistMemo']['id']) . '</td>
						<td>' . h($memo['DistMemo']['dist_memo_no']) . '</td>
						<td>' . h($memo['DistMemo']['dist_order_no']) . '</td>
                        <td>' . h($memo['Office']['office_name']) . '</td>
                        <td>' . h($memo['DistAE']['name']) . '</td>
                        <td>' . h($memo['DistTso']['name']) . '</td>
                        <td>' . h($memo['DistDistributor']['name']) . '</td>
						<td>' . h($memo['DistOutlet']['name']) . '</td>
						<td>' . h($memo['DistMarket']['name']) . '</td>
					
						<td>' . sprintf('%.2f', $memo['DistMemo']['gross_value']) . '</td>
						<td>' . date("d-m-Y h:i:sa", strtotime($memo['DistMemo']['memo_time'])) . '</td>
						
						<td>' . h($memo['DistRoute']['name']) . '</td>
                        
                        
                        
                        
					</tr>';

            $total_amount = $total_amount + $memo['DistMemo']['gross_value'];
        endforeach;

        $table .= '<tr>
						<td align="right" colspan="9"><b>Total Amount :</b></td>
						<td align="center"><b>' . sprintf('%.2f', $total_amount) . '</b></td>
						<td class="text-center" colspan="2"></td>
					</tr>
					</tbody>
				</table>';


        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment; filename="DistMemos.xls"');
        header("Cache-Control: ");
        header("Pragma: ");
        header("Expires: 0");
        echo "\xEF\xBB\xBF"; //UTF-8 BOM
        echo $table;
        $this->autoRender = false;
    }
}
