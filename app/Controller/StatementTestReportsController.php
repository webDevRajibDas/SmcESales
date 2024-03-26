<?php
App::uses('AppController', 'Controller');

/**
 * OutletCharacteristicSettings Controller
 *
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class StatementTestReportsController extends AppController
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
        $this->set('page_title', 'Statement Test Report');

        //For Region office list
        $region_offices = $this->Office->find('list', array(
            'conditions' => array('Office.office_type_id' => 3),
            'order' => array('office_name' => 'asc')
        ));

        $types = array(
            'territory' => 'By Territory',
            'so' => 'By SO',
        );

        $unit_types = array(
            '1' => 'Sales Unit',
            '2' => 'Base Unit',
        );
        $report_type = array(
            '1' => 'Inventory Report',
            '2' => 'Other Issue Report',
        );
        $product_types = $this->Product->ProductType->find('list', array('order' => array('name' => 'asc')));

        $sql = 'SELECT * FROM product_sources';
        $sources_datas = $this->Product->query($sql);
        $product_sources = array();
        foreach ($sources_datas as $sources_data) {
            $product_sources[$sources_data[0]['name']] = $sources_data[0]['name'];
        }

        $region_office_id = 0;
        $office_parent_id = $this->UserAuth->getOfficeParentId();

        $this->set(compact('types', 'region_offices', 'unit_types','report_type','product_types','product_sources','office_parent_id'));

        $office_conditions = array('Office.office_type_id' => 2);

        if ($office_parent_id ==0){
            //$this->dd('Head office');
            $office_id = 0;
        }elseif ($office_parent_id == 14){
            $this->dd('office condition 14');
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
        }else{
            //$this->dd('Area office');
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
            $office_id = $this->UserAuth->getOfficeId();

            $offices = $this->Office->find('list', array(
                'conditions' => array(
                    'id' => $office_id,
                ),
                'order' => array('office_name' => 'asc')
            ));
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
            $this->dd($territory_list);

            $territories = array();

            foreach ($territory_list as $key => $value) {
                $territories[$value['Territory']['id']] = $value['SalesPerson']['name'] . ' (' . $value['Territory']['name'] . ')';
            }
            $this->dd($offices);


        }


        //$this->set(compact( 'region_offices'));

    }
}
