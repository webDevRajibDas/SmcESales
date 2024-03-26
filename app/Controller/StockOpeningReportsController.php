<?php
App::uses('AppController', 'Controller');

/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class StockOpeningReportsController extends AppController
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
    public function admin_index($id = null){

        //$this->dd('stock_opening_reports');exit();

        $this->Session->delete('detail_results');
        $this->Session->delete('outlet_lists');

        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes

        $request_data = array();

        $this->set('page_title', "Stock Opening Report");


        //for product type
        $product_types = $this->Product->ProductType->find('list', array('order' => array('name' => 'asc')));
        $this->set(compact('product_types'));

        $product_categories = $this->Product->ProductCategory->find('list', array('order' => array('name' => 'asc')));
        $this->set(compact('product_categories'));


        //pr($offices);
        $so_opening_stock = array();
        $aso_opening_stock = array();
        $office_array = array();
        $result_array = array();
        $product_categories = array();
        $products = array();


        if ($this->request->is('post') || $this->request->is('put')) {
            $request_data = $this->request->data;


            //OPENING STOCK
            $day = date('Y-m-d', strtotime($request_data['StockOpeningReports']['day']));
            $product_type_id = $request_data['StockOpeningReports']['product_type_id'];
            $product_category_id = $request_data['StockOpeningReports']['product_category_id'];
            $product_id = $request_data['StockOpeningReports']['product_id'];

            $product_type_id = isset($this->request->data['StockOpeningReports']['product_type_id']) != '' ? $this->request->data['StockOpeningReports']['product_type_id'] : $product_type_id;
            $product_category_id = isset($this->request->data['StockOpeningReports']['product_category_id']) != '' ? $this->request->data['StockOpeningReports']['product_category_id'] : $product_category_id;
            $product_id = isset($this->request->data['StockOpeningReports']['product_id']) != '' ? $this->request->data['StockOpeningReports']['product_id'] : $product_id;
            if ($product_type_id) {


                $product_category = $this->ProductCategory->find('all', array(
                    'fields' => array('ProductCategory.id', 'ProductCategory.name'),

                    'recursive' => 0
                ));


                foreach ($product_category as $list_r) {
                    $product_categories[$list_r['ProductCategory']['id']] = $list_r['ProductCategory']['name'];
                }
            }

            if ($product_category_id) {
                //after submit selected value
                $products_list = $this->Product->find('all', array(
                    'fields' => array('Product.id', 'Product.name'),
                    'conditions' => array('Product.product_category_id' => $product_category_id),
                    'order' => array('Product.order' => 'ASC'),
                    'recursive' => 0
                ));
                foreach ($products_list as $list_r) {
                    $products[$list_r['Product']['id']] = $list_r['Product']['name'];
                }
            }
            //condition add for tso opening
            $con = array(
                'RptDailyTranBalance.tran_date' => $day,
                //'CurrentInventory.inventory_status_id' => 1,
                'Store.store_type_id' => 2,
            );

            if ($product_type_id) {
                $con['Product.product_type_id'] = $product_type_id;
            }
            if ($product_category_id) {
                $con['Product.product_category_id'] = $product_category_id;
            }

            if ($product_id) {
                $con['Product.id'] = $product_id;
            }
            // pr($con);
            // $aso_stock_opening_results_sql="Select sum(RptDailyTranBalance.opening_balance) AS opening_balance_tso, Store.office_id from stores as Store"


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
                        'conditions' => array(
                            'Product.id = RptDailyTranBalance.product_id'
                        )
                    )
                ),
                'fields' => array('sum(RptDailyTranBalance.opening_balance) AS opening_balance_tso', 'Store.office_id'),
                'group' => array('Store.office_id'),
                'recursive' => -1,
            ));
            // echo $this->Store->getLastQuery();


            foreach ($aso_stock_opening_results as $key => $value) {
                $aso_opening_stock[$value['Store']['office_id']] = $value[0]['opening_balance_tso'];
            }
            // pr($aso_opening_stock);
            //OPENING BALANCE for SO
            // $so_con = array(
            //     'RptDailyTranBalance.tran_date' => $day,


            //     //'CurrentInventory.inventory_status_id' => 1,
            //     'Store.store_type_id' => 3,
            // );
            $sql_condition = ' ';
            if ($product_type_id) {
                // $so_con['Product.product_type_id'] = $product_type_id;
                $sql_condition .= " AND [Product].[product_type_id] = " . $product_type_id;
            }
            if ($product_category_id) {
                // $so_con['Product.product_category_id'] = $product_category_id;
                $sql_condition .= " AND [Product].[product_category_id] = " . $product_category_id;
            }

            if ($product_id) {
                // $so_con['Product.id'] = $product_id;
                $sql_condition .= " AND [Product].[id] = " . $product_id;
            }
            // pr($so_con);

            $so_sql = "SELECT sum(RptDailyTranBalance.opening_balance) AS opening_balance_so, [Store].[office_id] AS [Store__office_id] 
                        FROM [stores] AS [Store] 
                        INNER JOIN [rpt_daily_tran_balance] AS [RptDailyTranBalance] ON ([Store].[id] = [RptDailyTranBalance].[store_id]) 
                        INNER JOIN [products] AS [Product] ON ([Product].[id] = [RptDailyTranBalance].[product_id]) 
                        WHERE [RptDailyTranBalance].[tran_date] = '" . $day . "' 
                        AND [Store].[store_type_id] = 3 
                        " . $sql_condition . " 
                        GROUP BY [Store].[office_id]";

            // echo  $so_sql;

            $so_stock_opening_results = $this->Store->query($so_sql); // all active SR

            // $so_stock_opening_results = $this->Store->find('all', array(
            //     'conditions' => $so_con,
            //     'joins' => array(
            //         array(
            //             'alias' => 'RptDailyTranBalance',
            //             'table' => 'rpt_daily_tran_balance',
            //             'type' => 'INNER',
            //             'conditions' => array(
            //                 'Store.id = RptDailyTranBalance.store_id'
            //             ),
            //             array(
            //                 'alias' => 'Product',
            //                 'table' => 'products',
            //                 'type' => 'INNER',
            //                 'conditions' => array(
            //                     'Product.id = RptDailyTranBalance.product_id'
            //                 )
            //             )
            //         ),

            //     ),
            //     'fields' => array('sum(RptDailyTranBalance.opening_balance) AS opening_balance_so', 'Store.office_id'),
            //     'group' => array('Store.office_id'),
            //     'recursive' => -1,
            // ));
            // echo $this->Store->getLastQuery();exit;
            // pr($so_stock_opening_results);

            foreach ($so_stock_opening_results as $key => $value) {
                $so_opening_stock[$value[0]['Store__office_id']] = $value[0]['opening_balance_so'];
            }
            // pr($so_opening_stock);exit;


        }


        // $offices = $this->Office->find('all', array(
        //     'fields' => array('Office.id',  'Office.office_name'),
        //     'condition' =>  array('Office.office_type_id' => 2),  
        //     'order' => array('Office.order' => 'asc'),
        //     'recursive' => 0,
        // ));

        $office_sql = "Select Office.id as id,Office.office_name as office_name from offices as Office where Office.office_type_id = 2 order by Office.[order]";

        $offices = $this->Office->query($office_sql);
        // pr($offices);exit;
        foreach ($offices as $key => $value) {
            $office_array[] = array(
                'id' => $value[0]['id'],
                'name' => $value[0]['office_name'],
            );
        }
        // pr($office_array);exit;
        foreach ($office_array as $key => $office) {
            if (array_key_exists($office['id'], $aso_opening_stock)) {
                $result_array[$office['id']]['tso_opening'] = $aso_opening_stock[$office['id']];
            } else {
                $result_array[$office['id']]['tso_opening'] = 0;
            }
            if (array_key_exists($office['id'], $so_opening_stock)) {
                $result_array[$office['id']]['so_opening'] = $so_opening_stock[$office['id']];
            } else {
                $result_array[$office['id']]['so_opening'] = 0;
            }


        }


        // pr($result_array);exit;

        $this->set(compact('result_array', 'office_array', 'request_data', 'product_categories', 'products'));


    }


    public function get_product_category()
    {
        $view = new View($this);

        $form = $view->loadHelper('Form');

        $product_type_id = $this->request->data['product_type_id'];

        $day = date('Y-m-d', strtotime($this->request->data['day']));


        //$conditions = array('SalesPerson.territory_id >' => 0, 'User.user_group_id' => 4, 'User.active' => 1);
        if ($product_type_id && $day) {


            $product_categories = array();


            $so_list_r = $this->ProductCategory->find('all', array(
                'fields' => array('ProductCategory.id', 'ProductCategory.name'),

                'recursive' => 0
            ));
            foreach ($so_list_r as $list_r) {
                $product_categories[$list_r['ProductCategory']['id']] = $list_r['ProductCategory']['name'];
            }

        }
        // pr($so_list);


        if ($product_categories) {
            $output = '<option value="">---- All -----</option>';
            foreach ($product_categories as $key => $product_category) {
                $output .= '<option value="' . $key . '">' . $product_category . '</option>';
            }

            echo $output;

            /*$form->create('MarketCharacteristicReports', array('role' => 'form', 'action'=>'index'))	;
            echo $form->input('so_id', array('label' => 'Sales Officers', 'id' => 'so_id', 'class' => 'form-control so_id', 'required'=>false, 'options' => $so_list, 'empty'=>'---- All ----'));
            $form->end();*/

        } else {
            echo '<option value="">---- All -----</option>';
        }


        $this->autoRender = false;
    }

    public function get_product_list()
    {
        $view = new View($this);

        $form = $view->loadHelper('Form');

        $product_category_id = $this->request->data['product_category_id'];


        //$conditions = array('SalesPerson.territory_id >' => 0, 'User.user_group_id' => 4, 'User.active' => 1);
        if ($product_category_id) {


            $products = array();


            $so_list_r = $this->Product->find('all', array(
                'fields' => array('Product.id', 'Product.name'),
                'conditions' => array('Product.product_category_id' => $product_category_id),
                'order' => array('Product.order' => 'ASC'),
                'recursive' => 0
            ));
            foreach ($so_list_r as $list_r) {
                $products[$list_r['Product']['id']] = $list_r['Product']['name'];
            }

        }
        // pr($so_list);


        if ($products) {
            $output = '<option value="">---- All -----</option>';
            foreach ($products as $key => $product) {
                $output .= '<option value="' . $key . '">' . $product . '</option>';
            }

            echo $output;

            /*$form->create('MarketCharacteristicReports', array('role' => 'form', 'action'=>'index'))	;
            echo $form->input('so_id', array('label' => 'Sales Officers', 'id' => 'so_id', 'class' => 'form-control so_id', 'required'=>false, 'options' => $so_list, 'empty'=>'---- All ----'));
            $form->end();*/

        } else {
            echo '';
        }


        $this->autoRender = false;
    }
}
