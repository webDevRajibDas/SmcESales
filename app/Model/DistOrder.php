<?php
App::uses('AppModel', 'Model');
/**
 * DistOrder Model
 *
 */
class DistOrder extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	
	// data filter
	public function filter($params, $conditions) {   
	
		$conditions = array();
       	
        if(!empty($params['DistOrder.office_id']))
        {
            $conditions[] = array('DistOrder.office_id' => $params['DistOrder.office_id']);
        
        }
        if(empty($params['DistOrder.office_id'])){
        	if(CakeSession::read('Office.parent_office_id') != 0)
			{
				$conditions[] = array('DistOrder.office_id' => CakeSession::read('Office.id'));
			}
        }
        if (!empty($params['DistOrder.order_reference_no'])) 
        {
            $conditions[] = array('DistOrder.dist_order_no Like' => "%".$params['DistOrder.order_reference_no']."%");
        }
		if (!empty($params['DistOrder.dist_route_id'])) 
		{
            $conditions[] = array('DistOrder.dist_route_id' => $params['DistOrder.dist_route_id']);
        }
		if (!empty($params['DistOrder.outlet_id'])) 
		{
            $conditions[] = array('DistOrder.outlet_id' => $params['DistOrder.outlet_id']);
        }
		if(!empty($params['DistOrder.market_id'])) 
		{
            $conditions[] = array('DistOrder.market_id' => $params['DistOrder.market_id']);
        }
        if (!empty($params['DistOrder.thana_id'])) 
        {
            $conditions[] = array('Market.thana_id' => $params['DistOrder.thana_id']);
        }
		if (!empty($params['DistOrder.territory_id'])) 
		{
            $conditions[] = array('DistOrder.territory_id' => $params['DistOrder.territory_id']);
        }
        if (!empty($params['DistOrder.tso_id'])) 
		{
            $conditions[] = array('DistOrder.tso_id' => $params['DistOrder.tso_id']);
        }else{
        	if(CakeSession::read('UserAuth.User.user_group_id') == 1029){
               
                App::import('Model', 'DistTso');
                $this->DistTso = new DistTso();
                $user_id = CakeSession::read('UserAuth.User.id');

	            $dist_tso_info = $this->DistTso->find('first',array(
                        'conditions'=>array('DistTso.user_id'=>$user_id),
                        'fields'=> array('DistTso.id','DistTso.name'),
                        'recursive'=> -1,
                    ));
                $dist_tso_id = $dist_tso_info['DistTso']['id'];
                $conditions[] = array('DistOrder.tso_id' => $dist_tso_id);
            }
        }
        if (!empty($params['DistOrder.distributor_id'])) 
        {
            $conditions[] = array('DistOrder.distributor_id' => $params['DistOrder.distributor_id']);
        }else{
        	if(CakeSession::read('UserAuth.User.user_group_id') == 1034){
                App::import('Model', 'DistUserMapping');
                $sp_id = CakeSession::read('UserAuth.User.sales_person_id');
                $this->DistUserMapping = new DistUserMapping();
                $data = $this->DistUserMapping->find('first',array('conditions'=>array('DistUserMapping.sales_person_id'=>$sp_id)));
                $distributor_id = $data['DistUserMapping']['dist_distributor_id'];
                $conditions[] = array('DistOrder.distributor_id' => $distributor_id);
            }
        }
        
        if (!empty($params['DistOrder.sr_id'])) 
        {
            $conditions[] = array('DistOrder.sr_id' => $params['DistOrder.sr_id']);
        }

		if (isset($params['DistOrder.status'])!='')
		{
			if($params['DistOrder.status'] == 2 || $params['DistOrder.status'] == 4){
				if($params['DistOrder.status'] == 2){
					$conditions[] = array('DistOrder.processing_status' => 1);
				}
				else{
					$conditions[] = array('DistOrder.processing_status' => 2);
				}
			}else{
				$conditions[] = array('DistOrder.status' => $params['DistOrder.status']);
			}
            
        }
		if (isset($params['DistOrder.date_from'])!='') 
		{
            $conditions[] = array('DistOrder.order_date >=' => Date('Y-m-d H:i:s',strtotime($params['DistOrder.date_from'])));
        }
		if (isset($params['DistOrder.date_to'])!='') 
		{
            $conditions[] = array('DistOrder.order_date <=' => Date('Y-m-d H:i:s',strtotime($params['DistOrder.date_to'].' 23:59:59')));
        }   
        if (isset($params['DistOrder.from_app'])) 
		{
            $conditions[] = array('DistOrder.from_app' => 0);
        }
		
        return $conditions;
    }
	
	
	public $validate = array(
		'office_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Office field is required.'
					)
		),
		'sale_type_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Sale Type field is required.'
					)
		),
            /*
		'distibutor_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Distributot field is required.'
					)
		),
               'sr_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'SR field is required.'
					)
		),
             * 
             */
		'territory_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Territory field is required.'
					)
		),
		 'market_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Market field is required.'
					)
		),		 
		 'outlet_id' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Outlet field is required.'
					)
		),
		 'entry_date' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'Entry Date field is required.'
					)
		),
		 'order_date' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message'=> 'CsaOrder Date field is required.'
					)
		),
		'dist_order_no' => array(
			'mustNotEmpty' => array(
						'rule' => 'notEmpty',
						'message' => 'CsaOrder No field is required.'
					)
		) 
	);

	
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(		
		'DistSalesRepresentative' => array(
			'className' => 'DistSalesRepresentative',
			'foreignKey' => 'sr_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
		),
		'Office' => array(
			'className' => 'Office',
			'foreignKey' => 'office_id',
			'conditions' => '',
			'fields' => 'office_name,address',
			'order' => ''
		),
		'Outlet' => array(
			'className' => 'DistOutlet',
			'foreignKey' => 'outlet_id',
			'conditions' => '',
			'fields' => 'name,address',
			'order' => ''
		),
		
		'Territory' => array(
			'className' => 'Territory',
			'foreignKey' => 'territory_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
		'Market' => array(
			'className' => 'DistMarket',
			'foreignKey' => 'market_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
        'Distributor' => array(
			'className' => 'DistDistributor',
			'foreignKey' => 'distributor_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		),
		 'Route' => array(
			'className' => 'DistRoute',
			'foreignKey' => 'dist_route_id',
			'conditions' => '',
			'fields' => 'name',
			'order' => ''
		)
	);
	
	
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'DistOrderDetail' => array(
			'className' => 'DistOrderDetail',
			'foreignKey' => 'dist_order_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	
	
	/*----- quaery Methods -----*/


}
