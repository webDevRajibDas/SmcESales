<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 1000); //300 seconds = 5 minutes
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package     app.Controller
 * @link        http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    public $theme = "CakeAdminLTE";
    var $helpers = array('App', 'Form', 'Html', 'Session', 'Js', 'Usermgmt.UserAuth');
    public $components = array('Session', 'RequestHandler', 'App', 'Usermgmt.UserAuth');

    function beforeFilter()
    {

        /* if($this->request->params['controller'] == 'products'){
          return true;
      } */
        /* if (isset($this->request->query['report'])) {
            $session_id = base64_decode($this->request->query['report']);

            session_write_close(); // close any session that has already initialized
            CakeSession::id($session_id);
            session_start();
        } */
        if ($this->UserAuth->getOfficeId() == '' and $this->request->params['action'] == 'login') {
            return true;
        } elseif (
            $this->UserAuth->getOfficeId() == ''
            and $this->request->params['action'] != 'login'
            and $this->request->params['action'] != 'delete_permission'
            /*AND $this->request->params['controller'] != 'api_data_retrives'
                AND $this->request->params['controller'] != 'api_data164_retrives'
                AND $this->request->params['controller'] != 'api_data166_retrives'
                AND $this->request->params['controller'] != 'api_data168_retrives'
                AND $this->request->params['controller'] != 'api_data1610_retrives'
                AND $this->request->params['controller'] != 'api_data1611_retrives'
                AND $this->request->params['controller'] != 'api_data1613_retrives'
                AND $this->request->params['controller'] != 'api_data170_retrives'*/
            and substr($this->request->params['controller'], 0, 8) != 'api_data'
        ) {
            $this->Session->setFlash('You need to be signed in to view this page.');
            $this->redirect(MAIN_URL . 'admin/login');
        }


        if (
            $this->RequestHandler->isAjax()
            or $this->request->params['action'] == 'delete_permission'
            /*OR $this->request->params['controller'] == 'api_data_retrives'
            OR $this->request->params['controller'] == 'api_data164_retrives'
            OR $this->request->params['controller'] == 'api_data166_retrives'
            OR $this->request->params['controller'] == 'api_data168_retrives'
            OR $this->request->params['controller'] == 'api_data1610_retrives'
            OR $this->request->params['controller'] == 'api_data1611_retrives'
            OR $this->request->params['controller'] == 'api_data1613_retrives'
            OR $this->request->params['controller'] == 'api_data170_retrives'*/
            or substr($this->request->params['controller'], 0, 8) == 'api_data'
        ) {
        } else {
            if (isset($GLOBALS['product_wise_measurement']) || empty($GLOBALS['product_wise_measurement'])) {
                $GLOBALS['product_wise_measurement'] = array();
                $this->loadModel('ProductMeasurement');
                $unit_info = $this->ProductMeasurement->find('all', array('recursive' => -1));
                $product_wise_unit = array();
                foreach ($unit_info as $data) {
                    $product_wise_unit[$data['ProductMeasurement']['product_id']][$data['ProductMeasurement']['measurement_unit_id']] = $data['ProductMeasurement']['qty_in_base'];
                }
                $GLOBALS['product_wise_measurement'] = $product_wise_unit;
            }
            $this->userAuth();
            /*  $report_menu = $this->report_link_array();
			if (!in_array($this->request->params['controller'], $report_menu)) {
                $this->redirect(MAIN_URL . $this->request->url);
            } */
            $this->set('page_title', 'Admin Login');
            $side_menu = $this->side_menu();
            $this->set('menu', $side_menu);
        }
        //parent::beforeFilter();
    }

    private function userAuth()
    {
        $this->UserAuth->beforeFilter($this);
    }

    private function side_menu()
    {
        $main_url = MAIN_URL;
        $report_url = REPORT_URL;


        $session_id = base64_encode(session_id());


        //$session_id = "";


        $menu = array(

            'products' => array(
                'title' => 'Products', 'controller' => 'products', 'action' => 'admin_index', 'icon' => '<i class="fa fa-shopping-cart"></i>', 'scroll' => '0', 'url' => $main_url,
                'child' => array(
                    'products' => array('title' => 'Product List', 'controller' => 'products', 'action' => 'admin_index', 'url' => $main_url),

                    'product_categories' => array('title' => 'Product Category List', 'controller' => 'product_categories', 'action' => 'admin_index', 'url' => $main_url),
                    'product_groups' => array('title' => 'Product Group List', 'controller' => 'product_groups', 'action' => 'admin_index', 'url' => $main_url),
                    'product_combinations' => array('title' => 'Product Combination List', 'controller' => 'product_combinations', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'product_prices' => array('title' => 'Product Price List', 'controller' => 'product_prices', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'open_combinations' => array('title' => 'Price Open Combination', 'controller' => 'open_combinations', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'measurement_units' => array('title' => 'Measurement Unit List', 'controller' => 'measurement_units', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'brands' => array('title' => 'Brand List', 'controller' => 'brands', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'variants' => array('title' => 'Variant List', 'controller' => 'variants', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'bonuses' => array('title' => 'Bonus List', 'controller' => 'bonuses', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'bonus_combinations' => array('title' => 'Bonus Open Combination', 'controller' => 'bonus_combinations', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'product_convert_histories' => array('title' => 'Product Convert', 'controller' => 'product_convert_histories', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'bonus_cards' => array('title' => 'Bonus Cards', 'controller' => 'bonus_cards', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'bonus_card_types' => array('title' => 'Bonus Card Type', 'controller' => 'bonus_card_types', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    //'dist_product_combinations' => array('title' => 'Distributor Product Combination List', 'controller' => 'dist_product_combinations', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    //'dist_product_prices' => array('title' => 'Distributor Product Price List', 'controller' => 'dist_product_prices', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'DistBonusCombinations' => array('title' => 'SR Bonus Open Combination', 'controller' => 'DistBonusCombinations', 'action' => 'admin_index', 'icon' => '<i class="fa fa-map-marker"></i>', 'scroll' => '500', 'url' => $main_url),
                    'special_product_combinations' => array('title' => 'Hotel & Resturent Product Combination List', 'controller' => 'special_product_combinations', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'special_product_prices' => array('title' => 'Hotel & Resturent Product Price List', 'controller' => 'special_product_prices', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'web_current_prices' => array('title' => 'Current Price', 'controller' => 'web_current_prices', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'product_prices_v2' => array('title' => 'Product Price List(V2)', 'controller' => 'product_prices_v2', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'special_groups' => array('title' => 'Special Group', 'controller' => 'special_groups', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'sr_special_groups' => array('title' => 'Special Group(SR)', 'controller' => 'sr_special_groups', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'combinations_v2' => array('title' => 'Product Combination List(v2)', 'controller' => 'combinations_v2', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'discount_bonus_policies' => array('title' => 'Discount Bonus Policy(V2)', 'controller' => 'discount_bonus_policies', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'vatexecuting_products' => array('title' => 'Vat Executing Products', 'controller' => 'vatexecuting_products', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'AreaOfficeRequisitionReport' => array('title' => 'Area Office Requisition Report', 'controller' => 'AreaOfficeRequisitionReport', 'action' => 'admin_index', 'icon' => '<i class="fa fa-bars" ></i>', 'scroll' => '2150', 'url' => $main_url),
                )
            ),
            'current_inventories' => array(
                'title' => 'Inventory', 'controller' => 'current_inventories', 'action' => 'admin_index', 'icon' => '<i class="fa fa-truck"></i>', 'scroll' => '0', 'url' => $main_url,

                'child' => array(
                    'challans' => array('title' => 'Challan', 'controller' => 'challans', 'action' => 'admin_index', 'icon' => '<i class="fa fa-truck"></i>', 'url' => $main_url),
                    'ncp_challans' => array('title' => 'NCP Challan', 'controller' => 'ncp_challans', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'return_challans' => array('title' => 'Return Challan ASO to CWH', 'controller' => 'return_challans', 'action' => 'admin_index', 'icon' => '<i class="fa fa-truck"></i>', 'url' => $main_url),
                    'ncp_return_challans' => array('title' => 'NCP Challan ASO to CWH', 'controller' => 'ncp_return_challans', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    //'damage_return_challans' => array('title'=>'Damage Return Challan List','controller'=>'damage_return_challans','action'=>'admin_index','icon' => '<i class="fa fa-list"></i>'),
                    'return_challans_to_aso' => array('title' => 'Return Challan SO To ASO', 'controller' => 'return_challans_to_aso', 'action' => 'admin_index', 'icon' => '<i class="fa fa-truck"></i>', 'url' => $main_url),
                    'ncp_return_challans_to_aso' => array('title' => 'NCP Return Challan SO To ASO', 'controller' => 'ncp_return_challans_to_aso', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    //'damage_return_challans_to_aso' => array('title'=>'Damage Return Challan To Aso','controller'=>'damage_return_challans_to_aso','action'=>'admin_index','icon' => '<i class="fa fa-list"></i>')
                    'requisitions' => array('title' => 'DO', 'controller' => 'requisitions', 'action' => 'admin_index', 'url' => $main_url),
                    'do_challans' => array('title' => 'DO Challans', 'controller' => 'do_challans', 'action' => 'admin_index', 'url' => $main_url),
                    'product_issues' => array('title' => 'Product Issue to SO', 'controller' => 'product_issues', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'ncp_product_issues' => array('title' => 'NCP Product Issue to SO', 'controller' => 'ncp_product_issues', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),

                    'ncp_product_dashboards' => array('title' => 'NCP Type', 'controller' => 'ncp_product_dashboards', 'action' => 'admin_index', 'icon' => '<i class="fa fa-truck"></i>', 'url' => $main_url),
                    'statement_test_reports' => array('title' => 'Inventory Test Report', 'controller' => 'statement_test_reports', 'action' => 'admin_index', 'icon' => '<i class="fa fa-truck"></i>', 'url' => $main_url),


                    //'stores' => array('title'=>'Store List','controller'=>'stores','action'=>'admin_index','icon' => '<i class="fa fa-home"></i>'),
                    //'current_inventories' => array('title'=>'Current Inventory','controller'=>'current_inventories','action'=>'admin_index','icon' => '<i class="fa fa-shopping-cart"></i>','scroll' => '150'),

                    'current_inventories' => array('title' => 'Current Inventory', 'controller' => 'current_inventories', 'action' => 'admin_index', 'icon' => '<i class="fa fa-shopping-cart"></i>', 'url' => $main_url),
                    'ncp_inventories' => array('title' => 'NCP Inventory', 'controller' => 'ncp_inventories', 'action' => 'admin_index', 'icon' => '<i class="fa fa-shopping-cart"></i>', 'url' => $main_url),
                    'inventory_adjustments' => array('title' => 'Inventory Adjustments', 'controller' => 'inventory_adjustments', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '150', 'url' => $main_url),
                    'claims' => array('title' => 'Claim', 'controller' => 'claims', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'claims_to_aso' => array('title' => 'Claim To Aso', 'controller' => 'claims_to_aso', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'opening_balances' => array('title' => 'Opening Balance List', 'controller' => 'opening_balances', 'action' => 'admin_index', 'url' => $main_url),
                    'opening_balance_collections' => array('title' => 'Collection List(Opening)', 'controller' => 'opening_balance_collections', 'action' => 'admin_index', 'url' => $main_url),

                    'opening_balance_deposites' => array('title' => 'Deposite List(Opening)', 'controller' => 'opening_balance_deposites', 'action' => 'admin_index', 'url' => $main_url),
                    'opening_balance_reports' => array('title' => 'Opening Balance Report(Opening)', 'controller' => 'opening_balance_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'inventory_statuses' => array('title' => 'Inventory Status', 'controller' => 'inventory_statuses', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '600', 'url' => $main_url),
                    'transaction_types' => array('title' => 'Transaction Types', 'controller' => 'transaction_types', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1090', 'url' => $main_url),
                    //'gift_items' => array('title' => 'Gift Item List', 'controller' => 'gift_items', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1500', 'url' => $main_url),
                    'Product_to_product_converts' => array('title' => 'Product to Product Converts', 'controller' => 'Product_to_product_converts', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1500', 'url' => $main_url),
                    'credit_notes' => array('title' => 'Credit Note', 'controller' => 'credit_notes', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1500', 'url' => $main_url),
                )
            ),
            'product_convert_histories' => array('title' => 'Product Convert History', 'controller' => 'product_convert_history', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '150', 'url' => $main_url),
            'so_attendances' => array('title' => 'So Attendance List', 'controller' => 'so_attendances', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '150', 'url' => $main_url),
            'sale_targets' => array(
                'title' => 'National Sales Target', 'controller' => 'sale_targets', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '150', 'url' => $main_url,
                'child' => array(
                    'sale_targets' => array('title' => 'National Sales Target', 'controller' => 'sale_targets', 'action' => 'admin_index', 'url' => $main_url),
                    'natioanl_sale_targets_area_wise' => array('title' => 'National Sales target(Area Wise)', 'controller' => 'natioanl_sale_targets_area_wise', 'action' => 'admin_index', 'url' => $main_url),
                    'sale_targets_base_wise' => array('title' => 'Sales target(Base wise)', 'controller' => 'sale_targets_base_wise', 'action' => 'admin_index', 'url' => $main_url),
                    'national_target_effective_call_outlet_coverage_sessions' => array('title' => 'National Effective Call,Outlet Coverage,Session', 'controller' => 'national_target_effective_call_outlet_coverage_sessions', 'action' => 'admin_index', 'url' => $main_url),
                    'effective_calls' => array('title' => 'Effective Call,Outlet Coverage,Session(Area wise)', 'controller' => 'effective_calls', 'action' => 'admin_index', 'url' => $main_url),
                    'effective_calls_base_wise' => array('title' => 'Effective Call,Outlet Coverage,Session(Base wise)', 'controller' => 'effective_calls_base_wise', 'action' => 'admin_index', 'url' => $main_url),
                )
            ),
            'message_lists' => array(
                'title' => 'Message', 'controller' => 'message_lists', 'action' => 'admin_index', 'icon' => '<i class="fa fa-envelope"></i>', 'scroll' => '350', 'url' => $main_url,
                'child' => array(
                    'message_lists' => array('title' => 'Message List', 'controller' => 'message_lists', 'action' => 'admin_index', 'url' => $main_url),
                    'promotion_message_lists' => array('title' => 'Promotional Message List', 'controller' => 'promotion_message_lists', 'action' => 'admin_index', 'url' => $main_url),
                    'message_categories' => array('title' => 'Message Category', 'controller' => 'message_categories', 'action' => 'admin_index', 'url' => $main_url)
                )
            ),
            'visit_plan_lists' => array(
                'title' => 'SO/Doctor Visit Plan', 'controller' => 'visit_plan_lists', 'action' => 'admin_index', 'icon' => '<i class="fa fa-user-md"></i>', 'scroll' => '350', 'url' => $main_url,
                'child' => array(
                    'doctors' => array('title' => 'Doctors', 'controller' => 'doctors', 'action' => 'admin_index', 'url' => $main_url),
                    'doctor_types' => array('title' => 'Doctor Types', 'controller' => 'doctor_types', 'action' => 'admin_index', 'url' => $main_url),
                    'doctor_qualifications' => array('title' => 'Doctor Qualifications', 'controller' => 'doctor_qualifications', 'action' => 'admin_index', 'url' => $main_url),
                    'doctor_visits' => array('title' => 'Doctor Visits', 'controller' => 'doctor_visits', 'action' => 'admin_index', 'icon' => '<i class="fa fa-user-md"></i>', 'scroll' => '350', 'url' => $main_url),
                    'user_doctor_visit_plans' => array('title' => 'Doctor Visit Plan', 'controller' => 'UserDoctorVisitPlans', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '350', 'url' => $main_url),
                    'visit_plan_lists' => array('title' => 'Visit Plan', 'controller' => 'visit_plan_lists', 'action' => 'admin_index', 'icon' => '<i class="fa fa-user"></i>', 'scroll' => '400', 'url' => $main_url),
                    'sessions' => array('title' => 'Sessions', 'controller' => 'sessions', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1500', 'url' => $main_url),
                )
            ),

            'outlets' => array(
                'title' => 'GEO Location/Market Hierarchy', 'controller' => 'outlets', 'action' => 'admin_index', 'icon' => '<i class="fa fa-map-marker"></i>', 'scroll' => '500', 'url' => $main_url,
                'child' => array(
                    'divisions' => array('title' => 'Division', 'controller' => 'divisions', 'action' => 'admin_index', 'icon' => '<i class="fa fa-map-marker"></i>', 'scroll' => '500', 'url' => $main_url),
                    'districts' => array('title' => 'District', 'controller' => 'districts', 'action' => 'admin_index', 'icon' => '<i class="fa fa-map-marker"></i>', 'scroll' => '500', 'url' => $main_url),
                    'thanas' => array('title' => 'Thana', 'controller' => 'thanas', 'action' => 'admin_index', 'icon' => '<i class="fa fa-map-marker"></i>', 'scroll' => '650', 'url' => $main_url),
                    'thana_transfers' => array('title' => 'Thana Transfer', 'controller' => 'thana_transfers', 'action' => 'admin_index', 'icon' => '<i class="fa fa-map-marker"></i>', 'scroll' => '680', 'url' => $main_url),
                    'markets' => array('title' => 'Market', 'controller' => 'markets', 'action' => 'admin_index', 'icon' => '<i class="fa fa-map-marker"></i>', 'scroll' => '800', 'url' => $main_url),
                    'markets_transfers' => array('title' => 'Market Transfer', 'controller' => 'markets_transfers', 'action' => 'admin_index', 'icon' => '<i class="fa fa-map-marker"></i>', 'scroll' => '810', 'url' => $main_url),
                    'territories' => array('title' => 'Territory', 'controller' => 'territories', 'action' => 'admin_index', 'icon' => '<i class="fa fa-map-marker"></i>', 'scroll' => '800', 'url' => $main_url),
                    'outlets' => array('title' => 'Outlet', 'controller' => 'outlets', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'outlet_categories' => array('title' => 'Outlet Category', 'controller' => 'outlet_categories', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'selective_outlets' => array('title' => 'Selective Outlet', 'controller' => 'selective_outlets', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                )
            ),

            'programs' => array(
                'title' => 'Program/ Project List', 'controller' => 'programs', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1050', 'url' => $main_url,
                'child' => array(
                    'programs/pchp_program_list' => array('title' => 'GSP Program', 'controller' => 'programs', 'action' => 'admin_pchp_program_list', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'programs/bsp_program_list' => array('title' => 'BSP Program', 'controller' => 'programs', 'action' => 'admin_bsp_program_list', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'programs/larc_program_list' => array('title' => 'Pink Star Program', 'controller' => 'programs', 'action' => 'admin_larc_program_list', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'programs/injective_program_list' => array('title' => 'Stockist For Injectable', 'controller' => 'programs', 'action' => 'admin_injective_program_list', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'programs/ngo_injective_program_list' => array('title' => 'NGO For Injectable', 'controller' => 'programs', 'action' => 'admin_ngo_injective_program_list', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'notundin_programs' => array('title' => 'Notundin Program', 'controller' => 'notundin_programs', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'projects' => array('title' => 'Projects', 'controller' => 'projects', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1060', 'url' => $main_url),
                    'project_ngo_outlets' => array('title' => 'Project ngo Outlet', 'controller' => 'project_ngo_outlets', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1100', 'url' => $main_url),
                    'program_officer_outlet_tags' => array('title' => 'Program Officer Tag To Outlet', 'controller' => 'program_officer_outlet_tags', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1100', 'url' => $main_url),
                )
            ),

            'memos' => array(
                'title' => 'Memo List', 'controller' => 'memos', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1400', 'url' => $main_url,
                'child' => array(
                    'memos' => array('title' => 'Memo List', 'controller' => 'memos', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1400', 'url' => $main_url),
                    'csa_memos' => array('title' => 'CSA Memo List', 'controller' => 'csa_memos', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1400', 'url' => $main_url),
                    'proxy_sells' => array('title' => 'Proxy Sell', 'controller' => 'proxy_sells', 'action' => 'admin_index', 'icon' => '<i class="fa fa-bars" ></i>', 'scroll' => '1850', 'url' => $main_url),
                    'credit_memo_transfers' => array('title' => 'Credit Memo Transfer', 'controller' => 'credit_memo_transfers', 'action' => 'admin_index', 'icon' => '<i class="fa fa-bars" ></i>', 'scroll' => '1850', 'url' => $main_url),
                    'memo_editable_permissions' => array('title' => 'Memo Editable Permission', 'controller' => 'memo_editable_permissions', 'action' => 'admin_index', 'icon' => '<i class="fa fa-bars" ></i>', 'scroll' => '1850', 'url' => $main_url),

                )
            ),
            'primary_memos' => array(
                'title' => 'Primary Memo List', 'controller' => 'primary_memos', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1400', 'url' => $main_url,
                'child' => array(
                    'primary_memos' => array('title' => 'Primary Memo List', 'controller' => 'primary_memos', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1400', 'url' => $main_url),
                    'primary_sender_receivers' => array('title' => 'Sender/Receiver', 'controller' => 'primary_sender_receivers', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1400', 'url' => $main_url),
                    'primary_memo_reports' => array('title' => 'Primary Memo report', 'controller' => 'primary_memo_reports', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1400', 'url' => $main_url),
                    'primary_product_sales_reports' => array('title' => 'Product Sales Report', 'controller' => 'primary_product_sales_reports', 'action' => 'admin_index', 'url' => $main_url),
                )
            ),
            'deposits' => array(
                'title' => 'Deposit List', 'controller' => 'deposits', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '0', 'url' => $main_url,
                'child' => array(
                    'deposits' => array('title' => 'Deposit List', 'controller' => 'deposits', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1000', 'url' => $main_url),
                    'collections' => array('title' => 'Collection List', 'controller' => 'collections', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1400', 'url' => $main_url),
                    'deposit_editable_permissions' => array('title' => 'Deposit Editable Permission', 'controller' => 'deposit_editable_permissions', 'action' => 'admin_index', 'icon' => '<i class="fa fa-bars" ></i>', 'scroll' => '1850', 'url' => $main_url),
                    'collection_editable_permissions' => array('title' => 'Collection Editable Permission', 'controller' => 'collection_editable_permissions', 'action' => 'admin_index', 'icon' => '<i class="fa fa-bars" ></i>', 'scroll' => '1850', 'url' => $main_url),
                )
            ),

            'fiscal_years' => array(
                'title' => 'Settings', 'controller' => 'fiscal_years', 'action' => 'admin_index', 'icon' => '<i class="fa fa-calendar"></i>', 'scroll' => '400', 'url' => $main_url,
                'child' => array(
                    'allUsers' => array('title' => 'User List', 'controller' => 'users', 'action' => 'index', 'url' => $main_url),
                    'DmsUsers' => array('title' => 'DB User List', 'controller' => 'dms_users', 'action' => 'admin_index', 'url' => $main_url),
                    //'SrUsers' => array('title' => 'SR User List', 'controller' => 'sr_users', 'action' => 'admin_index', 'url' => $main_url),
                    'allGroups' => array('title' => 'User Groups', 'controller' => 'user_groups', 'action' => 'index', 'url' => $main_url),
                    'permissions' => array('title' => 'Group Permissions', 'controller' => 'user_group_permissions', 'action' => 'index', 'url' => $main_url),
                    'fiscal_years' => array('title' => 'Fiscal Years', 'controller' => 'fiscal_years', 'action' => 'admin_index', 'icon' => '<i class="fa fa-calander"></i>', 'url' => $main_url),
                    'months' => array('title' => 'Month List', 'controller' => 'months', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'weeks' => array('title' => 'Weeks', 'controller' => 'weeks', 'action' => 'admin_index', 'icon' => '<i class="fa fa-user"></i>', 'url' => $main_url),
                    'designations' => array('title' => 'Designation List', 'controller' => 'designations', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '500', 'url' => $main_url),
                    'institutes' => array('title' => 'Institute List', 'controller' => 'institutes', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'institutes/mapping_to_area' => array('title' => 'Institute Mapping', 'controller' => 'institutes', 'action' => 'admin_mapping_to_area', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'offices' => array('title' => 'Office List', 'controller' => 'offices', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'office_types' => array('title' => 'Office Type List', 'controller' => 'office_types', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'target_types' => array('title' => 'Target Type List', 'controller' => 'target_types', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1100', 'url' => $main_url),
                    'banks' => array('title' => 'Bank List', 'controller' => 'banks', 'action' => 'admin_index', 'url' => $main_url),
                    'bank_branches' => array('title' => 'Bank Branches List', 'controller' => 'bank_branches', 'action' => 'admin_index', 'url' => $main_url),
                    'bank_accounts' => array('title' => 'Bank Accounts List', 'controller' => 'bank_accounts', 'action' => 'admin_index', 'url' => $main_url),
                    'user_territory_lists' => array('title' => 'User to Territory List', 'controller' => 'user_territory_lists', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1890', 'url' => $main_url),
                    'instrument_types' => array('title' => 'Instrument Type', 'controller' => 'instrument_types', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1890', 'url' => $main_url),
                    'outlet_delete_btn_hide_date_setting' => array('title' => 'OUtlet Delete Button Hide Date', 'controller' => 'outlet_delete_btn_hide_date_setting', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1890', 'url' => $main_url),
                    'product_maps' => array('title' => 'Product Map', 'controller' => 'product_maps', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'store_maps' => array('title' => 'Store Map', 'controller' => 'store_maps', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'unit_maps' => array('title' => 'Unit Map', 'controller' => 'unit_maps', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'common_macs' => array('title' => 'Common Mac Setting', 'controller' => 'common_macs', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'outlet_groups' => array('title' => 'Outlet Groups', 'controller' => 'outlet_groups', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'group_wise_discount_bonus_policies' => array('title' => 'Discount/Bonus Policies', 'controller' => 'group_wise_discount_bonus_policies', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'stores' => array('title' => 'Store', 'controller' => 'stores', 'action' => 'admin_index', 'icon' => '<i class="fa fa-home"></i>', 'url' => $main_url),
                    'store_types' => array('title' => 'Store Types', 'controller' => 'store_types', 'action' => 'admin_index', 'icon' => '<i class="fa fa-home"></i>', 'url' => $main_url),
                    'stock_processes' => array('title' => 'Stock Process', 'controller' => 'stock_processes', 'action' => 'admin_index', 'icon' => '<i class="fa fa-home"></i>', 'url' => $main_url),
                    'ProductSettingsForReports' => array('title' => 'Product Settings for Reports', 'controller' => 'ProductSettingsForReports', 'action' => 'admin_index', 'icon' => '<i class="fa fa-map-marker"></i>', 'scroll' => '4400', 'url' => $main_url),


                    'brands' => array('title' => 'Brand List', 'controller' => 'brands', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'url' => $main_url),
                    'employees' => array('title' => 'Employees List', 'controller' => 'employees', 'action' => 'admin_index', 'url' => $main_url),
                    'user_historys' => array('title' => 'allUsersHistory', 'controller' => 'AllUsersHistoy', 'action' => 'admin_index', 'url' => $main_url),
                
                )
            ),
            'sales_analysis_reports' => array(
                'title' => 'Report', 'controller' => 'sales_analysis_reports', 'action' => 'admin_index', 'icon' => '<i class="fa fa-bars" ></i>', 'scroll' => '1750', 'url' => $main_url,
                'child' => array(
                    'dashboards/sync_history' => array('title' => 'Sync History', 'controller' => 'dashboards', 'action' => 'admin_sync_history', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1400', 'url' => $main_url),
                    'mac_free_logs' => array('title' => 'Mac Audit Trail', 'controller' => 'mac_free_logs', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1400', 'url' => $main_url),
                    'deleted_memos' => array('title' => 'Audit Trail', 'controller' => 'deleted_memos', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1400', 'url' => $main_url),
                    'deposit_logs' => array('title' => 'Deposit Audit Trail', 'controller' => 'deposit_logs', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1400', 'url' => $main_url),
                    'collection_logs' => array('title' => 'Collection Audit Trail', 'controller' => 'collection_logs', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1400', 'url' => $main_url),
                    'current_inventory_reports' => array('title' => 'Stock Status by SO Report', 'controller' => 'current_inventory_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'sales_reports' => array('title' => 'Top Sheet Report', 'controller' => 'sales_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'product_sales_reports' => array('title' => 'Product Sales Report', 'controller' => 'product_sales_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'deposit_reports' => array('title' => 'Sales, Collection and Deposit Statement', 'controller' => 'deposit_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'national_sales_reports' => array('title' => 'National Sales Volume and Value Report', 'controller' => 'national_sales_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'esales_reports' => array('title' => 'E-Sales Report', 'controller' => 'esales_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'outlet_sales_reports' => array('title' => 'Outlet Sales Summary Report', 'controller' => 'outlet_sales_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'sales_analysis_reports' => array('title' => 'Sales Analysis Report', 'controller' => 'sales_analysis_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'dist_distribu_reports' => array('title' => 'District & Division Report', 'controller' => 'dist_distribu_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'program_provider_reports' => array('title' => 'Program Provider Report', 'controller' => 'program_provider_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'sales_deposit_monitor' => array('title' => 'Sales Deposition Monitor', 'controller' => 'sales_deposit_monitor', 'action' => 'admin_index', 'url' => $main_url), //add new
                    'projection_achievement_reports' => array('title' => 'Projection and Achievement Analysis Report', 'controller' => 'projection_achievement_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'projection_achievement_comparisons' => array('title' => 'Projection and Achievement Comparison', 'controller' => 'projection_achievement_comparisons', 'action' => 'admin_index', 'url' => $main_url),
                    'weekly_bank_deposition_information' => array('title' => 'Weekly Bank Deposition Information', 'controller' => 'weekly_bank_deposition_information', 'action' => 'admin_index', 'url' => $main_url),
                    'dcr_reports' => array('title' => 'DCR', 'controller' => 'dcr_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'lpc_reports' => array('title' => 'LPC Reports', 'controller' => 'lpc_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'outlet_characteristic_reports' => array('title' => 'Outlet Characteristics Report', 'controller' => 'outlet_characteristic_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'market_characteristic_reports' => array('title' => 'Market Characteristics Report', 'controller' => 'market_characteristic_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'ngo_institute_sale_reports' => array('title' => 'NGO/Institution Sales Report', 'controller' => 'ngo_institute_sale_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'query_on_sales_reports' => array('title' => 'Query on Sales Information', 'controller' => 'query_on_sales_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'combine_query_on_sales_reports' => array('title' => 'Combine Query on Sales Information', 'controller' => 'combine_query_on_sales_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'max_min_sales_outlet_reports' => array('title' => 'Max/Min Sales Outlets', 'controller' => 'max_min_sales_outlet_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'inventory_statement_reports' => array('title' => 'Inventory Statement Report', 'controller' => 'inventory_statement_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'so_wise_detail_sales' => array('title' => 'Product Wise Monthly Sales Detail', 'controller' => 'so_wise_detail_sales', 'action' => 'admin_index', 'url' => $main_url),
                    'so_territory_reports' => array('title' => 'So Territory Report', 'controller' => 'so_territory_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'consolidate_statement_of_sales' => array('title' => 'Consolidated Statement of Sale', 'controller' => 'consolidate_statement_of_sales', 'action' => 'admin_index', 'url' => $main_url),
                    'outlet_visit_information_reports' => array('title' => 'Outlet Visit Information', 'controller' => 'outlet_visit_information_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'transaction_list_stocks' => array('title' => 'Transaction List On Stocks', 'controller' => 'transaction_list_stocks', 'action' => 'admin_index', 'url' => $main_url),
                    'transaction_list_stock_sos' => array('title' => 'Transaction List On Stocks(Territory)', 'controller' => 'transaction_list_stock_sos', 'action' => 'admin_index', 'url' => $main_url), 'area_batch_lot_by_stocks' => array('title' => 'Area Stock By Batch/Lot', 'controller' => 'area_batch_lot_by_stocks', 'action' => 'admin_index', 'url' => $main_url),
                    'credit_collection_reports' => array('title' => 'CreditCollectionReports', 'controller' => 'credit_collection_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'national_stock_reports' => array('title' => 'National Stock Report', 'controller' => 'national_stock_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'mig_sale_reports' => array('title' => 'Mig Sales Report', 'controller' => 'mig_sale_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'DistCommissionReports' => array('title' => 'Distributor Commission Report', 'controller' => 'DistCommissionReports', 'action' => 'admin_index', 'url' => $main_url),
                    'WeeklySalesReports' => array('title' => 'Weekly Sales Report', 'controller' => 'WeeklySalesReports', 'action' => 'admin_index', 'url' => $main_url),
                    'visited_outlets' => array('title' => 'Visited Outlets', 'controller' => 'visited_outlets', 'action' => 'admin_index', 'icon' => '<i class="fa fa-bars" ></i>', 'scroll' => '1850', 'url' => $main_url),
                    'fullcare_reports' => array('title' => 'Full Care Report', 'controller' => 'fullcare_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'fullcare_sales_volume_provider_reports' => array('title' => 'FullCare Sales Volume Memo wise Report', 'controller' => 'fullcare_sales_volume_provider_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'nbr_reports' => array('title' => 'NBR Report', 'controller' => 'nbr_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'month_wise_product_value_qty_reports' => array('title' => 'Day Wise Product Value & Volume Report', 'controller' => 'month_wise_product_value_qty_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'stock_opening_reports' => array('title' => 'Stock Opening Reports', 'controller' => 'stock_opening_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'bonus_campaign_reports' => array('title' => 'Bonus Campaign Report', 'controller' => 'bonus_campaign_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'vat_reports' => array('title' => 'VAT Report', 'controller' => 'vat_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'product_frequency_reports' => array('title' => 'Product Frequency Report', 'controller' => 'product_frequency_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'other_emergency_reports' => array('title' => 'Other Report', 'controller' => 'other_emergency_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'gift_items' => array('title' => 'Gift Sample Issue', 'controller' => 'gift_items', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1500', 'url' => $main_url),
                    'ors_sales_thorugh_card_holders' => array('title' => ' ORS Sales Through Card Holder', 'controller' => 'ors_sales_thorugh_card_holders', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1500', 'url' => $main_url),
                )
            ),
            'day_closes' => array('title' => 'Day Close History', 'controller' => 'day_closes', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1450', 'url' => $main_url),
            'dist_ncp_product_issues' => array('title' => 'Dist Ncp Product Issue', 'controller' => 'dist_ncp_product_issues', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1450', 'url' => $main_url),
            'memo_notifications' => array('title' => 'Memo Notifications', 'controller' => 'memo_notifications', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1450', 'url' => $main_url),
            'map_sales_tracks' => array('title' => 'Map Sales Tracking', 'controller' => 'map_sales_tracks', 'action' => 'admin_index', 'icon' => '<i class="fa fa-list"></i>', 'scroll' => '1900', 'url' => $main_url),
            'product_settings' => array(
                'title' => 'Dashboard Setting', 'controller' => 'product_settings', 'action' => 'admin_index', 'icon' => '<i class="fa fa-bars" ></i>', 'scroll' => '1950', 'url' => $main_url,
                'child' => array(
                    'product_settings' => array('title' => 'Dashboard - Target and Achievement Products', 'controller' => 'product_settings', 'action' => 'admin_index', 'url' => $main_url),
                    'pie_product_settings' => array('title' => 'Dashboard - Pie Products', 'controller' => 'pie_product_settings', 'action' => 'admin_index', 'url' => $main_url),
                    'report_product_settings' => array('title' => 'Dashboard - Stock Report Products', 'controller' => 'report_product_settings', 'action' => 'admin_index', 'url' => $main_url),
                    'live_sales_tracks' => array('title' => 'Live Tracking Interval', 'controller' => 'live_sales_tracks', 'action' => 'admin_index', 'url' => $main_url),
                    'report_esales_settings' => array('title' => 'Esales Report Setting', 'controller' => 'report_esales_settings', 'action' => 'admin_index', 'url' => $main_url)
                )
            ),
            'bonus_card_calculate' => array(
                'title' => 'Bonus Cards', 'controller' => 'bonus_card_calculate', 'action' => 'admin_index', 'icon' => '<i class="fa fa-bars" ></i>', 'scroll' => '1950', 'url' => $main_url,
                'child' => array(
                    'bonus_card_calculate' => array('title' => 'Bonus Card Calculate', 'controller' => 'bonus_card_calculate', 'action' => 'admin_index', 'url' => $main_url),
                    'bonus_card_process' => array('title' => 'Bonus Card Process', 'controller' => 'bonus_card_process', 'action' => 'admin_index', 'url' => $main_url),
                    'bonus_summery_report' => array('title' => 'Bonus Summery Report', 'controller' => 'bonus_summery_report', 'action' => 'admin_index', 'url' => $main_url),
                    'bonus_card_summery_reports' => array('title' => 'Bonus Card Summery Report', 'controller' => 'bonus_card_summery_reports', 'action' => 'admin_index', 'url' => $main_url),
                    'bonus_card_party_reports' => array('title' => 'Bonus Card Party Report(ORSaline-N+ORSaline-N (25pcs))', 'controller' => 'bonus_card_party_reports', 'action' => 'admin_index', 'url' => $main_url),
                )
            ),

            'Orders' => array(
                'title' => 'Distributor Requisition', 'controller' => 'orders', 'action' => 'admin_index', 'icon' => '<i class="fa fa-bars" ></i>', 'scroll' => '1950', 'url' => $main_url,
                'child' => array(
                    'Orders' => array('title' => 'Product Requisition', 'controller' => 'Orders', 'action' => 'admin_index', 'icon' => '<i class="fa fa-bars" ></i>', 'scroll' => '2150', 'url' => $main_url),
                    'Manages' => array('title' => 'Distributor Product Issue', 'controller' => 'Manages', 'action' => 'admin_index', 'icon' => '<i class="fa fa-bars" ></i>', 'scroll' => '2150', 'url' => $main_url),

                )
            ),
        );


        return $menu;
    }

    private function report_link_array()
    {
        $report_query = array(
            'dashboards',
            'mac_free_logs',
            'deleted_memos',
            'deposit_logs',
            'collection_logs',
            'current_inventory_reports',
            'sales_reports',
            'product_sales_reports',
            'deposit_reports',
            'national_sales_reports',
            'esales_reports',
            'outlet_sales_reports',
            'sales_analysis_reports',
            'dist_distribu_reports',
            'program_provider_reports',
            'program_provider_reports',
            'sales_deposit_monitor',
            'projection_achievement_reports',
            'projection_achievement_comparisons',
            'weekly_bank_deposition_information',
            'dcr_reports',
            'lpc_reports',
            'outlet_characteristic_reports',
            'market_characteristic_reports',
            'ngo_institute_sale_reports',
            'query_on_sales_reports',
            'combine_query_on_sales_reports',
            'max_min_sales_outlet_reports',
            'inventory_statement_reports',
            'so_wise_detail_sales',
            'so_territory_reports',
            'consolidate_statement_of_sales',
            'outlet_visit_information_reports',
            'transaction_list_stocks',
            'transaction_list_stock_sos',
            'area_batch_lot_by_stocks',
            'credit_collection_reports',
            'national_stock_reports',
            'mig_sale_reports',
            'DistCommissionReports',
            'WeeklySalesReports',
            'visited_outlets',
            'visited_outlets',
            'fullcare_reports',
            'nbr_reports',
            'month_wise_product_value_qty_reports',
            'bonus_summery_report',
            'bonus_card_summery_reports',
            'bonus_card_party_reports',
            'stock_opening_reports',
            'bonus_campaign_reports',
            'vat_reports',
            'product_frequency_reports',
            'other_emergency_reports',
            'gift_items',
            'ors_sales_thorugh_card_holders',
            'fullcare_sales_volume_provider_reports',

        );
        return $report_query;
    }

    public function current_datetime()
    {
        date_default_timezone_set('Asia/Dhaka');
        return date('Y-m-d H:i:s');
    }

    public function current_date()
    {
        date_default_timezone_set('Asia/Dhaka');
        return date('Y-m-d');
    }

    public function to_expire_date($date)
    {
        date_default_timezone_set('Asia/Dhaka');
        $date = explode("-", $date);
        $new_date = "01-" . $date[0] . "-20" . $date[1];
        return Date("Y-m-t", strtotime($new_date));
    }

    public function from_expire_date($date)
    {
        date_default_timezone_set('Asia/Dhaka');
        return Date("m-y", strtotime($date));
    }


    public function unit_convert_from_global($product_id = '', $measurement_unit_id = '', $qty = '')
    {
        $number = $qty;
        if (isset($GLOBALS['product_wise_measurement'][$product_id][$measurement_unit_id]) && !empty($GLOBALS['product_wise_measurement'][$product_id][$measurement_unit_id])) {
            $number = $GLOBALS['product_wise_measurement'][$product_id][$measurement_unit_id] * $qty;
            $number = round($number);
            /*$decimals = 2;
       //$number = 221.12345;
       $number = $number * pow(10,$decimals);
       $number = intval($number);
       $number = $number / pow(10,$decimals);
       return sprintf('%.2f', ($number));*/
            return $number;
        } else {
            $number = round($number);
            return  $number;
        }
    }

    // unit convert to base unit
    public function unit_convert($product_id = '', $measurement_unit_id = '', $qty = '')
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
            $number = $unit_info['ProductMeasurement']['qty_in_base'] * $qty;
            $number = round($number);
            /*$decimals = 2;
       //$number = 221.12345;
       $number = $number * pow(10,$decimals);
       $number = intval($number);
       $number = $number / pow(10,$decimals);
       return sprintf('%.2f', ($number));*/
            return $number;
        } else {
            $number = round($number);
            return  $number;
        }
    }

    // unit convert to other unit
    public function unit_convertfrombase($product_id = '', $measurement_unit_id = '', $qty = '')
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
            //echo sprintf('%.2f', ($qty*10.0)/10.0);
            //echo ' ';
            //$number = 100;
            /*$decimals = 2;
                //$number = 221.12345;
     $number = $number * pow(10,$decimals);
     $number = intval($number);
     $number = $number / pow(10,$decimals);*/

            $number = explode('.', $number);
            $number = $number[0] . '.' . (isset($number[1]) ? substr($number[1], 0, 2) : 00);



            return sprintf('%.2f', $number);
        } else {
            return sprintf('%.2f', $number);
        }
    }

    // convert unit to unit
    public function convert_unit_to_unit($product_id = '', $from_unit_id = '', $to_unit_id = '', $qty = '')
    {
        $this->loadModel('ProductMeasurement');
        $from_unit_info = $this->ProductMeasurement->find('first', array(
            'conditions' => array(
                'ProductMeasurement.product_id' => $product_id,
                'ProductMeasurement.measurement_unit_id' => $from_unit_id
            ),
            'recursive' => -1
        ));

        if (!empty($from_unit_info)) {
            $from_quantity = $qty * $from_unit_info['ProductMeasurement']['qty_in_base'];
        } else {
            $from_quantity = $qty;
        }

        $to_unit_info = $this->ProductMeasurement->find('first', array(
            'conditions' => array(
                'ProductMeasurement.product_id' => $product_id,
                'ProductMeasurement.measurement_unit_id' => $to_unit_id
            )
        ));
        if (!empty($to_unit_info)) {
            $to_quantity = $to_unit_info['ProductMeasurement']['qty_in_base'];
        } else {
            $to_quantity = 1;
        }

        return sprintf('%.2f', ($from_quantity / $to_quantity));
    }

    function buildTree(array $elements, $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    function array_flatten(array $array)
    {
        $flat = array(); // initialize return array
        $stack = array_values($array); // initialize stack
        while ($stack) { // process stack until done
            $value = array_shift($stack);
            if (is_array($value)) { // a value to further process
                $stack = array_merge(array_values($value), $stack);
            } else { // a value to take
                $flat[] = $value;
            }
        }
        return $flat;
    }

    public function p($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

    public function page_limit()
    {
        if (isset($this->request->data['page_limit']) == '') {
            $this->request->data['page_limit'] = 20;
            return 20;
        } else {
            return $this->request->data['page_limit'];
        }
    }

    /* public function buildTree(Array $data, $parent_id = 0) {
      $tree = array();
      foreach ($data as $key => $val) {
      if ($val['parent_id'] == $parent_id) {
      $children = $this->buildTree($data, $val['id']);
      // set a trivial key
      if (!empty($children)) {
      $d['_children'] = $children;
      }
      $tree[] = $d;
      }
      }
      return $tree;
  } */

    public function get_store_list($store_type_id = '')
    {
        $this->loadModel('Store');

        if ($store_type_id == 1 or $store_type_id == 2) {
            $virtualFields = array(
                'name' => "CONCAT(Store.name, ' (', SalesPerson.name,')')"
            );

            $receiver_store = $this->Store->find('all', array(
                'joins' => array(
                    array(
                        'table' => 'sales_people',
                        'alias' => 'SalesPerson',
                        'type' => 'left',
                        'conditions' => array(
                            'SalesPerson.office_id = Store.office_id AND SalesPerson.designation_id <= 2'
                        )
                    )
                ),
                'conditions' => array('store_type_id' => $store_type_id),
                'fields' => array('Store.name', 'SalesPerson.name'),
                'order' => array('Store.name' => 'asc'),
                'recursive' => -1
            ));

            return $receiver_store;
        } elseif ($store_type_id == 3) {
        }
    }
    function get_user_id_from_so_id($so_id)
    {
        $this->loadModel('Usermgmt.User');
        $user_id = $this->User->find('first', array(
            'conditions' => array(
                'User.sales_person_id' => $so_id
            ),
            'recursive' => -1
        ));
        return  $user_id['User']['id'];
    }

    function get_vat_by_product_id_memo_date($product_id = 0, $memo_date = '', $is_distributor, $outlet_id = null)
    {
        if ($is_distributor == 1) {
            $this->LoadModel('ProductPricesV2');
            $product_prices = $this->ProductPricesV2->find('first', array(
                'conditions' => array(
                    'ProductPricesV2.product_id' => $product_id,
                    'ProductPricesV2.effective_date <=' => $memo_date,
                    'ProductPricesV2.has_combination' => 0
                ),
                'order' => array('ProductPricesV2.effective_date DESC'),
                'recursive' => -1

            ));
            // pr($product_prices);exit;
            return ($product_prices && $product_prices['ProductPricesV2']['vat']) ? $product_prices['ProductPricesV2']['vat'] : 0;
        } elseif ($is_distributor == 2) {
            $this->LoadModel('SpecialProductPrice');
            $product_prices = $this->SpecialProductPrice->find('first', array(
                'conditions' => array(
                    'SpecialProductPrice.product_id' => $product_id,
                    'SpecialProductPrice.effective_date <=' => $memo_date,
                    'SpecialProductPrice.has_combination' => 0
                ),
                'order' => array('SpecialProductPrice.effective_date DESC'),
                'recursive' => -1

            ));
            //echo $this->SpecialProductPrice->getLastQuery();
            //pr($product_prices);exit;
            return ($product_prices && $product_prices['SpecialProductPrice']['vat']) ? $product_prices['SpecialProductPrice']['vat'] : 0;
        } else {
            $this->LoadModel('ProductPrice');
            $this->LoadModel('OutletNgoPrice');
            $project_pricing_check = array();
            if ($outlet_id) {
                $project_pricing_check = $this->OutletNgoPrice->find('first', array(
                    'conditions' => array(
                        'OutletNgoPrice.outlet_id' => $outlet_id,
                        'ProductPrice.product_id' => $product_id,
                        "'" . $memo_date . "' " . 'BETWEEN ProductPrice.effective_date AND end_date'
                    ),
                    'order' => array('ProductPrice.effective_date' => 'DESC')
                ));
            }
            if ($project_pricing_check) {
                return ($project_pricing_check && $project_pricing_check['ProductPrice']['vat']) ? $project_pricing_check['ProductPrice']['vat'] : 0;
            } else {
                $product_prices = $this->ProductPrice->find('first', array(
                    'conditions' => array(
                        'ProductPrice.product_id' => $product_id,
                        'ProductPrice.effective_date <=' => $memo_date,
                        'ProductPrice.has_combination' => 0,
                        'OR' => array(
                            'ProductPrice.project_id IS NULL',
                            'ProductPrice.project_id' => 0
                        ),
                    ),
                    'order' => array('ProductPrice.effective_date DESC'),
                    'recursive' => -1
                ));
                // pr($product_prices);exit;
                return ($product_prices && $product_prices['ProductPrice']['vat']) ? $product_prices['ProductPrice']['vat'] : 0;
            }
        }
    }

    function get_vat_by_product_id_memo_date_v2($product_id = 0, $memo_date = '')
    {

        $this->LoadModel('ProductPricesV2');
        $product_prices = $this->ProductPricesV2->find('first', array(
            'conditions' => array(
                'ProductPricesV2.product_id' => $product_id,
                'ProductPricesV2.effective_date <=' => $memo_date,
                'ProductPricesV2.has_combination' => 0
            ),
            'order' => array('ProductPricesV2.effective_date DESC'),
            'recursive' => -1

        ));
        // pr($product_prices);exit;
        return ($product_prices && $product_prices['ProductPricesV2']['vat']) ? $product_prices['ProductPricesV2']['vat'] : 0;
    }

    public function update_notifications($controller, $methods)
    {
        $this->loadModel('SystemNotification');

        $all_notify = $this->SystemNotification->find('all', array(
            'conditions' => array(
                'SystemNotification.controller Like' => "%" . $controller . "%",
                'SystemNotification.methods Like' => "%" . $methods . "%",
                'SystemNotification.office_id' => $this->UserAuth->getOfficeId(),
                'SystemNotification.status' => 0,
                'SystemNotification.notification_seen' => 0,
            )
        ));
        $data_array = array();
        foreach ($all_notify as $key => $value) {
            $data = array();
            $data['id'] = $value['SystemNotification']['id'];
            $data['status'] = 1;
            $data['notification_seen'] = 1;
            $data_array[] = $data;
        }
        $this->SystemNotification->saveAll($data_array);
    }
	public function dd($data,$exit = true){
		echo "<pre>";
		 if(is_null($data)){
			echo "<i>NULL</i>";
		}elseif($data == ""){
			echo "<i>Empty</i>";
		}elseif(is_array($data)){
			print_r($data);
		}elseif(is_object($data)){
			var_dump($data);
		}elseif(is_bool($data)){
			echo "<i>" . ($data ? "True" : "False") . "</i>";
		}else{
			$str = $data;
			echo preg_replace("/\n/", "<br>\n", $str);
		}
		echo "</pre>";
		if($exit){
			exit;
		}
	}
}
