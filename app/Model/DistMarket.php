<?php

App::uses('AppModel', 'Model');

/**
 * Market Model
 *
 * @property LocationType $LocationType
 * @property Thana $Thana
 * @property Territory $Territory
 * @property MarketPerson $MarketPerson
 * @property Outlet $Outlet
 */
class DistMarket extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    /* -------- validation------- */
    public $validate = array(
        'name' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Name field is required.'
            )
        ),
        'location_type_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Location Type field is required.'
            )
        ),
        'thana_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Thana field is required.'
            )
        ),
        'territory_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Territory field is required.'
            )
        ),
    );

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'LocationType' => array(
            'className' => 'LocationType',
            'foreignKey' => 'location_type_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Thana' => array(
            'className' => 'Thana',
            'foreignKey' => 'thana_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Territory' => array(
            'className' => 'Territory',
            'foreignKey' => 'territory_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'DistRoute' => array(
            'className' => 'DistRoute',
            'foreignKey' => 'dist_route_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        /*'MarketPerson' => array(
            'className' => 'MarketPerson',
            'foreignKey' => 'dist_market_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),*/
        'DistOutlet' => array(
            'className' => 'DistOutlet',
            'foreignKey' => 'dist_market_id',
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

    // data filter
    public function filter($params, $conditions) {
        $conditions = array();

        if (CakeSession::read('Office.parent_office_id') != 0) {
            $conditions[] = array('Territory.office_id' => CakeSession::read('Office.id'));
        }
        if(CakeSession::read('UserAuth.User.user_group_id') == 1034){
            if(empty($params['DistMarket.dist_route_id'])){
                App::import('Model', 'DistUserMapping');
                App::import('Model', 'DistRouteMapping');
                $this->DistUserMapping = new DistUserMapping();
                $this->DistRouteMapping = new DistRouteMapping();

                $sp_id = CakeSession::read('UserAuth.User.sales_person_id');
                $data = $this->DistUserMapping->find('first',array('conditions'=>array('DistUserMapping.sales_person_id'=>$sp_id)));
                $distributor_id = $data['DistUserMapping']['dist_distributor_id'];

                $route_list = $this->DistRouteMapping->find('list',array(
                    'conditions'=>array('dist_distributor_id'=>$distributor_id),
                    'fields'=>array('DistRouteMapping.dist_route_id','DistRouteMapping.dist_distributor_id'),
                   ));
                $route_conditions = array('conditions' =>array('DistRoute.id'=>array_keys($route_list)), 'order' => array('DistRoute.name' => 'ASC'));
                $conditions[] = array('DistMarket.dist_route_id' => array_keys($route_list));
            }
        }
        if (!empty($params['DistMarket.code'])) {
            $conditions[] = array('DistMarket.code' => $params['DistMarket.code']);
        }
        if (!empty($params['DistMarket.name'])) {
            $conditions[] = array('DistMarket.name LIKE' => '%' . $params['DistMarket.name'] . '%');
        }
        if (!empty($params['DistMarket.location_type_id'])) {
            $conditions[] = array('DistMarket.location_type_id' => $params['DistMarket.location_type_id']);
        }
        if (!empty($params['DistMarket.district_id'])) {
            $conditions[] = array('Thana.district_id' => $params['DistMarket.district_id']);
        }
        if (!empty($params['DistMarket.thana_id'])) {
            $conditions[] = array('DistMarket.thana_id' => $params['DistMarket.thana_id']);
        }
        if (!empty($params['DistMarket.office_id'])) {
            $conditions[] = array('Territory.office_id' => $params['DistMarket.office_id']);
        }
        if (!empty($params['DistMarket.territory_id'])) {
            $conditions[] = array('DistMarket.territory_id' => $params['DistMarket.territory_id']);
        }
        if (!empty($params['DistMarket.dist_route_id'])) {
            $conditions[] = array('DistMarket.dist_route_id' => $params['DistMarket.dist_route_id']);
        }
        return $conditions;
    }

}
