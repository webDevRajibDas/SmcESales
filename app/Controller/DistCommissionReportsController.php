<?php

App::uses('AppController', 'Controller');

/**
 * CurrentInventories Controller
 *
 * @property CurrentInventory $CurrentInventory
 * @property PaginatorComponent $Paginator
 */
class DistCommissionReportsController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $uses = array('Memo', 'Thana', 'Market', 'SalesPerson', 'Outlet', 'Product', 'MeasurementUnit', 'ProductPrice', 'ProductCombination', 'Combination', 'MemoDetail', 'MeasurementUnit', 'ProductCategory', 'TerritoryAssignHistory', 'Office', 'Territory', 'ProductType');
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index() {
        $this->set('page_title', 'Distributor Commission Report');
        $region_offices = $this->Office->find('list', array(
            'conditions' => array('Office.office_type_id' => 3),
            'order' => array('office_name' => 'asc')
        ));
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
                    'office_type_id' => 2,
                    'parent_office_id' => $region_office_id,
                    "NOT" => array("id" => array(30, 31, 37))
                ),
                'order' => array('office_name' => 'asc')
            ));

            $office_id = array_keys($offices);
        } else {
            $office_conditions = array('Office.id' => $this->UserAuth->getOfficeId(), 'Office.office_type_id' => 2);
            $office_id = $this->UserAuth->getOfficeId();

            $offices = $this->Office->find('list', array(
                'conditions' => array(
                    'id' => $office_id,
                ),
                'order' => array('office_name' => 'asc')
            ));
        }
        $this->loadModel('DistTso');
        $this->loadModel('DistDistributor');
        $report_type = 0;
        $territory_id='';
        if ($this->request->is('post')) {
            if ($this->request->data['Memo']['report_type'] == 2) {
                $this->set('page_title', 'Distributor Summary Report');
            }
            $this->loadModel('Office');
            $this->loadModel('DistAreaExecutive');
            $this->loadModel('DistChallan');
            $date_from = date('Y-m-d', strtotime($this->request->data['Memo']['date_from']));
            $date_to = date('Y-m-d', strtotime($this->request->data['Memo']['date_to']));
            $date_from_plain = $this->request->data['Memo']['date_from'];
            $date_to_plain = $this->request->data['Memo']['date_to'];

            $date_from = "'" . $date_from . "'";
            $date_to = "'" . $date_to . "'";
            $office_id = array_key_exists('office_id', $this->request->data['Memo']) ? $this->request->data['Memo']['office_id'] : '';
            $dist_distributor_id = array_key_exists('dist_distributor_id', $this->request->data['Memo']) ? $this->request->data['Memo']['dist_distributor_id'] : '';
            $product_category_id = $this->request->data['Memo']['product_category_id'];
            $product_id = $this->request->data['Memo']['product_id'];
            $region_office_id = $this->request->data['Memo']['region_office_id'];
            $dist_area_executive_id = !empty($this->request->data['Memo']['dist_area_executive_id'])?$this->request->data['Memo']['dist_area_executive_id']:'';
            $report_type = $this->request->data['Memo']['report_type'];
            $dist_tso_id = array_key_exists('dist_tso_id', $this->request->data['Memo']) ? $this->request->data['Memo']['dist_tso_id'] : '';
            $territory_id = isset($this->request->data['Memo']['territory_id']) != '' ? $this->request->data['Memo']['territory_id'] : 0;
            $thana_id = isset($this->request->data['Memo']['thana_id']) != '' ? $this->request->data['Memo']['thana_id'] : 0;
            $market_id = isset($this->request->data['Memo']['market_id']) != '' ? $this->request->data['Memo']['market_id'] : 0;
                
            $string = ' ';
            $join = ' ';
            $field = ' ';
            $group = ' ';
            
            if (!empty($region_office_id) && $region_office_id != 41) {
                $string .= " and offices.parent_office_id=$region_office_id";
            } else {
                $string .= " and offices.parent_office_id in(20,38)";
            }
            if (!empty($office_id)) {
                $string .= " and ch.office_id=$office_id";
                $join .= "  INNER JOIN dist_tso_mappings   ON ch.office_id= dist_tso_mappings.office_id 
                and ch.dist_distributor_id= dist_tso_mappings.dist_distributor_id";
                $field .= " ,dist_tso_mappings.dist_tso_id";
                $group .= " ,dist_tso_id";
            }
            if (!empty($territory_id)) {
                $string .= " and dist_outlet_maps.territory_id=$territory_id";
                $join .= " INNER JOIN dist_outlet_maps ON dist_tso_mappings.dist_distributor_id=dist_outlet_maps.dist_distributor_id ";  
            }
            
            if (!empty($thana_id)) {
                $string .= " and markets.thana_id=$thana_id";
                $join .= " INNER JOIN markets ON markets.id=dist_outlet_maps.market_id ";  
            }
            
            if (!empty($market_id)) {
                $string .= " and dist_outlet_maps.market_id=$market_id";
            }
            
            
            if (!empty($dist_tso_id)) {
                $string .= " and ch.dist_distributor_id in (select distinct dist_distributor_id from dist_tso_mappings 
                where office_id=$office_id and dist_tso_id=$dist_tso_id)";
            }
            if (!empty($dist_distributor_id)) {
                $string .= " and ch.dist_distributor_id=$dist_distributor_id";
            }

            if (!empty($product_category_id)) {
                $string .= " and products.product_category_id=$product_category_id";
            }
            if (!empty($product_id)) {
                $string .= " and cd.product_id=$product_id";
            }

            // pr($this->request->data);die();
            $data = $this->DistChallan->query("SELECT 
                    offices.office_name ,
                    offices.id as office_id ,
                    offices.parent_office_id as parent_office_id,
                    count(distinct(ch.challan_no)) as number_of_challans,
                    dist.name as distributor_name,
                    ch.dist_distributor_id,
                    SUM(cd.challan_qty) as total_challan_qty,
                    SUM(cd.received_qty) as  total_received_qty,
                    SUM(cd.challan_qty*cd.price) as price,
                    products.id
                    $field
                from dist_challans AS ch 
                INNER JOIN dist_challan_details AS cd on ch.id=cd.challan_id 
                INNER JOIN offices ON ch.office_id= offices.id
                INNER JOIN products  ON cd.product_id= products.id
                INNER JOIN dist_distributors as dist  ON ch.dist_distributor_id= dist.id
                $join
                where  ch.challan_date between $date_from and $date_to and cd.price !=0
                $string 
                group by offices.parent_office_id,offices.office_name,offices.id$group,ch.dist_distributor_id,dist.name,products.id 
                ORDER BY offices.parent_office_id,offices.id,ch.dist_distributor_id,distributor_name,products.id
                ");
            if ($report_type == 2) {
                foreach ($data as $key => $value) {
                    $tso_id = $this->get_tso_name($value[0]['office_id'], $value[0]['dist_distributor_id']);
                    !isset($sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['number_of_challans']) ? $sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['number_of_challans'] = 0 : [];
                    !isset($sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['tp']) ? $sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['tp'] = 0 : [];
                    !isset($sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['commissions']) ? $sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['commissions'] = 0 : [];
                    !isset($sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['dp']) ? $sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['dp'] = 0 : [];
                    !isset($sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['office_name']) ? $sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['office_name'] = 0 : [];
                    !isset($sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['parent_office_id']) ? $sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['parent_office_id'] = 0 : [];

                    $sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['number_of_challans'] += $value[0]['number_of_challans'];
                    $tp = $sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['tp'] += $value[0]['price'];
                    $commission = $this->get_commissions($date_from_plain, $date_to_plain, $value[0]['price'], $value[0]['office_id'], $value[0]['dist_distributor_id'], $value[0]['id']);
                    $comm = $sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['commissions'] += $commission;
                    $sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['dp'] = $tp - $comm;
                    $sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['office_name'] = $value[0]['office_name'];
                    $sanitize_array[$value[0]['office_id']][$tso_id][$value[0]['dist_distributor_id']]['parent_office_id'] = $value[0]['parent_office_id'];
                }
            }
            //pr($this->request->data);pr($data);pr($sanitize_array);die();
            $offices = $this->Office->find('list', array(
                'conditions' => array('Office.parent_office_id' => $region_office_id),
                'order' => array('office_name' => 'asc')
            ));
            //pr($this->request->data);pr($data);die();
            if(!empty($market_id)){
                /*$distAreaExecutives = $this->DistAreaExecutive->find('list', array(
                    'conditions' => array('DistAreaExecutive.office_id' => $office_id),
                    'order' => array('name' => 'asc')
                ));*/   
                $ae_info=$this->DistDistributor->query("SELECT 
                        dist_area_executives.id as id,
                        dist_area_executives.name as name
                    from dist_tso_mappings  
                        INNER JOIN dist_outlet_maps ON dist_tso_mappings.dist_distributor_id=dist_outlet_maps.dist_distributor_id
                        INNER JOIN markets ON markets.id=dist_outlet_maps.market_id
                        INNER JOIN dist_area_executives on dist_tso_mappings.office_id=dist_area_executives.office_id
                    where dist_tso_mappings.office_id=$office_id 
                        and dist_outlet_maps.territory_id=$territory_id 
                        and dist_outlet_maps.market_id=$market_id
                    GROUP BY dist_area_executives.id,dist_area_executives.name 
                        ");
                foreach ($ae_info as $key=>$value) {
                  $distAreaExecutives[$value[0]['id']]=$value[0]['name']; 
                }
            }else{
              $distAreaExecutives =array();  
            }
            
            if(!empty($dist_area_executive_id)){
                /*$distTsos = $this->DistTso->find('list', array(
                    'conditions' => array('DistTso.office_id' => $office_id, 'DistTso.dist_area_executive_id' => $dist_area_executive_id),
                    'order' => array('name' => 'asc')
                )); */
                $tso_info=$this->DistDistributor->query("SELECT 
                        dist_tsos.id as id,
                        dist_tsos.name as name
                    from dist_tso_mappings  
                        INNER JOIN dist_outlet_maps ON dist_tso_mappings.dist_distributor_id=dist_outlet_maps.dist_distributor_id
                        INNER JOIN markets ON markets.id=dist_outlet_maps.market_id
                        INNER JOIN dist_area_executives on dist_tso_mappings.office_id=dist_area_executives.office_id
                        INNER JOIN dist_tsos on dist_tso_mappings.dist_tso_id=dist_tsos.id
                    where dist_tso_mappings.office_id=$office_id 
                        and dist_outlet_maps.territory_id=$territory_id 
                        and dist_outlet_maps.market_id=$market_id
                        and dist_area_executives.id=$dist_area_executive_id
                    GROUP BY dist_tsos.id,dist_tsos.name 
                        ");
                foreach ($tso_info as $key=>$value) {
                  $distTsos[$value[0]['id']]=$value[0]['name']; 
                }
            }else{
              $distTsos =array();  
            }

            if(!empty($dist_tso_id)){
                /*$distDistributors = $this->DistDistributor->find('list', array(
                    'conditions' => array('DistDistributor.office_id' => $office_id),
                    'order' => array('name' => 'asc')
                ));*/
                $distributor_info=$this->DistDistributor->query("SELECT 
                        dist_tso_mappings.dist_distributor_id,
                        dist_distributors.name
                    from dist_tso_mappings  
                        INNER JOIN dist_outlet_maps ON dist_tso_mappings.dist_distributor_id=dist_outlet_maps.dist_distributor_id
                        INNER JOIN markets ON markets.id=dist_outlet_maps.market_id
                        INNER JOIN dist_area_executives on dist_tso_mappings.office_id=dist_area_executives.office_id
                        INNER JOIN dist_tsos on dist_tso_mappings.dist_tso_id=dist_tsos.id
                        INNER JOIN dist_distributors ON dist_tso_mappings.dist_distributor_id=dist_distributors.id
                    where dist_tso_mappings.office_id=$office_id 
                        and dist_outlet_maps.territory_id=$territory_id 
                        and dist_outlet_maps.market_id=$market_id
                        and dist_area_executives.id=$dist_area_executive_id
                        and dist_tso_mappings.dist_tso_id=$dist_tso_id
                    GROUP BY dist_tso_mappings.dist_distributor_id,dist_distributors.name 
                        ");
                foreach ($distributor_info as $key=>$value) {
                  $distDistributors[$value[0]['dist_distributor_id']]=$value[0]['name']; 
                }
            }else{
              $distDistributors =array();  
            }

            $office_list = $this->Office->find('list');

            $this->set(compact('data', 'date_from', 'date_to', 'region_office_id', 'office_id', 'sanitize_array'));
        }
        $this->loadModel('Product');
        $this->loadModel('ProductCategory');
        $this->loadModel('DistAreaExecutive');
        $this->loadModel('Territory');
        $territory = $this->Territory->find('all', array(
            'fields' => array('Territory.id', 'Territory.name', 'SalesPerson.name'),
            'conditions' => array('Territory.office_id' => $office_id),
            'order' => array('Territory.name' => 'asc'),
            'recursive' => 0
        ));

        $data_array = array();

        foreach ($territory as $key => $value) {
            $t_id = $value['Territory']['id'];
            $t_val = $value['Territory']['name'] . ' (' . $value['SalesPerson']['name'] . ')';
            $data_array[$t_id] = $t_val;
        }

        $territories = $data_array;
        if ($territory_id) {
            $markets = $this->Memo->Market->find('list', array(
                'conditions' => array('Market.territory_id' => $territory_id),
                'order' => array('Market.name' => 'asc')
            ));
            
            if ($territory_id) {
            $thanas = $this->Thana->find('list', array(
                'conditions' => array('ThanaTerritory.territory_id' => $territory_id),
                'joins' => array(
                    array(
                        'table' => 'thana_territories',
                        'alias' => 'ThanaTerritory',
                        'conditions' => 'ThanaTerritory.thana_id=Thana.id'
                    )
                )
            ));

       }
            
        } else {
            $markets = array();
            $thanas = array();
        }
        
        $productCategories = $this->ProductCategory->find('list');        
        $products = $this->Product->find('list');  
        $distTsos_list = $this->DistTso->find('list');      
        $dist_ex_list = $this->DistAreaExecutive->find('list');
        $distDistributors_list = $this->DistDistributor->find('list');
        $offices_list = $this->Office->find('list');
        
        $this->set(compact('categories_products'));
        $this->set(compact('thanas','markets','territories','dist_ex_list', 'productCategories', 'offices', 'territories', 'office_list', 'outlets', 'current_date', 'distAreaExecutives', 'products', 'office_id', 'office_parent_id', 'distDistributors', 'date_from', 'date_to', 'region_offices', 'distTsos', 'report_type', 'distTsos_list', 'distDistributors_list', 'offices_list'));
    }
    public function get_respective_id($office_id=15,$dist_distributor_id=13,$flag='territory_id'){
        $this->loadModel('DistOutletMap');
        $get_data = $this->DistOutletMap->query("select 
	dist_outlet_maps.office_id,
	dist_outlet_maps.dist_distributor_id,
	dist_outlet_maps.outlet_id,
	--dist_outlet_maps.market_id,
	outlets.market_id,
	markets.thana_id,
	markets.territory_id,
	thanas.district_id
	from dist_outlet_maps 
	INNER JOIN outlets ON dist_outlet_maps.outlet_id=outlets.id
	INNER JOIN markets ON outlets.market_id=markets.id
	INNER JOIN thanas ON markets.thana_id=thanas.id
	where office_id=$office_id and dist_distributor_id=$dist_distributor_id");
        if(count($get_data)>0){
            if($flag=='district_id'){
                return $get_data[0][0]['district_id'];   
            }elseif($flag=='thana_id'){
                return $get_data[0][0]['thana_id'];            
            }elseif($flag=='market_id'){
                return $get_data[0][0]['market_id'];   
            }elseif($flag=='outlet_id'){
                return $get_data[0][0]['outlet_id'];  
            }else{
                return $get_data[0][0]['territory_id'];   
            }  
        }else{
           return ''; 
        }
        /*$data = $this->DistOutletMap->find('all', array(
            'conditions' => array(
                'DistOutletMap.office_id' => $office_id,
                'DistOutletMap.dist_distributor_id' =>array($dist_distributor_id),
            ),
            'recursive' => -1
        ));
        if(count($data)>0){
            $market_id=$data[0]['DistOutletMap']['market_id'];
            $territory_id=$data[0]['DistOutletMap']['territory_id'];
            $markets = $this->Market->query("select thanas.id as thana_id,thanas.district_id as district_id  
                from markets inner join thanas on  
                markets.thana_id=thanas.id
                where markets.id=$market_id and markets.territory_id=$territory_id"
                );
                    pr($data);pr($markets);
            if($flag=='district_id'){
                return $markets[0][0]['district_id'];   
            }elseif($flag=='thana_id'){
                return $markets[0][0]['thana_id'];            
            }elseif($flag=='market_id'){
                return $data[0]['DistOutletMap']['market_id'];   
            }elseif($flag=='outlet_id'){
                return $data[0]['DistOutletMap']['outlet_id'];  
            }else{
                return $data[0]['DistOutletMap']['territory_id'];   
            }
            
        }else{
            return '';
        }*/
    }
    //$date_from='18-04-2019',$date_to='22-04-2019',
    public function get_commissions1($date_from = '12-04-2019', $date_to = '18-04-2019', $tp = '123.00', $product_id = 29) {
        $this->loadModel('DistributorCommission');
        $date_from_filter = date('Y-m-d', strtotime($date_from));
        $date_to_filter = date('Y-m-d', strtotime($date_to));
        $data = $this->DistributorCommission->find('all', array(
            'conditions' => array(
                'DistributorCommission.effective_date >=' => $date_from_filter,
                'DistributorCommission.effective_date <=' => $date_to_filter,
                'DistributorCommission.product_id' => $product_id,
            ),
            'fields' => array('id', 'product_id', 'commission_amount'),
            'recursive' => -1
        ));
        $n = count($data);
        if ($n == 0) {
            return 0;
        }
        $get_commission = 0;
        foreach ($data as $key => $value) {
            $commission_amount = $value['DistributorCommission']['commission_amount'];
            $get_commission += ($tp * $value['DistributorCommission']['commission_amount']) / 100;
        }
        return $get_commission / $n;
    }

    public function get_commissions($date_from = '2019-04-10', $date_to = '2019-04-16', $tp = '123.00', $office_id = 16, $dist_distributor_id = 60, $product_id = 29) {
        $this->loadModel('DistributorCommission');
        $date_from_filter = date('Y-m-d', strtotime($date_from));
        $date_to_filter = date('Y-m-d', strtotime($date_to));

        $data = $this->DistributorCommission->find('all', array(
            'conditions' => array(
                'DistributorCommission.effective_date >=' => $date_from_filter,
                'DistributorCommission.effective_date <=' => $date_to_filter,
                'DistributorCommission.product_id' => $product_id,
            ),
            'fields' => array('id', 'product_id', 'commission_amount', 'effective_date'),
            'recursive' => -1
        ));
        if (count($data) == 0) {
            $data1 = $this->DistributorCommission->find('all', array(
                'conditions' => array(
                    'DistributorCommission.effective_date <' => $date_to_filter,
                    'DistributorCommission.product_id' => $product_id,
                ),
                'fields' => array('id', 'product_id', 'commission_amount', 'effective_date'),
                'recursive' => -1,
                'order' => 'DistributorCommission.effective_date DESC',
                'limit' => 1
            ));

            if (count($data1) == 0) {
                $commissions = 0;
            }
        }

        // $starting_date[0]['starting_date']=$date_from_filter;
        $i = 0;
        $n = count($data) - 1;
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $effective_date = $value['DistributorCommission']['effective_date'];
                if ($key == 0) {
                    if (date('Y-m-d', strtotime("$effective_date")) != $date_from_filter) {
                        $date_array[$i]['starting_date'] = $date_from_filter;
                        $date_array[$i]['ending_date'] = date('Y-m-d', strtotime("$effective_date-1day"));
                    }
                }

                if ($key != 0) {
                    $previous_date = $data[$i - 1]['DistributorCommission']['effective_date'];
                    $date_array[$i]['starting_date'] = date('Y-m-d', strtotime("$previous_date"));
                    $date_array[$i]['ending_date'] = date('Y-m-d', strtotime("$effective_date-1day"));
                }

                if ($key == $n) {
                    $date_array[++$i]['starting_date'] = date('Y-m-d', strtotime($value['DistributorCommission']['effective_date']));
                    $date_array[$i]['ending_date'] = date('Y-m-d', strtotime("$date_to"));
                }
                $i++;
            }
        } elseif (count($data1) > 0) {
            foreach ($data1 as $key => $value) {
                $date_array[$i]['starting_date'] = $date_from_filter;
                $date_array[$i]['ending_date'] = $date_to_filter;
            }
        } else {
            $date_array = array();
        }
        if (count($date_array) == 0) {
            return 0;
        } else {
            $this->loadModel('DistChallan');
            $commissions = 0;
            $total_qty = 0;
            foreach ($date_array as $key => $value) {
                $starting_date = $value['starting_date'];
                $ending_date = $value['ending_date'];

                $starting_date = "'" . $starting_date . "'";
                $ending_date = "'" . $ending_date . "'";

                $distributorCommissions = $this->DistributorCommission->find('all', array(
                    'conditions' => array(
                        'DistributorCommission.effective_date <=' => $value['ending_date'],
                        'DistributorCommission.product_id' => $product_id,
                    ),
                    'fields' => array('id', 'commission_amount'),
                    'recursive' => -1,
                    'order' => 'DistributorCommission.effective_date DESC',
                    'limit' => 1
                ));

                $distChallans = $this->DistChallan->query("SELECT 
                    ch.office_id,
                    ch.dist_distributor_id,
                    SUM(cd.challan_qty) as challan_qty,
                    SUM(cd.challan_qty*cd.price) as price,
                    cd.product_id
                    from dist_challans AS ch 
                    INNER JOIN dist_challan_details AS cd on ch.id=cd.challan_id 
                    where  ch.challan_date between $starting_date and $ending_date 
                    and ch.office_id=$office_id 
                    and ch.dist_distributor_id=$dist_distributor_id 
                    and cd.product_id=$product_id
                    and cd.price !=0  
                    group by ch.office_id,ch.dist_distributor_id,cd.product_id
                    ");

                if (count($distributorCommissions) == 0) {
                    $commissions += 0;
                } else {
                    if (count($distChallans) > 0) {
                        $total_amount = $distChallans[0][0]['price'];
                        $total_qty += $distChallans[0][0]['challan_qty'];
                        $commissions += ($total_amount * $distributorCommissions[0]['DistributorCommission']['commission_amount']) / 100;
                    } else {
                        $commissions += 0;
                    }
                }
            }

            //echo $commissions.'----------';
            if ($total_qty == 0) {
                return 0;
            }
            return $commissions;
        }
    }

    public function get_total_challan($office_id=22, $dist_distributor_id=4,$date_from='2018-04-01',$date_to='2019-04-30') {
        $date_from_filter = "'".date('Y-m-d', strtotime($date_from))."'";
        $date_to_filter = "'".date('Y-m-d', strtotime($date_to))."'";
        $this->loadModel('DistChallan');
        $data = $this->DistChallan->query("
                    select distinct(ch.challan_no),count(distinct(ch.challan_no)), 
                    SUM(count(distinct(ch.challan_no))) OVER() AS distinct_count
                    from dist_challans as ch  
                    where ch.challan_date between $date_from_filter and $date_to_filter
                    and ch.office_id=$office_id 
                    and ch.dist_distributor_id=$dist_distributor_id
                    group by ch.office_id,ch.dist_distributor_id,ch.challan_no");
        return (count($data)>0)?$data[0][0]['distinct_count']:'';
    }

    public function get_region_office($office_name) {
        $this->loadModel('Office');
        $data = $this->Office->find('all', array(
            'conditions' => array(
                'Office.office_name' => $office_name,
            ),
            'fields' => array('id', 'parent_office_id'),
            'recursive' => -1
        ));
        //pr($data);die();
        return $data[0]['Office']['parent_office_id'];
    }

    public function get_ae_tso_dist() {
        $office_id=$this->request->data['office_id'];
        $market_id=$this->request->data['market_id'];
        $territory_id=$this->request->data['territory_id'];
        $thana_id=$this->request->data['thana_id'];
        $dist_area_executive_id=$this->request->data['dist_area_executive_id'];
        $dist_tso_id=$this->request->data['dist_tso_id'];
        $tag=$this->request->data['tag'];
       /*$office_id=24;
        $market_id='';//168533;
        $territory_id=20081;
        $dist_area_executive_id=6;
        $dist_tso_id=25;
        $tag='tso';*/
        $this->loadModel('DistAreaExecutive');
        $where=$join=$field=$group='';
        if(!empty($territory_id)){
          $where .= " and dist_outlet_maps.territory_id=$territory_id  ";  
        }
        if(!empty($market_id)){
          $where .= " and dist_outlet_maps.market_id=$market_id ";  
        }
        if ($tag == 'tso') {
                $where .= " and dist_area_executives.id=$dist_area_executive_id ";
                $join .= "  INNER JOIN dist_tsos ON dist_tso_mappings.dist_tso_id=dist_tsos.id";
                $field .= " ,dist_tso_mappings.dist_tso_id as id,dist_tsos.name as name ";
                $group .=" ,dist_tso_mappings.dist_tso_id,dist_tsos.name";
                $table_name='DistTso';
        }elseif($tag == 'dist') {
                $where .= " and dist_area_executives.id=$dist_area_executive_id "
                        . " and dist_tso_mappings.dist_tso_id=$dist_tso_id ";
                $join .= "  INNER JOIN dist_tsos ON dist_tso_mappings.dist_tso_id=dist_tsos.id"
                        . " INNER JOIN dist_distributors ON dist_tso_mappings.dist_distributor_id=dist_distributors.id ";
                $field .= " ,dist_distributors.id,dist_distributors.name ";
                $group .=" ,dist_distributors.id,dist_distributors.name";
                $table_name='DistDistributor';
        }else{
           $field .= " ,dist_area_executives.id as id,dist_area_executives.name as name ";
           $group .=" ,dist_area_executives.id,dist_area_executives.name";
           $table_name='DistAreaExecutive';
        }
        $data = $this->DistAreaExecutive->query("	
        SELECT 
            dist_tso_mappings.office_id
            $field
        from dist_tso_mappings  
            INNER JOIN dist_outlet_maps ON dist_tso_mappings.dist_distributor_id=dist_outlet_maps.dist_distributor_id
            INNER JOIN markets ON markets.id=dist_outlet_maps.market_id 
            INNER JOIN dist_area_executives ON dist_tso_mappings.office_id=dist_area_executives.office_id
            $join
        where dist_tso_mappings.office_id=$office_id
            $where
            GROUP BY dist_tso_mappings.office_id $group
            ");
       $array=array();
           foreach($data as $key=>$value){
              $array[$key]['id']=$value[0]['id'];
              $array[$key]['name']=$value[0]['name'];
           } 
        //pr($data);pr($array);die();
        echo json_encode($array);
        $this->autoRender = false;
    }

    public function get_area_executive_id($office_id = 28, $dist_distributor_id = 115) {
        $this->loadModel('DistTso');
        $data = $this->DistTso->query("select dist_tso_mappings.office_id,dist_tso_mappings.dist_distributor_id,
dist_tso_mappings.dist_tso_id,dist_tsos.name as tso_name,
dist_area_executives.id as area_executive_id,
dist_area_executives.name as area_executive_name
from dist_tso_mappings 
inner join dist_distributors on dist_tso_mappings.dist_distributor_id=dist_distributors.id
inner join dist_tsos on dist_tso_mappings.dist_tso_id=dist_tsos.id
inner join dist_area_executives on dist_tso_mappings.office_id=dist_area_executives.office_id
where dist_tso_mappings.office_id=$office_id and dist_tso_mappings.dist_distributor_id=$dist_distributor_id");

        if (count($data) > 0) {
            return $data[0][0]['area_executive_id'];
        } else {
            return '';
        }
    }

    public function get_area_executive_name($office_id = 28, $dist_distributor_id = 115) {
        $this->loadModel('DistTso');
        $data = $this->DistTso->query("
            select dist_tso_mappings.office_id,dist_tso_mappings.dist_distributor_id,
            dist_tso_mappings.dist_tso_id,dist_tsos.name as tso_name,
            dist_area_executives.name as area_executive_name
            from dist_tso_mappings 
            inner join dist_distributors on dist_tso_mappings.dist_distributor_id=dist_distributors.id
            inner join dist_tsos on dist_tso_mappings.dist_tso_id=dist_tsos.id
            inner join dist_area_executives on dist_tso_mappings.office_id=dist_area_executives.office_id
            where dist_tso_mappings.office_id=$office_id and dist_tso_mappings.dist_distributor_id=$dist_distributor_id");

        if (count($data) > 0) {
            return $data[0][0]['area_executive_name'];
        } else {
            return '';
        }
    }

    public function get_tso_name($office_id = 28, $dist_distributor_id = 115) {
        $this->loadModel('DistTso');
        $data = $this->DistTso->query("select dist_tso_mappings.office_id,dist_tso_mappings.dist_distributor_id,
dist_tso_mappings.dist_tso_id,dist_tsos.id as tso_id,dist_tsos.name as tso_name
from dist_tso_mappings 
inner join dist_distributors on dist_tso_mappings.dist_distributor_id=dist_distributors.id
inner join dist_tsos on dist_tso_mappings.dist_tso_id=dist_tsos.id
where dist_tso_mappings.office_id=$office_id and dist_tso_mappings.dist_distributor_id=$dist_distributor_id");

        if (count($data) > 0) {
            return $data[0][0]['tso_id'];
        } else {
            return '';
        }
    }

    public function get_tso() {
        $this->loadModel('DistTso');
        $data = $this->DistTso->find('all', array(
            'conditions' => array(
                'DistTso.office_id' =>24,// $this->request->data['office_id'],
                'DistTso.dist_area_executive_id' =>6// $this->request->data['dist_area_executive_id'],
            ),
            'fields' => array('id', 'name'),
            'recursive' => -1
        ));
        pr($data);die();
        echo json_encode($data);
        $this->autoRender = false;
    }

    public function get_distributor() {
        $this->loadModel('DistDistributor');
        $office_id = $this->request->data['office_id'];
        $dist_tso_id = $this->request->data['dist_tso_id'];
        //pr($office_id);pr($dist_tso_id);die();
        $data1 = $this->DistDistributor->query("
            select dist_tso_mappings.office_id,dist_tso_mappings.dist_distributor_id,
            dist_tso_mappings.dist_tso_id,dist_distributors.name
            from dist_tso_mappings inner join dist_distributors on dist_tso_mappings.dist_distributor_id=dist_distributors.id
            where dist_tso_mappings.office_id=$office_id and dist_tso_mappings.dist_tso_id=$dist_tso_id");

        foreach ($data1 as $key => $value) {
            $array[$key]['DistDistributor']['id'] = $value[0]['dist_distributor_id'];
            $array[$key]['DistDistributor']['name'] = $value[0]['name'];
        }

        $data = $this->DistDistributor->find('all', array(
            'conditions' => array(
                'DistDistributor.office_id' => $this->request->data['office_id'] = 23,
            ),
            'fields' => array('id', 'name'),
            'recursive' => -1
        ));
        echo json_encode($array);
        $this->autoRender = false;
    }

    public function get_products() {
        $this->loadModel('Product');
        $data = $this->Product->find('all', array(
            'conditions' => array(
                'Product.product_category_id' => $this->request->data['product_category_id'],
            ),
            'fields' => array('id', 'name'),
            'recursive' => -1
        ));
        echo json_encode($data);
        $this->autoRender = false;
    }

}
