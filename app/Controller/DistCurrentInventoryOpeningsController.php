<?php

App::uses('AppController', 'Controller');

/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class DistCurrentInventoryOpeningsController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Filter.Filter');
    public $uses = array('DistCurrentInventoryBalanceLog', 'DistStore', 'ProductType', 'DistDistributor');

    /**
     * admin_index method
     *
     * @return void
     */

    /**
     * inventory_total method
     */
    public function admin_index()
    {
        $this->set('page_title', 'Current Inventories');
        $this->DistCurrentInventoryBalanceLog->recursive = 1;
        $this->loadModel('DistStore');
        $this->loadModel('InventoryStatuses');
        $this->loadModel('ProductCategory');

        $this->loadModel('Office');
        $this->loadModel('DistTso');
        $this->loadModel('DistTsoMapping');
        $this->loadModel('DistRouteMapping');
        $this->loadModel('DistAreaExecutive');

        $user_id = $this->UserAuth->getUserId();
        $user_group_id = $this->Session->read('UserAuth.UserGroup.id');


        $office_id = isset($this->request->data['DistCurrentInventoryBalanceLog']['office_id']) != '' ? $this->request->data['DistCurrentInventoryBalanceLog']['office_id'] : 0;

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        $this->set('office_parent_id', $office_parent_id);
        if ($office_parent_id == 0) {
            //$conditions = array('DistCurrentInventoryBalanceLog.inventory_status_id !=' => 2);
            $storeCondition = array();
            $office_conditions = array(
                'office_type_id' => 2
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

                $tso_dist_list = $this->DistTsoMapping->find('list', array(
                    'conditions' => array(
                        'dist_tso_id' => $dist_tso_id,
                    ),
                    'fields' => array('DistTsoMapping.dist_distributor_id', 'DistTsoMapping.dist_tso_id'),
                ));
                //$dist_conditions = array('DistDistributor.id'=>array_keys($tso_dist_list));
                $storeCondition = array('DistStore.dist_distributor_id' => array_keys($tso_dist_list));
                //$conditions = array('DistCurrentInventoryBalanceLog.inventory_status_id !=' => 2);
            } elseif ($user_group_id == 1034) {
                $sales_people_id = $this->Session->read('UserAuth.User.sales_person_id');
                $this->loadModel('DistUserMapping');
                $distributor = $this->DistUserMapping->find('first', array(
                    'conditions' => array('DistUserMapping.sales_person_id' => $sales_people_id),
                ));
                $distributor_id = $distributor['DistUserMapping']['dist_distributor_id'];

                $storeCondition = array('DistStore.dist_distributor_id' => $distributor_id);
            } else {
                //$conditions = array('DistCurrentInventoryBalanceLog.inventory_status_id !=' => 2);
                $storeCondition = array('DistStore.office_id' => $this->UserAuth->getOfficeId());
            }

            //$conditions = array('DistCurrentInventoryBalanceLog.Diststore_id' => $this->UserAuth->getStoreId(), 'DistCurrentInventoryBalanceLog.inventory_status_id !=' => 2);
            //$storeCondition = array('DistStore.office_id' => $this->UserAuth->getOfficeId());
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId());
        }

        $offices = $this->Office->find('list', array('conditions' => $office_conditions, 'order' => array('office_name' => 'asc')));

        $this->DistStore->virtualFields = array(
            "name" => "Distributor.name"
        );
        $distStores = $this->DistStore->find(
            'list',
            array(
                'conditions' => $storeCondition,
                //'fields' => array('DistStore.id', 'DistStore.name', 'DistDistributor.id'),
                //'order' => array('Store.name' => 'asc'),
                'joins' => array(
                    array(
                        'table' => 'dist_distributors',
                        'alias' => 'Distributor',
                        'conditions' => 'Distributor.id=DistStore.dist_distributor_id'
                    ),

                ),
                'recursive' => 0
            )
        );
        unset($this->DistStore->virtualFields['name']);
        if (!empty($distStores)) {
            $conditions = array('DistCurrentInventoryBalanceLog.inventory_status_id !=' => 2, 'DistCurrentInventoryBalanceLog.store_id' => array_keys($distStores));
        }
        if ($office_id) {
            $storeCondition = array('DistStore.office_id' => $office_id);
        } else {
            $storeCondition = array();
        }
        $this->DistCurrentInventoryBalanceLog->virtualFields = array(

            'office_order' => 'Office.order',

        );
        $conditions['DistCurrentInventoryBalanceLog.transaction_date'] = '2023-03-04';
        //$conditions['DistCurrentInventoryBalanceLog.store_id'] = '592';
        /* $conditions['OR'] = array(
            'DistCurrentInventoryBalanceLog.qty >' => 0,
            'DistCurrentInventoryBalanceLog.bonus_qty >' => 0
        ); */
		
        $this->paginate = array(
            'conditions' => $conditions,
            'order' => array('office_order' => 'ASC', 'Product.order' => 'ASC'),

            'joins' => array(
                array(
                    'table' => 'product_categories',
                    'alias' => 'ProductCategory',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Product.product_category_id = ProductCategory.id'
                    )
                ),

                array(
                    'table' => 'dist_distributors',
                    'alias' => 'Distributor',
                    'conditions' => 'Distributor.id=DistStore.dist_distributor_id'
                ),

                array(
                    'table' => 'dist_tso_mappings',
                    'alias' => 'DistTsoMapping',
                    'type' => 'LEFT',
                    'conditions' => 'DistTsoMapping.dist_distributor_id = DistStore.dist_distributor_id'

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
                    'conditions' => array('Office.id=DistStore.office_id')
                ),
            ),
            'fields' => array('DistCurrentInventoryBalanceLog.id', 'MAX(CAST(DistCurrentInventoryBalanceLog.other_column as NVARCHAR(max))) as other', 'DistCurrentInventoryBalanceLog.product_id', 'DistCurrentInventoryBalanceLog.store_id', 'SUM(DistCurrentInventoryBalanceLog.qty) AS total', 'SUM(DistCurrentInventoryBalanceLog.bonus_qty) as total_bonus', 'Product.name', 'Product.base_measurement_unit_id', 'Product.sales_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Distributor.name', 'ProductCategory.name', 'DistCurrentInventoryBalanceLog.inventory_status_id', 'Office.office_name', 'DistTso.name', 'DistAE.name'),
            'group' => array('DistCurrentInventoryBalanceLog.id',  'DistCurrentInventoryBalanceLog.product_id', 'Product.name', 'Product.order', 'Product.base_measurement_unit_id', 'Product.sales_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Distributor.name', 'DistCurrentInventoryBalanceLog.store_id', 'ProductCategory.name', 'DistCurrentInventoryBalanceLog.inventory_status_id', 'Office.office_name', 'DistTso.name', 'DistAE.name', 'Office.order'),

            // 'recursive'=>1



            // 'order' => array('Office.order asc','Product.order asc')
            // 'recursive'=>1
        );

        $currentInventories = $this->paginate();
        // echo $this->DistCurrentInventoryBalanceLog->getLastQuery();exit;
        // exit;

        /*   pr($currentInventories);
        die(); */
        // echo '<pre>'; print_r($currentInventories); echo '</pre>';exit;
        $this->loadModel('MeasurementUnit');
        $measurement_unit_list = $this->MeasurementUnit->find('list', array('fields' => 'name'));

        $this->set(compact('measurement_unit_list', 'currentInventories'));

        $market_id = isset($this->request->data['UserDoctorVisitPlanList']['market_id']) != '' ? $this->request->data['UserDoctorVisitPlanList']['market_id'] : 0;


        if (isset($this->request->data['DistCurrentInventoryBalanceLog']['product_categories_id']) != '') {
            $products = $this->DistCurrentInventoryBalanceLog->Product->find('list', array(
                'conditions' => array('Product.product_category_id' => $this->request->data['DistCurrentInventoryBalanceLog']['product_categories_id']),
                'order' => array('Product.order' => 'ASC'),
                'recursive' => -1
            ));
        } else {
            $products = array();
        }

        if (isset($this->request->data['DistCurrentInventoryBalanceLog']['category_summary'])) {
            $summaryCategoryList = $this->ProductCategory->find(
                'all',
                array(
                    //'order' => array('Product.order'=>'asc'),
                    'recursive' => -1
                )
            );

            $category_summary = $this->request->data['DistCurrentInventoryBalanceLog']['category_summary'];
        } else {
            $category_summary = false;
            $summaryCategoryList = '';
        }
        $this->set(compact('category_summary', 'summaryCategoryList'));

        $productCategories = $this->ProductCategory->find('list');

        $distStore_data = $this->DistStore->find(
            'all',
            array(
                'conditions' => $storeCondition,
                'fields' => array('DistStore.id', 'DistStore.name', 'DistDistributor.id'),
                //'order' => array('Store.name' => 'asc'),
                'recursive' => 0
            )
        );

        $inventoryStatuses = $this->InventoryStatuses->find('list', array('conditions' => array('InventoryStatuses.id !=' => 2)));
        if (isset($this->request['data']['DistCurrentInventoryBalanceLog']['store_id'])) {
            $StoreId = $this->request['data']['DistCurrentInventoryBalanceLog']['store_id'];
        } else {
            //$StoreId = $this->UserAuth->getStoreId();
            $StoreId = 0;
        }

        if (!$office_id) {
            $distStores = array();
        }


        $this->set(compact('distStores', 'inventoryStatuses', 'products', 'productCategories', 'StoreId', 'distStore_data', 'office_parent_id', 'offices', 'office_id'));
    }


    public function download_xl()
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes
        /* pr($this->request);
        exit; */
        $params = $this->request->query['data'];

        $this->loadModel('InventoryStatuses');
        $this->loadModel('ProductCategory');

        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if (CakeSession::read('Office.parent_office_id') != 0) {
            $conditions[] = array('DistStore.office_id' => CakeSession::read('Office.id'));
        }
        if (!empty($params['DistCurrentInventoryBalanceLog']['product_code'])) {
            $conditions[] = array('Product.product_code' => $params['DistCurrentInventoryBalanceLog']['product_code']);
        }
        if (!empty($params['DistCurrentInventoryBalanceLog']['inventory_status_id'])) {
            $conditions[] = array('DistCurrentInventoryBalanceLog.inventory_status_id' => $params['DistCurrentInventoryBalanceLog']['inventory_status_id']);
        } else {
            $conditions[] = array('DistCurrentInventoryBalanceLog.inventory_status_id !=' => 2);
        }
        if (!empty($params['DistCurrentInventoryBalanceLog']['product_id'])) {
            $conditions[] = array('DistCurrentInventoryBalanceLog.product_id' => $params['DistCurrentInventoryBalanceLog']['product_id']);
        }
        if (!empty($params['DistCurrentInventoryBalanceLog']['store_id'])) {
            $conditions[] = array('DistCurrentInventoryBalanceLog.store_id' => $params['DistCurrentInventoryBalanceLog']['store_id']);
        }
        if (!empty($params['DistCurrentInventoryBalanceLog']['product_categories_id'])) {
            $conditions[] = array('ProductCategory.id' => $params['DistCurrentInventoryBalanceLog']['product_categories_id']);
        }
        if (!empty($params['DistCurrentInventoryBalanceLog']['product_type_id'])) {
            $conditions[] = array('Product.product_type_id' => $params['DistCurrentInventoryBalanceLog']['product_type_id']);
        }
        $conditions['DistCurrentInventoryBalanceLog.transaction_date'] = '2023-03-04';
        // $conditions['OR'] = array(
        //     'DistCurrentInventoryBalanceLog.qty >' => 0,
        //     'DistCurrentInventoryBalanceLog.bonus_qty >' => 0
        // );
        //pr($conditions);die();
        $currentInventories = $this->DistCurrentInventoryBalanceLog->find('all', array(
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'product_categories',
                    'alias' => 'ProductCategory',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Product.product_category_id = ProductCategory.id'
                    )
                ),
                array(
                    'table' => 'dist_distributors',
                    'alias' => 'Distributor',
                    'conditions' => 'Distributor.id=DistStore.dist_distributor_id'
                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'INNER',
                    'conditions' => array('Office.id=DistStore.office_id')
                ),
                array(
                    'table' => 'dist_tsos',
                    'alias' => 'DistTso',
                    'type' => 'INNER',
                    'conditions' => array('DistTso.id=(
                                                          SELECT 
                                                            TOP 1 dist_tso_mappings.dist_tso_id
                                                          FROM dist_tso_mappings
                                                          WHERE dist_tso_mappings.dist_distributor_id = DistStore.dist_distributor_id order by dist_tso_mappings.id asc
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
            'fields' => array('DistCurrentInventoryBalanceLog.product_id',  'MAX(CAST(DistCurrentInventoryBalanceLog.other_column as NVARCHAR(max))) as other', 'DistCurrentInventoryBalanceLog.store_id', 'SUM(DistCurrentInventoryBalanceLog.qty) AS total', 'SUM(DistCurrentInventoryBalanceLog.bonus_qty) AS bonus_total', 'Product.name', 'Product.base_measurement_unit_id', 'Product.sales_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Distributor.name', 'ProductCategory.name', 'DistCurrentInventoryBalanceLog.inventory_status_id', 'Office.office_name', 'DistTso.name', 'DistAE.name'),
            'group' => array('DistCurrentInventoryBalanceLog.product_id', 'Product.name', 'Product.order', 'Product.base_measurement_unit_id', 'Product.sales_measurement_unit_id', 'Product.product_code', 'InventoryStatuses.name', 'Distributor.name', 'DistCurrentInventoryBalanceLog.store_id', 'ProductCategory.name', 'DistCurrentInventoryBalanceLog.inventory_status_id', 'Office.office_name', 'DistTso.name', 'DistAE.name', 'Office.order'),
            // 'order' => array('Product.order' => 'ASC'),
            'order' => array('Office.order asc', 'Product.order asc'),
            'recursive' => 1
        ));
        //     echo $this->DistCurrentInventoryBalanceLog->getLastQuery();
        // exit;
        /*  echo '<pre>';
        print_r($currentInventories);
        echo '</pre>';
        exit; */

        if (isset($params['DistCurrentInventoryBalanceLog']['category_summary']) && $params['DistCurrentInventoryBalanceLog']['category_summary'] == 1) {

            $summaryCategoryList = $this->ProductCategory->find(
                'all',
                array(
                    'recursive' => -1
                )
            );

            $category_summary = $params['DistCurrentInventoryBalanceLog']['category_summary'];
        } else {
            $category_summary = false;
            $summaryCategoryList = '';
        }
        $this->loadModel('MeasurementUnit');
        $measurement_unit_list = $this->MeasurementUnit->find('list', array('fields' => 'name'));
        $productCategories = $this->ProductCategory->find('list');
        $inventoryStatuses = $this->InventoryStatuses->find('list', array('conditions' => array('InventoryStatuses.id !=' => 2)));

        // echo $StoreId;exit; 
        /*----------------- Report Xl table creation ------------------*/
        $table = '';
        if ($summaryCategoryList) {

            $table .= '<table border="1">
            <tr>
                <th class="text-center">Category Name</th>
                <th class="text-center">Quantity</th>
            </tr>';
            foreach ($summaryCategoryList as $result) :

                if ($this->admin_getCategoryQtyTotal($result['ProductCategory']['id']) > 0) {
                    $table .= '<tr>';
                    $table .= '<td class="text-center">' . h($result['ProductCategory']['name']) . '</td>';
                    $table .= '<td class="text-center">' . $this->admin_getCategoryQtyTotal($result['ProductCategory']['id']) . '</td>';
                    $table .= '</tr>';
                }
            endforeach;
            $table .= '</table>';
        } else {

            $table .= '<table border="1">';
            $table .= '<tr>';

            $table .= '<th class="text-center">Area Office</th>
            <th class="text-center">Area Executive</th>
            <th class="text-center">TSO</th>';
            $table .= '<th class="text-center">Store</th>';

            $table .= '<th class="text-center">Product</th>
                <th class="text-center">Product Unit</th>
                <th class="text-center">Product Code</th>
                <th class="text-center">Inventory Status</th>
                <th class="text-center">Product Category</th>
                
                <th class="text-center">Quantity(Sale Unit)</th>
				<th class="text-center">Bonus Quantity(Sale Unit)</th>
				<th class="text-center">Damage Quantity(Sale Unit)</th>
            </tr>';
            foreach ($currentInventories as $currentInventory) {
                $table .= '<tr>';
                $table .= '<td>' . h($currentInventory['Office']['office_name']) . '</td>';
                $table .= '<td>' . h($currentInventory['DistAE']['name']) . '</td>';
                $table .= '<td>' . h($currentInventory['DistTso']['name']) . '</td>';
                $table .= '<td>' . trim($currentInventory['Distributor']['name']) . '</td>';
                $table .= '<td>' . h($currentInventory['Product']['name']) . '</td>';
                $table .= '<td>' . h($measurement_unit_list[$currentInventory['Product']['base_measurement_unit_id']]) . '</td>';
                $table .= '<td class="text-center">' . h($currentInventory['Product']['product_code']) . '</td>';
                $table .= '<td class="text-center">' . h($currentInventory['InventoryStatuses']['name']) . '</td>';
                $table .= '<td class="text-center">' . h($currentInventory['ProductCategory']['name']) . '</td>';

                $table .= '<td class="text-center">' . $this->unit_convertfrombase($currentInventory['DistCurrentInventoryBalanceLog']['product_id'], $currentInventory['Product']['sales_measurement_unit_id'], ($currentInventory[0]['total'])) . '</td>';
                $table .= '<td class="text-center">' . $this->unit_convertfrombase($currentInventory['DistCurrentInventoryBalanceLog']['product_id'], $currentInventory['Product']['sales_measurement_unit_id'], ($currentInventory[0]['bonus_total'])) . '</td>';
                $table .= '<td class="text-center">' . h(json_decode($currentInventory['0']['other'], true)['demage_qty']) . '</td>';
                $table .= '</tr>';
            }
            $table .= '</table>';
        }
        header('Content-Type:application/force-download');
        header('Content-Disposition: attachment; filename="inventory.xls"');
        header("Cache-Control: ");
        header("Pragma: ");
        echo $table;
        $this->autoRender = false;
    }
}
