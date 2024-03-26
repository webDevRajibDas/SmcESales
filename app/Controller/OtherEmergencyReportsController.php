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
class OtherEmergencyReportsController extends AppController
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
                        tt.name as territory,
                        mk.name as market,
                        ot.name as outlet,
                        oc.category_name as outlet_category,
                        /*m.id as memo_id,*/
                        m.memo_no as memo_no,
						m.memo_date as memo_date,
                        m.gross_value as memo_total_gross,
                        sum(sales_qty*price) as value_expect
                    from memos m
                    inner join 
                    (
                        select m.id as memo_id,o.category_id as outlet_category,count(md.id) as total_product from memos m
                        inner join memo_details md on m.id=md.memo_id
                        inner join products p on p.id=md.product_id
                        inner join outlets o on o.id=m.outlet_id
                        where 
                            m.memo_date between '2022-05-12' and '2022-05-31'
                            and p.source='SMCEL'
                            and p.brand_id not in (46,57)
                            and p.product_type_id=1
                            and md.price>0
                            and m.is_distributor !=1
                        group by
                            m.id,o.category_id having count(md.id)>=case when o.category_id=18 then 1 else 2 end
                    ) as eligible_memo on m.id=eligible_memo.memo_id
                    inner join memo_details md on m.id=md.memo_id
                    inner join products p on p.id=md.product_id
                    inner join outlets ot on ot.id=m.outlet_id
                    inner join outlet_categories oc on oc.id=ot.category_id
                    inner join markets mk on mk.id=ot.market_id
                    inner join territories tt on tt.id=mk.territory_id
                    inner join offices ofc on ofc.id=tt.office_id
                    where 
                        m.memo_date between '2022-05-12' and '2022-05-31'
                        and p.source='SMCEL'
                        and p.brand_id not in (46,57)
                        and p.product_type_id=1
                        and md.price>0
                        $office_conditions
                    group by
                        m.id,
                        m.memo_no,
						m.memo_date,
                        m.gross_value,
                        ot.name,
                        oc.category_name,
                        mk.name,
                        tt.name,
                        ofc.office_name,
                        ofc.[order]
                        having sum(sales_qty*price) >= 10000
                    order by
                        ofc.[order],
                        tt.name,
                        mk.name,
                        ot.name    
                    ";
        $report_data = $this->Office->query($report_data);
        $this->set(compact('report_data'));
    }

    public function admin_crash_trade_program_01_21june()
    {
        $this->set('page_title', 'Crash Trade Program(01 june 2022 TO 21 June 2022)');
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
                        tt.name as territory,
                        mk.name as market,
                        ot.name as outlet,
                        oc.category_name as outlet_category,
                        /*m.id as memo_id,*/
                        m.memo_no as memo_no,
						m.memo_date as memo_date,
                        m.gross_value as memo_total_gross,
                        sum(sales_qty*price) as value_expect
                    from memos m
                    inner join 
                    (
                        select m.id as memo_id,o.category_id as outlet_category,count(md.id) as total_product from memos m
                        inner join memo_details md on m.id=md.memo_id
                        inner join products p on p.id=md.product_id
                        inner join outlets o on o.id=m.outlet_id
                        where 
                            m.memo_date between '2022-06-01' and '2022-06-21'
                            and p.source='SMCEL'
                            and p.brand_id not in (46,57)
                            and p.product_type_id=1
                            and md.price>0
                            and m.is_distributor !=1
                        group by
                            m.id,o.category_id having count(md.id)>=case when o.category_id=18 then 1 else 2 end
                    ) as eligible_memo on m.id=eligible_memo.memo_id
                    inner join memo_details md on m.id=md.memo_id
                    inner join products p on p.id=md.product_id
                    inner join outlets ot on ot.id=m.outlet_id
                    inner join outlet_categories oc on oc.id=ot.category_id
                    inner join markets mk on mk.id=ot.market_id
                    inner join territories tt on tt.id=mk.territory_id
                    inner join offices ofc on ofc.id=tt.office_id
                    where 
                        m.memo_date between '2022-06-01' and '2022-06-21'
                        and p.source='SMCEL'
                        and p.brand_id not in (46,57)
                        and p.product_type_id=1
                        and md.price>0
                        $office_conditions
                    group by
                        m.id,
                        m.memo_no,
						m.memo_date,
                        m.gross_value,
                        ot.name,
                        oc.category_name,
                        mk.name,
                        tt.name,
                        ofc.office_name,
                        ofc.[order]
                        having sum(sales_qty*price) >= 10000
                    order by
                        ofc.[order],
                        tt.name,
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
                        tt.name as territory,
                        mk.name as market,
                        ot.name as outlet,
                        oc.category_name as outlet_category,
                        /*m.id as memo_id,*/
                        m.memo_no as memo_no,
						m.memo_date as memo_date,
                        m.gross_value as memo_total_gross,
                        sum(sales_qty*price) as value_expect
                    from memos m
                    inner join 
                    (
                        select m.id as memo_id,o.category_id as outlet_category,count(md.id) as total_product from memos m
                        inner join memo_details md on m.id=md.memo_id
                        inner join products p on p.id=md.product_id
                        inner join outlets o on o.id=m.outlet_id
                        where 
                            m.memo_date between '2022-06-22' and '2022-06-30'
                            and p.source='SMCEL'
                           
                            and p.product_type_id=1
                            and md.price>0
                            and m.is_distributor !=1
                        group by
                            m.id,o.category_id 
                    ) as eligible_memo on m.id=eligible_memo.memo_id
                    inner join memo_details md on m.id=md.memo_id
                    inner join products p on p.id=md.product_id
                    inner join outlets ot on ot.id=m.outlet_id
                    inner join outlet_categories oc on oc.id=ot.category_id
                    inner join markets mk on mk.id=ot.market_id
                    inner join territories tt on tt.id=mk.territory_id
                    inner join offices ofc on ofc.id=tt.office_id
                    where 
                        m.memo_date between '2022-06-22' and '2022-06-30'
                        and p.source='SMCEL'
                        and p.product_type_id=1
                        and md.price>0
                        $office_conditions
                    group by
                        m.id,
                        m.memo_no,
						m.memo_date,
                        m.gross_value,
                        ot.name,
                        oc.category_name,
                        mk.name,
                        tt.name,
                        ofc.office_name,
                        ofc.[order]
                        having sum(sales_qty*price) >= 10000
                    order by
                        ofc.[order],
                        tt.name,
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

        $cond['Memo.memo_date between ? AND ?'] = array('2022-06-22', '2022-07-05');
        $cond['Memo.office_id'] = array_keys($offices);
        $cond['MemoDetail.product_id'] = 47;
        $cond['MemoDetail.price >'] = 0;
        $cond['Memo.is_distributor !='] = 1;

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
                    'table' => 'outlets',
                    'alias' => 'Outlet',
                    'type' => 'Inner',
                    'conditions' => 'Memo.outlet_id=Outlet.id'
                ),
                array(
                    'table' => 'markets',
                    'alias' => 'Market',
                    'type' => 'Inner',
                    'conditions' => 'Memo.market_id=Market.id'
                ),
                array(
                    'table' => 'offices',
                    'alias' => 'Office',
                    'type' => 'Inner',
                    'conditions' => 'Memo.office_id=Office.id'
                ),
                array(
                    'table' => 'territories',
                    'alias' => 'Territory',
                    'type' => 'Inner',
                    'conditions' => 'Memo.territory_id=Territory.id'
                ),
            ),
            'group' => array(
                'Memo.memo_no',
                'Memo.memo_date',
                'Outlet.name',
                'Market.name',
                'Office.office_name',
                'Territory.name',
                'Office.order having SUM(ROUND(MemoDetail.sales_qty*CASE WHEN SalesToBase.qty_in_base is null then 1 else SalesToBase.qty_in_base END,0)/CASE WHEN BaseToCartoon.qty_in_base is null then 1 else BaseToCartoon.qty_in_base END)>=5'
            ),
            'order' => array(
                'Office.order',
                'Territory.name',
                'Memo.memo_date',
                'Outlet.name',
                'Market.name',
            ),
            'fields' => array(
                'Memo.memo_no as memo_no',
                'Memo.memo_date as memo_date',
                'Outlet.name as outlet',
                'Market.name as market',
                'Office.office_name as office',
                'Territory.name as territory',
                'SUM(ROUND(MemoDetail.sales_qty*CASE WHEN SalesToBase.qty_in_base is null then 1 else SalesToBase.qty_in_base END,0)/CASE WHEN BaseToCartoon.qty_in_base is null then 1 else BaseToCartoon.qty_in_base END) as qty'
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
                tt.name as territory,
                mk.name as market,
                ot.name as outlet,
                oc.category_name as outlet_category,
                m.memo_no as memo_no,
                m.memo_date as memo_date,
                m.gross_value as memo_total_gross,
                m.total_discount as memo_total_discount,
                sum(sales_qty*discount_amount) as detalails_discount
            from memos m WITH (NOLOCK)
            inner join memo_details md WITH (NOLOCK) on m.id=md.memo_id
            inner join products p WITH (NOLOCK) on p.id=md.product_id
            inner join outlets ot WITH (NOLOCK) on ot.id=m.outlet_id
            inner join outlet_categories oc WITH (NOLOCK) on oc.id=ot.category_id
            inner join markets mk WITH (NOLOCK) on mk.id=ot.market_id
            left join territories tt on tt.id=m.territory_id
            left join offices ofc on ofc.id=tt.office_id
            where 
                m.memo_date between '2022-11-08' and '2023-03-31'
                and m.total_discount > 0
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
                m.memo_no,
                m.memo_date,
                m.gross_value,
                m.total_discount,
                ot.name,
                oc.category_name,
                mk.name,
                tt.name,
                ofc.office_name,
                ofc.[order]
            order by
                ofc.[order],
                tt.name,
                mk.name,
                ot.name";
        $report_data = $this->Office->query($report_data);
        $this->set(compact('report_data'));
    }
    public function admin_cash_discount_offer_joya_13_dec()
    {
        $this->set('page_title', 'Cash Discount on Joya belt');
        $this->loadModel('Memo');
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
                tt.name as territory,
                mk.name as market,
                ot.name as outlet,
                oc.category_name as outlet_category,
                m.memo_no as memo_no,
                m.memo_date as memo_date,
                m.gross_value as memo_total_gross,
                m.total_discount as memo_total_discount,
                sum(sales_qty*discount_amount) as detalails_discount
            from memos m WITH (NOLOCK)
            inner join memo_details md WITH (NOLOCK) on m.id=md.memo_id
            inner join products p WITH (NOLOCK) on p.id=md.product_id
            inner join outlets ot WITH (NOLOCK) on ot.id=m.outlet_id
            inner join outlet_categories oc WITH (NOLOCK) on oc.id=ot.category_id
            inner join markets mk WITH (NOLOCK) on mk.id=ot.market_id
            inner join territories tt on tt.id=m.territory_id
            left join offices ofc on ofc.id=tt.office_id
            where 
                m.memo_date between '2022-12-13' and '2023-03-31'
                and m.total_discount > 0
                and md.price>0
                and md.product_id in (51,52,451)
                $office_conditions
            group by
                m.id,
                m.memo_no,
                m.memo_date,
                m.gross_value,
                m.total_discount,              
                ot.name,
                oc.category_name,
                mk.name,
                tt.name,
                ofc.office_name,
                ofc.[order]
            order by
                ofc.[order],
                tt.name,
                mk.name,
                ot.name";
        $report_data = $this->Office->query($report_data);
        $this->set(compact('report_data'));
    }

    public function admin_cash_discount_offer_joya_4_persent_09_may()
    {
        $this->set('page_title', '4% Cash Discount on Joya belt from 09-May-2023');
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

        if ($this->request->is('post')) {

            $this->request->data = $this->request->data;

            $date_from = date('Y-m-d', strtotime($this->request->data['OtherEmergencyReports']['date_from']));
            $date_to = date('Y-m-d', strtotime($this->request->data['OtherEmergencyReports']['date_to']));

            $office_id = $this->request->data['OtherEmergencyReports']['office_id'];

            if (!empty($office_id)) {
                $office_conditions = 'AND  ofc.id=' . $office_id;
            }
            $report_data = "
            select
                ofc.office_name as office,
                tt.name as territory,
                mk.name as market,
                ot.name as outlet,
                oc.category_name as outlet_category,
                m.memo_no as memo_no,
                m.memo_date as memo_date,
                m.gross_value as memo_total_gross,
                m.total_discount as memo_total_discount,
                sum(sales_qty*discount_amount) as detalails_discount
            from memos m WITH (NOLOCK)
            inner join memo_details md WITH (NOLOCK) on m.id=md.memo_id
            inner join products p WITH (NOLOCK) on p.id=md.product_id
            inner join outlets ot WITH (NOLOCK) on ot.id=m.outlet_id
            inner join outlet_categories oc WITH (NOLOCK) on oc.id=ot.category_id
            inner join markets mk WITH (NOLOCK) on mk.id=ot.market_id
            inner join territories tt on tt.id=m.territory_id
            left join offices ofc on ofc.id=tt.office_id
            where 
                m.memo_date between '$date_from' and '$date_to'
                and m.total_discount > 0
                and md.price>0
                and md.product_id in (
                    51,
                    52,
                    451,
                    53,
                    450,
                    89,
                    148,
                    149
                )
                $office_conditions
            group by
                m.id,
                m.memo_no,
                m.memo_date,
                m.gross_value,
                m.total_discount,              
                ot.name,
                oc.category_name,
                mk.name,
                tt.name,
                ofc.office_name,
                ofc.[order]
                having sum(sales_qty*discount_amount)>0
            order by
                ofc.[order],
                tt.name,
                mk.name,
                ot.name";

            $report_data = $this->Office->query($report_data);

            $show = 1;
            $this->set(compact('report_data'));
        }
        $this->set(compact('show'));
    }
    public function admin_cash_discount_offer_ors25_np_5_persent_20_may()
    {
        $this->set('page_title', '5% Cash Discount on ORSaline-N NP(25pcs) from 20-May-2023');
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

        if ($this->request->is('post')) {

            $this->request->data = $this->request->data;

            $date_from = date('Y-m-d', strtotime($this->request->data['OtherEmergencyReports']['date_from']));
            $date_to = date('Y-m-d', strtotime($this->request->data['OtherEmergencyReports']['date_to']));

            $office_id = $this->request->data['OtherEmergencyReports']['office_id'];

            if (!empty($office_id)) {
                $office_conditions = 'AND  ofc.id=' . $office_id;
            }

            $report_data = "
            select
                ofc.office_name as office,
                tt.name as territory,
                mk.name as market,
                ot.name as outlet,
                oc.category_name as outlet_category,
                m.memo_no as memo_no,
                m.memo_date as memo_date,
                m.gross_value as memo_total_gross,
                m.total_discount as memo_total_discount,
                sum(sales_qty*discount_amount) as detalails_discount
            from memos m WITH (NOLOCK)
            inner join memo_details md WITH (NOLOCK) on m.id=md.memo_id
            inner join products p WITH (NOLOCK) on p.id=md.product_id
            inner join outlets ot WITH (NOLOCK) on ot.id=m.outlet_id
            inner join outlet_categories oc WITH (NOLOCK) on oc.id=ot.category_id
            inner join markets mk WITH (NOLOCK) on mk.id=ot.market_id
            inner join territories tt on tt.id=m.territory_id
            left join offices ofc on ofc.id=tt.office_id
            where 
                m.memo_date between '$date_from' and '$date_to'
                and m.total_discount > 0
                and md.price>0
                and (
                    md.product_id in (648)
                OR
                    md.virtual_product_id in (648)
                )
                $office_conditions
            group by
                m.id,
                m.memo_no,
                m.memo_date,
                m.gross_value,
                m.total_discount,              
                ot.name,
                oc.category_name,
                mk.name,
                tt.name,
                ofc.office_name,
                ofc.[order]
                having sum(sales_qty*discount_amount)>0
            order by
                ofc.[order],
                tt.name,
                mk.name,
                ot.name";

            $report_data = $this->Office->query($report_data);

            $show = 1;
            $this->set(compact('report_data'));
        }
        $this->set(compact('show'));
    }
}
