<?php

App::uses('AppModel', 'Model');

/**
 * Territory Model
 *
 * @property Office $Office
 * @property Market $Market
 * @property TerritoryPerson $TerritoryPerson
 */
class DistDistributorClose extends AppModel {

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
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
        'DistDistributor' => array(
            'className' => 'DistDistributor',
            'foreignKey' => 'dist_distributor_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
    /* public $hasOne = array(
      'SalesPerson' => array(
      'className' => 'SalesPerson',
      'foreignKey' => 'territory_id',
      'conditions' => '',
      'fields' => '',
      'order' => ''
      )
      ); */

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
      //pr($conditions);die();
       // $conditions = array();
        if (!empty($params['DistDistributorCloses.dist_distributor_id'])) {
            $conditions[] = array('DistDistributorCloses.dist_distributor_id' => $params['DistDistributorCloses.dist_distributor_id']);
        }
        return $conditions;
    }

}
