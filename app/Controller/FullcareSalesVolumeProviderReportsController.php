<?php
App::uses('AppController', 'Controller');
/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
//Configure::write('debug',2);
class FullcareSalesVolumeProviderReportsController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $uses = array('Memo', 'Market', 'Program', 'Division', 'District', 'Thana', 'SalesPerson', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'Combination', 'MemoDetail', 'MeasurementUnit', 'ProductCategory', 'TerritoryAssignHistory', 'Office', 'Territory', 'ProductType');
    
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */

    public function admin_index()
	{

		$this->set('page_title', 'FullCare Sales Volume Program Provider Report');

		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 99999); //300 seconds = 5 minutes

        $conditions = array(
            'NOT' => array('Product.product_category_id' => 32),
            'is_active' => 1,
            'Product.product_type_id' => 1
        );

        $product_list = $this->Product->find('list', array(
            'fields' => array('id', 'name'),
            'conditions' => $conditions,
            'recursive' => -1
        ));
		
		//for divisions
		$divisions = $this->Division->find('list', array(
			'order' =>  array('name' => 'asc')
		));
		//end for divisions
        


		$office_parent_id = $this->UserAuth->getOfficeParentId();

		$this->set(compact('office_parent_id'));

		if ($office_parent_id == 0) {
			$office_conditions = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
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
					'office_type_id' 	=> 2,
					'parent_office_id' 	=> $region_office_id,

					"NOT" => array("id" => array(30, 31, 37))
				),
				'order' => array('office_name' => 'asc')
			));

			$office_id = array_keys($offices);
		} else {
			$office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
			$office_id = $this->UserAuth->getOfficeId();
		}

        $this->set(compact('product_list', 'divisions', 'office_id'));


		$request_data = array();

		if ($this->request->is('post')) {
			

            $product_id = isset($this->request->data['Memo']['product_id']) != '' ? $this->request->data['Memo']['product_id'] : 0;
            $division_id = isset($this->request->data['Memo']['division_id']) != '' ? $this->request->data['Memo']['division_id'] : 0;
            $district_id = isset($this->request->data['Memo']['district_id']) != '' ? $this->request->data['Memo']['district_id'] : 0;
			$thana_id = isset($this->request->data['Memo']['thana_id']) != '' ? $this->request->data['Memo']['thana_id'] : 0;

          
			$date_from = date('Y-m-d', strtotime($this->request->data['Memo']['date_from']));
			$date_to = date('Y-m-d', strtotime($this->request->data['Memo']['date_to']));


            //district list 
			$districts = $this->District->find('list', array(
				'conditions' => array('District.division_id' => $division_id),
				'order' => array('District.name' => 'asc')
			));
			

            $thanas = $this->Thana->find('list', array(
                'conditions' => array('Thana.district_id' => $district_id),
                'order' => array('Thana.name' => 'asc')
            ));
			
			
			$request_data = $this->request->data;

            $this->set(compact('districts', 'thanas', 'date_from', 'date_to', 'request_data'));

            $conditions = array(
                'Memo.memo_date BETWEEN ? and ? ' => array($date_from, $date_to),
                'Memo.gross_value >' => 0,
                'Memo.status !=' => 0,
            );
            
            if($product_id)$conditions['MemoDetail.product_id'] = $product_id;
            if ($division_id) $conditions['Division.id'] = $division_id;
            if ($district_id) $conditions['District.id'] = $district_id;
			if ($thana_id) $conditions['Thana.id'] = $thana_id;
			if ($office_id) $conditions['Office.id'] = $office_id;

            
           $fields = array(
                'Office.office_name',
                'Territory.name',
                'SalesPeople.name',
                'Thana.name',
                'Market.name',
                'Outlet.name',
                'ProgramCode.code',
                'Memo.memo_date',
                'Memo.memo_no',
                'MemoDetail.sales_qty as qty'
             );
            $group = array(
                'Office.office_name',
                'Territory.name',
                'SalesPeople.name',
                'Thana.name',
                'Market.name',
                'Outlet.name',
                'ProgramCode.code',
                'Memo.memo_date',
                'Memo.memo_no',
                'MemoDetail.sales_qty'
            );

            $where = " where programs.assigned_date <= '$date_to' AND (programs.deassigned_date is null OR programs.deassigned_date >'$date_from')";

            $results = $this->Memo->find('all', array(
                'conditions' => $conditions,
                'joins' => array(
                    array(
                        'alias' => 'MemoDetail',
                        'table' => 'memo_details',
                        'type' => 'INNER',
                        'conditions' => 'Memo.id = MemoDetail.memo_id'
                    ),
                    array(
                        'alias' => 'Office',
                        'table' => 'offices',
                        'type' => 'INNER',
                        'conditions' => 'Memo.office_id = Office.id'
                    ),
                    array(
                        'alias' => 'Product',
                        'table' => 'products',
                        'type' => 'INNER',
                        'conditions' => 'MemoDetail.product_id = Product.id'
                    ),
                    array(
                        'alias' => 'Territory',
                        'table' => 'territories',
                        'type' => 'LEFT',
                        'conditions' => 'Memo.territory_id = Territory.id'
                    ),
                    array(
                        'alias' => 'SalesPeople',
                        'table' => 'sales_people',
                        'type' => 'LEFT',
                        'conditions' => 'Territory.id = SalesPeople.territory_id'
                    ),
                    array(
                        'alias' => 'Outlet',
                        'table' => 'outlets',
                        'type' => 'INNER',
                        'conditions' => 'Memo.outlet_id = Outlet.id'
                    ),
                    array(
                        'alias' => 'ProgramOutlet',
                        'table' => "(select outlet_id from programs $where group by outlet_id )",
                        'type' => 'INNER',
                        'conditions' => 'ProgramOutlet.outlet_id = Memo.outlet_id'
                    ),
                    array(
                        'alias' => 'ProgramCode',
                        'table' => "programs",
                        'type' => 'INNER',
                        'conditions' => 'ProgramCode.outlet_id = ProgramOutlet.outlet_id'
                    ),
                    array(
                        'alias' => 'Market',
                        'table' => 'markets',
                        'type' => 'INNER',
                        'conditions' => 'Outlet.market_id = Market.id'
                    ),
                    array(
                        'alias' => 'Thana',
                        'table' => 'thanas',
                        'type' => 'INNER',
                        'conditions' => 'Market.thana_id = Thana.id'
                    ),
                    array(
                        'alias' => 'District',
                        'table' => 'districts',
                        'type' => 'INNER',
                        'conditions' => 'Thana.district_id = District.id'
                    ),
                    array(
                        'alias' => 'Division',
                        'table' => 'divisions',
                        'type' => 'INNER',
                        'conditions' => 'District.division_id = Division.id'
                    )
                ),
                'fields' => $fields,
                'group' => $group,
                'recursive' => -1
            ));
           //pr($results);exit;
            $this->set(compact('results'));

		}else {
            $this->request->data['Memo']['product_id'] = 465;
        }

	}
}
