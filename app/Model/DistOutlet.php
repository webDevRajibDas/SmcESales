<?php

App::uses('AppModel', 'Model');

/**
 * Outlet Model
 *
 * @property Market $Market
 * @property Category $Category
 */
class DistOutlet extends AppModel
{

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    /* ========== validate=============== */
    public $validate = array(
        'name' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Name field is required.'
            )/* ,
          'isUnique' => array(
          'rule' => 'isUnique',
          'message'=> 'Outlet already exists.'
          ) */
        ),
        /* 'telephone'      => array(
          'mustNotEmpty'    => array(
          'rule'        => 'notEmpty',
          'message' => 'Telephone field is required.'
          )
          ),
          'mobile'      => array(
          'mustNotEmpty'    => array(
          'rule'        => 'notEmpty',
          'message' => 'Mobile field is required.'
          )
          ), */
        'dist_market_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Market field is required.'
            )
        ),
        'category_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Category field is required.'
            )
        ),
        'dist_route_id' => array(
            'mustNotEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Route/Bit is required.'
            )
        )
    );

    function check_ngo($data, $fields)
    {
        if ($this->data[$this->name]['is_ngo'] == 1 and $this->data[$this->name]['institute_id'] == '') {
            return false;
        } else {
            return true;
        }
    }

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'DistMarket' => array(
            'className' => 'DistMarket',
            'foreignKey' => 'dist_market_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'OutletCategory' => array(
            'className' => 'DistOutletCategory',
            'foreignKey' => 'category_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Institute' => array(
            'className' => 'Institute',
            'foreignKey' => 'institute_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'DistBonusCardType' => array(
            'className' => 'DistBonusCardType',
            'foreignKey' => 'bonus_type_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    public $hasOne = array(
        /*'Program' => array(
            'className' => 'Program',
            'foreignKey' => 'outlet_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )*/);


    public $hasMany = array(
        'DistOutletImage' => array(
            'className' => 'DistOutletImage',
            'foreignKey' => 'dist_outlet_id',
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
    public function filter($params, $conditions)
    {
        $conditions = array();
        if (CakeSession::read('Office.parent_office_id') != 0) {
            $conditions[] = array('Territory.office_id' => CakeSession::read('Office.id'));
        }
        if (CakeSession::read('UserAuth.User.user_group_id') == 1034) {
            if (empty($params['DistOutlet.dist_route_id'])) {
                App::import('Model', 'DistUserMapping');
                App::import('Model', 'DistRouteMapping');
                $this->DistUserMapping = new DistUserMapping();
                $this->DistRouteMapping = new DistRouteMapping();

                $sp_id = CakeSession::read('UserAuth.User.sales_person_id');
                $data = $this->DistUserMapping->find('first', array('conditions' => array('DistUserMapping.sales_person_id' => $sp_id)));
                $distributor_id = $data['DistUserMapping']['dist_distributor_id'];

                $route_list = $this->DistRouteMapping->find('list', array(
                    'conditions' => array('dist_distributor_id' => $distributor_id),
                    'fields' => array('DistRouteMapping.dist_route_id', 'DistRouteMapping.dist_distributor_id'),
                ));
                $route_conditions = array('conditions' => array('DistRoute.id' => array_keys($route_list)), 'order' => array('DistRoute.name' => 'ASC'));
                $conditions[] = array('DistOutlet.dist_route_id' => array_keys($route_list));
            }
        }
        if (!empty($params['DistOutlet.name'])) {
            $conditions[] = array('DistOutlet.name LIKE' => '%' . $params['DistOutlet.name'] . '%');
        }
        if (!empty($params['DistOutlet.mobile'])) {
            $conditions[] = array('DistOutlet.mobile' => $params['DistOutlet.mobile']);
        }
        if (!empty($params['DistOutlet.category_id'])) {
            $conditions[] = array('DistOutlet.category_id' => $params['DistOutlet.category_id']);
        }
        if (!empty($params['DistOutlet.office_id'])) {
            $conditions[] = array('Territory.office_id' => $params['DistOutlet.office_id']);
        }
        if (!empty($params['DistOutlet.territory_id'])) {
            $conditions[] = array('DistMarket.territory_id' => $params['DistOutlet.territory_id']);
        }
        if (!empty($params['DistOutlet.dist_market_id'])) {
            $conditions[] = array('DistOutlet.dist_market_id' => $params['DistOutlet.dist_market_id']);
        }
        if (!empty($params['DistOutlet.thana_id'])) {
            $conditions[] = array('Thana.id' => $params['DistOutlet.thana_id']);
        }

        if (!empty($params['DistOutlet.dist_route_id'])) {
            $conditions[] = array('distRoute.id' => $params['DistOutlet.dist_route_id']);
        }
        if (!empty($params['DistOutlet.bonus_type'])) {
            $conditions[] = array('DistOutlet.bonus_type_id' => $params['DistOutlet.bonus_type']);
        }
        if (!empty($params['DistOutlet.status'])) {
            $conditions[] = array('DistOutlet.is_active' => ($params['DistOutlet.status'] == 1 ? 1 : 0));
        }
        return $conditions;
    }
}
