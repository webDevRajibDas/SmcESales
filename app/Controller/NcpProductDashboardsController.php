<?php

App::uses('AppController', 'Controller');

/**
 * Challans Controller
 *
 * @property Challan $Challan
 * @property PaginatorComponent $Paginator
 */
class NcpProductDashboardsController extends AppController{
	/**
	 * Components
	 * @var array
	 */
	public $components = array('Paginator', 'Filter.Filter');
	public $uses = array('NcpType','ReturnChallan','Store','Product','ReturnChallanDetail');

	/**
	 * admin_index method
	 *
	 * @return void
	 */
	public function admin_index(){
		$this->set('page_title', 'NCP View List');
        $sql_options = "
        SELECT
            ncp_types.name AS NCP_TYPE,
            return_challans.ncp_type_id,
            COUNT(DISTINCT return_challans.id) AS number_of_total_ncp,
            COUNT(return_challan_details.product_id) AS number_of_product
        FROM return_challan_details
        LEFT JOIN return_challans ON return_challan_details.challan_id = return_challans.id
        LEFT JOIN ncp_types ON return_challans.ncp_type_id = ncp_types.id
        WHERE return_challans.ncp_type_id > 0
        GROUP BY ncp_types.name, return_challans.ncp_type_id;
		";
		$results = $this->NcpType->query($sql_options);
		//$this->dd($results);
		$this->set(compact('results',$results));

	}

	public function admin_view($id = null){
		$this->set('page_title', 'NCP Product Details');
		$sqlQuery = "
			SELECT
			    nt.id as ncp_type_id,nt.name as NcpType,o.office_name as AreaOffice,t.name as teritorryName,
				p.name as ProductName,
				sum(rcd.challan_qty) as totalProductQty
           from
				return_challan_details rcd
			inner join products p on p.id = rcd.product_id
			inner join return_challans rc on rcd.challan_id = rc.id
			inner join ncp_types nt on rc.ncp_type_id = nt.id
			inner join stores s on s.id = rc.sender_store_id
			inner join territories t on t.id = s.territory_id
			inner join offices o on o.id = t.office_id
            where rc.ncp_type_id = $id
            group by nt.id,o.office_name,t.name,p.name,nt.name
		";
		$data_arrays = $this->NcpType->query($sqlQuery);
		//$this->dd($data_arrays);
		$this->set(compact('data_arrays'));
	}





}
