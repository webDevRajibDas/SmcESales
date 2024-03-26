<?php

App::uses('AppModel', 'Model');

/**
 * Territory Model
 *
 * @property Office $Office
 * @property Market $Market
 * @property TerritoryPerson $TerritoryPerson
 */
class DistSalesRepTransferHistory extends AppModel {

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
        'name' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty')
            ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Territory already exists.'
            )
        ),
        'office_id' => array(
            'NotMustBeEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Office Id field required.'
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
        'Territory' => array(
            'className' => 'Territory',
            'foreignKey' => 'territory_id',
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
        )
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
            /* 'SaleTarget' => array(
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
        $conditions[] = array('DistSalesRepTransferHistory.office_id' => $params['DistSalesRepTransferHistory.office_id']);
        if (!empty($params['DistSalesRepTransferHistory.name'])) {
            $conditions[] = array('DistSalesRepTransferHistory.name' => $params['DistSalesRepTransferHistory.name']);
        }
        return $conditions;
    }

}
