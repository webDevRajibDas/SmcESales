<?php
App::uses('AppController', 'Controller');
/**
 * OutletCharacteristicSettings Controller
 *
 * @property OutletCharacteristicReport $OutletCharacteristicReport
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class WebCurrentPricesController extends AppController {
	
	

/**
 * Components
 *
 * @var array
 */
 
	public $uses = array('Product');
	public $components = array('Paginator', 'Session', 'Filter.Filter');	
	
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() 
	{
		$this->set('page_title', 'Current Prices');
		$prices=$this->Product->Query("
				SELECT * FROM (
					Select * from 
					(
						select
							p.id as product_id,
							p.name as product_name,
							p.[order] as product_order,
							pp.id as tp_id,
							pp.effective_date as tp_effective_date,
							pp.mrp as tp_mrp,
							pp.general_price as tp_general_price,
							pc.effective_date as tp_c_effective,
							pc.min_qty as tp_c_min_qty,
							pc.price as tp_c_price,
							dpp.id as dbp_id,
							dpp.effective_date as db_effective,
							dpp.mrp as db_mrp,
							dpp.general_price db_general_price,
							dpc.effective_date as db_c_effective_date,
							dpc.min_qty as db_c_min_qty,
							dpc.price as db_c_price
							from products p
								inner join product_prices pp on p.id=pp.product_id and pp.has_combination=0 and pp.institute_id=0 and (pp.project_id is null or pp.project_id =0) and pp.effective_date = (select max(effective_date) from product_prices where product_id=p.id and has_combination=0 and institute_id=0 and (project_id is null or project_id =0))
								inner join product_combinations pc on pc.product_price_id=pp.id
								left join dist_product_prices dpp on p.id=dpp.product_id and dpp.has_combination=0 and dpp.effective_date = (select max(effective_date) from dist_product_prices where product_id=p.id and has_combination=0)
								left join dist_product_combinations dpc on dpp.id=dpc.product_price_id AND dpc.min_qty=pc.min_qty
						where 
							p.product_type_id=1
					)t
					UNiON
					Select * from 
					(
						select 
							p.id as product_id,
							p.name as product_name,
							p.[order] as product_order,
							pp.id as tp_id,
							pp.effective_date as tp_effective_date,
							pp.mrp as tp_mrp,
							pp.general_price as tp_general_price,
							pc.effective_date as tp_c_effective,
							pc.min_qty as tp_c_min_qty,
							pc.price as tp_c_price,
							dpp.id as dbp_id,
							dpp.effective_date as db_effective,
							dpp.mrp as db_mrp,
							dpp.general_price db_general_price,
							dpc.effective_date as db_c_effective_date,
							dpc.min_qty as db_c_min_qty,
							dpc.price as db_c_price
							from products p
								inner join dist_product_prices dpp on p.id=dpp.product_id and dpp.has_combination=0 and dpp.effective_date = (select max(effective_date) from dist_product_prices where product_id=p.id and has_combination=0)
								inner join dist_product_combinations dpc on dpp.id=dpc.product_price_id 
								Left join product_prices pp on p.id=pp.product_id and pp.has_combination=0 and pp.institute_id=0 and (pp.project_id is null or pp.project_id =0) and pp.effective_date = (select max(effective_date) from product_prices where product_id=p.id and has_combination=0 and institute_id=0 and (project_id is null or project_id =0))
								left join product_combinations pc on pc.product_price_id=pp.id AND dpc.min_qty=pc.min_qty
							where 
							p.product_type_id=1
					)t2
				) te
				order by product_order,ISNULL(tp_c_min_qty*0,1),tp_c_min_qty ASC
			");
		// pr($prices);exit;
		$this->set(compact('prices'));
	}
}
