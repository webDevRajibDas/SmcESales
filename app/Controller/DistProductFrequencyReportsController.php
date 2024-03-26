<?php

App::uses('AppController', 'Controller');

/**
 * Challans Controller
 *
 * @property Challan $Challan
 * @property PaginatorComponent $Paginator
 */
class DistProductFrequencyReportsController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Filter.Filter');
    public $uses = array('DistMemo', 'DistMemoDetail', 'Office');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index()
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes
        $this->set('page_title', 'Product Frequency Reports');

        //for region office list
        $region_offices = $this->Office->find('list', array(
            'conditions' => array('Office.office_type_id' => 3),
            'order' => array('office_name' => 'asc')
        ));

        $region_office_id = 0;

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
                    'office_type_id'     => 2,
                    'parent_office_id'     => $region_office_id,

                    "NOT" => array("id" => array(30, 31, 37))
                ),
                'order' => array('office_name' => 'asc')
            ));

            $office_ids = array_keys($offices);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
            $office_id = $this->UserAuth->getOfficeId();

            $offices = $this->Office->find('list', array(
                'conditions' => array(
                    'id'     => $office_id,
                ),
                'order' => array('office_name' => 'asc')
            ));
        }
        $rows_array = array(
            'national' => 'National',
            'region' => 'Region',
            'area' => 'Area',
            'ae' => 'SAE',
            'tso' => 'TSO',
            'sr' => 'SR'
        );
        if ($this->request->is('post')) {
            $request_data = $this->request->data;
            if (isset($request_data['PFR']['office_id']) && $request_data['PFR']['office_id'])
                $office_id = $request_data['PFR']['office_id'];
            $rows = $request_data['PFR']['rows'];
            if (isset($request_data['PFR']['region_office_id']) && $request_data['PFR']['region_office_id']) {
                $region_office_id = $request_data['PFR']['region_office_id'];
                $offices = $this->Office->find('list', array(
                    'conditions' => array(
                        'office_type_id'     => 2,
                        'parent_office_id'     => $region_office_id,

                        "NOT" => array("id" => array(30, 31, 37))
                    ),
                    'order' => array('office_name' => 'asc')
                ));

                $office_ids = array_keys($offices);
            }
            @$date_from = date('Y-m-d', strtotime($this->request->data['PFR']['date_from']));
            @$date_to = date('Y-m-d', strtotime($this->request->data['PFR']['date_to']));
            $group = array();
            $order = array();
            $fields = array();

            $group_main = '';
            $order_main = '';
            $fields_main = '';

            if ($rows == 'region') {
                $group[] = 'ParentOffice.office_name,ParentOffice.order';
                $fields[] = 'ParentOffice.office_name as reg_office,ParentOffice.[order] as reg_order';
                $order[] = 'ParentOffice.order';

                $group_main .= 'GROUP BY reg_office,reg_order';
                $order_main .= 'ORDER BY reg_order';
                $fields_main .= 'reg_office';
            } elseif ($rows == 'area') {
                $group[] = 'ParentOffice.office_name,ParentOffice.order,Office.office_name,Office.order';
                $fields[] = 'ParentOffice.office_name as reg_office,Office.office_name as office,ParentOffice.[order] as reg_order,Office.[order] as office_order';
                $order[] = 'ParentOffice.order,Office.order';

                $group_main .= 'GROUP BY reg_office,office,reg_order,office_order';
                $order_main .= 'ORDER BY reg_order,office_order';
                $fields_main .= 'reg_office,office';
            } elseif ($rows == 'sr') {
                $group[] = 'ParentOffice.office_name,ParentOffice.order,Office.office_name,Office.order,AE.name,TSO.name,db.name,SR.name';
                $fields[] = 'ParentOffice.office_name as reg_office,Office.office_name as office,ParentOffice.[order] as reg_order,Office.[order] as office_order,AE.name as ae,TSO.name as tso,db.name as db,SR.name as sr';
                $order[] = 'ParentOffice.order,Office.order';

                $group_main .= 'GROUP BY reg_office,office,reg_order,office_order,ae,tso,db,sr';
                $order_main .= 'ORDER BY reg_order,office_order,ae,tso,db,sr';
                $fields_main .= 'reg_office,office,ae,tso,db,sr';
            } elseif ($rows == 'tso') {
                $group[] = 'ParentOffice.office_name,ParentOffice.order,Office.office_name,Office.order,AE.name,TSO.name';
                $fields[] = 'ParentOffice.office_name as reg_office,Office.office_name as office,ParentOffice.[order] as reg_order,Office.[order] as office_order,AE.name as ae,TSO.name as tso';
                $order[] = 'ParentOffice.order,Office.order';

                $group_main .= 'GROUP BY reg_office,office,reg_order,office_order,ae,tso';
                $order_main .= 'ORDER BY reg_order,office_order,ae,tso';
                $fields_main .= 'reg_office,office,ae,tso';
            } elseif ($rows == 'ae') {
                $group[] = 'ParentOffice.office_name,ParentOffice.order,Office.office_name,Office.order,AE.name';
                $fields[] = 'ParentOffice.office_name as reg_office,Office.office_name as office,ParentOffice.[order] as reg_order,Office.[order] as office_order,AE.name as ae';
                $order[] = 'ParentOffice.order,Office.order';

                $group_main .= 'GROUP BY reg_office,office,reg_order,office_order,ae';
                $order_main .= 'ORDER BY reg_order,office_order,ae';
                $fields_main .= 'reg_office,office,ae';
            }
            $fields_main .= ',COUNT(case when total_product =1 then memo_id END) as count_1,
            COUNT(case when total_product =2 then memo_id END) as count_2,
            COUNT(case when total_product =3 then memo_id END) as count_3,
            COUNT(case when total_product =4 then memo_id END) as count_4,
            COUNT(case when total_product =5 then memo_id END) as count_5,
            COUNT(case when total_product =6 then memo_id END) as count_6,
            COUNT(case when total_product =7 then memo_id END) as count_7,
            COUNT(case when total_product =8 then memo_id END) as count_8,
            COUNT(case when total_product =9 then memo_id END) as count_9,
            COUNT(case when total_product =10 then memo_id END) as count_10,
            COUNT(case when total_product >10 then memo_id END) as count_11,
            COUNT(memo_id) as total,
            SUM(total_product) as total_product
            ';
            $group = array_merge($group, array('Memo.id'));
            $fields = array_merge($fields, array(
                'Memo.id as memo_id',
                'COUNT(case when MemoDetail.price >0 then MemoDetail.id end) as total_product'
            ));
            if ($office_id) {
                $conditions['Office.id'] = $office_id;
            } elseif ($region_office_id) {
                $conditions['ParentOffice.id'] = $region_office_id;
            }
            $conditions['Memo.memo_date BETWEEN ? AND ?'] = array($date_from, $date_to);
            $conditions['Memo.status >'] = 0;
            $DB = $this->DistMemo->getDataSource();
            $memo_subquery = $DB->buildStatement(
                array(
                    'conditions' => $conditions,
                    'fields' => $fields,
                    'group' => $group,
                    'table'      => $DB->fullTableName($this->DistMemo),
                    'alias'      => 'Memo',
                    'joins' => array(
                        array(
                            'table' => 'offices',
                            'alias' => 'Office',
                            'type' => 'Inner',
                            'conditions' => 'Office.id=Memo.office_id',
                        ),
                        array(
                            'table' => 'offices',
                            'alias' => 'ParentOffice',
                            'type' => 'Inner',
                            'conditions' => 'ParentOffice.id=Office.parent_office_id',
                        ),
                        array(
                            'table' => 'dist_area_executives',
                            'alias' => 'AE',
                            'type' => 'Inner',
                            'conditions' => 'AE.id=Memo.ae_id',
                        ),
                        array(
                            'table' => 'dist_tsos',
                            'alias' => 'TSO',
                            'type' => 'Inner',
                            'conditions' => 'TSO.id=Memo.tso_id',
                        ),
                        array(
                            'table' => 'dist_sales_representatives',
                            'alias' => 'SR',
                            'type' => 'left',
                            'conditions' => 'sr.id=Memo.sr_id',
                        ),
                        array(
                            'table' => 'dist_distributors',
                            'alias' => 'db',
                            'type' => 'left',
                            'conditions' => 'db.id=Memo.distributor_id',
                        ),
                        array(
                            'table' => 'dist_memo_details',
                            'alias' => 'MemoDetail',
                            'type' => 'Inner',
                            'conditions' => 'MemoDetail.dist_memo_id=Memo.id',
                        )
                    )
                ),
                $this->Memo
            );
            $fields_main = TRIM($fields_main, ',');
            $sql = "SELECT $fields_main FROM ($memo_subquery) as memo $group_main $order_main";
            $result_set_db = $this->DistMemo->query($sql);
            $this->set(compact('result_set_db'));
        }
        $this->set(compact('office_id', 'office_parent_id', 'date_from', 'date_to', 'region_offices', 'offices', 'rows', 'request_data', 'region_office_id', 'rows_array'));
    }
}
