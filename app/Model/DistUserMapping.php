<?php
App::uses('AppModel', 'Model');


class DistUserMapping extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
   
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        
        'office_id' => array(
            'NotMustBeEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Office Id field required.'
            )
        ),
        'dist_distributor_id' => array(
            'NotMustBeEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Distributor Id field required.'
            )
        )
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Office' => array(
            'className' => 'Office',
            'foreignKey' => 'office_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'DistDistributor' => array(
            'className' => 'DistDistributor',
            'foreignKey' => 'dist_distributor_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'SalesPerson' => array(
            'className' => 'SalesPerson',
            'foreignKey' => 'sales_person_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );
   

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
            /*  'Territory' => array(
              'className' => 'Territory',
              'foreignKey' => 'territory_id',
              'dependent' => false,
              'conditions' => '',
              'fields' => '',
              'order' => '',
              'limit' => '',
              'offset' => '',
              'exclusive' => '',
              'finderQuery' => '',
              'counterQuery' => ''
              ),
              'SaleTarget' => array(
              'className' => 'SaleTarget',
              'foreignKey' => 'territory_id',
              'dependent' => false,
              'conditions' => '',
              'fields' => '',
              'order' => '',
              'limit' => '',
              'offset' => '',
              'exclusive' => '',
              'finderQuery' => '',
              'counterQuery' => ''
              ),
              'SaleTargetMonth' => array(
              'className' => 'SaleTargetMonth',
              'foreignKey' => 'territory_id',
              'dependent' => false,
              'conditions' => '',
              'fields' => '',
              'order' => '',
              'limit' => '',
              'offset' => '',
              'exclusive' => '',
              'finderQuery' => '',
              'counterQuery' => ''
              ) */
    );

    public function filter($params, $conditions) {
        $conditions = array();
        /*if (!empty($params['DistRoute.office_id'])) {
            $conditions[] = array('DistRoute.office_id' => $params['DistRoute.office_id']);
        }
        if (!empty($params['DistRoute.name'])) {
            $conditions[] = array('DistRoute.name LIKE' => '%' . $params['DistRoute.name'] . '%');
        }*/
        return $conditions;
    }

}
