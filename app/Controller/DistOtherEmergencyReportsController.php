<?php
App::uses('AppController', 'Controller');
/**
 * OtherEmergencyReports Controller
 *
 *  This controller for all emergency report which SMC required
 * 
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
ini_set('memory_limit', '-1');
set_time_limit(0);
class DistOtherEmergencyReportsController extends AppController
{
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Session', 'Filter.Filter');
    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index()
    {
        $this->set('page_title', 'All Emergency Reports');
    }
    public function admin_crash_trade_program()
    {
        $this->set('page_title', 'Crash Trade Program(12 May 2022 TO 31 May 2022)');
        $this->loadModel('Office');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
        } elseif ($office_parent_id == 14) {
            $region_office_id = $this->UserAuth->getOfficeId();
            $office_conditions = array('Office.parent_office_id' => $region_office_id);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $offices = $this->Office->find('list', array(
            'conditions' => $office_conditions
        ));

        $office_conditions = 'AND  ofc.id in (' . join(',', array_keys($offices)) . ')';

        $report_data = "
                        select
                            ofc.office_name as office,
                            tso.name as tso,
                            ae.name as ae,
                            dd.name as db,
                            mk.name as market,
                            ot.name as outlet,
                            oc.category_name as outlet_category,
                            /*m.id as memo_id,*/
                            m.dist_memo_no as memo_no,
                            m.dist_order_no as order_no,
                            m.memo_date as memo_date,
                            eligible_memo.order_date as order_date,
                            m.gross_value as memo_total_gross,
                            sum(sales_qty*price) as value_expect
                        from dist_memos m
                        inner join 
                        (
                            select m.id as memo_id,o.category_id as outlet_category,count(md.id) as total_product,do.order_date as order_date from dist_memos m
                            inner join dist_orders do on do.dist_order_no=m.dist_order_no
                            inner join dist_memo_details md on m.id=md.dist_memo_id
                            inner join products p on p.id=md.product_id
                            inner join dist_outlets o on o.id=m.outlet_id
                            where 
                                do.order_date between '2022-05-12' and '2022-05-31'
                                and p.source='SMCEL'
                                and p.brand_id not in (46,57)
                                and p.product_type_id=1
                                and md.price>0
                            group by
                                m.id,do.order_date,o.category_id having count(md.id)>=case when o.category_id=18 then 1 else 2 end
                        ) as eligible_memo on m.id=eligible_memo.memo_id
                        inner join dist_orders do on do.dist_order_no=m.dist_order_no
                        inner join dist_memo_details md on m.id=md.dist_memo_id
                        inner join products p on p.id=md.product_id
                        inner join dist_distributors dd on dd.id=m.distributor_id
                        inner join dist_outlets ot on ot.id=m.outlet_id
                        inner join dist_outlet_categories oc on oc.id=ot.category_id
                        inner join dist_markets mk on mk.id=ot.dist_market_id
                        inner join dist_tsos tso on tso.id=do.tso_id
                        inner join dist_area_executives ae on ae.id=tso.dist_area_executive_id
                        inner join offices ofc on ofc.id=ae.office_id
                        where 
                            do.order_date between '2022-05-12' and '2022-05-31'
                            and p.source='SMCEL'
                            and p.brand_id not in (46,57)
                            and p.product_type_id=1
                            and md.price>0
                            $office_conditions
                        group by
                            m.id,
                            m.dist_memo_no,
                            m.dist_order_no,
                            m.memo_date,
                            eligible_memo.order_date,
                            m.gross_value,
                            dd.name,
                            ot.name,
                            oc.category_name,
                            mk.name,
                            tso.name,
                            ae.name,
                            ofc.office_name,
                            ofc.[order]
                            having sum(sales_qty*price) >= 10000
                        order by
                            ofc.[order],
                            tso.name,
                            ae.name,
                            mk.name,
                            ot.name
                    ";
        $report_data = $this->Office->query($report_data);
        $this->set(compact('report_data'));
    }

    public function admin_crash_trade_program_01_21june()
    {
        $this->set('page_title', 'Crash Trade Program(01 June 2022 TO 21 June 2022)');
        $this->loadModel('Office');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
        } elseif ($office_parent_id == 14) {
            $region_office_id = $this->UserAuth->getOfficeId();
            $office_conditions = array('Office.parent_office_id' => $region_office_id);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $offices = $this->Office->find('list', array(
            'conditions' => $office_conditions
        ));

        $office_conditions = 'AND  ofc.id in (' . join(',', array_keys($offices)) . ')';

        $report_data = "
                        select
                            ofc.office_name as office,
                            tso.name as tso,
                            ae.name as ae,
                            dd.name as db,
                            mk.name as market,
                            ot.name as outlet,
                            oc.category_name as outlet_category,
                            /*m.id as memo_id,*/
                            m.dist_memo_no as memo_no,
                            m.dist_order_no as order_no,
                            m.memo_date as memo_date,
                            eligible_memo.order_date as order_date,
                            m.gross_value as memo_total_gross,
                            sum(sales_qty*price) as value_expect
                        from dist_memos m WITH (NOLOCK)
                        inner join 
                        (
                            select m.id as memo_id,o.category_id as outlet_category,count(md.id) as total_product,do.order_date as order_date from dist_memos m WITH (NOLOCK)
                            inner join dist_orders do WITH (NOLOCK) on do.dist_order_no=m.dist_order_no
                            inner join dist_memo_details md WITH (NOLOCK) on m.id=md.dist_memo_id
                            inner join products p WITH (NOLOCK) on p.id=md.product_id
                            inner join dist_outlets o WITH (NOLOCK) on o.id=m.outlet_id
                            where 
                                do.order_date between '2022-06-01' and '2022-06-21'
                                and p.source='SMCEL'
                                and p.brand_id not in (46,57)
                                and p.product_type_id=1
                                and md.price>0
                            group by
                                m.id,do.order_date,o.category_id having count(md.id)>=case when o.category_id=18 then 1 else 2 end
                        ) as eligible_memo on m.id=eligible_memo.memo_id
                        inner join dist_orders do on do.dist_order_no=m.dist_order_no
                        inner join dist_memo_details md WITH (NOLOCK) on m.id=md.dist_memo_id
                        inner join products p WITH (NOLOCK) on p.id=md.product_id
                        inner join dist_distributors dd WITH (NOLOCK) on dd.id=m.distributor_id
                        inner join dist_outlets ot WITH (NOLOCK) on ot.id=m.outlet_id
                        inner join dist_outlet_categories oc WITH (NOLOCK) on oc.id=ot.category_id
                        inner join dist_markets mk WITH (NOLOCK) on mk.id=ot.dist_market_id
                        inner join dist_tsos tso on tso.id=do.tso_id
                        left join dist_area_executives ae WITH (NOLOCK) on ae.id=tso.dist_area_executive_id
                        left join offices ofc WITH (NOLOCK) on ofc.id=ae.office_id
                        where 
                            do.order_date between '2022-06-01' and '2022-06-21'
                            and p.source='SMCEL'
                            and p.brand_id not in (46,57)
                            and p.product_type_id=1
                            and md.price>0
                            $office_conditions
                        group by
                            m.id,
                            m.dist_memo_no,
                            m.dist_order_no,
                            m.memo_date,
                            eligible_memo.order_date,
                            m.gross_value,
                            dd.name,
                            ot.name,
                            oc.category_name,
                            mk.name,
                            tso.name,
                            ae.name,
                            ofc.office_name,
                            ofc.[order]
                            having sum(sales_qty*price) >= 10000
                        order by
                            ofc.[order],
                            tso.name,
                            ae.name,
                            mk.name,
                            ot.name
                    ";
        $report_data = $this->Office->query($report_data);
        $this->set(compact('report_data'));
    }

    public function admin_crash_trade_program_22_30june()
    {
        $this->set('page_title', 'Crash Trade Program(22 June 2022 TO 30 June 2022)');
        $this->loadModel('Office');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
        } elseif ($office_parent_id == 14) {
            $region_office_id = $this->UserAuth->getOfficeId();
            $office_conditions = array('Office.parent_office_id' => $region_office_id);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $offices = $this->Office->find('list', array(
            'conditions' => $office_conditions
        ));

        $office_conditions = 'AND  ofc.id in (' . join(',', array_keys($offices)) . ')';

        $report_data = "
                        select
                            ofc.office_name as office,
                            tso.name as tso,
                            ae.name as ae,
                            dd.name as db,
                            mk.name as market,
                            ot.name as outlet,
                            oc.category_name as outlet_category,
                            /*m.id as memo_id,*/
                            m.dist_memo_no as memo_no,
                            m.dist_order_no as order_no,
                            m.memo_date as memo_date,
                            eligible_memo.order_date as order_date,
                            m.gross_value as memo_total_gross,
                            sum(sales_qty*price) as value_expect
                        from dist_memos m WITH (NOLOCK)
                        inner join 
                        (
                            select m.id as memo_id,o.category_id as outlet_category,count(md.id) as total_product,do.order_date as order_date from dist_memos m WITH (NOLOCK)
                            inner join dist_orders do WITH (NOLOCK) on do.dist_order_no=m.dist_order_no
                            inner join dist_memo_details md WITH (NOLOCK) on m.id=md.dist_memo_id
                            inner join products p WITH (NOLOCK) on p.id=md.product_id
                            inner join dist_outlets o WITH (NOLOCK) on o.id=m.outlet_id
                            where 
                                do.order_date between '2022-06-22' and '2022-06-30'
                                and p.source='SMCEL'
                                and p.product_type_id=1
                                and md.price>0
                            group by
                                m.id,do.order_date,o.category_id
                        ) as eligible_memo on m.id=eligible_memo.memo_id
                        inner join dist_orders do on do.dist_order_no=m.dist_order_no
                        inner join dist_memo_details md WITH (NOLOCK) on m.id=md.dist_memo_id
                        inner join products p WITH (NOLOCK) on p.id=md.product_id
                        inner join dist_distributors dd WITH (NOLOCK) on dd.id=m.distributor_id
                        inner join dist_outlets ot WITH (NOLOCK) on ot.id=m.outlet_id
                        inner join dist_outlet_categories oc WITH (NOLOCK) on oc.id=ot.category_id
                        inner join dist_markets mk WITH (NOLOCK) on mk.id=ot.dist_market_id
                        inner join dist_tsos tso on tso.id=do.tso_id
                        left join dist_area_executives ae on ae.id=tso.dist_area_executive_id
                        left join offices ofc on ofc.id=ae.office_id
                        where 
                            do.order_date between '2022-06-22' and '2022-06-30'
                            and p.source='SMCEL'
                            and p.product_type_id=1
                            and md.price>0
                            $office_conditions
                        group by
                            m.id,
                            m.dist_memo_no,
                            m.dist_order_no,
                            m.memo_date,
                            eligible_memo.order_date,
                            m.gross_value,
                            dd.name,
                            ot.name,
                            oc.category_name,
                            mk.name,
                            tso.name,
                            ae.name,
                            ofc.office_name,
                            ofc.[order]
                            having sum(sales_qty*price) >= 10000
                        order by
                            ofc.[order],
                            tso.name,
                            ae.name,
                            mk.name,
                            ot.name
                    ";
        $report_data = $this->Office->query($report_data);
        $this->set(compact('report_data'));
    }

    public function admin_ors_traders_incentive_offer_june_22_to_july_05_2022()
    {
        $this->set('page_title', '(Traders)Tornado offer 2 ORS-N(22 June 2022 TO 05 July 2022)');
        $this->loadModel('Office');
        $this->loadModel('DistMemo');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
        } elseif ($office_parent_id == 14) {
            $region_office_id = $this->UserAuth->getOfficeId();
            $office_conditions = array('Office.parent_office_id' => $region_office_id);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $offices = $this->Office->find('list', array(
            'conditions' => $office_conditions
        ));
        $cond = array();

        $cond['DistOrder.order_date between ? AND ?'] = array('2022-06-22', '2022-07-05');
        $cond['DistMemo.office_id'] = array_keys($offices);
        $cond['MemoDetail.product_id'] = 47;
        $cond['MemoDetail.price >'] = 0;

        $memos = $this->DistMemo->find('all', array(
            'conditions' => $cond,
            'joins' => array(
                array(
                    'table' => 'dist_orders',
                    'alias' => 'DistOrder',
                    'type' => 'Inner',
                    'conditions' => 'DistOrder.dist_order_no=DistMemo.dist_order_no'
                ),
                array(
                    'table' => 'dist_memo_details',
                    'alias' => 'MemoDetail',
                    'type' => 'Inner',
                    'conditions' => 'MemoDetail.dist_memo_id=DistMemo.id'
                ),
                array(
                    'table' => 'product_measurements',
                    'alias' => 'SalesToBase',
                    'type' => 'left',
                    'conditions' => 'SalesToBase.product_id=MemoDetail.product_id AND SalesToBase.measurement_unit_id=CASE WHEN MemoDetail.measurement_unit_id is null OR MemoDetail.measurement_unit_id=0 then 7 ELSE MemoDetail.measurement_unit_id END'
                ),
                array(
                    'table' => 'product_measurements',
                    'alias' => 'BaseToCartoon',
                    'type' => 'left',
                    'conditions' => 'BaseToCartoon.product_id=MemoDetail.product_id AND BaseToCartoon.measurement_unit_id=16'
                ),
                array(
                    'table' => 'dist_outlets',
                    'alias' => 'Outlet',
                    'type' => 'Inner',
                    'conditions' => 'DistMemo.outlet_id=Outlet.id'
                ),
                array(
                    'table' => 'dist_markets',
                    'alias' => 'Market',
                    'type' => 'Inner',
                    'conditions' => 'DistMemo.market_id=Market.id'
                ),
                array(
                    'table' => 'dist_tsos',
                    'alias' => 'TSO',
                    'type' => 'Inner',
                    'conditions' => 'TSO.id=DistOrder.tso_id'
                ),
                array(
                    'table' => 'dist_area_executives',
                    'alias' => 'AE',
                    'type' => 'Inner',
                    'conditions' => 'AE.id=TSO.dist_area_executive_id'
                ),
                array(
                    'table' => 'dist_distributors',
                    'alias' => 'DD',
                    'type' => 'Inner',
                    'conditions' => 'DD.id=DistMemo.distributor_id'
                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'Inner',
                    'conditions' => 'AE.office_id=Office.id'
                ),

            ),
            'group' => array(
                'DistMemo.dist_memo_no',
                'DistOrder.dist_order_no',
                'DistOrder.order_date',
                'DistMemo.memo_date',
                'dd.name',
                'Outlet.name',
                'Market.name',
                'Office.office_name',
                'TSO.name',
                'AE.name',
                'Office.order having SUM(ROUND(MemoDetail.sales_qty*CASE WHEN SalesToBase.qty_in_base is null then 1 else SalesToBase.qty_in_base END,0)/CASE WHEN BaseToCartoon.qty_in_base is null then 1 else BaseToCartoon.qty_in_base END)>=5'
            ),
            'order' => array(
                'Office.order',
                'AE.name',
                'TSO.name',
                'DistMemo.memo_date',
                'Outlet.name',
                'Market.name',
            ),
            'fields' => array(
                'DistMemo.dist_memo_no as memo_no',
                'DistOrder.dist_order_no as order_no',
                'DistMemo.memo_date as memo_date',
                'DistOrder.order_date as order_date',
                'dd.name as db',
                'Outlet.name as outlet',
                'Market.name as market',
                'Office.office_name as office',
                'TSO.name as tso',
                'Ae.name as ae',
                'SUM(ROUND(MemoDetail.sales_qty*CASE WHEN SalesToBase.qty_in_base is null then 1 else SalesToBase.qty_in_base END,0)/CASE WHEN BaseToCartoon.qty_in_base is null then 1 else BaseToCartoon.qty_in_base END) as qty'
            ),
            'recursive' => -1
        ));
        $this->set(compact('memos'));
    }

    public function admin_ors_dbs_incentive_offer_june_22_to_july_05_2022()
    {
        $this->set('page_title', '(DB)Tornado offer 2 ORS-N(22 June 2022 TO 28th june 2022)');
        $this->loadModel('Office');
        $this->loadModel('Memo');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
        } elseif ($office_parent_id == 14) {
            $region_office_id = $this->UserAuth->getOfficeId();
            $office_conditions = array('Office.parent_office_id' => $region_office_id);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $offices = $this->Office->find('list', array(
            'conditions' => $office_conditions
        ));
        $cond = array();

        $cond['Memo.memo_date between ? AND ?'] = array('2022-06-22', '2022-06-28');
        $cond['Memo.office_id'] = array_keys($offices);
        $cond['MemoDetail.product_id'] = 47;
        $cond['MemoDetail.price >'] = 0;
        $cond['Memo.is_distributor'] = 1;

        $memos = $this->Memo->find('all', array(
            'conditions' => $cond,
            'joins' => array(
                array(
                    'table' => 'memo_details',
                    'alias' => 'MemoDetail',
                    'type' => 'Inner',
                    'conditions' => 'MemoDetail.memo_id=Memo.id'
                ),
                array(
                    'table' => 'product_measurements',
                    'alias' => 'SalesToBase',
                    'type' => 'left',
                    'conditions' => 'SalesToBase.product_id=MemoDetail.product_id AND SalesToBase.measurement_unit_id=CASE WHEN MemoDetail.measurement_unit_id is null OR MemoDetail.measurement_unit_id=0 then 7 ELSE MemoDetail.measurement_unit_id END'
                ),
                array(
                    'table' => 'product_measurements',
                    'alias' => 'BaseToCartoon',
                    'type' => 'left',
                    'conditions' => 'BaseToCartoon.product_id=MemoDetail.product_id AND BaseToCartoon.measurement_unit_id=16'
                ),
                array(
                    'table' => 'dist_outlet_maps',
                    'alias' => 'DOM',
                    'type' => 'Inner',
                    'conditions' => 'Memo.outlet_id=DOM.outlet_id'
                ),
                array(
                    'table' => 'dist_distributors',
                    'alias' => 'db',
                    'type' => 'Inner',
                    'conditions' => 'db.id=DOM.dist_distributor_id'
                ),
                array(
                    'table' => 'dist_tsos',
                    'alias' => 'TSO',
                    'type' => 'Inner',
                    'conditions' => 'TSO.id=(
                        SELECT 
                        TOP 1 dsmh.dist_tso_id
                        FROM [dist_tso_mapping_histories] AS dsmh
                        WHERE 
                        (
                            db.id = dsmh.dist_distributor_id
                        AND is_change = 1
                        AND Memo.memo_date BETWEEN dsmh.effective_date
                        AND (
                            CASE
                                WHEN dsmh.end_date IS NULL THEN GETDATE()
                                ELSE dsmh.end_date
                                END
                            )
                        )
                        order by dsmh.id asc
                    )'
                ),
                array(
                    'table' => 'dist_area_executives',
                    'alias' => 'AE',
                    'type' => 'Inner',
                    'conditions' => 'AE.id=TSO.dist_area_executive_id'
                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'Inner',
                    'conditions' => 'AE.office_id=Office.id'
                ),

            ),
            'group' => array(
                'db.name',
                'Office.office_name',
                'TSO.name',
                'AE.name',
                'Office.order'
            ),
            'order' => array(
                'Office.order',
                'AE.name',
                'TSO.name',
                'db.name'
            ),
            'fields' => array(
                'db.name as db',
                'Office.office_name as office',
                'TSO.name as tso',
                'AE.name as ae',
                'SUM(ROUND(MemoDetail.sales_qty*CASE WHEN SalesToBase.qty_in_base is null then 1 else SalesToBase.qty_in_base END,0)/CASE WHEN BaseToCartoon.qty_in_base is null then 1 else BaseToCartoon.qty_in_base END) as qty'
            ),
            'recursive' => -1
        ));
        $this->set(compact('memos'));
    }

    public function admin_ors_dbs_incentive_offer_june_29_to_july_05_2022()
    {
        $this->set('page_title', '(DB)Tornado offer 2 ORS-N(29th June 2022 TO 05th July 2022)');
        $this->loadModel('Office');
        $this->loadModel('Memo');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
        } elseif ($office_parent_id == 14) {
            $region_office_id = $this->UserAuth->getOfficeId();
            $office_conditions = array('Office.parent_office_id' => $region_office_id);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $offices = $this->Office->find('list', array(
            'conditions' => $office_conditions
        ));
        $cond = array();

        $cond['Memo.memo_date between ? AND ?'] = array('2022-06-29', '2022-07-05');
        $cond['Memo.office_id'] = array_keys($offices);
        $cond['Product.source'] = 'SMCEL';
        $cond['MemoDetail.price >'] = 0;
        $cond['Memo.is_distributor'] = 1;

        $memos = $this->Memo->find('all', array(
            'conditions' => $cond,
            'joins' => array(
                array(
                    'table' => 'memo_details',
                    'alias' => 'MemoDetail',
                    'type' => 'Inner',
                    'conditions' => 'MemoDetail.memo_id=Memo.id'
                ),
                array(
                    'table' => 'products',
                    'alias' => 'Product',
                    'type' => 'Inner',
                    'conditions' => 'Product.id=MemoDetail.product_id'
                ),
                array(
                    'table' => 'dist_outlet_maps',
                    'alias' => 'DOM',
                    'type' => 'Inner',
                    'conditions' => 'Memo.outlet_id=DOM.outlet_id'
                ),
                array(
                    'table' => 'dist_distributors',
                    'alias' => 'db',
                    'type' => 'Inner',
                    'conditions' => 'db.id=DOM.dist_distributor_id'
                ),
                array(
                    'table' => 'dist_tsos',
                    'alias' => 'TSO',
                    'type' => 'Inner',
                    'conditions' => 'TSO.id=(
                        SELECT 
                        TOP 1 dsmh.dist_tso_id
                        FROM [dist_tso_mapping_histories] AS dsmh
                        WHERE 
                        (
                            db.id = dsmh.dist_distributor_id
                        AND is_change = 1
                        AND Memo.memo_date BETWEEN dsmh.effective_date
                        AND (
                            CASE
                                WHEN dsmh.end_date IS NULL THEN GETDATE()
                                ELSE dsmh.end_date
                                END
                            )
                        )
                        order by dsmh.id asc
                    )'
                ),
                array(
                    'table' => 'dist_area_executives',
                    'alias' => 'AE',
                    'type' => 'Inner',
                    'conditions' => 'AE.id=TSO.dist_area_executive_id'
                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'Inner',
                    'conditions' => 'AE.office_id=Office.id'
                ),

            ),
            'group' => array(
                'db.name',
                'Office.office_name',
                'TSO.name',
                'AE.name',
                'Office.order HAVING SUM(MemoDetail.sales_qty*MemoDetail.price) >= 400000'
            ),
            'order' => array(
                'Office.order',
                'AE.name',
                'TSO.name',
                'db.name'
            ),
            'fields' => array(
                'db.name as db',
                'Office.office_name as office',
                'TSO.name as tso',
                'AE.name as ae',
                'SUM(MemoDetail.sales_qty*MemoDetail.price) as memo_value'
            ),
            'recursive' => -1
        ));
        $this->set(compact('memos'));
    }
    public function admin_cash_discount_offer_06_nov()
    {
        $this->set('page_title', '(DB) 10% Cash Discount on Smile belt type baby diaper');
        $this->loadModel('DistMemo');
        $this->loadModel('Office');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
        } elseif ($office_parent_id == 14) {
            $region_office_id = $this->UserAuth->getOfficeId();
            $office_conditions = array('Office.parent_office_id' => $region_office_id);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $offices = $this->Office->find('list', array(
            'conditions' => $office_conditions
        ));

        $office_conditions = 'AND  ofc.id in (' . join(',', array_keys($offices)) . ')';

        $report_data = "
            select
                ofc.office_name as office,
                tso.name as tso,
                ae.name as ae,
                dd.name as db,
                mk.name as market,
                ot.name as outlet,
                oc.category_name as outlet_category,
                m.dist_memo_no as memo_no,
                m.dist_order_no as order_no,
                do.order_date as order_date,
                m.memo_date as memo_date,
                m.gross_value as memo_total_gross,
                m.discount_value as memo_total_discount,
                sum(sales_qty*discount_amount) as detalails_discount
            from dist_memos m WITH (NOLOCK)
            inner join dist_orders do on do.dist_order_no=m.dist_order_no
            inner join dist_memo_details md WITH (NOLOCK) on m.id=md.dist_memo_id
            inner join products p WITH (NOLOCK) on p.id=md.product_id
            inner join dist_distributors dd WITH (NOLOCK) on dd.id=m.distributor_id
            inner join dist_outlets ot WITH (NOLOCK) on ot.id=m.outlet_id
            inner join dist_outlet_categories oc WITH (NOLOCK) on oc.id=ot.category_id
            inner join dist_markets mk WITH (NOLOCK) on mk.id=ot.dist_market_id
            inner join dist_tsos tso on tso.id=do.tso_id
            left join dist_area_executives ae on ae.id=tso.dist_area_executive_id
            left join offices ofc on ofc.id=ae.office_id
            where 
                m.memo_date between '2022-11-08' and '2023-03-31'
                and m.discount_value > 0
                and md.price>0
                and md.product_id in (
                    135,
                    136,
                    137,
                    138,
                    142,
                    143,
                    144,
                    145,
                    146
                )
                $office_conditions
            group by
                m.id,
                m.dist_memo_no,
                do.order_date,
                m.dist_order_no,
                m.memo_date,
                m.gross_value,
                m.discount_value,
                dd.name,
                ot.name,
                oc.category_name,
                mk.name,
                tso.name,
                ae.name,
                ofc.office_name,
                ofc.[order]
            order by
                ofc.[order],
                tso.name,
                ae.name,
                mk.name,
                ot.name";
        $report_data = $this->Office->query($report_data);
        $this->set(compact('report_data'));
    }
    public function admin_cash_discount_offer_joya_13_dec()
    {
        $this->set('page_title', '(DB) Cash Discount on Joya belt');
        $this->loadModel('DistMemo');
        $this->loadModel('Office');
        $office_parent_id = $this->UserAuth->getOfficeParentId();
        if ($office_parent_id == 0) {
            $office_conditions = array('Office.office_type_id' => 2, "NOT" => array("id" => array(30, 31, 37)));
        } elseif ($office_parent_id == 14) {
            $region_office_id = $this->UserAuth->getOfficeId();
            $office_conditions = array('Office.parent_office_id' => $region_office_id);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
        }

        $offices = $this->Office->find('list', array(
            'conditions' => $office_conditions
        ));

        $this->set(compact('offices', 'office_parent_id'));

        $office_conditions = 'AND  ofc.id in (' . join(',', array_keys($offices)) . ')';
        
        $show = 0;

        if($this->request->is('post'))
		{

            $this->request->data = $this->request->data;

            $date_from = date('Y-m-d', strtotime($this->request->data['DistOtherEmergencyReports']['date_from']));
            $date_to = date('Y-m-d', strtotime($this->request->data['DistOtherEmergencyReports']['date_to']));
           
            $office_id = $this->request->data['DistOtherEmergencyReports']['office_id'];
           
            if(!empty($office_id)){
                $office_conditions = 'AND  ofc.id=' . $office_id;
            }
           

            $report_data = "
                select
                    ofc.office_name as office,
                    tso.name as tso,
                    ae.name as ae,
                    dd.name as db,
                    mk.name as market,
                    ot.name as outlet,
                    oc.category_name as outlet_category,
                    m.dist_memo_no as memo_no,
                    m.dist_order_no as order_no,
                    do.order_date as order_date,
                    m.memo_date as memo_date,
                    m.gross_value as memo_total_gross,
                    m.discount_value as memo_total_discount,
                    sum(sales_qty*discount_amount) as detalails_discount
                from dist_memos m WITH (NOLOCK)
                inner join dist_orders do on do.dist_order_no=m.dist_order_no
                inner join dist_memo_details md WITH (NOLOCK) on m.id=md.dist_memo_id
                inner join products p WITH (NOLOCK) on p.id=md.product_id
                inner join dist_distributors dd WITH (NOLOCK) on dd.id=m.distributor_id
                inner join dist_outlets ot WITH (NOLOCK) on ot.id=m.outlet_id
                inner join dist_outlet_categories oc WITH (NOLOCK) on oc.id=ot.category_id
                inner join dist_markets mk WITH (NOLOCK) on mk.id=ot.dist_market_id
                inner join dist_tsos tso on tso.id=do.tso_id
                left join dist_area_executives ae on ae.id=tso.dist_area_executive_id
                left join offices ofc on ofc.id=ae.office_id
                where 
                    m.memo_date between '$date_from' and '$date_to'
                    and m.discount_value > 0
                    and md.price>0
                    and md.product_id in (51,52,451)
                    $office_conditions
                group by
                    m.id,
                    m.dist_memo_no,
                    do.order_date,
                    m.dist_order_no,
                    m.memo_date,
                    m.gross_value,
                    m.discount_value,
                    dd.name,
                    ot.name,
                    oc.category_name,
                    mk.name,
                    tso.name,
                    ae.name,
                    ofc.office_name,
                    ofc.[order]
                order by
                    ofc.[order],
                    tso.name,
                    ae.name,
                    mk.name,
                    ot.name";
                 //   echo $report_data;exit;
            $report_data = $this->Office->query($report_data);

            $show = 1;
            $this->set(compact('report_data','show'));

        }

    }
}
