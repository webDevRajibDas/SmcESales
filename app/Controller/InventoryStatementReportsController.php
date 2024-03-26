<?php
App::uses('AppController', 'Controller');

/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class InventoryStatementReportsController extends AppController
{


    /**
     * Components
     *
     * @var array
     */

    public $uses = array('Store', 'CurrentInventory', 'Product', 'ProductCategory', 'Memo', 'Office', 'Territory', 'Division', 'District', 'Thana', 'Market', 'OutletCategory', 'Outlet', 'TerritoryAssignHistory', 'SalesPerson', 'GiftItem', 'DoctorVisit', 'InventoryAdjustment', 'Claim');
    public $components = array('Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index($id = null)
    {
        $this->Session->delete('detail_results');
        $this->Session->delete('outlet_lists');

        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes


        $this->set('page_title', 'Inventory Statement Report');

        $territories = array();
        $request_data = array();
        $report_type = array();
        $so_list = array();


        //for region office list
        $region_offices = $this->Office->find('list', array(
            'conditions' => array('Office.office_type_id' => 3),
            'order' => array('office_name' => 'asc')
        ));


        //types
        $types = array(
            'territory' => 'By Territory',
            'so' => 'By SO',
        );
        $this->set(compact('types'));

        //report type
        $unit_types = array(
            '1' => 'Sales Unit',
            '2' => 'Base Unit',
        );
        $this->set(compact('unit_types'));

        $report_type = array(
            '1' => 'Inventory Report',
            '2' => 'Other Issue Report',
        );
        $this->set(compact('report_type'));

        //for product type
        $product_types = $this->Product->ProductType->find('list', array('order' => array('name' => 'asc')));
        $this->set(compact('product_types'));


        //for product source type
        $sql = 'SELECT * FROM product_sources';
        $sources_datas = $this->Product->query($sql);
        $product_sources = array();
        foreach ($sources_datas as $sources_data) {
            $product_sources[$sources_data[0]['name']] = $sources_data[0]['name'];
        }

        /*$product_types = array(
            'smcel'         => 'SMCEL',
            'smc'           => 'SMC',
        );*/
        $this->set(compact('product_sources'));

        $region_office_id = 0;

        $office_parent_id = $this->UserAuth->getOfficeParentId();

        $this->set(compact('office_parent_id'));

        $office_conditions = array('Office.office_type_id' => 2);

        if ($office_parent_id == 0) {
            $office_id = 0;
        } elseif ($office_parent_id == 14) {
            $region_office_id = $this->UserAuth->getOfficeId();
            $region_offices = $this->Office->find('list', array(
                'conditions' => array('Office.office_type_id' => 3, 'Office.id' => $region_office_id),
                'order' => array('office_name' => 'asc')
            ));

            $office_conditions = array('Office.parent_office_id' => $region_office_id);

            $office_id = 0;

            $offices = $this->Office->find('list', array(
                'conditions' => array(
                    'office_type_id' => 2,
                    'parent_office_id' => $region_office_id,

                    'NOT' => array('id' => array(30, 31, 37))
                ),
                'order' => array('office_name' => 'asc')
            ));

            $office_ids = array_keys($offices);

            if ($office_ids) {
                $conditions['Territory.office_id'] = $office_ids;
            }

            //pr($conditions);
            //exit;
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
            $office_id = $this->UserAuth->getOfficeId();

            $offices = $this->Office->find('list', array(
                'conditions' => array(
                    'id' => $office_id,
                ),
                'order' => array('office_name' => 'asc')
            ));

            /*$territories = $this->Territory->find('list', array(
                'conditions' => array('Territory.office_id' => $office_id),
                'order' => array('Territory.name' => 'asc')
                )); */

            /***Show Except Parent(Who has Child) Territory ***/

            $child_territory_parent_id = $this->Territory->find('list', array(
                'conditions' => array(
                    'parent_id !=' => 0,

                ),
                'fields' => array('Territory.parent_id', 'Territory.name'),

            ));

            $territory_list = $this->Territory->find('all', array(
                'conditions' => array('Territory.office_id' => $office_id, 'NOT' => array('Territory.id' => array_keys($child_territory_parent_id))),
                /*'conditions' => array('Territory.office_id' => $office_id, 'User.user_group_id !=' => 1008),*/
                'joins' => array(
                    array(
                        'alias' => 'User',
                        'table' => 'users',
                        'type' => 'INNER',
                        'conditions' => 'SalesPerson.id = User.sales_person_id'
                    )
                ),
                'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
                'order' => array('Territory.name' => 'asc'),
                'recursive' => 0
            ));

            $territories = array();

            foreach ($territory_list as $key => $value) {
                $territories[$value['Territory']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
            }

            //get SO list
            $so_list_r = $this->SalesPerson->find('all', array(
                'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
                'conditions' => array(
                    'SalesPerson.office_id' => $office_id,
                    'SalesPerson.territory_id >' => 0,
                    'User.user_group_id' => array(4, 1008),
                ),
                'recursive' => 0
            ));
            foreach ($so_list_r as $key => $value) {
                $so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
            }
        }

        //pr($offices);

        //for product list
        $pro_conditions = array(
            //'NOT' => array('Product.product_category_id'=>32),
            'is_active' => 1,
            //'Product.product_type_id'=>1
        );

        $product_measurement = $this->Product->find('list', array(
            //'conditions'=> $pro_conditions,
            'fields' => array('Product.id', 'Product.sales_measurement_unit_id'),
            'order' => array('order' => 'asc'),
            'recursive' => -1
        ));


        if ($this->request->is('post') || $this->request->is('put')) {
            $request_data = $this->request->data;
            // pr($request_data);
            // exit;
            $is_day_wise_closing = isset($request_data['InventoryStatementReports']['day_wise_closing']['0']) ? 1 : 0;
            // echo $is_day_wise_closing;
            $date_from = date('Y-m-d', strtotime($request_data['InventoryStatementReports']['date_from']));
            $date_to = date('Y-m-d', strtotime($request_data['InventoryStatementReports']['date_to']));
            $datediff = strtotime($request_data['InventoryStatementReports']['date_to']) - strtotime($request_data['InventoryStatementReports']['date_from']);
            $total_day = round($datediff / (60 * 60 * 24));
            $this->set(compact('date_from', 'date_to', 'is_day_wise_closing', 'total_day'));

            $type = $this->request->data['InventoryStatementReports']['type'];
            $reports_type = $this->request->data['InventoryStatementReports']['report_type'];
            $this->set(compact('type', 'reports_type'));

            $region_office_id = isset($this->request->data['InventoryStatementReports']['region_office_id']) != '' ? $this->request->data['InventoryStatementReports']['region_office_id'] : $region_office_id;
            $this->set(compact('region_office_id'));
            $office_ids = array();
            if ($region_office_id) {
                $offices = $this->Office->find('list', array(
                    'conditions' => array(
                        'office_type_id' => 2,
                        'parent_office_id' => $region_office_id,

                        'NOT' => array('id' => array(30, 31, 37))
                    ),
                    'order' => array('office_name' => 'asc')
                ));

                $office_ids = array_keys($offices);
            } else {
                $offices = $this->Office->find('list', array(
                    'conditions' => array(
                        'office_type_id' => 2,

                        'NOT' => array('id' => array(30, 31, 37))
                    ),
                    'order' => array('office_name' => 'asc')
                ));

                $office_ids = array_keys($offices);
            }

            $office_id = isset($this->request->data['InventoryStatementReports']['office_id']) != '' ? $this->request->data['InventoryStatementReports']['office_id'] : $office_id;
            $this->set(compact('office_id'));

            $territory_id = isset($this->request->data['InventoryStatementReports']['territory_id']) != '' ? $this->request->data['InventoryStatementReports']['territory_id'] : 0;
            $this->set(compact('territory_id'));

            $so_id = isset($this->request->data['InventoryStatementReports']['so_id']) != '' ? $this->request->data['InventoryStatementReports']['so_id'] : 0;
            $this->set(compact('so_id'));

            $unit_type = $this->request->data['InventoryStatementReports']['unit_type'];
            $this->set(compact('unit_type'));
            $unit_type_text = ($unit_type == 2) ? 'Base' : 'Sales';
            $this->set(compact('unit_type_text'));

            $product_type_id = isset($this->request->data['InventoryStatementReports']['product_type_id']) != '' ? $this->request->data['InventoryStatementReports']['product_type_id'] : 0;
            $this->set(compact('product_type_id'));

            $source = isset($this->request->data['InventoryStatementReports']['source']) != '' ? $this->request->data['InventoryStatementReports']['source'] : 0;
            $this->set(compact('source'));

            /***Show Except Parent(Who has Child) Territory ***/

            $child_territory_parent_id = $this->Territory->find('list', array(
                'conditions' => array(
                    'parent_id !=' => 0,

                ),
                'fields' => array('Territory.parent_id', 'Territory.name'),

            ));

            //territory list
            $territory_list = $this->Territory->find('all', array(
                'conditions' => array('Territory.office_id' => $office_id, 'NOT' => array('Territory.id' => array_keys($child_territory_parent_id))),
                'joins' => array(
                    array(
                        'alias' => 'User',
                        'table' => 'users',
                        'type' => 'LEFT',
                        'conditions' => 'SalesPerson.id = User.sales_person_id'
                    )
                ),
                'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
                'order' => array('Territory.name' => 'asc'),
                'recursive' => 0
            ));

            $territories = array();

            foreach ($territory_list as $key => $value) {
                $territories[$value['Territory']['id']] = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
            }


            //get SO list
            $so_list_r = $this->SalesPerson->find('all', array(
                'fields' => array('SalesPerson.id', 'SalesPerson.name', 'Territory.name'),
                'conditions' => array(
                    'SalesPerson.office_id' => $office_id,
                    'SalesPerson.territory_id >' => 0,
                    'User.user_group_id' => 4,
                ),
                'recursive' => 0
            ));

            foreach ($so_list_r as $key => $value) {
                $so_list[$value['SalesPerson']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
            }

            //add old so from territory_assign_histories
            if ($office_id) {
                $conditions = array('Territory.office_id' => $office_id, 'TerritoryAssignHistory.assign_type' => 2);
                // $conditions['TerritoryAssignHistory.date BETWEEN ? and ? '] = array($date_from, $date_to);
                $conditions['TerritoryAssignHistory.date >= '] = $date_from;
                //pr($conditions);
                $old_so_list = $this->TerritoryAssignHistory->find('all', array(
                    'conditions' => $conditions,
                    'order' => array('Territory.name' => 'asc'),
                    'recursive' => 0
                ));
                if ($old_so_list) {
                    foreach ($old_so_list as $old_so) {
                        $so_list[$old_so['TerritoryAssignHistory']['so_id']] = $old_so['SalesPerson']['name'];
                    }
                }
            }

            if ($reports_type == 1) {
                //For Product List Conditon Update
                if ($source) {
                    $pro_conditions['Product.source'] = $source;
                }
                if ($product_type_id) {
                    $pro_conditions['Product.product_type_id'] = $product_type_id;
                }

                /*===== For ASO Stock Query =====*/
                //OPENING STOCK
                $con = array(
                    'RptDailyTranBalance.tran_date BETWEEN ? and ? ' => array($date_from, $date_from),
                    //'CurrentInventory.inventory_status_id' => 1,
                    'Store.store_type_id' => 2,
                );
                if ($office_ids) {
                    $con['Store.office_id'] = $office_ids;
                }
                if ($office_id) {
                    $con['Store.office_id'] = $office_id;
                }
                //pr($aso_con);
                $aso_stock_opening_results = $this->Store->find('all', array(
                    'conditions' => $con,
                    'joins' => array(
                        array(
                            'alias' => 'RptDailyTranBalance',
                            'table' => 'rpt_daily_tran_balance',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Store.id = RptDailyTranBalance.store_id'
                            )
                        ),
                        array(
                            'alias' => 'Product',
                            'table' => 'products',
                            'type' => 'INNER',
                            'conditions' => 'Product.id=RptDailyTranBalance.product_id'
                        )
                    ),
                    'fields' => array(
                        'sum(RptDailyTranBalance.opening_balance) AS opening_balance, CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END as product_id'
                    ),
                    'group' => array(
                        'CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END'
                    ),
                    'recursive' => -1,
                ));
                //echo $this->Store->getLastQuery(); exit;
                //pr($aso_stock_opening_results);
                //exit;


                //CLOSING STOCK
                $con = array(
                    'RptDailyTranBalance.tran_date BETWEEN ? and ? ' => array($date_to, $date_to),
                    //'CurrentInventory.inventory_status_id' => 1,
                    'Store.store_type_id' => 2,
                );
                if ($office_ids) {
                    $con['Store.office_id'] = $office_ids;
                }
                if ($office_id) {
                    $con['Store.office_id'] = $office_id;
                }

                $aso_stock_closing_results = $this->Store->find('all', array(
                    'conditions' => $con,
                    'joins' => array(
                        array(
                            'alias' => 'RptDailyTranBalance',
                            'table' => 'rpt_daily_tran_balance',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Store.id = RptDailyTranBalance.store_id'
                            )
                        ), array(
                            'alias' => 'Product',
                            'table' => 'products',
                            'type' => 'INNER',
                            'conditions' => 'Product.id=RptDailyTranBalance.product_id'
                        )
                    ),
                    'fields' => array(
                        'sum(RptDailyTranBalance.closing_balance) AS closing_balance,CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END as product_id'
                    ),
                    'group' => array(
                        'CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END'
                    ),
                    'recursive' => -1,
                ));

                //echo $this->Store->getLastQuery(); exit;

                /* pr($aso_stock_closing_results);
                exit; */


                //$sales_qty = ($unit_type==1)?$result[0]['sales_qty']:$this->unit_convertfrombase($result['MemoDetail']['product_id'], $result['Product']['sales_measurement_unit_id'], $result[0]['sales_qty']);


                $aso_final_opening_closing = array();

                foreach ($aso_stock_opening_results as $aso_stock) {
                    $aso_final_opening_closing[$aso_stock['0']['product_id']]['opening_balance'] = sprintf('%01.2f', ($unit_type == 2) ? $aso_stock[0]['opening_balance'] : $this->unit_convertfrombase($aso_stock['0']['product_id'], $product_measurement[$aso_stock['0']['product_id']], $aso_stock[0]['opening_balance']));
                }
                //pr($aso_final_opening_closing);
                foreach ($aso_stock_closing_results as $aso_stock) {
                    $aso_final_opening_closing[$aso_stock['0']['product_id']]['closing_balance'] = sprintf('%01.2f', ($unit_type == 2) ? $aso_stock[0]['closing_balance'] : $this->unit_convertfrombase($aso_stock['0']['product_id'], $product_measurement[$aso_stock['0']['product_id']], $aso_stock[0]['closing_balance']));
                }
                //pr($aso_final_opening_closing);
                //exit;
                $this->set(compact('aso_final_opening_closing'));


                if ($is_day_wise_closing == 1) {
                    //Day CLOSING STOCK
                    $con = array(
                        'RptDailyTranBalance.tran_date BETWEEN ? and ? ' => array($date_from, $date_to),
                        //'CurrentInventory.inventory_status_id' => 1,
                        'Store.store_type_id' => 2,
                    );
                    if ($office_ids) {
                        $con['Store.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $con['Store.office_id'] = $office_id;
                    }

                    $aso_day_stock_closing_results = $this->Store->find('all', array(
                        'conditions' => $con,
                        'joins' => array(
                            array(
                                'alias' => 'RptDailyTranBalance',
                                'table' => 'rpt_daily_tran_balance',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Store.id = RptDailyTranBalance.store_id'
                                )
                            ), array(
                                'alias' => 'Product',
                                'table' => 'products',
                                'type' => 'INNER',
                                'conditions' => 'Product.id=RptDailyTranBalance.product_id'
                            )
                        ),
                        'fields' => array('sum(RptDailyTranBalance.closing_balance) AS closing_balance,CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END as product_id', 'RptDailyTranBalance.tran_date'),
                        'group' => array('RptDailyTranBalance.tran_date', 'CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END'),
                        'recursive' => -1,
                    ));
                    $aso_day_wise_closing = array();
                    foreach ($aso_day_stock_closing_results as $aso_stock) {
                        $aso_day_wise_closing[$aso_stock['0']['product_id']][$aso_stock['RptDailyTranBalance']['tran_date']] = sprintf('%01.2f', ($unit_type == 2) ? $aso_stock[0]['closing_balance'] : $this->unit_convertfrombase($aso_stock['0']['product_id'], $product_measurement[$aso_stock['0']['product_id']], $aso_stock[0]['closing_balance']));
                    }

                    $this->set(compact('aso_day_wise_closing'));
                }
                /*==== FOR SO STOCK QUERY ====*/
                //OPENING BALANCE
                $so_con = array(
                    'RptDailyTranBalance.tran_date BETWEEN ? and ? ' => array($date_from, $date_from),
                    //'CurrentInventory.inventory_status_id' => 1,
                    'Store.store_type_id' => 3,
                );
                if ($office_ids) {
                    $so_con['Store.office_id'] = $office_ids;
                }
                if ($office_id) {
                    $so_con['Store.office_id'] = $office_id;
                }
                if ($type == 'so') {
                    if ($so_id) {
                        $so_con['SalesPeople.id'] = $so_id;
                    }
                } else {
                    if ($territory_id) {
                        $so_con['Store.territory_id'] = $territory_id;
                    }
                }

                $so_stock_opening_results = $this->Store->find('all', array(
                    'conditions' => $so_con,
                    'joins' => array(
                        array(
                            'alias' => 'RptDailyTranBalance',
                            'table' => 'rpt_daily_tran_balance',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Store.id = RptDailyTranBalance.store_id'
                            )
                        ),
                        array(
                            'alias' => 'Product',
                            'table' => 'products',
                            'type' => 'INNER',
                            'conditions' => 'Product.id=RptDailyTranBalance.product_id'
                        ),
                        array(
                            'alias' => 'SalesPeople',
                            'table' => 'sales_people',
                            'type' => 'left',
                            'conditions' => 'Store.territory_id = SalesPeople.territory_id'
                        ),
                    ),
                    'fields' => array('sum(RptDailyTranBalance.opening_balance) AS opening_balance,CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END as product_id'),
                    'group' => array('CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END'),
                    'recursive' => -1,
                ));


                /*pr($so_stock_opening_results);
                exit;*/


                //CLOSING BALANCE
                $so_con = array(
                    'RptDailyTranBalance.tran_date BETWEEN ? and ? ' => array($date_to, $date_to),
                    //'CurrentInventory.inventory_status_id' => 1,
                    'Store.store_type_id' => 3,
                );
                if ($office_ids) {
                    $so_con['Store.office_id'] = $office_ids;
                }
                if ($office_id) {
                    $so_con['Store.office_id'] = $office_id;
                }
                if ($type == 'so') {
                    if ($so_id) {
                        $so_con['SalesPeople.id'] = $so_id;
                    }
                } else {
                    if ($territory_id) {
                        $so_con['Store.territory_id'] = $territory_id;
                    }
                }

                $so_stock_closing_results = $this->Store->find('all', array(
                    'conditions' => $so_con,
                    'joins' => array(
                        array(
                            'alias' => 'RptDailyTranBalance',
                            'table' => 'rpt_daily_tran_balance',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Store.id = RptDailyTranBalance.store_id'
                            )
                        ),
                        array(
                            'alias' => 'Product',
                            'table' => 'products',
                            'type' => 'INNER',
                            'conditions' => 'Product.id=RptDailyTranBalance.product_id'
                        ),
                        array(
                            'alias' => 'SalesPeople',
                            'table' => 'sales_people',
                            'type' => 'left',
                            'conditions' => 'Store.territory_id = SalesPeople.territory_id'
                        ),
                    ),
                    'fields' => array('sum(RptDailyTranBalance.closing_balance) AS closing_balance,CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END as product_id'),
                    'group' => array('CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END'),
                    'recursive' => -1,
                ));

                // echo $this->Store->getLastQuery();
                // pr($so_stock_closing_results);exit;

                $so_final_opening_closing = array();

                foreach ($so_stock_opening_results as $so_stock) {
                    $so_final_opening_closing[$so_stock['0']['product_id']]['opening_balance'] = sprintf('%01.2f', ($unit_type == 2) ? $so_stock[0]['opening_balance'] : $this->unit_convertfrombase($so_stock['0']['product_id'], $product_measurement[$so_stock['0']['product_id']], $so_stock[0]['opening_balance']));
                }

                foreach ($so_stock_closing_results as $so_stock) {
                    $so_final_opening_closing[$so_stock['0']['product_id']]['closing_balance'] = sprintf('%01.2f', ($unit_type == 2) ? $so_stock[0]['closing_balance'] : $this->unit_convertfrombase($so_stock['0']['product_id'], $product_measurement[$so_stock['0']['product_id']], $so_stock[0]['closing_balance']));
                }

                //pr($so_final_opening_closing);
                //exit;

                $this->set(compact('so_final_opening_closing'));
                if ($is_day_wise_closing == 1) {
                    //Day Wise CLOSING BALANCE
                    $so_con = array(
                        'RptDailyTranBalance.tran_date BETWEEN ? and ? ' => array($date_from, $date_to),
                        //'CurrentInventory.inventory_status_id' => 1,
                        'Store.store_type_id' => 3,
                    );
                    if ($office_ids) {
                        $so_con['Store.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $so_con['Store.office_id'] = $office_id;
                    }
                    if ($type == 'so') {
                        if ($so_id) {
                            $so_con['SalesPeople.id'] = $so_id;
                        }
                    } else {
                        if ($territory_id) {
                            $so_con['Store.territory_id'] = $territory_id;
                        }
                    }

                    $so_day_wise_closing_results = $this->Store->find('all', array(
                        'conditions' => $so_con,
                        'joins' => array(
                            array(
                                'alias' => 'RptDailyTranBalance',
                                'table' => 'rpt_daily_tran_balance',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Store.id = RptDailyTranBalance.store_id'
                                )
                            ),
                            array(
                                'alias' => 'Product',
                                'table' => 'products',
                                'type' => 'INNER',
                                'conditions' => 'Product.id=RptDailyTranBalance.product_id'
                            ),
                            array(
                                'alias' => 'SalesPeople',
                                'table' => 'sales_people',
                                'type' => 'lEFT',
                                'conditions' => 'Store.territory_id = SalesPeople.territory_id'
                            ),
                        ),
                        'fields' => array('sum(RptDailyTranBalance.closing_balance) AS closing_balance,CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END as product_id', 'RptDailyTranBalance.tran_date'),
                        'group' => array('RptDailyTranBalance.tran_date', 'CASE WHEN Product.parent_id is null OR Product.parent_id=0 THEN Product.id ELSE Product.parent_id END '),
                        'recursive' => -1,
                    ));
                    $so_day_wise_closing = array();
                    foreach ($so_day_wise_closing_results as $so_stock) {
                        $so_day_wise_closing[$so_stock['0']['product_id']][$so_stock['RptDailyTranBalance']['tran_date']] = sprintf('%01.2f', ($unit_type == 2) ? $so_stock[0]['closing_balance'] : $this->unit_convertfrombase($so_stock['0']['product_id'], $product_measurement[$so_stock['0']['product_id']], $so_stock[0]['closing_balance']));
                    }
                    /* pr($so_day_wise_closing);
                    exit; */
                    $this->set(compact('so_day_wise_closing'));
                }

                /*==== FOR SALES, BONUS/GIFT QUERY ====*/

                /*---Product Grouping CR (If Child Territory Memo Condition Add for Child Territory)----Start*/
                $child_territory_info = $this->Territory->find('first', array(
                    'conditions' => array(
                        'Territory.id' => $territory_id

                    ),
                    'recursive' => 0
                ));

                if ($child_territory_info['Territory']['parent_id'] > 0) {
                    $child_territory_flag = 1;
                } else {

                    $child_territory_flag = 0;

                }
                /*---Product Grouping CR (If Child Territory Memo Condition Add for Child Territory)----End*/
                //sales and bonus
                $sales_con = array(
                    'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
                    'Memo.gross_value >' => 0,
                    'Memo.status !=' => 0,
                    'MemoDetail.price >' => 0,
                );

                if ($office_ids) {
                    $sales_con['Memo.office_id'] = $office_ids;
                }
                if ($office_id) {
                    $sales_con['Memo.office_id'] = $office_id;
                }
                if ($type == 'so') {
                    if ($so_id) {
                        $sales_con['Memo.sales_person_id'] = $so_id;
                    }
                } else {
                    if ($territory_id) {

                        /**IF Memo Created by Child Territory */

                        if ($child_territory_flag == 1) {

                            $sales_con['Memo.child_territory_id'] = $territory_id;

                        } else {

                            $sales_con['Memo.territory_id'] = $territory_id;

                        }


                    }
                }
                $sales_query_results = $this->Memo->find('all', array(
                    'conditions' => $sales_con,
                    'joins' => array(
                        array(
                            'alias' => 'MemoDetail',
                            'table' => 'memo_details',
                            'type' => 'INNER',
                            'conditions' => 'Memo.id = MemoDetail.memo_id'
                        ),
                        array(
                            'alias' => 'Product',
                            'table' => 'products',
                            'type' => 'INNER',
                            'conditions' => 'Product.id = MemoDetail.product_id'
                        ),
                        array(
                            'alias' => 'ProductMeasurement',
                            'table' => 'product_measurements',
                            'type' => 'LEFT',
                            'conditions' => 'Product.id = ProductMeasurement.product_id AND 
								CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
								ELSE 
									MemoDetail.measurement_unit_id
								END =ProductMeasurement.measurement_unit_id'
                        ),
                    ),
                    'fields' => array('sum(ROUND((MemoDetail.sales_qty* CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0)) AS sales_qty', 'MemoDetail.product_id'),
                    'group' => array('MemoDetail.product_id'),
                    'recursive' => -1,
                ));

                //for bounus
                $bonus_con = array(
                    'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
                    //'Memo.gross_value >' => 0,
                    'Memo.status !=' => 0,
                    'MemoDetail.price <' => 1,
                );

                if ($office_ids) {
                    $bonus_con['Memo.office_id'] = $office_ids;
                }
                if ($office_id) {
                    $bonus_con['Memo.office_id'] = $office_id;
                }
                if ($type == 'so') {
                    if ($so_id) {
                        $bonus_con['Memo.sales_person_id'] = $so_id;
                    }
                } else {
                    if ($territory_id) {

                        /**IF Memo Created by Child Territory */

                        if ($child_territory_flag == 1) {

                            $bonus_con['Memo.child_territory_id'] = $territory_id;

                        } else {

                            $bonus_con['Memo.territory_id'] = $territory_id;

                        }
                    }
                }
                $bonus_query_results = $this->Memo->find('all', array(
                    'conditions' => $bonus_con,
                    'joins' => array(
                        array(
                            'alias' => 'MemoDetail',
                            'table' => 'memo_details',
                            'type' => 'INNER',
                            'conditions' => 'Memo.id = MemoDetail.memo_id'
                        ),
                        array(
                            'alias' => 'Product',
                            'table' => 'products',
                            'type' => 'INNER',
                            'conditions' => 'Product.id = MemoDetail.product_id'
                        ),
                        array(
                            'alias' => 'ProductMeasurement',
                            'table' => 'product_measurements',
                            'type' => 'LEFT',
                            'conditions' => 'Product.id = ProductMeasurement.product_id AND 
							CASE WHEN (MemoDetail.measurement_unit_id is null or MemoDetail.measurement_unit_id=0) THEN Product.sales_measurement_unit_id
								ELSE 
									MemoDetail.measurement_unit_id
								END=ProductMeasurement.measurement_unit_id'
                        ),
                    ),
                    'fields' => array('sum(ROUND((MemoDetail.sales_qty* CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END ),0))  AS bonus_qty', 'MemoDetail.product_id'),
                    'group' => array('MemoDetail.product_id'),
                    'recursive' => -1,
                ));

                //Gift from GiftItme table
                $g_con = array(
                    'GiftItem.date BETWEEN ? and ? ' => array($date_from, $date_to),
                );
                if ($office_ids) {
                    $g_con['Territory.office_id'] = $office_ids;
                }
                if ($office_id) {
                    $g_con['Territory.office_id'] = $office_id;
                }
                if ($type == 'so') {
                    if ($so_id) {
                        $g_con['SalesPeople.id'] = $so_id;
                    }
                } else {
                    if ($territory_id) {
                        $g_con['GiftItem.territory_id'] = $territory_id;
                    }
                }
                $g_con['GiftItem.memo_no'] = null;

                //pr($g_con);
                $g_query_results = $this->GiftItem->find('all', array(
                    'conditions' => $g_con,
                    'joins' => array(
                        array(
                            'alias' => 'GiftItemDetail',
                            'table' => 'gift_item_details',
                            'type' => 'INNER',
                            'conditions' => array(
                                'GiftItem.id = GiftItemDetail.gift_item_id'
                            )
                        ),
                        array(
                            'alias' => 'SalesPeople',
                            'table' => 'sales_people',
                            'type' => 'LEFT',
                            'conditions' => 'GiftItem.territory_id = SalesPeople.territory_id'
                        ),
                        array(
                            'alias' => 'Territory',
                            'table' => 'territories',
                            'type' => 'INNER',
                            'conditions' => 'GiftItem.territory_id = Territory.id'
                        ),
                        array(
                            'alias' => 'Product',
                            'table' => 'products',
                            'type' => 'INNER',
                            'conditions' => 'Product.id = GiftItemDetail.product_id'
                        ),
                        array(
                            'alias' => 'ProductMeasurement',
                            'table' => 'product_measurements',
                            'type' => 'LEFT',
                            'conditions' => 'Product.id = ProductMeasurement.product_id AND Product.sales_measurement_unit_id=ProductMeasurement.measurement_unit_id'
                        ),

                    ),
                    'fields' => array('sum(ROUND((GiftItemDetail.quantity*CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END),0)) AS gift_qty', 'GiftItemDetail.product_id'),
                    'group' => array('GiftItemDetail.product_id', 'ProductMeasurement.qty_in_base'),
                    'recursive' => -1,
                ));

                //pr($g_query_results);
                //exit;


                //Gift from Doctor Visit table
                $g_con = array(
                    'DoctorVisit.visit_date BETWEEN ? and ? ' => array($date_from, $date_to),
                );
                if ($office_ids) {
                    $g_con['Territory.office_id'] = $office_ids;
                }
                if ($office_id) {
                    $g_con['Territory.office_id'] = $office_id;
                }
                if ($type == 'so') {
                    if ($so_id) {
                        $g_con['SalesPeople.id'] = $so_id;
                    }
                } else {
                    if ($territory_id) {
                        $g_con['DoctorVisit.territory_id'] = $territory_id;
                    }
                }
                //pr($g_con);
                $g_doc_query_results = $this->DoctorVisit->find('all', array(
                    'conditions' => $g_con,
                    'joins' => array(
                        array(
                            'alias' => 'DoctorVisitDetail',
                            'table' => 'doctor_visit_details',
                            'type' => 'INNER',
                            'conditions' => array(
                                'DoctorVisit.id = DoctorVisitDetail.doctor_visit_id'
                            )
                        ),
                        array(
                            'alias' => 'SalesPeople',
                            'table' => 'sales_people',
                            'type' => 'LEFT',
                            'conditions' => 'DoctorVisit.territory_id = SalesPeople.territory_id'
                        ),
                        array(
                            'alias' => 'Territory',
                            'table' => 'territories',
                            'type' => 'INNER',
                            'conditions' => 'DoctorVisit.territory_id = Territory.id'
                        ),
                        array(
                            'alias' => 'Product',
                            'table' => 'products',
                            'type' => 'INNER',
                            'conditions' => 'Product.id = DoctorVisitDetail.product_id'
                        ),
                        array(
                            'alias' => 'ProductMeasurement',
                            'table' => 'product_measurements',
                            'type' => 'LEFT',
                            'conditions' => 'Product.id = ProductMeasurement.product_id AND Product.sales_measurement_unit_id =ProductMeasurement.measurement_unit_id'
                        ),
                    ),
                    'fields' => array('sum(ROUND((DoctorVisitDetail.quantity * CASE WHEN ProductMeasurement.qty_in_base is null then 1 ELSE ProductMeasurement.qty_in_base END),0)) AS gift_qty', 'DoctorVisitDetail.product_id'),
                    'group' => array('DoctorVisitDetail.product_id', 'ProductMeasurement.qty_in_base'),
                    'recursive' => -1,
                ));


                //pr($g_doc_query_results);
                //exit;


                $sbg_results = array();

                foreach ($sales_query_results as $s_query_result) {
                    $sbg_results[$s_query_result['MemoDetail']['product_id']]['sales_qty'] = sprintf('%01.2f', ($unit_type == 2) ? $s_query_result[0]['sales_qty'] : $this->unit_convertfrombase($s_query_result['MemoDetail']['product_id'], $product_measurement[$s_query_result['MemoDetail']['product_id']], $s_query_result[0]['sales_qty']));
                }
                foreach ($bonus_query_results as $b_query_result) {
                    $sbg_results[$b_query_result['MemoDetail']['product_id']]['bonus_qty'] = sprintf('%01.2f', ($unit_type == 2) ? $b_query_result[0]['bonus_qty'] : $this->unit_convertfrombase($b_query_result['MemoDetail']['product_id'], $product_measurement[$b_query_result['MemoDetail']['product_id']], $b_query_result[0]['bonus_qty']));
                }


                foreach ($g_query_results as $g_query_result) {
                    $sbg_results[$g_query_result['GiftItemDetail']['product_id']]['gift_qty'] = sprintf('%01.2f', ($unit_type == 2) ? $g_query_result[0]['gift_qty'] : $this->unit_convertfrombase($g_query_result['GiftItemDetail']['product_id'], $product_measurement[$g_query_result['GiftItemDetail']['product_id']], $g_query_result[0]['gift_qty']));
                }

                foreach ($g_doc_query_results as $g_doc_query_result) {
                    $old_gift_qty = @$sbg_results[$g_doc_query_result['DoctorVisitDetail']['product_id']]['gift_qty'] ? $sbg_results[$g_doc_query_result['DoctorVisitDetail']['product_id']]['gift_qty'] : 0;

                    $sbg_results[$g_doc_query_result['DoctorVisitDetail']['product_id']]['gift_qty'] = $old_gift_qty + $g_doc_query_result[0]['gift_qty'];
                }

                //pr($sbg_results);
                //exit;
                $this->set(compact('sbg_results'));


                /*==== FOR PRODUCT RECEIVED ====*/
                if ($so_id || $territory_id) {
                    /*$con = array(
                    'CurrentInventoryHistory.transaction_date BETWEEN ? and ? ' => array($date_from, $date_to),
                    'CurrentInventoryHistory.transaction_type_id' => 5,
                    'CurrentInventoryHistory.inventory_status_id' => 1,
                    'Store.store_type_id' => 3,
                    );*/
                    $con = array(
                        'Challan.received_date BETWEEN ? and ? ' => array($date_from, $date_to),
                        'Challan.transaction_type_id' => array(5),
                        // 2 product isssue, when product received it will be changed to 5 (to territory)
                        // 28 product isssue, when product received it will be changed to 29 (to CWH)
                        'Store.store_type_id' => 3,
                    );
                    if ($office_ids) {
                        $con['Store.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $con['Store.office_id'] = $office_id;
                    }
                    if ($type == 'so') {
                        if ($so_id) {
                            $con['SalesPeople.id'] = $so_id;
                        }
                    } else {
                        if ($territory_id) {
                            $con['Store.territory_id'] = $territory_id;
                        }
                    }

                    //pr($con);
                    $received_query_results = $this->Store->find('all', array(
                        'conditions' => $con,

                        'joins' => array(
                            array(
                                'alias' => 'Challan',
                                'table' => 'challans',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Store.id = Challan.receiver_store_id'
                                )
                            ),
                            array(
                                'alias' => 'ChallanDetails',
                                'table' => 'challan_details',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Challan.id = ChallanDetails.challan_id'
                                )
                            ),
                            array(
                                'alias' => 'SalesPeople',
                                'table' => 'sales_people',
                                'type' => 'LEFT',
                                'conditions' => 'Store.territory_id = SalesPeople.territory_id'
                            ),
                        ),

                        'fields' => array('sum(ChallanDetails.received_qty) AS qty', 'ChallanDetails.product_id', 'Challan.transaction_type_id'),
                        'group' => array('ChallanDetails.product_id', 'Challan.transaction_type_id'),
                        'recursive' => -1,
                    ));

                    $received_results = array();

                    foreach ($received_query_results as $received_query_result) {
                        $received_results[$received_query_result['ChallanDetails']['product_id']]['from_cwh_to_aso'] = '';

                        $received_results[$received_query_result['ChallanDetails']['product_id']]['from_territory_to_aso'] = '';

                        if ($received_query_result['Challan']['transaction_type_id'] == 5) {
                            $received_results[$received_query_result['ChallanDetails']['product_id']]['from_aso'] = sprintf('%01.2f', ($unit_type == 1) ? $received_query_result[0]['qty'] : $this->unit_convert($received_query_result['ChallanDetails']['product_id'], $product_measurement[$received_query_result['ChallanDetails']['product_id']], $received_query_result[0]['qty']));
                        }

                        //will do later
                        $received_results[$received_query_result['ChallanDetails']['product_id']]['from_orthers'] = '';
                    }


                    //--For Received Orther's
                    /*$con = array(
                    'CurrentInventoryHistory.transaction_date BETWEEN ? and ? ' => array($date_from, $date_to),
                    //'CurrentInventoryHistory.transaction_type_id' => array(4, 19, 29),
                     'NOT' => array('CurrentInventoryHistory.transaction_type_id'=>29),
                    'CurrentInventoryHistory.inventory_status_id' => 1,
                    'TransactionType.inout' => 2,
                    'TransactionType.adjust' => 1,
                    );
                    if($office_ids)$con['Store.office_id'] = $office_ids;
                    if($office_id)$con['Store.office_id'] = $office_id;
                    if($type=='so'){
                        if($so_id)$con['SalesPeople.id'] = $so_id;
                    }else{
                        if($territory_id)$con['Store.territory_id'] = $territory_id;
                    }

                    $o_received_query_results = $this->Store->find('all', array(
                        'conditions' => $con,
                        'joins' => array(
                            array(
                                'alias' => 'CurrentInventoryHistory',
                                'table' => 'current_inventory_history',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Store.id = CurrentInventoryHistory.store_id'
                                )
                            ),
                            array(
                                'alias' => 'SalesPeople',
                                'table' => 'sales_people',
                                'type' => 'INNER',
                                'conditions' => 'Store.territory_id = SalesPeople.territory_id'
                            ),
                            array(
                                'alias' => 'TransactionType',
                                'table' => 'transaction_types',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'CurrentInventoryHistory.transaction_type_id = TransactionType.id'
                                )
                            ),
                         ),
                        'fields'=> array('sum(CurrentInventoryHistory.qty) AS qty', 'CurrentInventoryHistory.product_id', 'CurrentInventoryHistory.transaction_type_id'),
                        'group' => array('CurrentInventoryHistory.product_id', 'CurrentInventoryHistory.transaction_type_id'),
                        'recursive' => -1,
                    ));

                    foreach($o_received_query_results as $received_query_result)
                    {
                        //others will do later
                        $received_results[$received_query_result['CurrentInventoryHistory']['product_id']]['from_orthers']=sprintf("%01.2f", ($unit_type==2)?$received_query_result[0]['qty']:$this->unit_convertfrombase($received_query_result['CurrentInventoryHistory']['product_id'], $product_measurement[$received_query_result['CurrentInventoryHistory']['product_id']], $received_query_result[0]['qty']));
                    }*/

                    //pr($o_received_query_results);
                    //exit;
                } else {
                    /*$con = array(
                    'Challan.received_date BETWEEN ? and ? ' => array($date_from, $date_to),
                    'Challan.transaction_type_id' => array(4, 29),
                    'Store.store_type_id' => 2,
                    );
                    if($office_ids)$con['Store.office_id'] = $office_ids;
                    if($office_id)$con['Store.office_id'] = $office_id;

                    $received_query_results = $this->Store->find('all', array(
                        'conditions' => $con,

                        'joins' => array(
                            array(
                                'alias' => 'Challan',
                                'table' => 'challans',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Store.id = Challan.receiver_store_id'
                                )
                            ),
                            array(
                                'alias' => 'ChallanDetails',
                                'table' => 'challan_details',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Challan.id = ChallanDetails.challan_id'
                                )
                            ),
                         ),

                        'fields'=> array('sum(ChallanDetails.received_qty) AS qty', 'ChallanDetails.product_id', 'Challan.transaction_type_id'),
                        'group' => array('ChallanDetails.product_id', 'Challan.transaction_type_id'),
                        'recursive' => -1,
                    ));*/

                    if ($office_ids) {
                        $office_id_challan = implode(',', $office_ids);
                    }
                    if ($office_id) {
                        $office_id_challan = $office_id;
                    }

                    $sql = "select p.id as product_id,
					(sum(cd.received_qty*(case when pmb.qty_in_base is null then 1 else pmb.qty_in_base end))/(case when min(pms.qty_in_base) is null then 1 else min(pms.qty_in_base) end)) as qty,
					ch.transaction_type_id from challan_details cd
					inner join challans ch on ch.id=cd.challan_id
					inner join products p on p.id=cd.product_id
					left join product_measurements pmb on pmb.product_id=p.id and pmb.measurement_unit_id=cd.measurement_unit_id
					left join product_measurements pms on pms.product_id=p.id and pms.measurement_unit_id=p.sales_measurement_unit_id
					inner join stores st on st.id=ch.receiver_store_id
					where  ch.received_date BETWEEN '" . $date_from . "' AND '" . $date_to . "' AND st.office_id in ($office_id_challan) AND st.store_type_id=2 AND ch.transaction_type_id in (4,29)
					group by p.id,ch.transaction_type_id";
                    $received_query_results = $this->Store->query($sql);
                    // echo $this->Store->getLastQuery();
                    // pr($received_query_results);
                    // exit;

                    $received_results = array();

                    /*foreach($received_query_results as $received_query_result)
                    {

                        if($received_query_result['Challan']['transaction_type_id']==4)
                        {
                            $received_results[$received_query_result['ChallanDetails']['product_id']]['from_cwh_to_aso']= sprintf("%01.2f", ($unit_type==1)?$received_query_result[0]['qty']:$this->unit_convert($received_query_result['ChallanDetails']['product_id'], $product_measurement[$received_query_result['ChallanDetails']['product_id']], $received_query_result[0]['qty']));
                        }

                        if($received_query_result['Challan']['transaction_type_id']==29)
                        {
                            $received_results[$received_query_result['ChallanDetails']['product_id']]['from_aso']= sprintf("%01.2f", ($unit_type==1)?$received_query_result[0]['qty']:$this->unit_convert($received_query_result['ChallanDetails']['product_id'], $product_measurement[$received_query_result['ChallanDetails']['product_id']], $received_query_result[0]['qty']));
                        }

                        //others will do later
                        $received_results[$received_query_result['ChallanDetails']['product_id']]['from_orthers']= '';
                    }*/

                    foreach ($received_query_results as $received_query_result) {
                        if ($received_query_result[0]['transaction_type_id'] == 4) {
                            $received_results[$received_query_result[0]['product_id']]['from_cwh_to_aso'] = sprintf('%01.2f', ($unit_type == 1) ? $received_query_result[0]['qty'] : $this->unit_convert($received_query_result[0]['product_id'], $product_measurement[$received_query_result[0]['product_id']], $received_query_result[0]['qty']));
                        }

                        if ($received_query_result[0]['transaction_type_id'] == 29) {
                            $received_results[$received_query_result[0]['product_id']]['from_aso'] = sprintf('%01.2f', ($unit_type == 1) ? $received_query_result[0]['qty'] : $this->unit_convert($received_query_result[0]['product_id'], $product_measurement[$received_query_result[0]['product_id']], $received_query_result[0]['qty']));
                        }

                        //others will do later
                        $received_results[$received_query_result[0]['product_id']]['from_orthers'] = '';
                    }


                    //for return recieved from territory
                    $con = array(
                        'ReturnChallan.received_date BETWEEN ? and ? ' => array($date_from, $date_to),
                        'ReturnChallan.transaction_type_id' => array(19),
                        'Store.store_type_id' => 2,
                    );
                    if ($office_ids) {
                        $con['Store.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $con['Store.office_id'] = $office_id;
                    }

                    $received_query_results = $this->Store->find('all', array(
                        'conditions' => $con,

                        'joins' => array(
                            array(
                                'alias' => 'ReturnChallan',
                                'table' => 'return_challans',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Store.id = ReturnChallan.receiver_store_id'
                                )
                            ),
                            array(
                                'alias' => 'ReturnChallanDetails',
                                'table' => 'return_challan_details',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'ReturnChallan.id = ReturnChallanDetails.challan_id'
                                )
                            ),
                            array(
                                'alias' => 'ProductMeasurement',
                                'table' => 'product_measurements',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'ProductMeasurement.product_id = ReturnChallanDetails.product_id AND ProductMeasurement.measurement_unit_id= ReturnChallanDetails.measurement_unit_id'
                                )
                            ),
                        ),

                        'fields' => array('sum(ROUND((ReturnChallanDetails.received_qty*CASE WHEN ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base  END),0)) AS qty', 'ReturnChallanDetails.product_id', 'ReturnChallan.transaction_type_id'),
                        'group' => array('ReturnChallanDetails.product_id', 'ReturnChallan.transaction_type_id', 'ProductMeasurement.qty_in_base'),
                        'recursive' => -1,
                    ));
                    //echo $this->Store->getLastQuery();
                    //pr($received_query_results);
                    //exit;

                    //$received_results = array();

                    foreach ($received_query_results as $received_query_result) {
                        if ($received_query_result['ReturnChallan']['transaction_type_id'] == 19) {
                            $received_results[$received_query_result['ReturnChallanDetails']['product_id']]['from_territory_to_aso'] = sprintf('%01.2f', ($unit_type == 2) ? $received_query_result[0]['qty'] : $this->unit_convertfrombase($received_query_result['ReturnChallanDetails']['product_id'], $product_measurement[$received_query_result['ReturnChallanDetails']['product_id']], $received_query_result[0]['qty']));
                        }
                    }


                    //--Received From Orther's
                    $con = array(
                        'Convert(Date, InventoryAdjustment.created_at) BETWEEN ? and ? ' => array($date_from, $date_to),
                        //'CurrentInventoryHistory.transaction_type_id' => array(4, 19, 29),
                        //'NOT' => array('CurrentInventoryHistory.transaction_type_id'=>29),
                        //'CurrentInventoryHistory.inventory_status_id' => 1,
                        'TransactionType.inout' => 2,
                        'TransactionType.adjust' => 1,
                        'InventoryAdjustment.approval_status' => 1
                    );
                    if ($office_ids) {
                        $con['Store.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $con['Store.office_id'] = $office_id;
                    }

                    $o_received_query_results = $this->Store->find('all', array(
                        'conditions' => $con,
                        'joins' => array(
                            array(
                                'alias' => 'InventoryAdjustment',
                                'table' => 'inventory_adjustments',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Store.id = InventoryAdjustment.store_id'
                                )
                            ),
                            array(
                                'alias' => 'InventoryAdjustmentDetail',
                                'table' => 'inventory_adjustment_details',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'InventoryAdjustment.id = InventoryAdjustmentDetail.inventory_adjustment_id'
                                )
                            ),
                            array(
                                'alias' => 'CurrentInventory',
                                'table' => 'current_inventories',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'InventoryAdjustmentDetail.current_inventory_id = CurrentInventory.id'
                                )
                            ),
                            array(
                                'alias' => 'Product',
                                'table' => 'products',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Product.id = CurrentInventory.product_id'
                                )
                            ),
                            array(
                                'alias' => 'TransactionType',
                                'table' => 'transaction_types',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'InventoryAdjustment.transaction_type_id = TransactionType.id'
                                )
                            ),
                        ),
                        'fields' => array('sum(InventoryAdjustmentDetail.quantity) AS qty ,CASE WHEN Product.parent_id>0 then Product.parent_id else CurrentInventory.product_id end product_id'),
                        'group' => array('CASE WHEN Product.parent_id>0 then Product.parent_id else CurrentInventory.product_id end'),
                        'recursive' => -1,
                    ));
                    //echo $this->Store->getLastQuery();
                    //pr($o_received_query_results);
                    //exit;

                    foreach ($o_received_query_results as $received_query_result) {
                        //others will do later
                        $received_results[$received_query_result['0']['product_id']]['from_orthers'] = sprintf('%01.2f', ($unit_type == 2) ? $received_query_result[0]['qty'] : $this->unit_convertfrombase($received_query_result['0']['product_id'], $product_measurement[$received_query_result['0']['product_id']], $received_query_result[0]['qty']));
                    }


                    //for received claim orthers
                    $con = array(
                        'Claim.received_date BETWEEN ? and ? ' => array($date_from, $date_to),
                        'Claim.status' => 2,
                        'Claim.isApproved' => 1,
                        'Store.store_type_id' => 2,
                    );
                    if ($office_ids) {
                        $con['Store.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $con['Store.office_id'] = $office_id;
                    }

                    //-- 0 means product received
                    //-- 1 means product issues

                    $claim_results = $this->Claim->find('all', array(
                        'conditions' => $con,

                        'joins' => array(
                            array(
                                'alias' => 'ClaimDetail',
                                'table' => 'claim_details',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'Claim.id = ClaimDetail.claim_id'
                                )
                            ),
                            array(
                                'alias' => 'Store',
                                'table' => 'stores',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'Claim.receiver_store_id = Store.id'
                                )
                            ),
                        ),

                        'fields' => array('ClaimDetail.claim_type as claim_type', 'SUM(ClaimDetail.claim_qty) as qty', 'ClaimDetail.product_id'),
                        'group' => array('ClaimDetail.claim_type', 'ClaimDetail.product_id'),
                        'recursive' => -1,
                    ));
                    /*$sql = "select cd.claim_type as claim_type,sum(cd.claim_qty) as total_qty, cd.product_id  from claims c
                            left join claim_details cd on c.id=cd.claim_id
                            left join stores s on c.receiver_store_id=s.id
                            where s.office_id IN(18)
                            and s.store_type_id=2
                            and c.challan_date='2018-10-23'
                            and c.status=2
                            and c.isApproved=1
                            group by cd.claim_type, cd.product_id";*/
                    //pr($claim_results);
                    //exit;

                    //echo $unit_type;

                    foreach ($claim_results as $claim_result) {
                        if ($claim_result[0]['claim_type'] == 0) {
                            /*@$received_results[$claim_result['ClaimDetail']['product_id']]['from_orthers']+= sprintf("%01.2f", ($unit_type==2)?$claim_result[0]['qty']:$this->unit_convertfrombase($claim_result['ClaimDetail']['product_id'], $product_measurement[$claim_result['ClaimDetail']['product_id']], $claim_result[0]['qty']));*/
                            @$received_results[$claim_result['ClaimDetail']['product_id']]['from_orthers'] += sprintf('%01.2f', ($unit_type == 1) ? $claim_result[0]['qty'] : $this->unit_convert($claim_result['ClaimDetail']['product_id'], $product_measurement[$claim_result['ClaimDetail']['product_id']], $claim_result[0]['qty']));
                        }
                    }

                    /*--------------- DB Return To Area : Start----------------------*/
                    /*
                    Added By : Naser
                    Added At : 20-July-2020 06:11 PM
                 */

                    $this->LoadModel('DistReturnChallan');
                    $con = array(
                        'DistReturnChallan.received_date BETWEEN ? and ? ' => array($date_from, $date_to),

                        'DistReturnChallan.status' => 2,
                    );

                    if ($office_ids) {
                        //$con['DistReturnChallan.receiver_store_id'] = $area_stores[$office_id];
                        $con['DistReturnChallan.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $con['DistReturnChallan.office_id'] = $office_id;
                    }


                    $con['DistReturnChallanDetail.is_ncp'] = 0;
                    $r_challan_q_results = $this->DistReturnChallan->find('all', array(
                        'conditions' => $con,
                        'joins' => array(
                            array(
                                'alias' => 'DistReturnChallanDetail',
                                'table' => 'dist_return_challan_details',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'DistReturnChallan.id = DistReturnChallanDetail.challan_id'
                                )
                            ),
                            array(
                                'alias' => 'Store',
                                'table' => 'stores',
                                'type' => 'INNER',
                                'conditions' => 'DistReturnChallan.receiver_store_id = Store.id'
                            ),
                            array(
                                'alias' => 'Product',
                                'table' => 'products',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'DistReturnChallanDetail.product_id = Product.id'
                                )
                            )
                        ),
                        'fields' => array(
                            'DistReturnChallanDetail.product_id',
                            'sum(DistReturnChallanDetail.challan_qty) as challan_qty'
                        ),
                        'group' => array(
                            'DistReturnChallanDetail.product_id'
                        ),
                        'recursive' => -1,
                    ));
                    foreach ($r_challan_q_results as $received_query_result) {
                        //others will do later
                        $received_results[$received_query_result['DistReturnChallanDetail']['product_id']]['db_return'] = sprintf('%01.2f', ($unit_type == 1) ? $received_query_result[0]['challan_qty'] : $this->unit_convert($received_query_result['DistReturnChallanDetail']['product_id'], $product_measurement[$received_query_result['DistReturnChallanDetail']['product_id']], $received_query_result[0]['challan_qty']));
                    }
                    /*--------------- DB Return To Area : END----------------------*/

                    /*pr($o_received_query_results);
                    exit;*/
                }


                //pr($received_results);
                //exit;

                $this->set(compact('received_results'));


                //FOR PRODUCT ISSUED
                if ($so_id || $territory_id) {
                    $con = array(
                        'ReturnChallan.received_date BETWEEN ? and ? ' => array($date_from, $date_to),
                        'ReturnChallan.transaction_type_id' => array(19), //so to aso
                        //'Store.store_type_id' => 2,
                    );
                    if ($office_ids) {
                        $con['Store.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $con['Store.office_id'] = $office_id;
                    }
                    if ($type == 'so') {
                        if ($so_id) {
                            $con['SalesPeople.id'] = $so_id;
                        }
                    } else {
                        if ($territory_id) {
                            $con['Store.territory_id'] = $territory_id;
                        }
                    }


                    $issue_query_results = $this->Store->find('all', array(
                        'conditions' => $con,
                        'joins' => array(
                            array(
                                'alias' => 'ReturnChallan',
                                'table' => 'return_challans',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Store.id = ReturnChallan.sender_store_id'
                                )
                            ),
                            array(
                                'alias' => 'ReturnChallanDetails',
                                'table' => 'return_challan_details',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'ReturnChallan.id = ReturnChallanDetails.challan_id'
                                )
                            ),
                            array(
                                'alias' => 'SalesPeople',
                                'table' => 'sales_people',
                                'type' => 'LEFT',
                                'conditions' => 'Store.territory_id = SalesPeople.territory_id'
                            ),
                            array(
                                'alias' => 'ProductMeasurement',
                                'table' => 'product_measurements',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'ProductMeasurement.product_id = ReturnChallanDetails.product_id AND ProductMeasurement.measurement_unit_id= ReturnChallanDetails.measurement_unit_id'
                                )
                            ),
                        ),

                        'fields' => array('sum(ROUND((ReturnChallanDetails.challan_qty*CASE WHEN ProductMeasurement.qty_in_base is null then 1 else ProductMeasurement.qty_in_base  END),0)) AS qty', 'ReturnChallanDetails.product_id', 'ReturnChallan.transaction_type_id'),
                        'group' => array('ReturnChallanDetails.product_id', 'ReturnChallan.transaction_type_id', 'ProductMeasurement.qty_in_base'),
                        'recursive' => -1,
                    ));
                    //echo $this->ModelName->getLastQuery();
                    //pr($issue_query_results);
                    //exit;

                    $issue_results = array();

                    foreach ($issue_query_results as $issue_query_result) {
                        $issue_results[$issue_query_result['ReturnChallanDetails']['product_id']]['to_cwh'] = '';

                        $issue_results[$issue_query_result['ReturnChallanDetails']['product_id']]['to_territory'] = '';

                        if ($issue_query_result['ReturnChallan']['transaction_type_id'] == 19) {
                            $issue_results[$issue_query_result['ReturnChallanDetails']['product_id']]['to_aso'] = sprintf('%01.2f', ($unit_type == 2) ? $issue_query_result[0]['qty'] : $this->unit_convertfrombase($issue_query_result['ReturnChallanDetails']['product_id'], $product_measurement[$issue_query_result['ReturnChallanDetails']['product_id']], $issue_query_result[0]['qty']));
                        }

                        //will do later
                        $issue_results[$issue_query_result['CurrentInventoryHistory']['product_id']]['to_orthers'] = '';
                    }

                    //For Issue Orther's
                    /*$con = array(
                    'CurrentInventoryHistory.transaction_date BETWEEN ? and ? ' => array($date_from, $date_to),
                    //'CurrentInventoryHistory.transaction_type_id' => 9,
                    'NOT' => array('CurrentInventoryHistory.transaction_type_id' => 28),
                    'TransactionType.inout' => 1,
                    'TransactionType.adjust' => 1,
                    'CurrentInventoryHistory.inventory_status_id' => 1,
                    //'Store.store_type_id' => 2,
                    );
                    if($office_ids)$con['Store.office_id'] = $office_ids;
                    if($office_id)$con['Store.office_id'] = $office_id;
                    if($type=='so'){
                        if($so_id)$con['SalesPeople.id'] = $so_id;
                    }else{
                        if($territory_id)$con['Store.territory_id'] = $territory_id;
                    }

                    //pr($con);
                    $o_issue_query_results = $this->Store->find('all', array(
                        'conditions' => $con,
                        'joins' => array(
                         array(
                                'alias' => 'CurrentInventoryHistory',
                                'table' => 'current_inventory_history',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Store.id = CurrentInventoryHistory.store_id'
                                )
                            ),
                            array(
                                'alias' => 'SalesPeople',
                                'table' => 'sales_people',
                                'type' => 'INNER',
                                'conditions' => 'Store.territory_id = SalesPeople.territory_id'
                            ),
                            array(
                                'alias' => 'TransactionType',
                                'table' => 'transaction_types',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'CurrentInventoryHistory.transaction_type_id = TransactionType.id'
                                )
                            ),
                         ),
                        'fields'=> array('sum(CurrentInventoryHistory.qty) AS qty', 'CurrentInventoryHistory.product_id', 'CurrentInventoryHistory.transaction_type_id'),
                        'group' => array('CurrentInventoryHistory.product_id', 'CurrentInventoryHistory.transaction_type_id'),
                        'recursive' => -1,
                    ));

                    //pr($o_issue_query_results);

                    foreach($o_issue_query_results as $issue_query_result)
                    {
                        //will do later
                        $issue_results[$issue_query_result['CurrentInventoryHistory']['product_id']]['to_orthers']= sprintf("%01.2f", ($unit_type==2)?$issue_query_result[0]['qty']:$this->unit_convertfrombase($issue_query_result['CurrentInventoryHistory']['product_id'], $product_measurement[$issue_query_result['CurrentInventoryHistory']['product_id']], $issue_query_result[0]['qty']));

                    }*/
                } else {
                    $con = array(
                        'Challan.challan_date BETWEEN ? and ? ' => array($date_from, $date_to),
                        'Challan.transaction_type_id' => array(2, 5, 28, 29),
                        'Challan.status >' => 0,
                        // 2 product isssue, when product received it will be changed to 5 (to territory)
                        // 28 product isssue, when product received it will be changed to 29 (to CWH)
                        'Store.store_type_id' => 2,
                    );
                    if ($office_ids) {
                        $con['Store.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $con['Store.office_id'] = $office_id;
                    }

                    $issue_query_results = $this->Store->find('all', array(
                        'conditions' => $con,

                        'joins' => array(
                            array(
                                'alias' => 'Challan',
                                'table' => 'challans',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Store.id = Challan.sender_store_id'
                                )
                            ),
                            array(
                                'alias' => 'ChallanDetails',
                                'table' => 'challan_details',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Challan.id = ChallanDetails.challan_id'
                                )
                            ),
                        ),

                        'fields' => array('sum(ChallanDetails.challan_qty) AS qty', 'ChallanDetails.product_id', 'Challan.transaction_type_id'),
                        'group' => array('ChallanDetails.product_id', 'Challan.transaction_type_id'),
                        'recursive' => -1,
                    ));

                    /*echo $this->Store->getLastQuery();
                    pr($issue_query_results);
                    exit;*/

                    $issue_results = array();

                    foreach ($issue_query_results as $issue_query_result) {
                        /*if($issue_query_result['Challan']['transaction_type_id']==5)
                        {
                            @$issue_results[$issue_query_result['ChallanDetails']['product_id']]['to_territory']+= sprintf("%01.2f", ($unit_type==1)?$issue_query_result[0]['qty']:$this->unit_convert($issue_query_result['ChallanDetails']['product_id'], $product_measurement[$issue_query_result['ChallanDetails']['product_id']], $issue_query_result[0]['qty']));
                        }*/
                        if ($issue_query_result['Challan']['transaction_type_id'] == 2 || $issue_query_result['Challan']['transaction_type_id'] == 5) {
                            @($issue_results[$issue_query_result['ChallanDetails']['product_id']]['to_territory']) ? $issue_results[$issue_query_result['ChallanDetails']['product_id']]['to_territory'] : $issue_results[$issue_query_result['ChallanDetails']['product_id']]['to_territory'] = 0;

                            @$issue_results[$issue_query_result['ChallanDetails']['product_id']]['to_territory'] += sprintf('%01.2f', ($unit_type == 1) ? $issue_query_result[0]['qty'] : $this->unit_convert($issue_query_result['ChallanDetails']['product_id'], $product_measurement[$issue_query_result['ChallanDetails']['product_id']], $issue_query_result[0]['qty']));
                        }


                        if ($issue_query_result['Challan']['transaction_type_id'] == 28) {
                            @$issue_results[$issue_query_result['ChallanDetails']['product_id']]['to_aso'] += sprintf('%01.2f', ($unit_type == 1) ? $issue_query_result[0]['qty'] : $this->unit_convert($issue_query_result['ChallanDetails']['product_id'], $product_measurement[$issue_query_result['ChallanDetails']['product_id']], $issue_query_result[0]['qty']));
                        }
                        if ($issue_query_result['Challan']['transaction_type_id'] == 29) {
                            @$issue_results[$issue_query_result['ChallanDetails']['product_id']]['to_aso'] += sprintf('%01.2f', ($unit_type == 1) ? $issue_query_result[0]['qty'] : $this->unit_convert($issue_query_result['ChallanDetails']['product_id'], $product_measurement[$issue_query_result['ChallanDetails']['product_id']], $issue_query_result[0]['qty']));
                        }


                        //others will do later
                        $issue_results[$issue_query_result['ChallanDetails']['product_id']]['to_orthers'] = '';
                    }

                    // pr($issue_results);
                    // exit;

                    /* -------------- Transit Qty : START----------------------- */

                    $con = array(
                        'Challan.challan_date BETWEEN ? and ? ' => array($date_from, $date_to),
                        /*'Challan.challan_date <=' => $date_to,*/
                        'Challan.status !=' => 0,
                        'OR' => array(
                            'Challan.received_date >' => $date_to,
                            'Challan.received_date is null',
                        ),
                        'Challan.transaction_type_id' => array(2, 5),
                        /*'Challan.status' => 2, */
                        // 2 product isssue, when product received it will be changed to 5 (to territory)
                        // 28 product isssue, when product received it will be changed to 29 (to CWH)
                        'Store.store_type_id' => 2,
                    );
                    if ($office_ids) {
                        $con['Store.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $con['Store.office_id'] = $office_id;
                    }

                    $issue_query_results = $this->Store->find('all', array(
                        'conditions' => $con,

                        'joins' => array(
                            array(
                                'alias' => 'Challan',
                                'table' => 'challans',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Store.id = Challan.sender_store_id'
                                )
                            ),
                            array(
                                'alias' => 'ChallanDetails',
                                'table' => 'challan_details',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Challan.id = ChallanDetails.challan_id'
                                )
                            ),
                        ),

                        'fields' => array('sum(ChallanDetails.challan_qty) AS qty', 'ChallanDetails.product_id', 'Challan.transaction_type_id'),
                        'group' => array('ChallanDetails.product_id', 'Challan.transaction_type_id'),
                        'recursive' => -1,
                    ));

                    /*echo $this->Store->getLastQuery();
                        pr($issue_query_results);
                        exit;*/


                    // $issue_results = array();

                    foreach ($issue_query_results as $issue_query_result) {
                        @($issue_results[$issue_query_result['ChallanDetails']['product_id']]['transit_qty']) ? $issue_results[$issue_query_result['ChallanDetails']['product_id']]['transit_qty'] : $issue_results[$issue_query_result['ChallanDetails']['product_id']]['transit_qty'] = 0;

                        @$issue_results[$issue_query_result['ChallanDetails']['product_id']]['transit_qty'] += sprintf('%01.2f', ($unit_type == 1) ? $issue_query_result[0]['qty'] : $this->unit_convert($issue_query_result['ChallanDetails']['product_id'], $product_measurement[$issue_query_result['ChallanDetails']['product_id']], $issue_query_result[0]['qty']));
                    }
                    // pr($issue_results);
                    // exit;
                    $con = array(
                        'Challan.received_date BETWEEN ? and ? ' => array($date_from, $date_to),
                        /*'Challan.challan_date <=' => $date_to,*/
                        'Challan.status !=' => 0,
                        'Challan.challan_date <' => $date_from,
                        'Challan.transaction_type_id' => array(2, 5),
                        /*'Challan.status' => 2, */
                        // 2 product isssue, when product received it will be changed to 5 (to territory)
                        // 28 product isssue, when product received it will be changed to 29 (to CWH)
                        'Store.store_type_id' => 2,
                    );
                    if ($office_ids) {
                        $con['Store.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $con['Store.office_id'] = $office_id;
                    }

                    $issue_query_results = $this->Store->find('all', array(
                        'conditions' => $con,

                        'joins' => array(
                            array(
                                'alias' => 'Challan',
                                'table' => 'challans',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Store.id = Challan.sender_store_id'
                                )
                            ),
                            array(
                                'alias' => 'ChallanDetails',
                                'table' => 'challan_details',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Challan.id = ChallanDetails.challan_id'
                                )
                            ),
                        ),

                        'fields' => array('sum(ChallanDetails.challan_qty) AS qty', 'ChallanDetails.product_id', 'Challan.transaction_type_id'),
                        'group' => array('ChallanDetails.product_id', 'Challan.transaction_type_id'),
                        'recursive' => -1,
                    ));

                    /*echo $this->Store->getLastQuery();
                        pr($issue_query_results);
                        exit;*/


                    // $issue_results = array();

                    foreach ($issue_query_results as $issue_query_result) {
                        @($issue_results[$issue_query_result['ChallanDetails']['product_id']]['transit_qty_rcv']) ? $issue_results[$issue_query_result['ChallanDetails']['product_id']]['transit_qty_rcv'] : $issue_results[$issue_query_result['ChallanDetails']['product_id']]['transit_qty_rcv'] = 0;

                        @$issue_results[$issue_query_result['ChallanDetails']['product_id']]['transit_qty_rcv'] += sprintf('%01.2f', ($unit_type == 1) ? $issue_query_result[0]['qty'] : $this->unit_convert($issue_query_result['ChallanDetails']['product_id'], $product_measurement[$issue_query_result['ChallanDetails']['product_id']], $issue_query_result[0]['qty']));
                    }
                    /* -------------- Transit Qty : END ------------------------ */

                    //product issue aso to chw
                    $con = array(
                        'ReturnChallan.challan_date BETWEEN ? and ? ' => array($date_from, $date_to),
                        'ReturnChallan.transaction_type_id' => array(7, 22),
                        'Store.store_type_id' => 2,
                        'ReturnChallan.status !=' => 0,
                    );
                    if ($office_ids) {
                        $con['Store.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $con['Store.office_id'] = $office_id;
                    }

                    $issue_query_results = $this->Store->find('all', array(
                        'conditions' => $con,

                        'joins' => array(
                            array(
                                'alias' => 'ReturnChallan',
                                'table' => 'return_challans',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Store.id = ReturnChallan.sender_store_id'
                                )
                            ),
                            array(
                                'alias' => 'ReturnChallanDetails',
                                'table' => 'return_challan_details',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'ReturnChallan.id = ReturnChallanDetails.challan_id'
                                )
                            ),
                        ),

                        'fields' => array('sum(ReturnChallanDetails.challan_qty) AS qty', 'ReturnChallanDetails.product_id', 'ReturnChallan.transaction_type_id'),
                        'group' => array('ReturnChallanDetails.product_id', 'ReturnChallan.transaction_type_id'),
                        'recursive' => -1,
                    ));
                    //echo $this->Store->getLastQuery();
                    //pr($issue_query_results);
                    //exit;

                    //$issue_results = array();

                    foreach ($issue_query_results as $issue_query_result) {
                        if (@$issue_query_result['ReturnChallan']['transaction_type_id'] == 22) {
                            @$issue_results[$issue_query_result['ReturnChallanDetails']['product_id']]['to_cwh'] += sprintf('%01.2f', ($unit_type == 1) ? $issue_query_result[0]['qty'] : $this->unit_convert($issue_query_result['ReturnChallanDetails']['product_id'], $product_measurement[$issue_query_result['ReturnChallanDetails']['product_id']], $issue_query_result[0]['qty']));
                        }
                        if (@$issue_query_result['ReturnChallan']['transaction_type_id'] == 7) {
                            @$issue_results[$issue_query_result['ReturnChallanDetails']['product_id']]['to_cwh'] += sprintf('%01.2f', ($unit_type == 1) ? $issue_query_result[0]['qty'] : $this->unit_convert($issue_query_result['ReturnChallanDetails']['product_id'], $product_measurement[$issue_query_result['ReturnChallanDetails']['product_id']], $issue_query_result[0]['qty']));
                        }
                    }


                    //For Issue Orther's
                    $con = array(
                        'Convert(Date, InventoryAdjustment.created_at) BETWEEN ? and ? ' => array($date_from, $date_to),
                        'TransactionType.inout' => 1,
                        'TransactionType.adjust' => 1,
                        'InventoryAdjustment.approval_status' => 1
                    );
                    if ($office_ids) {
                        $con['Store.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $con['Store.office_id'] = $office_id;
                    }

                    $o_issue_query_results = $this->Store->find('all', array(
                        'conditions' => $con,
                        'joins' => array(
                            array(
                                'alias' => 'InventoryAdjustment',
                                'table' => 'inventory_adjustments',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Store.id = InventoryAdjustment.store_id'
                                )
                            ),
                            array(
                                'alias' => 'InventoryAdjustmentDetail',
                                'table' => 'inventory_adjustment_details',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'InventoryAdjustment.id = InventoryAdjustmentDetail.inventory_adjustment_id'
                                )
                            ),
                            array(
                                'alias' => 'CurrentInventory',
                                'table' => 'current_inventories',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'InventoryAdjustmentDetail.current_inventory_id = CurrentInventory.id'
                                )
                            ),
                            array(
                                'alias' => 'Product',
                                'table' => 'products',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Product.id = CurrentInventory.product_id'
                                )
                            ),
                            array(
                                'alias' => 'TransactionType',
                                'table' => 'transaction_types',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'InventoryAdjustment.transaction_type_id = TransactionType.id'
                                )
                            ),
                        ),
                        'fields' => array('sum(InventoryAdjustmentDetail.quantity) AS qty, CASE WHEN Product.parent_id>0 then Product.parent_id else CurrentInventory.product_id end product_id', 'InventoryAdjustment.transaction_type_id'),
                        'group' => array('CASE WHEN Product.parent_id>0 then Product.parent_id else CurrentInventory.product_id end', 'InventoryAdjustment.transaction_type_id'),
                        'recursive' => -1,
                    ));
                    //echo $this->Store->getLastQuery();
                    //pr($o_issue_query_results);
                    //exit;

                    foreach ($o_issue_query_results as $issue_query_result) {
                        @$issue_results[$issue_query_result['0']['product_id']]['to_orthers'] += sprintf('%01.2f', ($unit_type == 2) ? $issue_query_result[0]['qty'] : $this->unit_convertfrombase($issue_query_result['0']['product_id'], $product_measurement[$issue_query_result['0']['product_id']], $issue_query_result[0]['qty']));
                    }


                    //for issue claim orthers
                    $con = array(
                        'Claim.challan_date BETWEEN ? and ? ' => array($date_from, $date_to),
                        'Claim.status' => 2,
                        'Claim.isApproved' => 1,
                        'Store.store_type_id' => 2,
                    );
                    if ($office_ids) {
                        $con['Store.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $con['Store.office_id'] = $office_id;
                    }
                    //-- 0 means product received
                    //-- 1 means product issues

                    $claim_results = $this->Claim->find('all', array(
                        'conditions' => $con,

                        'joins' => array(
                            array(
                                'alias' => 'ClaimDetail',
                                'table' => 'claim_details',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'Claim.id = ClaimDetail.claim_id'
                                )
                            ),
                            array(
                                'alias' => 'Store',
                                'table' => 'stores',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'Claim.receiver_store_id = Store.id'
                                )
                            ),
                        ),

                        'fields' => array('ClaimDetail.claim_type as claim_type', 'SUM(ClaimDetail.claim_qty) as qty', 'ClaimDetail.product_id'),
                        'group' => array('ClaimDetail.claim_type', 'ClaimDetail.product_id'),
                        'recursive' => -1,
                    ));
                    /*$sql = "select cd.claim_type as claim_type,sum(cd.claim_qty) as total_qty, cd.product_id  from claims c
                            left join claim_details cd on c.id=cd.claim_id
                            left join stores s on c.receiver_store_id=s.id
                            where s.office_id IN(18)
                            and s.store_type_id=2
                            and c.challan_date='2018-10-23'
                            and c.status=2
                            and c.isApproved=1
                            group by cd.claim_type, cd.product_id";*/
                    //pr($claim_results);
                    //exit;

                    //echo $unit_type;

                    foreach ($claim_results as $claim_result) {
                        if ($claim_result[0]['claim_type'] == 1) {
                            /*@$issue_results[$claim_result['ClaimDetail']['product_id']]['from_orthers']+= sprintf("%01.2f", ($unit_type==2)?$claim_result[0]['qty']:$this->unit_convertfrombase($claim_result['ClaimDetail']['product_id'], $product_measurement[$claim_result['ClaimDetail']['product_id']], $claim_result[0]['qty']));*/
                            @$issue_results[$claim_result['ClaimDetail']['product_id']]['from_orthers'] += sprintf('%01.2f', ($unit_type == 1) ? $claim_result[0]['qty'] : $this->unit_convert($claim_result['ClaimDetail']['product_id'], $product_measurement[$claim_result['ClaimDetail']['product_id']], $claim_result[0]['qty']));
                        }
                    }


                    /*-------------------- DB Memo part : Start --------------------*/
                    /*
                    Added by Naser
                    Date : 20-July-2020 08:44 PM
                 */
                    $con = array(
                        'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
                        'Memo.status !=' => 0,
                        'Territory.name LIKE' => '%corporate%'
                    );

                    if ($office_ids) {
                        $con['Memo.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $con['Memo.office_id'] = $office_id;
                    }

                    $dist_issue = $this->Memo->find('all', array(
                        'conditions' => $con,
                        'joins' => array(
                            array(
                                'table' => 'memo_details',
                                'alias' => 'MemoDetail',
                                'conditions' => 'Memodetail.memo_id=Memo.id'
                            ),
                            array(
                                'table' => 'products',
                                'alias' => 'Product',
                                'conditions' => 'Memodetail.product_id=Product.id'
                            ),
                            array(
                                'table' => 'territories',
                                'alias' => 'Territory',
                                // 'conditions' => 'Memo.territory_id=Territory.id'
                                'conditions' => 'CASE WHEN Memo.child_territory_id is null or Memo.child_territory_id=0 then Memo.territory_id else Memo.child_territory_id end = Territory.id'


                            ),
                            array(
                                'table' => 'product_measurements',
                                'alias' => 'PM',
                                'type' => 'left',
                                'conditions' => 'PM.product_id=Product.id AND 
							PM.measurement_unit_id = 
								case when MemoDetail.measurement_unit_id is null 
									or MemoDetail.measurement_unit_id=0 
								then Product.sales_measurement_unit_id 
								else MemoDetail.measurement_unit_id end'
                            ),
                        ),
                        'recursive' => -1,
                        'fields' => array('MemoDetail.product_id', 'SUM(ROUND((MemoDetail.sales_qty * case when PM.qty_in_base is null then 1 else PM.qty_in_base end),0)) as challan_qty'),
                        'group' => array('MemoDetail.product_id'),
                    ));
                    foreach ($dist_issue as $issue_query_result) {
                        @$issue_results[$issue_query_result['MemoDetail']['product_id']]['db_issue'] += sprintf('%01.2f', ($unit_type == 2) ? $issue_query_result[0]['challan_qty'] : $this->unit_convertfrombase($issue_query_result['MemoDetail']['product_id'], $product_measurement[$issue_query_result['MemoDetail']['product_id']], $issue_query_result[0]['challan_qty']));
                    }
                    /*-------------------- DB Memo part : END --------------------*/
                    /*pr($o_issue_query_results);
                    exit;*/
                }
                //pr($issue_results);
                //exit;

                $this->set(compact('issue_results'));


                //pr($pro_conditions);
                /*$product_list = $this->Product->find('list', array(
                    'conditions'=> $pro_conditions,
                    'order'=>  array('order'=>'asc')
                ));*/

                /*$pro_conditions = array(
                    //'NOT' => array('Product.product_category_id'=>32),
                    'is_active' => 1,
                    'Product.id'=>65
                );*/

                $pro_conditions['is_virtual'] = 0;

                $product_results = $this->Product->find('all', array(
                    'conditions' => $pro_conditions,
                    'joins' => array(
                        array(
                            'alias' => 'ProductType',
                            'table' => 'product_type',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Product.product_type_id = ProductType.id'
                            )
                        ),
                    ),
                    'fields' => array('Product.id', 'Product.name', 'Product.product_type_id', 'ProductType.name', 'Product.source'),
                    'recursive' => -1,
                    'order' => array('Product.product_type_id' => 'asc', 'Product.source' => 'desc', 'Product.order' => 'asc',)
                ));

                $product_list = array();

                foreach ($product_results as $product_result) {
                    $product_list[$product_result['Product']['source']][$product_result['ProductType']['name']][$product_result['Product']['id']] = array(
                        'product_id' => $product_result['Product']['id'],
                        'product_name' => $product_result['Product']['name'],
                        'product_type_id' => $product_result['Product']['product_type_id'],
                        'product_type_name' => $product_result['ProductType']['name'],
                        'Product' => $product_result['Product']['source'],
                    );
                }
            } elseif ($reports_type == 2) {
                if (empty($so_id) && empty($territory_id)) {
                    $con = array(
                        'Convert(Date, InventoryAdjustment.created_at) BETWEEN ? and ? ' => array($date_from, $date_to),
                        'InventoryAdjustment.approval_status' => 1
                    );
                    if ($office_ids) {
                        $con['Store.office_id'] = $office_ids;
                    }
                    if ($office_id) {
                        $con['Store.office_id'] = $office_id;
                    }
                    if ($source) {
                        $con['Product.source'] = $source;
                    }
                    if ($product_type_id) {
                        $con['Product.product_type_id'] = $product_type_id;
                    }
                    $adjustment_query = $this->Store->find('all', array(
                        'conditions' => $con,
                        'joins' => array(
                            array(
                                'alias' => 'InventoryAdjustment',
                                'table' => 'inventory_adjustments',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Store.id = InventoryAdjustment.store_id'
                                )
                            ),
                            array(
                                'alias' => 'InventoryAdjustmentDetail',
                                'table' => 'inventory_adjustment_details',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'InventoryAdjustment.id = InventoryAdjustmentDetail.inventory_adjustment_id'
                                )
                            ),
                            array(
                                'alias' => 'CurrentInventory',
                                'table' => 'current_inventories',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'InventoryAdjustmentDetail.current_inventory_id = CurrentInventory.id'
                                )
                            ),
                            array(
                                'alias' => 'Product',
                                'table' => 'products',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'Product.id = CurrentInventory.product_id'
                                )
                            ),
                            array(
                                'alias' => 'TransactionType',
                                'table' => 'transaction_types',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'InventoryAdjustment.transaction_type_id = TransactionType.id'
                                )
                            ),
                        ),
                        'fields' => array('sum(InventoryAdjustmentDetail.quantity) AS qty', 'CurrentInventory.product_id', 'InventoryAdjustment.transaction_type_id', 'TransactionType.inout', 'Product.id', 'Product.name', 'Product.product_type_id', 'Product.source'),
                        'group' => array('CurrentInventory.product_id', 'InventoryAdjustment.transaction_type_id', 'TransactionType.inout', 'Product.id', 'Product.name', 'Product.product_type_id', 'Product.source', 'Product.order'),
                        'order' => array('Product.product_type_id' => 'asc', 'Product.source' => 'desc', 'Product.order' => 'asc',),
                        'recursive' => -1,
                    ));

                    $adjustment_result = array();
                    $adjustment_result_product = array();
                    foreach ($adjustment_query as $adjustment_query_result) {
                        @$adjustment_result_product[$adjustment_query_result['TransactionType']['inout']][$adjustment_query_result['Product']['id']] =
                            array(
                                'product' => $adjustment_query_result['Product']['name']
                            );
                        @$adjustment_result[$adjustment_query_result['TransactionType']['inout']][$adjustment_query_result['Product']['id']][$adjustment_query_result['InventoryAdjustment']['transaction_type_id']] += sprintf('%01.2f', ($unit_type == 2) ? $adjustment_query_result[0]['qty'] : $this->unit_convertfrombase($adjustment_query_result['CurrentInventory']['product_id'], $product_measurement[$adjustment_query_result['CurrentInventory']['product_id']], $adjustment_query_result[0]['qty']));
                    }
                    $this->loadModel('TransactionType');
                    $adjustment_in = $this->TransactionType->find('list', array(
                        'conditions' => array(
                            'TransactionType.adjust' => 1,
                            'TransactionType.inout' => 2
                        )
                    ));
                    $adjustment_out = $this->TransactionType->find('list', array(
                        'conditions' => array(
                            'TransactionType.adjust' => 1,
                            'TransactionType.inout' => 1
                        )
                    ));
                    $this->set(compact('adjustment_in', 'adjustment_out', 'adjustment_result', 'adjustment_result_product'));
                }
            }
        }
        $this->set(compact('offices', 'territories', 'outlet_type', 'region_offices', 'office_id', 'request_data', 'so_list', 'product_list'));
    }
}
