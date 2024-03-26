<?php
App::uses('AppController', 'Controller');
/**
 * OrsSalesThorughCardHolders Controller
 *
 * @property OutletAccount $OutletAccount
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class OrsSalesThorughCardHoldersController extends AppController
{

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator', 'Session');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index()
	{
        $this->LoadModel('Memo');
       // echo 'hllo';exit;
       $outlet_value = array();
       if ($this->request->is('post') || $this->request->is('put')) {
            $this->request=$this->request;
           
            $date_from = date('Y-m-d', strtotime($this->request['data']['date_from']));
            $date_to = date('Y-m-d', strtotime($this->request['data']['date_to']));
            $product_id = 47;
       
            $qty_value_info = $this->Memo->query(
                " select 
                    o.bonus_type_id, sum(md.sales_qty) as total_sales, sum(md.sales_qty*md.price) as total_value 
                from 
                    memos m 
                left join 
                    memo_details md on md.memo_id=m.id 
                left join outlets o on o.id=m.outlet_id 
                where 
                    m.memo_date >='$date_from' and m.memo_date <='$date_to' and m.gross_value > 0
                    and o.bonus_type_id IN(1,2) and md.product_id='$product_id' and md.price > 0
                    group by o.bonus_type_id "
            );

            $outlet_qty_count = $this->Memo->query("select COUNT(id) as id, bonus_type_id from outlets where bonus_type_id IN (1,2) group by bonus_type_id");

            $orstotalsales = $this->Memo->query(
                " select 
                    sum(md.sales_qty) as total_sales 
                from 
                    memos m 
                left join 
                    memo_details md on md.memo_id=m.id 
                left join outlets o on o.id=m.outlet_id 
                where 
                    m.memo_date >='$date_from' and m.memo_date <='$date_to' and m.gross_value > 0
                    and md.product_id='$product_id' and md.price > 0"
            );

            $totalsaleqty = $orstotalsales[0][0]['total_sales'];

            $orsretailsper = (($qty_value_info[0][0]['total_sales']/$totalsaleqty) * 100);
            $orsstockper = (($qty_value_info[1][0]['total_sales']/$totalsaleqty) * 100);


            $outlet_value[0] = array(
                'name'=>'Retail Card',
                'total_card_holder'=>$outlet_qty_count[0][0]['id'],
                'total_qty'=>$qty_value_info[0][0]['total_sales'],
                'total_value'=>$qty_value_info[0][0]['total_value'],
                'ors_per'=> sprintf('%.2f', $orsretailsper),
            );

            $outlet_value[1] = array(
                'name'=>'Stockist Card',
                'total_card_holder'=>$outlet_qty_count[1][0]['id'],
                'total_qty'=>$qty_value_info[1][0]['total_sales'],
                'total_value'=>$qty_value_info[1][0]['total_value'],
                'ors_per'=> sprintf('%.2f', $orsstockper)
            );

            $dist_qty_value_info = $this->Memo->query(
                " select 
                    o.bonus_type_id, sum(md.sales_qty) as total_sales, sum(md.sales_qty*md.price) as total_value 
                from 
                    dist_memos m 
                left join 
                    dist_memo_details md on md.dist_memo_id=m.id 
                left join dist_outlets o on o.id=m.outlet_id 
                where 
                    m.memo_date >='$date_from' and m.memo_date <='$date_to' and m.gross_value > 0
                    and o.bonus_type_id IN(1,2) and md.product_id='$product_id' and md.price > 0
                    group by o.bonus_type_id "
            );


            $dist_outlet_qty_count = $this->Memo->query("select COUNT(id) as id, bonus_type_id from dist_outlets where bonus_type_id IN (1,2) group by bonus_type_id");

            $distorstotalsales = $this->Memo->query(
                " select 
                    sum(md.sales_qty) as total_sales 
                from 
                    dist_memos m 
                left join 
                dist_memo_details md on md.dist_memo_id=m.id 
                left join dist_outlets o on o.id=m.outlet_id 
                where 
                    m.memo_date >='$date_from' and m.memo_date <='$date_to' and m.gross_value > 0
                    and md.product_id='$product_id' and md.price > 0"
            );

            $disttotalsaleqty = $distorstotalsales[0][0]['total_sales'];

            $distorsretailsper = (($dist_qty_value_info[0][0]['total_sales']/$disttotalsaleqty) * 100);
            $distorsstockper = (($dist_qty_value_info[1][0]['total_sales']/$disttotalsaleqty) * 100);


            $outlet_value[2] = array(
                'name'=>'Retail Card',
                'total_card_holder'=>$dist_outlet_qty_count[0][0]['id'],
                'total_qty'=>$dist_qty_value_info[0][0]['total_sales'],
                'total_value'=>$dist_qty_value_info[0][0]['total_value'],
                'ors_per'=>sprintf('%.2f', $distorsretailsper)
            );

            $outlet_value[3] = array(
                'name'=>'Stockist  Card',
                'total_card_holder'=>$dist_outlet_qty_count[1][0]['id'],
                'total_qty'=>$dist_qty_value_info[1][0]['total_sales'],
                'total_value'=>$dist_qty_value_info[1][0]['total_value'],
                'ors_per'=>sprintf('%.2f', $distorsstockper)
            );

           

            $this->set(compact('outlet_value', 'date_from', 'date_to'));

        }
	}

}
