<?php
App::uses('AppController', 'Controller');
/**
 * VATReports Controller
 *
 * @property VatReports $VatReports
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class VatReportsController extends AppController
{

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Session');
    public $uses = array('Product');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index()
    {
        $this->set('page_title', 'Value Added Tax Report');

        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); //300 seconds = 5 minutes


        $unit_types = array(
            '1' => 'Sales Unit',
            '2' => 'Base Unit',
        );
        $sql = "SELECT * FROM product_sources";
        $sources_datas = $this->Product->query($sql);
        $companies = array();
        foreach ($sources_datas as $sources_data) {
            $companies[$sources_data[0]['name']] = $sources_data[0]['name'];
        }
        $this->set(compact('unit_types', 'product_list', 'companies'));
        $product_types = $this->Product->ProductType->find('list', array('order' => array('name' => 'asc')));
        $this->set(compact('product_types'));
        $dataresult = array();
        if ($this->request->is('post')) {
            $request_data = $this->request->data;
            $unit_type = $request_data['Memo']['unit_type'];
            $company = $request_data['Memo']['company'];
            $product_type_id = $request_data['Memo']['product_type_id'];
            // $productid = $request_data['Memo']['product_id'];
            $date_from = date('Y-m-d', strtotime($request_data['Memo']['date_from']));
            $date_to = date('Y-m-d', strtotime($request_data['Memo']['date_to']));
            $this->set(compact('date_from', 'date_to', 'unit_type', 'request_data'));

            $conditions = array();
            if (!empty($company)) {
                $conditions['Product.source'] = $company;
            }
            //$conditions['Product.id'] = 51;
            $conditions['Product.product_type_id'] = $product_type_id;
            $dataresult = $this->Product->find('all', array(
                'joins' => array(
                    array(
                        'alias' => 'Brand',
                        'table' => 'brands',
                        'conditions' => 'Brand.id=Product.brand_id'
                    ),
                    array(
                        'alias' => 'Category',
                        'table' => 'product_categories',
                        'conditions' => 'Category.id=Product.product_category_id'
                    ),
                    array(
                        'alias' => 'ProductData',
                        'type' => 'left',
                        'table' => '(select 
                                        md.product_id,
                                        vat_so.vat,

                                       /* MAX(free_vat_so.price) as bonus_price, MAX(free_vat_so.vat) as bonus_vat,*/

                                        ROUND(sum(case when md.price > 0 then (ROUND((md.sales_qty * (case when pm.qty_in_base is null then 1 else pm.qty_in_base end)),0)/(case when pm_sales.qty_in_base is null then 1 else pm_sales.qty_in_base end)) end),2,1) sales_qty_sale_unit,

                                        sum(case when md.price > 0 then (ROUND((md.sales_qty * (case when pm.qty_in_base is null then 1 else pm.qty_in_base end)),0)) end) sales_qty_base_unit,

                                        ROUND(sum(case when md.price = 0 then (ROUND((md.sales_qty * (case when pm.qty_in_base is null then 1 else pm.qty_in_base end)),0)/(case when pm_sales.qty_in_base is null then 1 else pm_sales.qty_in_base end)) end),2,1) bonus_qty_sale_unit,
                                        
                                        sum(case when md.price = 0 then (ROUND((md.sales_qty * (case when pm.qty_in_base is null then 1 else pm.qty_in_base end)),0)) end) bonus_qty_base_unit,

                                        /* sum(case when md.price > 0 then md.sales_qty*md.price end) as total_value, */

                                        sum(case when md.price > 0 then (md.price - (md.price*vat_so.vat/(vat_so.vat+100)))*((ROUND((md.sales_qty * (case when pm.qty_in_base is null then 1 else pm.qty_in_base end)),0)/(case when pm_sales.qty_in_base is null then 1 else pm_sales.qty_in_base end))) end) excluding_vat,

                                        sum(case when md.price = 0 then (free_vat_so.price - (free_vat_so.price*free_vat_so.vat/(free_vat_so.vat+100)))*((ROUND((md.sales_qty * (case when pm.qty_in_base is null then 1 else pm.qty_in_base end)),0)/(case when pm_sales.qty_in_base is null then 1 else pm_sales.qty_in_base end))) end) bonus_excluding_vat,
                                        sum(case when md.price = 0 then free_vat_so.price*((ROUND((md.sales_qty * (case when pm.qty_in_base is null then 1 else pm.qty_in_base end)),0)/(case when pm_sales.qty_in_base is null then 1 else pm_sales.qty_in_base end))) end) total_bonus_value,
                                        
                                        
                                        
                                        sum(case when md.price > 0 then (md.price*vat_so.vat/(vat_so.vat+100))*((ROUND((md.sales_qty * (case when pm.qty_in_base is null then 1 else pm.qty_in_base end)),0)/(case when pm_sales.qty_in_base is null then 1 else pm_sales.qty_in_base end))) end) total_vat
                                        /*sum(case when md.price > 0 then (ROUND((md.sales_qty * (case when pm.qty_in_base is null then 1 else pm.qty_in_base end)),0)) end),
                                        sum(case when md.price > 0 then md.sales_qty  end)*/
                                    from memos m
                                    inner join memo_details md on m.id=md.memo_id
                                    inner join products p on p.id=md.product_id
                                    left join product_measurements pm on pm.product_id=md.product_id and pm.measurement_unit_id=(case when md.measurement_unit_id is null or md.measurement_unit_id=0 then p.sales_measurement_unit_id else md.measurement_unit_id end)
                                    left join product_measurements pm_sales on pm_sales.product_id=md.product_id and pm_sales.measurement_unit_id=p.sales_measurement_unit_id
                                    OUTER APPLY(
                                        select   TOP 1 pc.* from product_prices_v2  pc 
                                        inner join product_price_section_v2 pcs on pc.id=pcs.product_price_id and pcs.is_so=1
                                        where pc.product_id=md.product_id and pc.effective_date<=m.memo_date 
                                        order by pc.effective_date DESC
                                    ) AS vat_so
                                    OUTER APPLY(
                                        select   TOP 1 vat_pc.* from vatexecuting_products  vat_pc 
                                         where vat_pc.product_id=md.product_id and vat_pc.effective_date<=m.memo_date 
                                        order by vat_pc.effective_date DESC
                                    ) AS free_vat_so
                                    where m.memo_date between \'' . $date_from . '\' and \'' . $date_to . '\' and m.status>0
                                    group by 
                                        md.product_id,vat_so.vat
                                    )',
                        'conditions' => 'ProductData.product_id=Product.id'
                    ),
                    array(
                        'alias' => 'GiftIssue',
                        'type' => 'left',
                        'table' => '(
                                    select 
                                        gid.product_id,
                                        sum(gid.quantity) as bonus_qty_sale_unit,
                                        sum(ROUND(gid.quantity * case when pm.qty_in_base is null then 1 else pm.qty_in_base end,0)) as bonus_qty_base_unit,

                                        sum((free_vat_so.price - (free_vat_so.price*free_vat_so.vat/(free_vat_so.vat+100)))*(gid.quantity)) as bonus_excluding_vat,
										
										sum(gid.quantity*free_vat_so.price) as total_bonus_value
                                       
                                       

                                    from gift_items gi
                                    inner join gift_item_details gid on gi.id=gid.gift_item_id
                                    inner join products p on p.id=gid.product_id
                                    left join product_measurements pm on pm.product_id=gid.product_id and pm.measurement_unit_id=p.sales_measurement_unit_id
                                    OUTER APPLY(
                                        select   TOP 1 vat_pc.* from vatexecuting_products  vat_pc 
                                         where vat_pc.product_id=gid.product_id and vat_pc.effective_date<=gi.date
                                        order by vat_pc.effective_date DESC
                                    ) AS free_vat_so
                                    where
                                        gi.date between \'' . $date_from . '\' and \'' . $date_to . '\' and gi.memo_no is null
                                    group by
                                        gid.product_id
                                    )',
                        'conditions' => 'GiftIssue.product_id=Product.id'
                    ),

                    array(
                        'alias' => 'DoctorVisit',
                        'type' => 'left',
                        'table' => '(
                                    select 
                                        dvd.product_id,
                                        sum(dvd.quantity) as bonus_qty_sale_unit,
                                        sum(ROUND(dvd.quantity * case when pm.qty_in_base is null then 1 else pm.qty_in_base end,0)) as bonus_qty_base_unit,

                                        sum((free_vat_so.price - (free_vat_so.price*free_vat_so.vat/(free_vat_so.vat+100)))*(dvd.quantity)) as bonus_excluding_vat,
										
										sum(dvd.quantity*free_vat_so.price) as total_bonus_value

                                    from doctor_visits dv
                                    inner join doctor_visit_details dvd on dv.id=dvd.doctor_visit_id
                                    inner join products p on p.id=dvd.product_id
                                    left join product_measurements pm on pm.product_id=dvd.product_id and pm.measurement_unit_id=p.sales_measurement_unit_id
                                    OUTER APPLY(
                                        select   TOP 1 vat_pc.* from vatexecuting_products  vat_pc 
                                         where vat_pc.product_id=dvd.product_id and vat_pc.effective_date<=dv.visit_date
                                        order by vat_pc.effective_date DESC
                                    ) AS free_vat_so
                                    where 
                                        dv.visit_date between \'' . $date_from . '\' and \'' . $date_to . '\'
                                    group by
                                        dvd.product_id
                                    )',
                        'conditions' => 'DoctorVisit.product_id=Product.id'
                    ),
                ),
                'conditions' => array(
                    'OR' => array(
                        'ProductData.product_id is not null',
                        'GiftIssue.product_id is not null',
                        'DoctorVisit.product_id is not null'
                    ),
                    'AND' => $conditions
                ),
                'fields' => array(
                    'Product.name',
                    'Product.product_code',
                    'Brand.name',
                    'Category.name',
                    'ProductData.vat',
                    'ProductData.sales_qty_sale_unit',
                    'ProductData.sales_qty_base_unit',
                    '(case when ProductData.bonus_qty_sale_unit is null then 0 else ProductData.bonus_qty_sale_unit end +case when GiftIssue.bonus_qty_sale_unit is null then 0 else GiftIssue.bonus_qty_sale_unit end +case when DoctorVisit.bonus_qty_sale_unit is null then 0 else DoctorVisit.bonus_qty_sale_unit end) as  bonus_qty_sale_unit',
                    '(case when ProductData.bonus_qty_base_unit is null then 0 else ProductData.bonus_qty_base_unit end +case when GiftIssue.bonus_qty_base_unit is null then 0 else GiftIssue.bonus_qty_base_unit end + case when DoctorVisit.bonus_qty_base_unit is null then 0 else DoctorVisit.bonus_qty_base_unit end) as bonus_qty_base_unit',
                    'ProductData.excluding_vat',
                    'ProductData.total_vat',
					'(case when ProductData.bonus_excluding_vat is null then 0 else ProductData.bonus_excluding_vat end +case when GiftIssue.bonus_excluding_vat is null then 0 else GiftIssue.bonus_excluding_vat end +case when DoctorVisit.bonus_excluding_vat is null then 0 else DoctorVisit.bonus_excluding_vat end) as  bonus_excluding_vat',
					
					'(case when ProductData.total_bonus_value is null then 0 else ProductData.total_bonus_value end +case when GiftIssue.total_bonus_value is null then 0 else GiftIssue.total_bonus_value end +case when DoctorVisit.total_bonus_value is null then 0 else DoctorVisit.total_bonus_value end) as  total_bonus_value'
					
                   // 'ProductData.bonus_price', 
                   // 'ProductData.bonus_vat'
                ),
                'order' => array('Product.product_category_id', 'Product.order'),
                'recursive' => -1
            ));
            // echo $this->Product->getLastQuery();
            //pr($dataresult);
            //exit; 
        }
        $this->set(compact('dataresult'));
    }

    public function bonus_product_vat(){

        
    }




}
